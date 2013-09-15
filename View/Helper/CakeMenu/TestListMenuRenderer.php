<?php

App::uses('BaseMenuRenderer', 'CakeMenu.View/Helper/CakeMenu');

class TestListMenuRenderer extends BaseMenuRenderer {

	public function item($key, $label, $url, $options, $params) {
		return '<li>' . $label . '</li>';
	}

	public function menu($key, $contents, $options) {
		return '<ul>' . $contents . '</ul>';
	}

	public function submenu($key, $label, $url, $content, $options, $params) {
		return '<li>' . $label . '<ul>' . $content . '</ul></li>';
	}
}