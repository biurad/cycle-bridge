<?php

declare(strict_types=1);

/*
 * This file is part of Biurad opensource projects.
 *
 * PHP version 7.2 and above required
 *
 * @author    Divine Niiquaye Ibok <divineibok@gmail.com>
 * @copyright 2019 Biurad Group (https://biurad.com/)
 * @license   https://opensource.org/licenses/BSD-3-Clause License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Biurad\Cycle\Annotated;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Relation\RelationInterface;
use Cycle\Annotated\Configurator;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationException as DoctrineException;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Embeddings implements GeneratorInterface
{
    /** @var class-string[] */
    private $locator;

    /** @var AnnotationReader */
    private $reader;

    /** @var Configurator */
    private $generator;

    /**
     * @param class-string[]        $locator
     * @param null|AnnotationReader $reader
     */
    public function __construct(array $classes, AnnotationReader $reader = null)
    {
        $this->locator   = $classes;
        $this->reader    = $reader ?? new AnnotationReader();
        $this->generator = new Configurator($this->reader);
    }

    /**
     * @param Registry $registry
     *
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($this->locator as $class) {
            try {
                $class = new ReflectionClass($class);

                /** @var Embeddable $em */
                $em = $this->reader->getClassAnnotation($class, Embeddable::class);
            } catch (DoctrineException $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($em === null) {
                continue;
            }

            $e = $this->generator->initEmbedding($em, $class);

            $this->verifyNoRelations($e, $class);

            // columns
            $this->generator->initFields($e, $class, $em->getColumnPrefix());

            // register entity (OR find parent)
            $registry->register($e);
        }

        return $registry;
    }

    /**
     * @param EntitySchema    $entity
     * @param ReflectionClass $class
     */
    public function verifyNoRelations(EntitySchema $entity, ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $ann = $this->reader->getPropertyAnnotations($property);
            } catch (DoctrineException $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            foreach ($ann as $ra) {
                if ($ra instanceof RelationInterface) {
                    throw new AnnotationException(
                        "Relations are not allowed within embeddable entities in `{$entity->getClass()}`"
                    );
                }
            }
        }
    }
}
