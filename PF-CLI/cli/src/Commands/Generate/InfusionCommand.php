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

class InfusionCommand extends Command {
    protected function configure() {
        $this->setName('generate:infusion')
            ->setDescription('Infusions generator')
            ->addArgument('name', InputArgument::REQUIRED, 'Infusion name')
            ->addOption('author', 'a', InputOption::VALUE_OPTIONAL, 'Author name', '')
            ->addOption('email', 'e', InputOption::VALUE_OPTIONAL, 'Author email', '')
            ->addOption('website', 'w', InputOption::VALUE_OPTIONAL, 'Website', '')
            ->addOption('rights', 'r', InputOption::VALUE_OPTIONAL, 'Admin Rights. E.g. XX. Max. 4 characters', '')
            ->addOption('activate', 'A', InputOption::VALUE_OPTIONAL, 'Set this option to enable infusion', false)
            ->addOption('license', 'l', InputOption::VALUE_OPTIONAL, 'License type', 'agpl');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $gen = new AddonGenerator();
        $core = new CoreFunctions();

        $name = $core->normalize($input->getArgument('name'));
        $author = $input->getOption('author');
        $email = $input->getOption('email');
        $website = $input->getOption('website');
        $rights = $input->getOption('rights');
        $activate = $input->getOption('activate');
        $license = $input->getOption('license');

        $gen->setBoilerplatePath(__DIR__.'/../../boilerplates/infusion/');
        $folder_name = str_replace(' ', '_', strtolower($name));
        $root = $core->getCurrentRoot();
        $gen->setTargetPath($root.'infusions/');

        $rights = strtoupper(strlen($rights) <= 4 ? $rights : substr($rights, 0, 4));
        $length = [2, 3, 4];
        $rights = !empty($rights) ? $rights : substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length[array_rand($length)]);

        $locale_prefix = strtolower(strlen($rights) <= 4 ? $rights : substr($rights, 0, 4));

        $license = $gen->license($license);

        $gen->setReplace([
            'folder_name'   => $folder_name,
            'INF_EXIST'     => strtoupper($folder_name),
            'ADDON_NAME'    => $folder_name,
            'YOUR_NAME'     => $author,
            'YOUR_EMAIL'    => $email,
            'YOUR_WEBSITE'  => $website,
            'ADMIN_RIGHTS'  => $rights,
            'LOCALE_PREFIX' => $locale_prefix,
            'LICENSE_TEXT'  => $license['text']
        ]);

        if ($gen->generate()) {
            $output->writeln(sprintf('<fg=green>Infusion "%s" has been generated.</>', $name));
            $output->writeln(sprintf('In path: %s', $gen->getTargetPath().$folder_name));
        } else {
            $output->writeln('<error>Failed to generate infusion.</>');

            return Command::FAILURE;
        }

        if ($activate !== false) {
            if ($gen->activate('infusion', $name, $folder_name)) {
                $output->writeln(sprintf('<fg=green>Infusion "%s" has been activated.</>', $name));
            } else {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
