CLI Tools for Elgg
==================

## Installation

 * For now just drop the package in to `/cli` in the root of your Elgg installation

## Run Commands

```sh

cd /path/to/elgg/

# Add a new user
php cli/application.php user:add [--admin] [--notify] <username> <name> <email>
```