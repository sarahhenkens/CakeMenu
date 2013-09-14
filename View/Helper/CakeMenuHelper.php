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
 * Holds the renderer instances
 *
 * @var array
 */
	protected $_renderers = array();

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
 * Adds a new item to a menu
 *
 * @param string $menu
 * @param string $key
 * @param string $label
 * @param array $options
 * @return void
 */
	public function add($menu, $key, $label, $options = array()) {
		$path = array();
		if (strpos($menu, '.') !== false) {
			$path = explode('.', $menu);
			$menu = $path[0];
		} else {
			$path = array($menu);
		}

		if (!array_key_exists($menu, $this->_menus)) {
			$this->create($menu);
		}

		$subitems = array();
		if (!empty($options['items'])) {
			$subitems = $options['items'];
			unset($options['items']);
		}

		$item = array(
			'label' => $label,
			'options' => $options
		);

		$hashPath = implode($path, '.items.') . '.items.' . $key;
		$this->_menus = Hash::insert($this->_menus, $hashPath, $item);

		if (!empty($subitems)) {
			foreach ($subitems as $subitemKey => $subitem) {
				$subitem = array_merge(array('options' => array()), $subitem);

				$submenuKey = implode($path, '.') . '.' . $key;
				$this->add($submenuKey, $subitemKey, $subitem['label'], $subitem['options']);
			}
		}
	}

/**
 * Returns the config for a menu
 *
 * @param string $menu
 * @return array
 */
	public function config($menu = null) {
		if ($menu === null) {
			return $this->_menus;
		}

		return $this->_menus[$menu];
	}

/**
 * Returns the rendered menu
 *
 * @param string $menu
 * @return string
 */
	public function render($menu) {
		$config = $this->config($menu);
		$renderer = $this->_renderer($menu);

		$menuContent = '';
		foreach ($config['items'] as $key => $item) {
			if (!empty($item['items'])) {
				$menuContent .= $this->_renderSubmenu($menu, $key, $item);
			} else {
				$menuContent .= $renderer->item($key, $item['label'], $item['options']);
			}
		}

		return $renderer->menu($menu, $menuContent);
	}

/**
 * Recursive method to render all the submenus
 *
 * @param string $menu
 * @param string $key
 * @param array $item
 * @return string
 */
	protected function _renderSubmenu($menu, $key, $item) {
		$renderer = $this->_renderer($menu);

		$content = '';
		foreach ($item['items'] as $key => $subItem) {
			if (!empty($subItem['items'])) {
				$content .= $this->_renderSubmenu($menu, $key, $subItem);
			} else {
				$content .= $renderer->item($key, $subItem['label'], $subItem['options']);
			}
		}

		return $renderer->submenu($key, $item['label'], $content, $item['options']);
	}

/**
 * Generates and returns the renderer for a menu
 *
 * @param string $menu
 * @return object
 */
	protected function _renderer($menu) {
		if (!array_key_exists($menu, $this->_renderers)) {
			$plugin = 'CakeMenu';
			$className = 'TestListMenuRenderer';

			App::uses($className, $plugin . '.View/Helper/CakeMenu');

			$this->_renderers[$menu] = new $className();
		}

		return $this->_renderers[$menu];
	}
}