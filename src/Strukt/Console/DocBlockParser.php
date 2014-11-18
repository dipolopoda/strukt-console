<?php

namespace Strukt\Console;

class DocBlockParser{

	private $block;

	public function __construct($class){

		$reflector = new \ReflectionClass($class);

		$this->block = $reflector->getDocComment();
	}

	public function getBlock(){

		return implode("\n ", array_map(function($line){

			return trim(trim($line, "*"), "/");

		}, explode("\n", $this->block)));
	}

	private function sanitize($raw_block){

		return array_map(function($line){ 

			return preg_replace('!\s+!', ' ', trim(ltrim($line, "*")));

		}, explode("\n", trim(trim(trim($raw_block, "/**"), "*/"))));
	}

	public function parse(){

		$docblock_list = $this->sanitize($this->block);

		foreach($docblock_list as $line){

			if(!empty($line)){

				if(empty($case)){

					$case = "command";
				}
				elseif(in_array(strtolower($line), array("usage:", "arguments:", "options:"))){

					$case = trim(strtolower($line),":");
					continue;
				}

				switch($case){

					case "command":

						$parts = explode(" ", $line, 2);
						$block_list["command"] = array("alias"=>$parts[0], "descr"=>$parts[1]);
					break;
					case "usage":

						$parts = explode(" ", $line);
						$command = array_shift($parts);

						while($part = array_shift($parts)){

							if(preg_match("/^\[[-\w]+$/", $part)){

								$part = str_replace(array("["), "", $part);
								array_shift($parts);
								$block_list["usage"][$part] = array("required"=>false, "input"=>true);
							}
							elseif(preg_match("/^\[[-\w]+\]$/", $part)){

								$part = str_replace(array("[","]"), "", $part);
								$block_list["usage"][$part] = array("required"=>false, "input"=>false);
							}
							elseif(preg_match("/^(-|--)\w+$/", $part)){

								array_shift($parts);
								$block_list["usage"][$part] = array("required"=>true, "input"=>true);
							}
							elseif(preg_match("/^<\w+>$/", $part)){

								$part = str_replace(array("<",">"), "", $part);
								$block_list["usage"][$part] = array("required"=>true);
							}
							elseif(preg_match("/^\[<\w+>\]$/", $part)){

								$part = str_replace(array("[","]","<",">"), "", $part);
								$block_list["usage"][$part] = array("required"=>false);
							}
						}
					break;
					case "arguments":

						$parts = explode(" ", $line, 2);
						$block_list["arguments"][$parts[0]]["descr"] = $parts[1];
						if(strpos($line, "optional")!==false)
							$block_list["arguments"][$parts[0]]["optional"] = true;
						else
							$block_list["arguments"][$parts[0]]["optional"] = false;
					break;
					case "options":

						$parts = explode(" ", $line, 3);

						$details["descr"] = @$parts[2];
						if(preg_match("/^-\w$/", $parts[1])){

							$details["alias"] = $parts[1];
							$block_list["aliases"][$parts[1]] = $parts[0];
						}

						$block_list["options"][$parts[0]] = $details;
					break;	
				}
			}
		}

		return $block_list;
	}
}