<?php
use fitshelf\ClassLoader;

require_once 'config/config.php';
require_once '../src/fitshelf/ClassLoader.php';
require_once 'PHPFIT.php';

if(!isset($_GET['input_filename'])) {
    die('no input file received!');
}

$loader = new ClassLoader();
$loader->setSpaceMap($fitConfig->nameSpacedMap);
$loader->registerAutoLoad();

$unSafe = $_GET['input_filename'];
forEach($fitConfig->exampleDirs as $safe) 
{
    if (subStr($unSafe, 0, strLen($safe)) == $safe) { //begins with example dir path
        $unSafe = ltrim(subStr($unSafe, strLen($safe)), '/');
        break;
    }
}
if ($unSafe[0]=='/' || strPos($unSafe, '..') !== false || preg_match("'[^A-Za-z0-9_\-./]'", $unSafe))
	die("Unsafe file name: ". $_GET['input_filename']);

PHPFIT::run($_GET['input_filename'], $fitConfig->output); //PHPFIT is autoloaded because it is in the includepath

echo file_get_contents( $fitConfig->output, true );
?>
