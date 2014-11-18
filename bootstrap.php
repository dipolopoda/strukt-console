<?php

require "lib/Symfony/Component/ClassLoader/UniversalClassLoader.php";

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(

    'Strukt' => 'src',
    'Command' => 'fixtures/src'
));
$loader->register();