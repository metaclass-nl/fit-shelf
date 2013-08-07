INTRODUCTION

Fit Shelf is a reimplementation in PHP of the functionality of Fit Library as 
described in the book "Fit for developing Software" of Rick Mugridge
and Ward Cunningham, mostly chapters 10 and 28. For usage documentation
i refer to these chapters of this excellent book and http://fit.c2.com.

Fit shelf is not a port of Fit Library. The reason to reimplement was
that the porting of the  1175 KB java source files of Fit Library 
(not including Fit and examples) would have taken too much work. 
With only 63.5 KB in PHP source files* Fit Shelf is much simpeler. 
As its name suggest is does not pretend to be a complete library,
but rather a small shelf. But easyer to understand, use and port.
And written in PHP!

  
INSTALLATION AND CONFIGURATION

0. You need a http server with PHP 5.0 or higher. For security reasons it should only be accessable 
   to you and those who need to run tests.

1. Download or fetch PhpFit from https://github.com/metaclass-nl/phpfit

2. Extract and/or upload the PhpFit files and folders into a folder on your http server

3. Download or fetch Fit Shelf from https://github.com/metaclass-nl/fit-shelf

4.  Extract and/or upload the shelf subfolder from the fit_shelf folder to a folder on your http server

5. Edit the conf_shelf.php file in the script folder you copied in step 4 and correct the include paths
   with respect to the locations of phpfit and the shelf folder on your server.
   
6. If necessary make the folder in which phpFits output file $output is situated 
   writable for the  run-web.php script.

7. Enter the url on your http server to index.html from the script/book folder in your browser

8. You should see some hyperlinks to some tests from the book. Click one that is not under development.


TO RUN YOUR OWN TESTS

9. Make your own fixtures folder (may be outside of the fit shelf folder)
   and set $fixturesDir in conf_shelf.php to point to it.
   
10. Upload you own Fixture file to your fixtures folder

11. Make your own tests folder  (may be outside of the fit shelf folder)

12. Upload your own test to your tests folder 
   
11. Enter the url in your browser to run-web.php?input_filename= folowed by the relative 
    path from run-web.php to your test
    
12. You should see your own test results in your browser 


RELEASE NOTES

fit_shelf_1_0_beta2

This beta has been tested sucessfully with the tests and fixtures of the 
Historical Data Management (HDM) extension to the PhpPeanuts framework
(The HDM framework is not freely available for download).

The beta does not include CalculateFixture and SetupFixture.

The beta does not include all examples ported from the tests described 
in the book "Fit for developing Software". Please help by porting more
tests that are meant to run on Fit Library.

The Adapters should be refactored in order to support strict typing,
see PHPFIT_TypeAdapter_PhpTolerant.php.
Some smaller refactorings may be made with respect to reoccurring code.

Modifications may be required for installation and autoloading with Composer.


   
SPECIAL FEATURES

1. Mixed data typing

In order to support the mixed data typing of PHP, a special adapter
is included: PHPFIT_TypeAdapter_PhpTolerant.php. Because adapter is
meant to work with any type, it is used as a fallback whenever 
type information is missing. In practice this means that
you can in most case simply run any tests and forget about data typing.

However, if you DO supply type information, this adapter will delegate
to the corresponding PhpFit data type adapter.

2. Support for not-yet-defined properties and __get, __set and __call magic methods

Fit Library supports the direct usage of properties and methods
of the system under test from tests. With DoFixture if a method or
property does not exist, a fixture is loaded. But in PHP5, 
if the right magic methods are implemented you may
get or set a property or call a method that does not exist. 
And you may also set a property that is not defined at all.

In order to allow you to get, set and call unconditionally, 
Shelf's DoFixture supports the fixture methods 'get', 'set' and 'call' 
that do not default to fixure loading. 

3. Adaptation to application meta models

In Java an single meta model is prescibed by the Java Beans standard:
properties have getters, setters and maybe a field, meta data can be
obtained through Bean Descriptors and reflection.

In PHP there is not such a strict standard. The documentation and the
__get and __set magic methods suggest the meaning of properties,
but specific applications and frameworks may well follow a different 
meta model. This will for example be the case with a one to one port
of the examples of of the book: they follow the Java Beans standard,
which is different from the way suggested by the documentation of PHP.

In order to support that, Shelf uses two layers of adapters, one 
for the meta model, and a second for the actual typing. Currently
three MetaModel adapters are available:
A. PHPFIT_TypeAdapter_PhpTolerant for ordinary PHP objects
   this is the default adapter used by Shelf fixtures
B. PHPFIT_TypeAdapter_BeanTolerant for Beans-like objects 
   like those from book examples
C. PHPFIT_TypeAdapter_PntTolerant for usage with phpPeanuts.

You may activate an adapter from your fixture class by adding the following method:
	protected function interpretTablesInit() {
		parent::interpretTablesInit();
		shelf_ClassHelper::adapterType('BeanTolerant');
	}
Usually your tests will start with activating some subclass of DoFixture and 
that fixture will run this method. From then on the other shelf fixtures that 
your tests use will also use this adapter.

If the meta model of your own application (or framework) is different
you may implement your own type adapter using the adapters that come
with shelf as examples. Warning: The adapers will be refactored in order
to support strict typing for the php metamodel.

4. Support for PhpPeanuts

For usage with the phpPeanuts framework (http://phppeanuts.org).
Usually your tests will start with activating 
your subclass of PntDoFixture. This will activate PHPFIT_TypeAdapter_PntTolerant
so that you can use phpPeanuts properties on the system under test.

Some special methods are available from PntDoFixture to allow your tests
to create new peanuts, validate, save, retrieve and delete them. Using the
phpPeanuts meta data, type-correct stringconversions will be done by
StingConverter instead of the PhpFit datatype adapters. This way all
datatypes of phpPeanuts are supported.


SUPPORT (Dutch)

MetaClass biedt hulp en ondersteuning binnen Nederland bij onderhoud 
en ontwikkeling van software, tests en fixtures. 
Voor meer informatie kijk op http://www.metaclass.nl/


COPYRIGHT AND LICENCE

Courtesy to Rick Mugridge, http://www.RimuResearch.com for the 
functional specification of Fit Library (http://sourceforge.net/projects/fitlibrary/) 
and the ideas behind it to which MetaClass claims no rights.
 
This implementation (Fit Shelf) is Copyright (c) 2010-2012 H. Verhoeven Beheer BV, 
holding of MetaClass Groningen Nederland.

Licensed under the GNU General Public License version 3 or later.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS
THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY
GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE
USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF
DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD
PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS),
EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF
SUCH DAMAGES.


* not including CalculateFixture and SetupFixture but including the extensions for phpPeanuts. 
  Fit Shelf does not support (usage from) Fitnesse. Is not inteded to work the
  same as Fit Library, only to work like descibed in the book, but with the 
  special features as described below.

