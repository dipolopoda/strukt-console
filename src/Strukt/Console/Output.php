<?php

namespace Strukt\Console;

class Output{

	private $output;

	public function __construct(){

		$this->output = array();
	}

	public function add($output){

		$this->output[] = $output;

		return $this;
	}

	public function write(){

		return implode("", $this->output);
	}
}