<?php

namespace BespokeSupport\SqlReserved;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckCommand
 * @package BespokeSupport\SqlReserved
 */
class CheckCommand extends \Symfony\Component\Console\Command\Command
{
    public static $defaultName = 'check';

    /**
     * Symfony Console configure
     */
    public function configure()
    {
        $this->setDescription('Check a directories entities for uses of database reserved words');

        $this->addArgument('dir', InputArgument::REQUIRED);
        $this->addArgument('databases', InputArgument::OPTIONAL);
    }

    /**
     * Arguments - dir + comma separated list of engines
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('dir');

        $dir = rtrim($_SERVER['PWD'] . DIRECTORY_SEPARATOR . $path, '/') . '/';

        if (!$dir) {
            $output->writeln("<error>Directory does not exist | $dir</error>");
            exit;
        }

        $keywords = SqlReservedChecker::keywords($input->getArgument('databases'));

        if (!\count($keywords)) {
            $output->writeln("<error>No Keywords to check</error>");
            exit;
        }

        $autoload = false;

        $autoload = SqlReservedLoader::tryIncludeComposerAutoload($_SERVER['PWD']) ?: $autoload;
        $autoload = SqlReservedLoader::tryIncludeComposerAutoload($dir) ?: $autoload;
        $autoload = SqlReservedLoader::tryIncludeComposerAutoload($dir . '../') ?: $autoload;
        $autoload = SqlReservedLoader::tryIncludeComposerAutoload($dir . '../../') ?: $autoload;

        $res = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS);

        foreach ($res as $it) {
            if (!$autoload) {
                require_once $it->getRealPath();
            }

            /**
             * @var $it \SplFileInfo
             */
            $contents = file_get_contents($it->getRealPath());

            if (!preg_match('#namespace\s+(.+?);#sm', $contents, $m)) {
                $output->writeln("<error>Namespace not found in {$it->getRealPath()}</error>");
                continue;
            }

            $namespace = $m[1] ?? null;

            if (!preg_match('#class\s+([^\s]+)#sm', $contents, $m)) {
                $output->writeln("<error>Class not found in {$it->getRealPath()}</error>");
                continue;
            }

            $className = $m[1] ?? null;

            $fullClassName = $namespace . "\\" . $className;

            $errors = SqlReservedChecker::checkClassName($fullClassName, $keywords);

            if (!\count($errors)) {
                $output->writeln("<info>OK\t| $fullClassName</info>");
                continue;
            } else {
                $output->writeln("<error>ERR\t| $fullClassName</error>");
            }

            foreach ($errors as $error) {
                $output->writeln("\t  <error>$error</error>");
            }
        }
    }
}
