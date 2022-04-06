<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PFCli\Commands\Generate;

use PFCli\CoreFunctions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AdminThemeCommand extends Command {
    protected function configure() {
        $this->setName('generate:admintheme')
            ->setDescription('Admin themes generator')
            ->addArgument('name', InputArgument::REQUIRED, 'Admin theme name')
            ->addOption('author', 'a', InputOption::VALUE_OPTIONAL, 'Author name', '')
            ->addOption('oop', 'o', InputOption::VALUE_OPTIONAL, 'Set this option you want OOP admin theme', false)
            ->addOption('activate', 'A', InputOption::VALUE_OPTIONAL, 'Set this option to enable admin theme', false)
            ->addOption('license', 'l', InputOption::VALUE_OPTIONAL, 'License type', 'agpl');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $gen = new AddonGenerator();
        $core = new CoreFunctions();

        $name = $core->normalize($input->getArgument('name'));
        $author = $input->getOption('author');
        $oop = $input->getOption('oop') !== false ? 'oop/' : '';
        $activate = $input->getOption('activate');
        $license = $input->getOption('license');

        $gen->setBoilerplatePath(__DIR__.'/../../boilerplates/'.$oop.'admin_theme/');
        $folder_name = str_replace(' ', '_', $name);
        $root = $core->getCurrentRoot();
        $gen->setTargetPath($root.'themes/admin_themes/');

        $license = $gen->license($license);

        $gen->setReplace([
            'folder_name'  => $folder_name,
            'ADDON_NAME'   => $folder_name,
            'YOUR_NAME'    => $author,
            'LICENSE_TEXT' => $license['text']
        ]);

        if ($gen->generate()) {
            $output->writeln(sprintf('<fg=green>Admin theme "%s" has been generated.</>', $name));
            $output->writeln(sprintf('In path: %s', $gen->getTargetPath().$folder_name));
        } else {
            $output->writeln('<error>Failed to generate admin theme.</>');

            return Command::FAILURE;
        }

        if ($activate !== false) {
            if ($gen->activate('admintheme', $name, $folder_name)) {
                $output->writeln(sprintf('<fg=green>Admin theme "%s" has been activated.</>', $name));
            } else {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
