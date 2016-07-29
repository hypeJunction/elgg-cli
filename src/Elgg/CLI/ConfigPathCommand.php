<?php

namespace Elgg\CLI;

use Elgg\Application;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * config:path CLI command
 */
class ConfigPathCommand extends Command {

	protected function configure() {
		$this->setName('config:path')
				->setDescription('Display or change root path')
				->addArgument('path', InputArgument::OPTIONAL, 'New root path');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		Application::start();

		$path = $input->getArgument('path');

		try {
			if ($path) {
				// make sure the path ends with a slash
				$path = rtrim($path, DIRECTORY_SEPARATOR);
				$path .= DIRECTORY_SEPARATOR;

				if (!is_dir($path)) {
					throw new Exception("$path is not a valid directory");
				}
				
				if (datalist_set('path', $path)) {
					$output->writeln("Root path has been changed");
				} else {
					$output->writeln("Root path could not be changed");
				}
			}
			$output->writeln("Current root path: " . datalist_get('path'));
		} catch (Exception $ex) {
			$output->writeln("Exception: " . $ex->getMessage());
			return;
		}
	}

}
