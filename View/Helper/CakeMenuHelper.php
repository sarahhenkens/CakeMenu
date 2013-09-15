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
 * Holds the parsed items map
 *
 * @var array
 */
	protected $_itemsMap = array();

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
 * @param string $menu The menu alias
 * @param string $key
 * @param string $label
 * @param array $options
 * @return void
 */
	public function add($menu, $key, $label = null, $url = null, $options = array()) {
		if (is_array($key)) {
			foreach ($key as $k => $item) {
				if (is_string($k)) {
					$item['key'] = $k;
				}

				$item = array_merge(array('url' => null, 'options' => array()), $item);
				$this->add($menu, $item['key'], $item['label'], $item['url'], $item['options']);
			}

			return;
		}

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
			'url' => $url,
			'options' => $options
		);

		$hashPath = implode($path, '.items.') . '.items.' . $key;
		$this->_menus = Hash::insert($this->_menus, $hashPath, $item);

		if (!empty($subitems)) {
			$submenuKey = implode($path, '.') . '.' . $key;
			$this->add($submenuKey, $subitems);
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
				$menuContent .= $this->_renderSubmenu($menu, $key, $item, 1);
			} else {
				$params = array(
					'level' => 0
				);
				$menuContent .= $renderer->item($key, $item['label'], $item['url'], $item['options'], $params);
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
	protected function _renderSubmenu($menu, $key, $item, $level) {
		$renderer = $this->_renderer($menu);

		$content = '';
		foreach ($item['items'] as $key => $subItem) {
			if (!empty($subItem['items'])) {
				$content .= $this->_renderSubmenu($menu, $key, $subItem, $level + 1);
			} else {
				$params = array(
					'level' => $level
				);
				$content .= $renderer->item($key, $subItem['label'], $subItem['url'], $subItem['options'], $params);
			}
		}

		return $renderer->submenu($key, $item['label'], $item['url'], $content, $item['options']);
	}

/**
 * Generates and returns the renderer for a menu
 *
 * @param string $menu
 * @return object
 */
	protected function _renderer($menu) {
		if (!array_key_exists($menu, $this->_renderers)) {
			$renderer = $this->_menus[$menu]['options']['renderer'];

			$plugin = '';
			$className = $renderer;

			if (strpos($renderer, '.') !== false) {
				list($plugin, $className) = explode('.', $renderer);
				$plugin .= '.';
			}
			$className .= 'MenuRenderer';
			App::uses($className, $plugin . 'View/Helper/CakeMenu');

			$this->_renderers[$menu] = new $className($this->_View);
		}

		return $this->_renderers[$menu];
	}

/**
 * Flattens all the leafs in the menu to match the active path
 *
 * @param string $menu
 * @param array $items
 * @param string $path
 * @return void
 */
	protected function _generateItemsMap($menu, $items = null, $path = '') {
		if ($items === null) {
			$items = $this->_menus[$menu]['items'];
		}

		foreach ($items as $key => $item) {
			if (empty($item['items'])) {
				$this->_itemsMap[$menu][$path . $key] = $item;
			} else {
				$this->_generateItemsMap($menu, $item['items'], $path . $key . '.');
			}
		}
	}

/**
 * Returns the flattened items map
 *
 * @param string $menu
 * @return array
 */
	public function itemsMap($menu) {
		if (empty($this->_itemsMap[$menu])) {
			$this->_generateItemsMap($menu);
		}

		return $this->_itemsMap[$menu];
	}

/**
 * Will detect the active menu leaf based on the CakeRequest
 *
 * @param string $menu
 * @return string|boolean Will return false if nothing can be detected
 */
	public function detectActive($menu) {
		$map = $this->itemsMap($menu);

		$activePath = false;

		foreach ($map as $path => $item) {
			if (!is_array($item['url'])) {
				continue;
			}

			$match = array_intersect_key($item['url'], array_flip(array('plugin', 'controller', 'action')));
			$test = array_diff_assoc($match, $this->request->params);
			if (empty($test)) {
				$activePath = $path;
				break;
			}
		}

		return $activePath;
	}
}