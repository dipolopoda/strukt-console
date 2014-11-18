<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* mysql:auth          MySQL Authentication
* 
* Usage:
*   
*      mysql:auth <database> --username <username> --password <password> [--host <127.0.0.1>]
*
* Arguments:
*
*      database  MySQL database name - optional argument
* 
* Options:
* 
*      --username -u   MySQL Username
*      --password -p   MySQL Password
*      --host -h       MySQL Host - optional default 127.0.0.1
*/
class MySQLAuthCommand extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		$out->add(sprintf("%s:%s:%s", 
							$in->get("database"), 
							$in->get("username"), 
							$in->get("password")));
	}
}