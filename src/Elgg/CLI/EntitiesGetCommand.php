<?php

namespace Elgg\CLI;

use ElggBatch;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;

/**
 * entities:get CLI command
 */
class EntitiesGetCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('entities:get')
				->setDescription('Returns a list of entities that match the criteria')
				->addOption('guid', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Entity GUID(s)')
				->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Entity type')
				->addOption('subtype', null,  InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Entity subtype(s)')
				->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of records to show')
				->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset', 0)
				->addOption('as', null, InputOption::VALUE_OPTIONAL, 'Username of the user to login');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle() {

		$batch = new ElggBatch('elgg_get_entities', [
			'guids' => $this->option('guid') ? : ELGG_ENTITIES_ANY_VALUE,
			'types' => $this->option('type') ? : 'object',
			'subtypes' => $this->option('subtype')  ? : ELGG_ENTITIES_ANY_VALUE,
			'limit' => $this->option('limit') ? : 100,
			'offset' => $this->option('offset') ? : 0,
		]);
		
		$table = new Table($this->output);
        $table->setHeaders([
			'GUID',
			'Title/name',
			'Description',
			'Owner',
			'Container',
			'Access',
		]);
		
		foreach ($batch as $entity) {
			$table->addRow([
				$entity->guid,
				$entity->getDisplayName(),
				elgg_get_excerpt($entity->descriptin),
				$owner = $entity->getOwnerEntity() ? $owner->getDisplayName() . ' [guid: ' . $owner->guid . ']' : '',
				$container = $entity->getContainerEntity() ? $container->getDisplayName() . ' [guid: ' . $container->guid . ']' : '',
				get_readable_access_level($entity->access_id) . ' [' . $entity->access_id . ']',
			]);
		}
		
		$table->render();
	}

}
