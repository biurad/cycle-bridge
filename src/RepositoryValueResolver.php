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

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\Select;
use DivineNii\Invoker\Interfaces\ArgumentValueResolverInterface;
use ReflectionClass;
use ReflectionParameter;

/**
 * Find a role then Injects it into a repository class object,
 * returning the repository class object.
 *
 * @author Divine Niiquaye Ibok <divineibok@gmail.com>
 */
class RepositoryValueResolver implements ArgumentValueResolverInterface
{
    /** @var ORMInterface */
    private $orm;

    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ReflectionParameter $parameter, array $providedParameters)
    {
        $parameterClass = $parameter->getClass();

        if (!$parameterClass instanceof ReflectionClass) {
            return;
        }

        $schema = $this->orm->getSchema();

        foreach ($schema->getRoles() as $role) {
            $repository = $schema->define($role, Schema::REPOSITORY);

            if (
                $repository !== Select\Repository::class
                && $repository === $parameterClass->getName()
            ) {
                return $this->orm->getRepository($role);
            }
        }
    }
}
