<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* mysql:setup          MySQL Setup
*/
class MySQLSetUpCommand extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		$username = $in->getInput("Username:");
		$nickname = $in->getInput("Nickname:");
		$password = $in->getMaskedInput("Password:");
		$cpassword = $in->getMaskedInput("Confirm Password:");

		$out->add(sprintf("%s:%s:%s:%s", $username, $nickname, $password, $cpassword));
	}
}