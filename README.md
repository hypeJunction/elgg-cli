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

# Add a new user
vendor/bin/elgg-cli user:add [--admin] [--notify] <username> <name> <email>

# Display or change site URL
vendor/bin/elgg-cli site:url <new_url>

# Display or change root path
vendor/bin/elgg-cli config:path <new_path>

# Display or change data directory path
vendor/bin/elgg-cli config:dataroot <new_path>

```

