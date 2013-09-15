<?php

App::uses('AppHelper', 'View/Helper');

abstract class BaseMenuRenderer extends AppHelper {

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
	abstract public function item($key, $label, $url, $options, $params);

/**
 * Render method for the navigation menu itself, this method is called last
 * with $contents containing all the rendered submenus and menu items.
 *
 * @param string $key The name of the menu
 * @param string $contents The HTML contents
 * @param array $options The options defined in the configuration
 * @return string
 */
	abstract function menu($key, $contents, $options);

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
	abstract function submenu($key, $label, $url, $content, $options, $params);
}