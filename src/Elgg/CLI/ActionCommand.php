<?php

namespace Elgg\CLI;

use Elgg\Application;
use Elgg\Http\Request;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * route CLI command
 */
class RouteCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('action')
				->setDescription('Execute an action')
				->addArgument('action_name', InputArgument::REQUIRED, 'Name of the action')
				->addOption('as', null, InputOption::VALUE_OPTIONAL, 'Username of the user to login');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle() {

		$action = trim($this->argument('action'), '/');
		$uri = "action/$action";

		$site_url = elgg_get_site_url();
		$uri = elgg_normalize_url($uri);
		$path_key = Application::GET_PATH_KEY;

		$parameters = [];

		$ts = time();
		$parameters['__elgg_ts'] = $ts;
		$parameters['__elgg_token'] = _elgg_services()->actions->generateActionToken($ts);

		$request = Request::create("?$path_key=" . urlencode($uri), 'POST', $parameters);

		$cookie_name = _elgg_services()->config->getCookieConfig()['session']['name'];
		$session_id = _elgg_services()->session->getId();
		$request->cookies->set($cookie_name, $session_id);

		$request->headers->set('Referer', elgg_normalize_url('cli'));
		$request->headers->set('X-Elgg-Ajax-API', 2);
		elgg_set_viewtype('json');

		Application::index();
	}

}
