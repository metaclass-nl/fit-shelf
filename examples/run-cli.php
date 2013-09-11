<?php
//usage: php run-cli.php tests/Fig1TestDisconnect.html
require_once 'config/config.php';

require_once '../src/fitshelf/ClassLoader.php';
require_once 'PHPFIT.php';

if( count( $argv ) < 2 ) {
	fwrite( STDERR, "Invalid number of arguments!!!\nUsage: phpfit path/to/input.html [path/to/output.html] [paths/to/fixtures]\n" );
	return 1;
}

$loader = new ClassLoader();
$loader->setSpaceMap($fitConfig->nameSpacedMap);
$loader->registerAutoLoad();

$output = isset($argv[2]) ? $argv[2] : 'output.html';
$fixturesDir = isset($argv[3]) ? $argv[3] : null;

echo PHPFIT::run($argv[1], $output, $fixturesDir) . "\n";

?>