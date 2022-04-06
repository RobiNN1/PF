<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PFCli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends Command {
    protected function configure() {
        $this->setName('info')
            ->setDescription('System info');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $system_os = PHP_MAJOR_VERSION < 7 ?
            php_uname() :
            sprintf('%s %s %s %s', php_uname('s'), php_uname('r'), php_uname('v'), php_uname('m'));

        $output->writeln(sprintf('OS: %s', $system_os));
        $output->writeln(sprintf('PHP Version: %s', phpversion()));
        $output->writeln(sprintf('php.ini used: %s', get_cfg_var('cfg_file_path')));

        return Command::SUCCESS;
    }
}
