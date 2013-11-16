<?php 
require_once '../examples/config/config.php';

if (!class_exists('PHPFIT')) {
    $fitConfig->nameSpacedMap[''] = '../examples/src';
    $fitConfig->output = '../examples/'. $fitConfig->output;

    require_once '../src/fitshelf/ClassLoader.php';
    require_once 'PHPFIT.php';
    
    $loader = new fitshelf\ClassLoader();
    $loader->setSpaceMap($fitConfig->nameSpacedMap);
    $loader->registerAutoLoad();
}

?>