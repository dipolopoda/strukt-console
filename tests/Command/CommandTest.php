<?php

class CommandTest extends PHPUnit_Framework_TestCase{

	public function setUp(){

		$this->app = new Strukt\Console\Application("Strukt Console");
		$this->app->add(new Command\DoctrineGenerateEntitiesCommand);
		$this->app->add(new Command\MySQLAuthCommand);

		$name = "Strukt Console";
		$isWin = $this->app->isWindows();
		$this->$name = sprintf(($isWin)?"\n%s\n":"\033[1;32m%s\n%s\033[0m\n", $name, str_repeat("=", strlen($name)));
	}

	public function testMySQLAuthCommand(){

		$mysqlCmd = "console mysql:auth payroll -u root -p p@55w0rd";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals("payroll:root:p@55w0rd", $hash);
	}
}