<?php 
error_reporting( E_ALL | E_STRICT); //make this less strict if your application requires so

//set_include_path(get_include_path() . PATH_SEPARATOR . 'path to phpFit folder');
set_include_path(get_include_path() . PATH_SEPARATOR . '../../phpFit');


//set_include_path(get_include_path() . PATH_SEPARATOR . 'path to fit shelf folder');
set_include_path(get_include_path() . PATH_SEPARATOR . '../shelf');

//folder in which this file is situated must be writable for the run_web.php script
$output = 'output.html'; //if only file name: in the same folder as the script.

//replace this by the path to your own fixtures folder
$fixturesDir = '../classes/fit';

//remove this if you do not need the book fixtures 
if (subStr($_GET['input_filename'],0, 5) == 'book/')
	$fixturesDir = 'book/src';

//only for usage with phpPeanuts
/*
include ("../classes/classSite.php");
$site = new Site('fit');
$site->startSession();
$sm = $site->getSecurityManager(); 
//if (!$sm->isAuthenticated($_REQUEST)) {
	//$auth = $sm->getAuthenticator();
	//print $auth->getSessionDataKey();
	//Gen::show($_SESSION);

	//$site->forwardToLoginPage($requestData);
	//HACK: login page does not work here
	print "First Log in to the main application";
	exit;
}
*/

?>