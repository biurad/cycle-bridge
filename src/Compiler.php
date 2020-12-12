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

namespace Biurad\Cycle;

use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Schema;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use ReflectionClass;
use Spiral\Database\Exception\CompilerException;

final class Compiler
{
    /** @var array */
    private $result = [];

    /** @var GeneratorInterface[] */
    private $generators;

    /** @var array */
    private $defaults = [
        Schema::MAPPER     => Mapper::class,
        Schema::REPOSITORY => Repository::class,
        Schema::SOURCE     => Source::class,
        Schema::CONSTRAIN  => null,
    ];

    /** @var \Doctrine\Inflector\Inflector */
    private $inflector;

    /**
     * @param GeneratorInterface[] $generators
     * @param array                $defaults
     */
    public function __construct(array $generators = [], array $defaults = [])
    {
        $this->generators = $generators;
        $this->defaults   = $defaults + $this->defaults;

        $this->inflector = (new \Doctrine\Inflector\Rules\English\InflectorFactory())->build();
    }

    /**
     * Add a schema generator
     *
     * @param GeneratorInterface $generator
     */
    public function addGenerator(GeneratorInterface $generator): void
    {
        $this->generators[] = $generator;
    }

    /**
     * Compile the registry schema.
     *
     * @param Registry  $registry
     *
     * @return array
     */
    public function compile(Registry $registry): array
    {
        foreach ($this->generators as $generator) {
            if (!$generator instanceof GeneratorInterface) {
                throw new CompilerException(\sprintf(
                    'Invalid generator `%s`',
                    \is_object($generator) ? \get_class($generator) : \gettype($generator)
                ));
            }

            $registry = $generator->run($registry);
        }

        foreach ($registry->getIterator() as $entity) {
            if ($this->getPrimary($entity) === null) {
                // incomplete entity, skip
                continue;
            }

            $this->compute($registry, $entity);
        }

        return $this->result;
    }

    /**
     * Get compiled schema result.
     *
     * @return array
     */
    public function getSchema(): array
    {
        return $this->result;
    }

    /**
     * Compile entity and relation definitions into packed ORM schema.
     *
     * @param Registry $registry
     * @param Entity   $entity
     */
    protected function compute(Registry $registry, Entity $entity): void
    {
        $schema = [
            Schema::ENTITY       => $entity->getClass(),
            Schema::SOURCE       => $entity->getSource() ?? $this->defaults[Schema::SOURCE],
            Schema::MAPPER       => $entity->getMapper() ?? $this->defaults[Schema::MAPPER],
            Schema::REPOSITORY   => $entity->getRepository() ?? $this->defaults[Schema::REPOSITORY],
            Schema::CONSTRAIN    => $entity->getConstrain() ?? $this->defaults[Schema::CONSTRAIN],
            Schema::SCHEMA       => $entity->getSchema(),
            Schema::PRIMARY_KEY  => $this->getPrimary($entity),
            Schema::COLUMNS      => $this->renderColumns($entity),
            Schema::FIND_BY_KEYS => $this->renderReferences($entity),
            Schema::TYPECAST     => $this->renderTypecast($entity),
            Schema::RELATIONS    => $this->renderRelations($registry, $entity),
        ];

        if ($registry->hasTable($entity)) {
            $schema[Schema::DATABASE] = $registry->getDatabase($entity);
            $schema[Schema::TABLE]    = $registry->getTable($entity);
        }

        // table inheritance
        foreach ($registry->getChildren($entity) as $child) {
            $this->result[$child->getClass()]                    = [Schema::ROLE => $entity->getRole()];
            $schema[Schema::CHILDREN][$this->childAlias($child)] = $child->getClass();
        }

        \ksort($schema);
        $this->result[$entity->getRole()] = $schema;
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected function renderColumns(Entity $entity): array
    {
        $schema = [];

        foreach ($entity->getFields() as $name => $field) {
            $schema[$name] = $field->getColumn();
        }

        return $schema;
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected function renderTypecast(Entity $entity): array
    {
        $schema = [];

        foreach ($entity->getFields() as $name => $field) {
            if ($field->hasTypecast()) {
                $schema[$name] = $field->getTypecast();
            }
        }

        return $schema;
    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected function renderReferences(Entity $entity): array
    {
        $schema = [$this->getPrimary($entity)];

        foreach ($entity->getFields() as $name => $field) {
            if ($field->isReferenced()) {
                $schema[] = $name;
            }
        }

        return \array_unique($schema);
    }

    /**
     * @param Registry $registry
     * @param Entity   $entity
     *
     * @return array
     */
    protected function renderRelations(Registry $registry, Entity $entity): array
    {
        $result = [];

        foreach ($registry->getRelations($entity) as $name => $relation) {
            $result[$name] = $relation->packSchema();
        }

        return $result;
    }

    /**
     * @param Entity $entity
     *
     * @return null|string
     */
    protected function getPrimary(Entity $entity): ?string
    {
        foreach ($entity->getFields() as $name => $field) {
            if ($field->isPrimary()) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Return the unique alias for the child entity.
     *
     * @param Entity $entity
     *
     * @return string
     */
    protected function childAlias(Entity $entity): string
    {
        $r = new ReflectionClass($entity->getClass());

        return $this->inflector->classify($r->getShortName());
    }
}
