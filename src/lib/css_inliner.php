<?php

class CssInliner {

	private $_css_paths = null;

	/**
	 * @param array $css_paths document root-relative paths to css files to inline
	 */
	function __construct($css_paths) {
		$this->_css_paths = $css_paths;
	}

	function printStyles() {
		global $CFG;

		$css_output = '';
		if ($this->_css_paths && count($this->_css_paths) > 0) {
			foreach ($this->_css_paths as $path) {
				$css_output .= file_get_contents($CFG->dirroot . $path);
			}
		}

		$html_output = <<<HTML_OUTPUT
		<style>
		$css_output
		</style>
HTML_OUTPUT;

		echo $html_output;
	}
}