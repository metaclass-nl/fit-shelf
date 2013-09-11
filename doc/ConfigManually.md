TO INSTALL AND RUN EXAMPLES
===========================

1. You need a http server with PHP 5.3.2 or higher. For security reasons it should only be accessable 
   to you and those who need to run tests.

2. Download or fetch PhpFit from https://github.com/metaclass-nl/phpfit

3. Extract and/or upload the PhpFit files and folders into a folder on your http server

4. Download or fetch Fit Shelf from https://github.com/metaclass-nl/fit-shelf

5.  Extract and/or upload the shelf subfolder from the fit_shelf folder to a folder on your http server

6. Edit the examples/config/config.php file in the folder you copied in step 5 and correct the include paths
   with respect to the location of phpfit on your server.
   
7. If necessary create a file with the name from $fitConfig->output and make it writable for the run-web.php script.
   If no path it should be situated in the same folder as the run-web.php script.

8. Enter the url on your http server to index.php from the examples folder in your browser

9. You should see some hyperlinks to some tests from the book and from PHPFIT. Click one 
   (Fig5TestDiscountVariousTables.html is still under develpment and will cause an errror).
   
   Alternatively you may run run-cli.php on the command line with the test file name as the argument.


TO RUN YOUR OWN TESTS
=====================

1. Make your own fixtures folder and add it to $fitConfig->nameSpacedMap. 
   Be aware that only one folder will be used per (non)namespace for autoloading. 
   However, PHPFIT can load unnamespaced fixtures from aditional folders if you call
   PHPFIT_FixtureLoader::addFixturesDirectory or from the include path.
   
2. Upload you own Fixture files to your fixtures folder

3. Eventually make aditional folders for the application classes you want to test and add it to $fitConfig->nameSpacedMap
   or to the include path.
   
4. Eventually upload the application classes.

5. Make your own tests folder and add it to $fitConfig->exampleDirs.

6. Upload your own test to your tests folder.
   
5. Enter the url in your browser to index.php from the examples folder on your http server.
    
6. Click on one of your own tests


TO LOAD YOUR OWN TYPE ADAPTERS
==============================

You may explicitly include_once your own type adapters from anywhere,
 
or you may add your own type adapters to the src/PHPFIT/TypeAdapters so that the ClassLoader will autoload them,

or you may use Composer, see the [fit-skeleton package](https://github.com/metaclass-nl/fit-skeleton).     