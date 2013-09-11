<?php 
error_reporting( E_ALL | E_STRICT); //make this less strict if your application requires so

set_include_path(get_include_path(). PATH_SEPARATOR. '../../phpfit'); //replace with actual path to phpfit folder 

$fitConfig = new StdClass();

$fitConfig->exampleDirs["Fit Shelf"] = "tests";
$fitConfig->exampleDirs["PHPFIT"] = "../../phpfit/examples/input";
//add your own input folders here

//may add relative paths from the run scripts to your own fixtures folders
//if (class_exists('fitshelf\FixtureLoader')) {
//    PHPFIT_FixtureLoader::addFixturesDirectory('src');
//}

//map for name spaced and PEAR style auto classloading
$fitConfig->nameSpacedMap = array(
    'fitshelf\\' => '../src', 
    'PHPFIT_TypeAdapter_' => '../src', 
    '' => 'src');

//this file must be writable for PHP
$fitConfig->output = 'output.html';

?>