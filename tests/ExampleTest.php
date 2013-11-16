<?php
$GLOBALS['SIMPLE_SUMMARY'] = 1;

class ExampleTest extends PHPUnit_Framework_TestCase {
    public $mustFilename;
    public $isFilename;
    public $runFilename;

    public function setUp() {
        global $fitConfig;
        $this->isFilename = $fitConfig->output;
    }

    public function tearDown() {
        PHPFIT::run($this->runFilename, $this->isFilename);

        $must = str_replace("\r\n", "\n", file_get_contents($this->mustFilename, true));
        $is = str_replace("\r\n", "\n", file_get_contents($this->isFilename, true));

        $this->assertEquals($is, $must);
    }

    public function testFig1TestDisconnect() {
        $this->mustFilename = "../examples/output/Fig1TestDisconnect.html";
        $this->runFilename = "../examples/input/Fig1TestDisconnect.html";
    }

    public function Fig2TestCloseRoomFails() {
        $this->mustFilename = "../examples/output/Fig2TestCloseRoomFails.html";
        $this->runFilename = "../examples/input/Fig2TestCloseRoomFails.html";
    }

    public function testFig3TestUser() {
        $this->mustFilename = "../examples/output/Fig3TestUser.html";
        $this->runFilename = "../examples/input/Fig3TestUser.html";
    }

    public function testFig4TestDiscountGroupsSetUp() {
        $this->mustFilename = "../examples/output/Fig4TestDiscountGroupsSetUp.html";
        $this->runFilename = "../examples/input/Fig4TestDiscountGroupsSetUp.html";
    }

}

?>