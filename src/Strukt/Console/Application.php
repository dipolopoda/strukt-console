<?php

namespace Strukt\Console;

/**
* Strukt Console Application class
*
* @author Samuel Weru <pitsolu@gmail.com>
*/
class Application{

	/**
	* List of commands
	*
	* @var array $commands
	*/
	private $commands;

	/**
	* Name of console application
	*
	* @var string $name 
	*/
	private $name;

	/**
	* Left padding 
	*
	* @var int $padlen
	*/
	private $padlen = 0;

	/**
	* Construct
	*
	* @param string $name console application name
	*
	* @return void
	*/
	public function __construct($name){

		if(!empty($name))
			$this->name = $name;
		else
			$this->name = "Strukt Console";

		$this->add(new \Strukt\Console\Command\ConsoleCommand);
	}

	/**
	* Which OS
	*
	* @return string
	*/
	public function isWindows(){

		return strtoupper(substr(PHP_OS, 0, 3)) == "WIN";
	}

	/**
	* Add commands to console application
	*
	* Utiliese {@link DocBlockParser} to extract documentation
	* from command class
	*
	* @param \Strukt\Console\Command $command
	*
	* @return void
	*/
	public function add(\Strukt\Console\Command $command){

		$class = get_class($command);
		$docparser = new \Strukt\Console\DocBlockParser($class);
		$doclist = $docparser->parse();

		$command_alias = $doclist["command"]["alias"];
		$this->commands[$command_alias]["object"] = $command;
		$this->commands[$command_alias]["docparser"] = $docparser;
		$this->commands[$command_alias]["doclist"] = $doclist;

		if($this->padlen == 0 || strlen($doclist["command"]["alias"]) > $this->padlen)
			$this->padlen = strlen($doclist["command"]["alias"]);
	}

	/**
	* Execute console application
	*
	* @param array $argv parse in commandline parameters
	*
	* @return string
	*/
	public function run($argv){

		$isWin = $this->isWindows();

		$output = new \Strukt\Console\Output();
		$output
			->add("\n")
			->add(sprintf(($isWin)?"%s\n%s\n":"\033[1;32m%s\n%s\033[0m\n", $this->name, str_repeat("=", strlen($this->name))));
		
		try{

			if(empty(@$argv[1]))
				$argv[1] = "-h";

			switch(@$argv[1]){

				case "--list":
				case "-l":
					$output->add("\n");
					foreach($this->commands as $key=>$command)
						if(!$command["object"] instanceof \Strukt\Console\Command\ConsoleCommand)
							$output->add(sprintf(($isWin)?"%s %s\n":"\033[1;29m%s \033[1;32m%s\033[0m\n", 
											str_pad($command["doclist"]["command"]["alias"], $this->padlen), 
											$command["doclist"]["command"]["descr"]));
				break;
				case "--help":
				case "-h":
					$command = reset($this->commands);
					if(in_array(@$argv[1], $command["doclist"]["aliases"]) ||
						in_array(@$argv[1], array_keys($command["doclist"]["aliases"])))
							$output->add(sprintf(($isWin)?"%s\n":"\033[1;32m%s\033[0m\n", $command["docparser"]->getBlock()));
				break;
				default:
					if(in_array(@$argv[1], array_keys($this->commands))){

						$command = $this->commands[@$argv[1]];
						if(in_array(@$argv[2], array("-h", "--help"))){

							$output->add(sprintf(($isWin)?"%s\n":"\033[1;32m%s\033[0m\n", $command["docparser"]->getBlock()));
						}
						else{

							$input = new \Strukt\Console\Input($argv, $command["docparser"]);
							$input->getInputs();

							$command["object"]->execute($input, $output);
						}
					}
				break;
			}
		}
		catch(\Exception $e){

			return sprintf(($isWin)?"%s\n":"\033[1;41m%s\033[0m\n", $e->getMessage());
		}

		if(!$output->isEmpty())
			return $output->add("\n")->write();
	}
}
