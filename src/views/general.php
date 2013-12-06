<?php
require_once dirname(dirname(__FILE__)) . '/lib.php';

class GeneralView {
	
	static $instance = null;	
	protected $contents = null; 
	protected $template = null; 
	protected $action = null;
	protected $default_contents = null;
	protected $name = null;
	
	function __construct() {
		global $CFG;

		$this->name = $this->getViewName();

		//echo get_class($this).":".__METHOD__." name: $this->name<br/>\n";//TMP

		$this->default_contents = array('general', $this->name);
		try {
			$this->contents = fetchContents($this->default_contents, $CFG->sitelang);
		}
		catch (NoSuchContentException $e) {
			showError($e->getMessage());
		}
	}
	
	function getViewName() {
		$name = preg_replace('/\.php$/', '', basename(__FILE__));
		return $name;
	}
	
	function setTemplate($template, $action='') {
		$this->action = $action;
		$this->template = $template;
		$path = getTemplatePath($template);
		if (!file_exists($path)) {
			throw new TemplateNotFoundException("Could not find template file for '$template'.");
		}
	}
	
	function display() {
		global $CFG;
		
		if (empty($this->template)) {
			throw new TemplateNotFoundException('Cannot render view '.$this->getViewName().'. No template found.');
		}
		else {
			$path = getTemplatePath($this->template);
			require_once $path;
		}
	}
	
	/**
	 * 
	 * @param string $content_key
	 * @param unknown $string_id
	 * @return string|unknown
	 */
	function getStr($string_id, $content_key='', $noeditwrap=false) {
		global $CFG;

		$content_key = (empty($content_key) ? $this->name : $content_key);
		if (!isset($this->contents->$content_key)) {
			return "[$content_key(?)::$string_id]";
		}
		$content = $this->contents->$content_key;

		if (!isset($content[$string_id])) {
			return "[$content_key::$string_id(?)]";
		}

		$str = $content[$string_id];

		if ($CFG->isediting && !$noeditwrap) {
			$content_id = generateContentId($string_id);
			$id = $content_key . '_' . $content_id;
			$str = "<span id=\"$id\" class=\"editable\">$str</span>";
		}
		
		return $str;
	}
	
	function ps($string_id, $content_key='', $noeditwrap=false) {
		echo $this->getStr($string_id, $content_key, $noeditwrap);
	}

	/**
	 * Singleton constructor - only works with PHP 5.3 >
	 * @return GenericView
	 */
	/*
	static function getInstance() {
		if (empty(self::$instance)) {
			$class = get_called_class();
			self::$instance = new $class();
		}
		
		return self::$instance;
	}
	*/
}