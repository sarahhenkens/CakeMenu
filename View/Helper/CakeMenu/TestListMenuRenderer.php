<?php

class TestListMenuRenderer {

	public function item($key, $label, $options) {
		return '<li>' . $label . '</li>';
	}

	public function menu($key, $contents) {
		return '<ul>' . $contents . '</ul>';
	}

	public function submenu($key, $label, $content, $options) {
		return '<li>' . $label . '<ul>' . $content . '</ul></li>';
	}
}