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

class PageCommand extends Command {
    protected function configure() {
        $this->setName('generate:page')
            ->setDescription('Pages generator')
            ->addArgument('name', InputArgument::REQUIRED, 'Page name')
            ->addOption('author', 'a', InputOption::VALUE_OPTIONAL, 'Author name', '')
            ->addOption('license', 'l', InputOption::VALUE_OPTIONAL, 'License type', 'agpl');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $gen = new AddonGenerator();
        $core = new CoreFunctions();
        $author = $input->getOption('author');
        $name = $core->normalize($input->getArgument('name'));

        $license = $input->getOption('license');

        $gen->setBoilerplatePath(__DIR__.'/../../boilerplates/page.php', true);
        $folder_name = str_replace(' ', '_', $name);
        $root = $core->getCurrentRoot();
        $gen->setTargetPath($root);

        $license = $gen->license($license);

        $gen->setReplace([
            'folder_name'  => $folder_name,
            'YOUR_NAME'    => $author,
            'LICENSE_TEXT' => $license['text']
        ]);

        if ($gen->generate()) {
            $output->writeln(sprintf('<fg=green>Page "%s" has been generated.</>', $name));
            $output->writeln(sprintf('In path: %s', $gen->getTargetPath().$folder_name));
        } else {
            $output->writeln('<error>Failed to generate page.</>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
