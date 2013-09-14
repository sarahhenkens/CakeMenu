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

/**
 * testAddSimple method
 *
 * @return void
 */
	public function testAddSimple() {
		$this->CakeMenu->add('default', 'first', 'First Label');
		$this->CakeMenu->add('default', 'second', 'Second Label');

		$result = $this->CakeMenu->config('default');
		
		$expected = array('first', 'second');
		$this->assertEquals($expected, array_keys($result['items']));

		$expected = array(
			'label' => 'Second Label',
			'options' => array()
		);
		$this->assertEquals($expected, $result['items']['second']);
	}

/**
 * testAddSubmenus method
 *
 * @return void
 */
	public function testAddSubmenus() {
		$this->CakeMenu->add('default', 'root_one', 'Root First');
		$this->CakeMenu->add('default', 'root_two', 'Root Second');

		$this->CakeMenu->add('default.root_one', 'item_a', 'Item A');
		$this->CakeMenu->add('default.root_one', 'item_b', 'Item B');

		$result = $this->CakeMenu->config();
		$expected = array('default');
		$this->assertEquals($expected, array_keys($result));

		$result = $this->CakeMenu->config('default');

		$expected = array('root_one', 'root_two');
		$this->assertEquals($expected, array_keys($result['items']));

		$expected = array('label', 'options', 'items');
		$this->assertEquals($expected, array_keys($result['items']['root_one']));
		$expected = array('label', 'options');
		$this->assertEquals($expected, array_keys($result['items']['root_two']));

		$expected = array('item_a', 'item_b');
		$this->assertEquals($expected, array_keys($result['items']['root_one']['items']));
	}

/**
 * testAddBulkSubmenus method
 *
 * @return void
 */
	public function testAddBulkSubmenus() {
		$this->CakeMenu->add('default', 'jelle', 'Jelle The Root', array(
			'items' => array(
				'sub_item_a' => array('label' => 'Sub Label A'),
				'sub_item_b' => array('label' => 'Sub Label B', 'options' => array(
					'items' => array(
						'deeper' => array('label' => 'We have to go deeper'),
						'awesome' => array('label' => 'This is awesome', 'options' => array('foo' => 'bar'))
					)
				))
			)
		));

		$result = $this->CakeMenu->config('default');
		$expected = array('jelle');
		$this->assertEquals($expected, array_keys($result['items']));

		$expected = array('sub_item_a', 'sub_item_b');
		$this->assertEquals($expected, array_keys($result['items']['jelle']['items']));

		$expected = array('deeper', 'awesome');
		$this->assertEquals($expected, array_keys($result['items']['jelle']['items']['sub_item_b']['items']));

		$expected = array('foo' => 'bar');
		$this->assertEquals($expected, $result['items']['jelle']['items']['sub_item_b']['items']['awesome']['options']);
	}

/**
 * testRenderBasic method
 *
 * @return void
 */
	public function testRenderBasic() {
		$this->CakeMenu->create('default', array(
			'renderer' => 'CakeMenu.TestList'
		));

		$this->CakeMenu->add('default', 'foo', 'Foo Item');
		$this->CakeMenu->add('default', 'bar', 'Bar Item');

		$result = $this->CakeMenu->render('default');

		$expected = '<ul><li>Foo Item</li><li>Bar Item</li></ul>';
		$this->assertEquals($expected, $result);
	}

/**
 * testRenderSubmenus method
 *
 * @return void
 */
	public function testRenderSubmenus() {
		$this->CakeMenu->create('default', array(
			'renderer' => 'CakeMenu.TestList'
		));

		$this->CakeMenu->add('default', 'foo_root', 'Foo Root');
		$this->CakeMenu->add('default.foo_root', 'subitem_a', 'Item A');
		$this->CakeMenu->add('default.foo_root', 'subitem_b', 'Item B');

		$this->CakeMenu->add('default', 'bar_root', 'Bar Root');

		$result = $this->CakeMenu->render('default');

		$expected = '<ul><li>Foo Root<ul><li>Item A</li><li>Item B</li></ul></li><li>Bar Root</li></ul>';
		$this->assertEquals($expected, $result);
	}
}
