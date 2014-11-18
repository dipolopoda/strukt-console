<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* orm:convert-mapping       Generate Annotation Mappings
* 
* Usage:
*	
*      orm:convert-mapping [--from-database] [--namespace <namespace>] [<type_to_generate>] <path_to_entities>
*
* Arguments:
*
*      type_to_generate     Argument options (xml|yaml|annotation)
*      path_to_entities     Path to generate entities
*
* Options:
*
*      --from-database      Database name
*      --namespace          Namespace
*/
class DoctrineGenerateEntitiesCommand extends \Strukt\Console\Command{

	public function execute(Input $in, Output $out){

		//
	}
}