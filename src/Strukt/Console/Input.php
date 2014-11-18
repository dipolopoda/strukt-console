<?php

namespace Strukt\Console;

class Input{

	private $docparser = null;
	private $args = null;
	private $argv;

	public function __construct($argv, \Strukt\Console\DocBlockParser $docparser){

		$this->argv = $argv;
		$this->docparser = $docparser;
	}

	private function parse(){

		$doclist = $this->docparser->parse();

		$filename = array_shift($this->argv);
		$command = array_shift($this->argv);

		/**
		* Aquire parameters
		*/
		while($arg = array_shift($this->argv)){

			$quotes = array("'",'"');

			if(preg_match("/-[-\w]+/", $arg))
				if(in_array($arg, array_keys($doclist["options"]))){

					if($doclist["usage"][$arg]["input"])
						$args[$arg] = str_replace($quotes, "", array_shift($this->argv));
					else
						$args[$arg] = 1;
				}
				elseif(in_array($arg, array_keys($doclist["aliases"]))){

					$arg = $doclist["aliases"][$arg];
					if($doclist["usage"][$arg]["input"])
						$args[$arg] = str_replace($quotes, "", array_shift($this->argv));
					else
						$args[$arg] = 1;
				}
				else
					$unknowns[] = $arg;
			else
				if(!empty(key($doclist["arguments"]))){

					$args[key($doclist["arguments"])] = $arg;
					next($doclist["arguments"]);	
				}
				else
					$unknowns[] = $arg;
		}

		/**
		* Validate parameters
		*/
		if(!empty($doclist["usage"]))
			foreach($doclist["usage"] as $param=>$usage){

				$name = trim(trim($param, "-"),"--");

				if(@in_array($param, array_keys($args)))
					if(!empty($args[$param])){

						$this->args[$name] = $args[$param];
					}
					else{

						if($usage["input"])
							throw new \Exception("Input required for $param!");
					}
				else
					if(in_array($param, array_keys($doclist["arguments"]))){

						if($usage["required"])
							throw new \Exception("Argument $param is required!");
					}
					else{

						if($usage["required"])
							throw new \Exception("Option $param is required!");
					}
			}

		if(!empty($unknowns))
			throw new \Exception(sprintf("Unknown parameter/input %s!", current($unknowns)));
	}

	public function getInputs(){

		if(is_null($this->args))
			$this->parse();

		return $this->args;
	}

	public function get($key){

		if(!is_null($this->args))
			return $this->args[$key];
		else
			return null;
	}

	/**
	* Interactive unmasked input
	*/
	public function getInput($query){

		echo($query);
		$stdin = fopen('php://stdin', 'r');
		return fgets($stdin);
	}

	/**
	 * Troels Knak-Nielsen - sitepoint
	 *
	 * Interactively prompts for input without echoing to the terminal.
	 * Requires a bash shell or Windows and won't work with
	 * safe_mode settings (Uses `shell_exec`)
	 */
	public function getMaskedInput($prompt = "Enter Password:") {

		if (preg_match('/^win/i', PHP_OS)) {

		    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
		    file_put_contents(
		      $vbscript, 'wscript.echo(InputBox("'
		      . addslashes($prompt)
		      . '", "", "password here"))');
		    $command = "cscript //nologo " . escapeshellarg($vbscript);
		    $password = rtrim(shell_exec($command));
		    unlink($vbscript);

		    return $password;
	  	} 
	  	else {

		    $command = "/usr/bin/env bash -c 'echo OK'";
		    if (rtrim(shell_exec($command)) !== 'OK') {
		      trigger_error("Can't invoke bash");
		      return;
		    }
		    $command = "/usr/bin/env bash -c 'read -s -p \""
		      . addslashes($prompt)
		      . "\" mypassword && echo \$mypassword'";
		    $password = rtrim(shell_exec($command));
		    echo "\n";
		    
		    return $password;
	 	}
	}
}