<?php
class ViewFactory {
	
	static $instances = array();
	
	public static function getInstance($view_class) {

		if (!isset(self::$instances[$view_class])) {
			self::$instances[$view_class] = new $view_class();  
		}		
		
		return self::$instances[$view_class];
	}
}