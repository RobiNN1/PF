<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PFCli\Commands\Core;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command {
    protected function configure() {
        $this->setName('core:install')
            ->setDescription('Install PHPFusion');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('W.I.P.');

        return Command::SUCCESS;
    }
}
