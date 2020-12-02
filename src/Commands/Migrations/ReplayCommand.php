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

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ReplayCommand extends AbstractCommand
{
    protected static $defaultName = 'migrations:replay';

    /**
     * {@inheritdoc}
     */
    protected function defineDescription(): string
    {
        return 'Replay (down, up) one or multiple migrations';
    }

    /**
     * {@inheritdoc}
     */
    protected function defineOption(): array
    {
        return [new InputOption('all', 'a', InputOption::VALUE_NONE, 'Replay all migrations.')];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->verifyEnvironment($input, $io)) {
            //Making sure we can safely migrate in this environment
            return 1;
        }

        $rollback = ['--force' => true];
        $migrate  = ['--force' => true];

        if ($input->getOption('all')) {
            $rollback['--all'] = true;
        } else {
            $migrate['--one'] = true;
        }

        $output->writeln('Rolling back executed migration(s)...');
        $this->getApplication()->find('migrations:rollback')
            ->run(new ArrayInput($rollback), $output);

        $output->writeln('');

        if ($io->confirm('Do you want to execute <info>migrations:start</info> immediately?', false)) {
            $output->writeln('Executing outstanding migration(s)...');
            $this->getApplication()->find('migrations:start')
                ->run(new ArrayInput($migrate), $output);
        }

        return 0;
    }
}
