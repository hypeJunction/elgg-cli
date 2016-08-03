<?php

namespace Elgg\CLI;

use ElggInstaller;

/**
 * install CLI command
 */
class InstallCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('install')->setDescription('Install Elgg');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle() {

		$params = array(
			/**
			 * Admin account
			 */
			'displayname' => 'Administrator',
			'username' => $this->ask('Enter admin username: ', 'admin'),
			'password' => $this->ask('Enter admin password: ', null, true),
			'email' => $email = $this->ask('Enter admin email: '),
			/**
			 * Database parameters
			 */
			'dbuser' => $this->ask('Enter database username: '),
			'dbpassword' => $this->ask('Enter database password: ', null, true),
			'dbname' => $this->ask('Enter database name: '),
			'dbprefix' => $this->ask('Enter database prefix [elgg_]: ', 'elgg_'),
			/**
			 * Site settings
			 */
			'sitename' => $this->ask('Enter site name: '),
			'siteemail' => $this->ask("Enter site email [$email]: ", $email),
			'wwwroot' => $this->ask('Enter site URL [http://localhost/]: ', 'http://localhost/'),
			'dataroot' => $this->ask('Enter data directory path: '),
			// timezone
			'timezone' => 'UTC'
		);

		global $CONFIG;
		$CONFIG = new \stdClass();
		$CONFIG->system_cache_enabled = false;

		foreach ($params as $key => $value) {
			$CONFIG->$key = $value;
		}

		$installer = new ElggInstaller();
		$htaccess = !is_file(\Elgg\Filesystem\Directory\Local::root()->getPath('.htaccess'));
		$installer->batchInstall($params, $htaccess);

		system_message('Installation is successful');
	}

}
