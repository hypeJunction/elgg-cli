<?php

namespace Elgg\CLI;

use Elgg\Application;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * site:url CLI command
 */
class SiteUrlCommand extends Command {

	protected function configure() {
		$this->setName('site:url')
				->setDescription('Display or change site url')
				->addArgument('url', InputArgument::OPTIONAL, 'New site url');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		Application::start();

		$url = $input->getArgument('url');

		try {
			$site = elgg_get_site_entity();
			if ($url) {
				if (!filter_var($url, FILTER_VALIDATE_URL)) {
					throw new Exception("$url is not a valid URL");
				}

				// make sure the URL ends with a slash
				$url = rtrim($url, '/');
				$url .= '/';

				$ia = elgg_set_ignore_access(true);
				$site->url = $url;
				if ($site->save()) {
					$output->writeln("Site URL has been changed");
				} else {
					$output->writeln("Site URL could not be changed");
				}
				elgg_set_ignore_access($ia);
			}
			$output->writeln("Current site URL: $site->url");
		} catch (Exception $ex) {
			$output->writeln("Exception: " . $ex->getMessage());
			return;
		}
	}

}
