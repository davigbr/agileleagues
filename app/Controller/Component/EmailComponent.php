<?php

App::uses('CakeEmail', 'Network/Email');

class EmailComponent extends Component {
	
	public function template($template, $viewVars) {
		$this->template = $template;
		$this->viewVars = $viewVars;
	}

	public function subject($subject) {
		$this->subject = $subject;
	}

	public function send($email) {
		$message = new CakeEmail();
		$message->config('smtp');
		$message->from(array('contact@agileleagues.com' => 'Agile Leagues'));
		$message->to($email);
		$message->template($this->template);
		$message->emailFormat('html');
		$message->viewVars($this->viewVars);
		$message->subject($this->subject);
		$message->send();
	}
}