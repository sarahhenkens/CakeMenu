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
 * A big test menu config to test active mapping
 *
 * @var array
 */
	protected $_testMenuConfig = array(
		'dashboard' => array('label' => 'Dashboard', 'url' => array('controller' => 'users', 'action' => 'dashboard')),
		'users' => array(
			'label' => 'Users Menu',
			'options' => array('items' => array(
				'overview' => array('label' => 'Overview', 'url' => array('controller' => 'users', 'action' => 'index'))
			))
		)
	);

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
			'options' => array('renderer' => 'CakeMenu.Simple'),
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
		$this->CakeMenu->add('default', 'first', 'First Label', array());
		$this->CakeMenu->add('default', 'second', 'Second Label', array('controller' => 'users', 'action' => 'index'));

		$result = $this->CakeMenu->config('default');
		
		$expected = array('first', 'second');
		$this->assertEquals($expected, array_keys($result['items']));

		$expected = array(
			'label' => 'Second Label',
			'url' => array('controller' => 'users', 'action' => 'index'),
			'options' => array()
		);
		$this->assertEquals($expected, $result['items']['second']);
	}

/**
 * testAddMultiple method
 *
 * @return void
 */
	public function testAddMultiple() {
		$data = array(
			'first' => array('label' => 'First Label'),
			'second' => array('label' => 'Second Label')
		);
		$this->CakeMenu->add('foobar', $data);
		$result = $this->CakeMenu->config('foobar');
		$expected = array('first', 'second');
		$this->assertEquals($expected, array_keys($result['items']));

		$data = array(
			array('key' => 'first', 'label' => 'First Label', 'options' => array(
				'items' => array(
					array('key' => 'foo', 'label' => 'Foo Label'),
					array('key' => 'bar', 'label' => 'Foo Bar')
				)
			)),
			array('key' => 'companies', 'label' => 'Companies', 'options' => array(
				'items' => array(
					'google' => array('label' => 'Google Inc.', 'url' => 'www.google.com')
				)
			))
		);
		$this->CakeMenu->add('another', $data);
		$result = $this->CakeMenu->config('another');
		$expected = array('first', 'companies');
		$this->assertEquals($expected, array_keys($result['items']));
		$this->assertArrayHasKey('google', $result['items']['companies']['items']);
		$this->assertEquals('www.google.com', $result['items']['companies']['items']['google']['url']);

		$expected = array('foo', 'bar');
		$this->assertEquals($expected, array_keys($result['items']['first']['items']));
	}

/**
 * testAddSubmenus method
 *
 * @return void
 */
	public function testAddSubmenus() {
		$this->CakeMenu->add('default', 'root_one', 'Root First', array());
		$this->CakeMenu->add('default', 'root_two', 'Root Second', array());

		$this->CakeMenu->add('default.root_one', 'item_a', 'Item A', array());
		$this->CakeMenu->add('default.root_one', 'item_b', 'Item B', array());

		$result = $this->CakeMenu->config();
		$expected = array('default');
		$this->assertEquals($expected, array_keys($result));

		$result = $this->CakeMenu->config('default');

		$expected = array('root_one', 'root_two');
		$this->assertEquals($expected, array_keys($result['items']));

		$expected = array('label', 'url', 'options', 'items');
		$this->assertEquals($expected, array_keys($result['items']['root_one']));
		$expected = array('label', 'url', 'options');
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
		$this->CakeMenu->add('default', 'jelle', 'Jelle The Root', 'www.google.com', array(
			'items' => array(
				'sub_item_a' => array('label' => 'Sub Label A'),
				'sub_item_b' => array('label' => 'Sub Label B', 'url' => array(), 'options' => array(
					'items' => array(
						'deeper' => array('label' => 'We have to go deeper', 'url' => array()),
						'awesome' => array('label' => 'This is awesome', 'url' => array(), 'options' => array('foo' => 'bar'))
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

		$this->assertNull($result['items']['jelle']['items']['sub_item_a']['url']);
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

		$this->CakeMenu->add('default', 'foo', 'Foo Item', array());
		$this->CakeMenu->add('default', 'bar', 'Bar Item', array());

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

		$this->CakeMenu->add('default', 'foo_root', 'Foo Root', array());
		$this->CakeMenu->add('default.foo_root', 'subitem_a', 'Item A', array());
		$this->CakeMenu->add('default.foo_root.subitem_a', 'deep', 'Deep', array());
		$this->CakeMenu->add('default.foo_root', 'subitem_b', 'Item B', array());

		$this->CakeMenu->add('default', 'bar_root', 'Bar Root', array());

		$result = $this->CakeMenu->render('default');

		$expected = '<ul><li>Foo Root<ul><li>Item A<ul><li>Deep</li></ul></li><li>Item B</li></ul></li><li>Bar Root</li></ul>';
		$this->assertEquals($expected, $result);
	}

/**
 * testDetectActive method
 *
 * @return void
 */
	public function testDetectActive() {
		$this->CakeMenu->add('default', $this->_testMenuConfig);

		$this->CakeMenu->request->params['controller'] = 'users';
		$this->CakeMenu->request->params['action'] = 'index';
		$this->assertEquals('users.overview', $this->CakeMenu->detectActive('default'));

		$this->CakeMenu->request->params['controller'] = 'users';
		$this->CakeMenu->request->params['action'] = 'dashboard';
		$this->assertEquals('dashboard', $this->CakeMenu->detectActive('default'));

		$this->CakeMenu->request->params['controller'] = 'unknowncontroller';
		$this->CakeMenu->request->params['action'] = 'foobar';
		$this->assertFalse($this->CakeMenu->detectActive('default'));
	}

/**
 * testDetectActiveWithMatchOptions method
 *
 * @return void
 */
	public function testDetectActiveWithMatchOptions() {
		$this->CakeMenu->add('default', 'first', 'First', array('controller' => 'users', 'action' => 'index'), array(
			'match' => array(
				array('controller' => 'users', 'action' => 'foo'),
				array('controller' => 'users', 'action' => 'bar')
			)
		));
		$this->CakeMenu->add('default', 'second', 'Second', array('controller' => 'shows', 'action' => 'index'), array(
			'match' => array(
				array('controller' => 'shows', 'action' => 'foobar')
			)
		));

		$this->CakeMenu->request->params['controller'] = 'users';
		$this->CakeMenu->request->params['action'] = 'foo';
		$this->assertEquals('first', $this->CakeMenu->detectActive('default'));

		$this->CakeMenu->request->params['controller'] = 'users';
		$this->CakeMenu->request->params['action'] = 'index';
		$this->assertEquals('first', $this->CakeMenu->detectActive('default'));

		$this->CakeMenu->request->params['controller'] = 'shows';
		$this->CakeMenu->request->params['action'] = 'foobar';
		$this->assertEquals('second', $this->CakeMenu->detectActive('default'));
	}
}
