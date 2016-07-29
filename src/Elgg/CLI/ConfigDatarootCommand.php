<?php

namespace Elgg\CLI;

use Elgg\Application;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * config:dataroot CLI command
 */
class ConfigDatarootCommand extends Command {

	protected function configure() {
		$this->setName('config:dataroot')
				->setDescription('Display or change data directory path')
				->addArgument('path', InputArgument::OPTIONAL, 'New data directory path');
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
				
				if (datalist_set('dataroot', $path)) {
					$output->writeln("Data directory path has been changed");
				} else {
					$output->writeln("Data directory path could not be changed");
				}
			}
			$output->writeln("Current data directory path: " . datalist_get('dataroot'));
		} catch (Exception $ex) {
			$output->writeln("Exception: " . $ex->getMessage());
			return;
		}
	}

}
