<?php

namespace Elgg;

use Elgg\CLI\AddUserCommand;
use Elgg\CLI\ConfigDatarootCommand;
use Elgg\CLI\ConfigPathCommand;
use Elgg\CLI\InstallCommand;
use Elgg\CLI\PluginsActivateCommand;
use Elgg\CLI\PluginsDeactivateCommand;
use Elgg\CLI\SiteFlushCacheCommand;
use Elgg\CLI\SiteUpgradeCommand;
use Elgg\CLI\SiteUrlCommand;
use Elgg\Filesystem\Directory\Local;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;

/**
 * CLI bootstrap
 */
class CLI {

	/**
	 * @var ConsoleApplication
	 */
	private $console;

	/**
	 * Constructor
	 *
	 * @param ConsoleApplication $console Console application instance
	 */
	public function __construct(ConsoleApplication $console) {
		$this->console = $console;
	}

	/**
	 * Lists default commands
	 * @return array
	 */
	private function getCommands() {
		return [
			SiteUrlCommand::class,
			SiteFlushCacheCommand::class,
			SiteUpgradeCommand::class,
			ConfigDatarootCommand::class,
			ConfigPathCommand::class,
			AddUserCommand::class,
			PluginsActivateCommand::class,
			PluginsDeactivateCommand::class,
			RoouteCommand::class,
		];
	}

	/**
	 * Add CLI tools to the console application
	 * @return void
	 */
	protected function bootstrap() {

		if ($this->isInstalled()) {
			Application::start();

			$commands = elgg_trigger_plugin_hook('commands', 'cli', null, $this->getCommands());
			foreach ($commands as $command) {
				if (class_exists($command) && is_subclass_of($command, Command::class)) {
					$this->console->add(new $command());
				}
			}
		} else {
			$this->console->add(new InstallCommand());
		}
	}

	/**
	 * Bootstrap and run console application
	 * @return void
	 */
	public function run() {
		$this->bootstrap();
		$this->console->run();
	}

	/**
	 * Check if Elgg has been installed
	 * @return bool
	 */
	private function isInstalled() {
		$path = Local::root()->getPath('engine/settings.php');
		if (!is_file($path)) {
			$path = Local::root()->getPath('elgg-config/settings.php');
		}
		return is_file($path);
	}

}
