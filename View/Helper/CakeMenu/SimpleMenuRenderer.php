<?php

App::uses('BaseMenuRenderer', 'CakeMenu.View/Helper/CakeMenu');

class SimpleMenuRenderer extends BaseMenuRenderer {

/**
 * Helpers used in this renderer
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * Render method for an item in the menu, this can be an item on the root
 * or an item in one of the submenus.
 *
 * Available parameters in $params
 * -------------------------------
 * - level: The depth of the item (0 being a root item)
 * - path: An array containing the path to the root
 * - active: A boolean value if the item is active or not
 *
 * @param string $key The key of the menu item
 * @param string $label The item label
 * @param mixed $url The passed URL
 * @param array $options The options defined in the configuration
 * @param array $params
 * @return string
 */
	public function item($key, $label, $url, $options, $params) {
		$linkOptions = isset($options['linkOptions']) ? $options['linkOptions'] : array();
		$link = $this->Html->link($label, $url, $linkOptions);

		$class = '';
		if ($params['active']) {
			$class = ' class="active"';
		}
		return '<li' .$class . '>' . $link . '</li>';
	}

/**
 * Render method for the navigation menu itself, this method is called last
 * with $contents containing all the rendered submenus and menu items.
 *
 * @param string $key The name of the menu
 * @param string $contents The HTML contents
 * @param array $options The options defined in the configuration
 * @return string
 */
	public function menu($key, $contents, $options) {
		return '<ul class="menu">' . $contents . '</ul>';
	}

/**
 * Render method for the submenu.
 *
 * Available parameters in $params
 * -------------------------------
 * - level: The depth of the item (0 being a root item)
 * - path: An array containing the path to the root
 * - active: A boolean value if the item is active or any of its children
 *
 * @param string $key The submenu name
 * @param string $label The submenu label
 * @param mixed $url The passed URL
 * @param string $content The HTML of the rendered subitems/submenus
 * @param array $options The options defined in the configuration
 * @param array $params
 * @return string
 */
	public function submenu($key, $label, $url, $content, $options, $params) {
		$linkOptions = isset($options['linkOptions']) ? $options['linkOptions'] : array();
		$link = $this->Html->link($label, $url, $linkOptions);

		$class = '';
		if ($params['active']) {
			$class = ' class="active"';
		}
		return '<li' . $class . '>' . $link . '<ul class="submenu">' . $content . '</ul></li>'; 
	}
}