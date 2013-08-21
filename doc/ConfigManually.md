TO INSTALL AND RUN EXAMPLES
===========================

1. You need a http server with PHP 5.1 or higher. For security reasons it should only be accessable 
   to you and those who need to run tests.

2. Download or fetch PhpFit from https://github.com/metaclass-nl/phpfit

3. Extract and/or upload the PhpFit files and folders into a folder on your http server

4. Download or fetch Fit Shelf from https://github.com/metaclass-nl/fit-shelf

5.  Extract and/or upload the shelf subfolder from the fit_shelf folder to a folder on your http server

6. Edit the conf_shelf.php file in the script folder you copied in step 4 and correct the include paths
   with respect to the locations of phpfit and the shelf folder on your server.
   
7. If necessary make the folder in which phpFits output file $output is situated 
   writable for the  run-web.php script.

8. Enter the url on your http server to index.html from the script/book folder in your browser

9. You should see some hyperlinks to some tests from the book. Click one that is not under development.


TO RUN YOUR OWN TESTS
=====================

1. Make your own fixtures folder (may be outside of the fit shelf folder)
   and set $fixturesDir in conf_shelf.php to point to it.
   
2. Upload you own Fixture file to your fixtures folder

3. Make your own tests folder  (may be outside of the fit shelf folder)

4. Upload your own test to your tests folder 
   
5. Enter the url in your browser to run-web.php?input_filename= folowed by the relative 
    path from run-web.php to your test
    
6. You should see your own test results in your browser 