<?php

App::uses('BaseMenuRenderer', 'CakeMenu.View/Helper/CakeMenu');

class TestListMenuRenderer extends BaseMenuRenderer {

	public function item($key, $label, $url, $options) {
		return '<li>' . $label . '</li>';
	}

	public function menu($key, $contents) {
		return '<ul>' . $contents . '</ul>';
	}

	public function submenu($key, $label, $url, $content, $options) {
		return '<li>' . $label . '<ul>' . $content . '</ul></li>';
	}
}