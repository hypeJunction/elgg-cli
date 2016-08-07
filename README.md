CLI Tools for Elgg
==================
![Elgg 2.2](https://img.shields.io/badge/Elgg-2.2-orange.svg?style=flat-square)

## Installation

### Project Scope

```sh
cd /path/to/elgg/
composer require hypejunction/elgg-cli:~1.0
vendor/bin/elgg-cli --help
```

### Global Scope

```sh
composer global require hypejunction/elgg-cli:~1.0
cd /path/to/elgg
# if you have composer bin in your environment variables use the shortcut
elgg-cli --help
```

## Run Commands

```sh
cd /path/to/elgg/

# Get help
vendor/bin/elgg-cli --help

# List all commands
vendor/bin/elgg-cli list

# Install Elgg
vendor/bin/elgg-cli install

# Flush caches
vendor/bin/elgg-cli site:flush_cache

# Run upgrades
vendor/bin/elgg-cli site:upgrade

# Activate plugins
vendor/bin/elgg-cli plugins:activate [--all]

# Deactivate plugins
vendor/bin/elgg-cli plugins:deactivate [--all]

# Add a new user
vendor/bin/elgg-cli user:add [--admin] [--notify]

# Display or change site URL
vendor/bin/elgg-cli site:url <new_url>

# Display or change root path
vendor/bin/elgg-cli config:path <new_path>

# Display or change data directory path
vendor/bin/elgg-cli config:dataroot <new_path>

# Request a page
vendor/bin/elgg-cli route <uri> <method> [--tokens] [--export] [--as]

# Execute an action
vendor/bin/elgg-cli action <action_name> [--as]

# Run cron
vendor/bin/elgg-cli route cron/run
```

## Custom Commands

Plugins can add their commands to the CLI application, by adding command class name via
`'commands','cli'` hoook. Command class must extend `\Symfony\Component\Console\Command\Command`.

```php
class MyCommand extends \Symfony\Component\Console\Command\Command {}

elgg_register_plugin_hook_handler('commands', 'cli', function($hook, $type, $return) {
	$return[] = MyCommand::class;
	return $return;
});
```
