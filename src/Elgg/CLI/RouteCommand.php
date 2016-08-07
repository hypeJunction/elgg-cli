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
		$this->setName('route')
				->setDescription('Route a request for a given path')
				->addArgument('uri', InputArgument::REQUIRED, 'URI of the request (route path)')
				->addArgument('ajax', InputArgument::OPTIONAL, 'AJAX api version (0 for non-ajax)', 0)
				->addArgument('method', InputArgument::OPTIONAL, 'HTTP method', 'GET')
				->addOption('tokens', null, InputOption::VALUE_NONE, 'Add CSRF tokens to the request')
				->addOption('json', null, InputOption::VALUE_NONE, 'Set viewtype to JSON')
				->addOption('bypass-walled-garden', null, InputOption::VALUE_NONE, 'Bypass walled garden');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handle() {

		$uri = '/' . ltrim($this->argument('uri'), '/');
		$ajax = (int) $this->argument('ajax');
		$method = $this->argument('method');
		$add_csrf_tokens = $this->option('tokens');

		$site_url = elgg_get_site_url();
		$uri = substr(elgg_normalize_url($uri), strlen($site_url));
		$path_key = Application::GET_PATH_KEY;

		$parameters = [];
		if ($add_csrf_tokens) {
			$ts = time();
			$parameters['__elgg_ts'] = $ts;
			$parameters['__elgg_token'] = _elgg_services()->actions->generateActionToken($ts);
		}

		if ($this->option('bypass-walled-garden')) {
			elgg_set_config('walled_garden', false);
		}

		$request = Request::create("?$path_key=" . urlencode($uri), $method, $parameters);

		$cookie_name = _elgg_services()->config->getCookieConfig()['session']['name'];
		$session_id = _elgg_services()->session->getId();
		$request->cookies->set($cookie_name, $session_id);

		$request->headers->set('Referer', elgg_normalize_url('cli'));

		if ($ajax) {
			$request->headers->set('X-Requested-With', 'XMLHttpRequest');
			if ($ajax >= 2) {
				$request->headers->set('X-Elgg-Ajax-API', (string) $ajax);
			}
		}

		if ($this->option('json')) {
			elgg_set_viewtype('json');
		}

		_elgg_services()->setValue('request', $request);
		Application::index();
	}

}
