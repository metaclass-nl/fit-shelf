<?php
require_once 'conf_shelf.php';
require_once 'PHPFIT.php';

if(!isset($_GET['input_filename'])) {
    die('no input file received!');
}

//fitshelf/fitlibrary requires fixturedir to be set on FixtureLoader
require_once 'PHPFIT/FixtureLoader.php';
PHPFIT_FixtureLoader::setFixturesDirectory($fixturesDir);
//PHPFIT_FixtureLoader::addFixturesDirectory('');

if ($_GET['input_filename'][0]=='/'
	|| strPos($_GET['input_filename'], '..') !== false 
	|| preg_match("'[^A-Za-z0-9_\-./]'", $_GET['input_filename']))
	die("Unsafe file name: ". $_GET['input_filename']);

PHPFIT::run($_GET['input_filename'], $output, $fixturesDir);

echo file_get_contents( $output, true );
?>
