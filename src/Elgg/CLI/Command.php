<?php

namespace Elgg\CLI;

use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Abstract command with some utility methods
 */
abstract class Command extends SymfonyCommand {

	/**
	 * @var InputInterface 
	 */
	protected $input;

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * {@inheritdoc}
	 */
	final public function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;

		$dump_message_registers = function() {
			$msgs = _elgg_services()->systemMessages->dumpRegister();
			if (!empty($msgs['success'])) {
				foreach ($msgs['success'] as $msg) {
					$this->write("<info>$msg</info>");
				}
			}
			if (!empty($msgs['error'])) {
				foreach ($msgs['error'] as $msg) {
					$this->write("<error>$msg</error>");
				}
			}
			return false;
		};

		elgg_register_plugin_hook_handler('forward', 'all', $dump_message_registers());

		// Allow calling action()
		// $ts = time();
		// $token = generate_action_token($ts);
		// set_input('__elgg_ts', $ts);
		// set_input('__elgg_token', $token);

		$result = $this->handle();

		$dump_message_registers();

		return $result;
	}

	/**
	 * Execute a command
	 * @return int|null
	 * @see Command::execute()
	 */
	abstract protected function handle();

	/**
	 * Ask a question
	 * 
	 * @param string $question  Question to ask
	 * @param mixed  $default   Default value
	 * @param bool   $hidden    Hide response
	 * @param bool   $required  User input is required
	 * @return mixed
	 */
	public function ask($question, $default = null, $hidden = false, $required = true) {

		$helper = $this->getHelper('question');

		$q = new Question($question, $default);

		if ($hidden) {
			$q->setHidden(true);
			$q->setHiddenFallback(false);
		}

		if ($required) {
			$q->setValidator([$this, 'assertNotEmpty']);
			$q->setMaxAttempts(2);
		}

		return $helper->ask($this->input, $this->output, $q);
	}

	/**
	 * Write messages to output buffer
	 *
	 * @param string|array $messages Messages
	 * @return void
	 */
	public function write($messages) {
		$this->output->writeln($messages);
	}

	/**
	 * Returns option value
	 * 
	 * @param string $name Option name
	 * @return mixed
	 */
	public function option($name) {
		return $this->input->getOption($name);
	}

	/**
	 * Returns argument value
	 *
	 * @param string $name Argument name
	 * @return string
	 */
	public function argument($name) {
		return $this->input->getArgument($name);
	}

	/**
	 * Question validator for required user response
	 * 
	 * @param mixed $answer User answer
	 * @return bool
	 */
	public function assertNotEmpty($answer) {
		if (empty($answer)) {
			throw new RuntimeException('Please enter a required answer');
		}
		return $answer;
	}

}