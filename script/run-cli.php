<?php
//usage: php run-cli.php vendor/metaclass-nl/fit-shelf/script/book/tests/Fig1TestDisconnect.html
require_once 'conf_shelf.php';

require_once 'PHPFIT.php';
require_once 'PHPFIT/FixtureLoader.php';
PHPFIT_FixtureLoader::setFixturesDirectory($fixturesDir);

$output = isset($argv[2]) ? $argv[2] : 'output.html';

if (isset($argv[3])) {
	$fixturesDir = $argv[3];
}

if( count( $argv ) < 2 ) {
	fwrite( STDERR, "Invalid number of arguments!!!\nUsage: phpfit path/to/input.html [path/to/output.html] [paths/to/fixtures]\n" );
	return 1;
}

echo PHPFIT::run($argv[1], $output) . "\n";

?>