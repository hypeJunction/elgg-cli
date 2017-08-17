<?php

namespace Elgg\CLI;

use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


/**
 * site:url CLI command
 */
class ConfigSettingCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('config:setting')
				->setDescription('Changes a system config or plugin setting value')
				->addArgument('name', InputArgument::REQUIRED, 'Setting name')
				->addArgument('value', InputArgument::OPTIONAL, 'Setting new value, you can use \'unset\' to unset the setting')
				->addOption('plugin', null, InputOption::VALUE_OPTIONAL, 'Plugin ID');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle() {

		$name = $this->argument('name');
		$value = $this->argument('value');
		$plugin = $this->option('plugin');

		if ($plugin) {
			if (!elgg_plugin_exists($plugin)) {
				system_message("Abort! The plugin $plugin doesn't exist");
				return;
			}

			if (isset($value)) {
				if ($value == 'unset') {
					elgg_unset_plugin_setting($name,$plugin);
					system_message("$plugin:$name is now unset");
				} else {
					if (elgg_set_plugin_setting($name,$value,$plugin)) {
						system_message("New value for $plugin:$name is $value");
					}
				}
			} else {
				$actual = elgg_get_plugin_setting($name,$plugin,'none');
				system_message("Actual value for $plugin:$name is $actual");
			}

		} else {

			// System config
			if (isset($value)) {
				if ($value == 'unset') {
					unset_config($name,0);
					system_message("System config $name is now unset");
				} else {
					switch ($name) {
						case 'system_cache_enabled':
							if ($value == '0') {
								elgg_disable_system_cache();
							} else {
								elgg_enable_system_cache();
							}
							system_message("New value for system config $name is $value");
							break;
						case 'simplecache_enabled':
							if ($value == '0') {
								elgg_disable_simplecache();
							} else {
								elgg_enable_simplecache();
							}
							system_message("New value for system config $name is $value");
							break;
						default:
							if (elgg_save_config($name,$value,0)) {
								system_message("New value for system config $name is $value");
							}
							break;
					}
				}
			} else {
				$actual = elgg_get_config($name);
				system_message("Actual value for system config $name is $actual");
			}
		}
	}

}
