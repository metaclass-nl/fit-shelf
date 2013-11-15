<?php 
require_once '../examples/config/config.php';

require_once '../src/fitshelf/ClassLoader.php';
require_once 'PHPFIT.php';

$loader = new fitshelf\ClassLoader();
$loader->setSpaceMap($fitConfig->nameSpacedMap);
$loader->registerAutoLoad();


?>