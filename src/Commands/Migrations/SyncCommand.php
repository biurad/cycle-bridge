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

namespace Biurad\Cycle\Commands\Migrations;

use Biurad\Cycle\Compiler;
use Biurad\Cycle\Generators\ShowChanges;
use Biurad\Cycle\Migrator;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Migrations\Config\MigrationConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SyncCommand extends AbstractCommand
{
    protected static $defaultName = 'migrations:sync';

    /** @var Registry */
    private $registry;

    /** @var Compiler */
    private $compiler;

    public function __construct(Migrator $migrator, MigrationConfig $config, Registry $registry, Compiler $compiler)
    {
        $this->compiler = $compiler;
        $this->registry = $registry;

        parent::__construct($migrator, $config);
    }

    /**
     * {@inheritdoc}
     */
    protected function defineDescription(): string
    {
        return 'Sync Cycle ORM schema with database without intermediate migration (risk operation)';
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->verifyConfigured($output)) {
            return 1;
        }

        $this->compiler->addGenerator($show = new ShowChanges($output));
        $this->compiler->addGenerator(new SyncTables());
        $this->compiler->compile($this->registry);

        if ($show->hasChanges()) {
            $output->writeln("\n<info>ORM Schema has been synchronized</info>");
        }

        return 0;
    }
}
