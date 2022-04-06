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

class RequirementsCommand extends Command {
    protected function configure() {
        $this->setName('core:requirements')
            ->setDescription('Checks if the server meets the system requirements');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $php_current = phpversion();
        $php_required = '7.0';
        $valid = true;

        if (version_compare($php_current, $php_required) < 0) {
            $output->writeln(sprintf('<error>The current installed version "%s" is invalid, it requires "%s" or higher</error>', $php_current, $php_required));

            return Command::FAILURE;
        }

        $required_extensions = [
            'date',
            'dom',
            'filter',
            'gd',
            'hash',
            'json',
            'pcre',
            'pdo',
            'session',
            'SimpleXML',
            'SPL',
            'tokenizer',
            'xml'
        ];

        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $output->writeln(sprintf('<error>The extension "%s" is missing.</error>', $extension));
                $valid = false;
            }
        }

        if ($valid) {
            $output->writeln('<fg=green>Checks passed.</>');
        }

        return Command::SUCCESS;
    }
}
