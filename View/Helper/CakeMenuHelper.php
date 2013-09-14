<?php

App::uses('AppHelper', 'View/Helper');

class CakeMenuHelper extends AppHelper {

/**
 * Holds menu configurations
 *
 * @var array
 */
	protected $_menus = array();

/**
 * Default options for various menus
 *
 * @var array
 */
	protected $_defaults = array(
		'menu' => array(
			'renderer' => 'CakeMenu.ListMenuRenderer'
		)
	);

/**
 * Creates a fresh menu and set options
 *
 * @param string $menu
 * @param array $options
 * @return void
 */
	public function create($menu, $options = array()) {
		$options = array_merge($this->_defaults['menu'], $options);

		$this->_menus[$menu] = array(
			'options' => $options,
			'items' => array()
		);
	}

/**
 * Returns the config for a menu
 *
 * @param string $menu
 * @return array
 */
	public function config($menu) {
		return $this->_menus[$menu];
	}
}