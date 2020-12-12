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
use Cycle\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CycleCommand extends AbstractCommand
{
    protected static $defaultName = 'migrations:cycle';

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
        return 'Generate ORM schema migrations';
    }

    /**
     * {@inheritdoc}
     */
    protected function defineOption(): array
    {
        return [new InputOption('run', 'r', InputOption::VALUE_NONE, 'Automatically run generated migration.')];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->verifyConfigured($output)) {
            return 1;
        }

        foreach ($this->migrator->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                $output->writeln('<fg=red>Outstanding migrations found, run `migrate` first.</fg=red>');

                return 1;
            }
        }

        $this->compiler->addGenerator($show = new ShowChanges($output));
        $this->compiler->compile($this->registry);

        if ($show->hasChanges()) {
            $this->compiler->addGenerator(new GenerateMigrations($this->migrator->getRepository(), $this->config));
            $this->compiler->compile($this->registry);

            if ($input->getOption('run')) {
                return $this->getApplication()->find('migrations:start')
                    ->run($input, $output);
            }
        }

        return 0;
    }
}
