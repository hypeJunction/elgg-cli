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
				->addArgument('method', InputArgument::OPTIONAL, 'HTTP method', 'GET')
				->addOption('tokens', null, InputOption::VALUE_NONE, 'Add CSRF tokens to the request')
				->addOption('export', null, InputOption::VALUE_NONE, 'Attempt to export entity data on the page');
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

		$request = Request::create("?$path_key=" . urlencode($uri), $method, $parameters);

		$cookie_name = _elgg_services()->config->getCookieConfig()['session']['name'];
		$session_id = _elgg_services()->session->getId();
		$request->cookies->set($cookie_name, $session_id);

		$request->headers->set('Referer', elgg_normalize_url());

		if ($this->option('export')) {
			elgg_set_viewtype('json');
			$request->headers->set('X-Elgg-Ajax-API', '2');
		}

		_elgg_services()->setValue('request', $request);

		ob_start();
		Application::index();
		$output = ob_get_clean();

		if ($this->option('export')) {
			$json = json_decode($output);
			$this->write(var_export($json, true));
		} else {
			$this->write($output);
		}
	}

}
