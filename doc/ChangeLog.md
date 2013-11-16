v0.5
----
- renamed examples/tests to examples/input
- Added output of examples to the repository
- Added tests folder with phpunit.xml and bootstrap.php for testing with PHPUnit
- Addes tests/ExapleTest.php that compares the actual output of the examples with the output from the repository 
- Added instructions for running unit tests to doc/ConfigManually.md

v0.4
----  
- introduced name spaces fitshelf, chat and PHPFIT_TypeAdapter  
- reorganised folders  
- added PSR-0 classloader  
- adapted scripts and config  
- added examples/index.php  
- adapted composer.json  
- removed support for phpPeanuts framework    
- In tests shelf. prefix is no longer supported, but you can use fitshelf.     
  It will be mapped by PHPFIT to fitshelf\ namespace.  

v0.3
----
- added composer.json
- adapted README.md to install and use with Composer from fit-skeleton,
  extracting doc/ConfigManually.md
- PHP version requirement increased to 5.1 because of PHPFIT requires it
- added script/run-cli.php for running from the command line
- adapted script/config_shelf.php to support run-cli.php
- added ChangeLog.md

v0.2 (tagged 1.0.beta2)
----------------------
- added more book fixtures and tests
- adapted to PhpFit clone on github.com/metaclass-nl/phpfit

v0.1 (tagged 1.0.beta1) 
----------------------
- comitted 1.0.beta1 release from www.metaclass.nl (please ignore this version number)
- ReadMe.txt renamed to README.md and script.conf_shelf.php adapted for testing