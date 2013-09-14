<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('CakeMenuHelper', 'CakeMenu.View/Helper');

/**
 * MenuHelper Test Case
 *
 */
class CakeMenuHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->CakeMenu = new CakeMenuHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CakeMenu);

		parent::tearDown();
	}

/**
 * testCreate method
 *
 * @return void
 */
	public function testCreate() {
		$this->CakeMenu->create('foobar');

		$result = $this->CakeMenu->config('foobar');
		$expected = array(
			'options' => array('renderer' => 'CakeMenu.ListMenuRenderer'),
			'items' => array()
		);
		$this->assertEquals($expected, $result);
	}
}
