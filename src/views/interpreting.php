<?php

require_once dirname(__FILE__) . '/general.php';


class InterpretingView extends GeneralView {
	function __construct() {
		parent::__construct();
		//echo "<pre>".print_r($this->contents, true)."</pre>";//TMP
	}
	
	function getViewName() {
		$name = preg_replace('/\.php$/', '', basename(__FILE__));
		return $name;
	}	
}