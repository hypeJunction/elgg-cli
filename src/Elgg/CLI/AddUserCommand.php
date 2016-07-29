<?php

namespace Elgg\CLI;

use Elgg\Application;
use Exception;
use RegistrationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * user:add CLI command
 */
class AddUserCommand extends Command {

	protected function configure() {
		$this->setName('user:add')
				->setDescription('Add a new user')
				->addArgument('username', InputArgument::REQUIRED, 'Username')
				->addArgument('name', InputArgument::REQUIRED, 'Display Name')
				->addArgument('email', InputArgument::REQUIRED, 'Email Address')
				->addOption('admin', null, InputOption::VALUE_NONE, 'If set, will make the user an admin')
				->addOption('notify', null, InputOption::VALUE_NONE, 'If set, will send a notification to the new user');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		Application::start();

		$admin = $input->getOption('admin');
		$notify = $input->getOption('notify');
		
		$name = $input->getArgument('name');
		$username = $input->getArgument('username');
		$email = $input->getArgument('email');

		$helper = $this->getHelper('question');
		
		$question = new Question('Enter new user password? Leave empty to autegenerate');
		$question->setHidden(true);
		$question->setHiddenFallback(false);

		$password = $helper->ask($input, $output, $question);
		if (empty($password)) {
			$password = generate_random_cleartext_password();
		}

		try {
			$guid = register_user($username, $password, $name, $email);
			$user = get_entity($guid);

			$user->admin_created = true;
			elgg_set_user_validation_status($user->guid, true, 'cli');

			$params = [
				'user' => $user,
				'password' => $password,
			];

			if (!elgg_trigger_plugin_hook('register', 'user', $params, TRUE)) {
				$ia = elgg_set_ignore_access(true);
				$user->delete();
				elgg_set_ignore_access($ia);
				// @todo this is a generic messages. We could have plugins
				// throw a RegistrationException, but that is very odd
				// for the plugin hooks system.
				throw new RegistrationException(elgg_echo('registerbad'));
			}

			if ($admin) {
				$ia = elgg_set_ignore_access(true);
				$user->makeAdmin();
				elgg_set_ignore_access($ia);
			}

			if ($notify) {
				$subject = elgg_echo('useradd:subject', array(), $user->language);
				$body = elgg_echo('useradd:body', array(
					$name,
					elgg_get_site_entity()->name,
					elgg_get_site_entity()->url,
					$username,
					$password,
						), $user->language);

				notify_user($user->guid, elgg_get_site_entity()->guid, $subject, $body, [
					'password' => $password,
				]);
			}

			if ($user->isAdmin()) {
				$output->writeln("New admin user has been registered [guid: $user->guid]");
			} else {
				$output->writeln("New user has been registered [guid: $user->guid]");
			}
		} catch (Exception $ex) {
			$output->writeln("Exception: " . $ex->getMessage());
			return;
		}
	}

}
