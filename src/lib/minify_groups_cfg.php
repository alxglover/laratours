<?php
/**
 * Define javascript/css group id's to include minified script within markup templates
 *
 * e.g.  <script src="/min/?g=libjs&view=guiding"></script>
 * calls groups with id 'libjs' with get param 'view=guiding'
 *  
 */

$minify_groups_config = array(
	// general site js/jquery
	'libjs' => 
		new Minify_Source(array(
		    'id' => 'libjs',
		    'getContentFunc' => function() {
				$view = (isset($_GET['view']) ? $_GET['view'] : '');
		        $js_content = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/lib/lib.js.php?view=' . $view);
		    	//exit('view: '.$view."\n<pre>$js_content</pre>");//TMP
		        return $js_content;		    	
		    },
		    'contentType' => Minify::TYPE_JS,
		    'lastModified' => (
		    	$_SERVER['REQUEST_TIME'] // force cache-refresh
		    )
		)),
	// skype buttons js
	'skypebuttonsjs' => 
		new Minify_Source(array(
		    'id' => 'skypebuttonsjs',
		    'getContentFunc' => function() {
		        $js_content = file_get_contents('http://www.skypeassets.com/i/scom/js/skype-uri.js');
		        return $js_content;
		    },
		    'contentType' => Minify::TYPE_JS,
		    'lastModified' => (
		    	$_SERVER['REQUEST_TIME'] // force cache-refresh
		 	)
		)),
	// skype analytics js
	'skypejs' => 
		new Minify_Source(array(
		    'id' => 'skypejs',
		    'getContentFunc' => function() {
		        $js_content = file_get_contents('http://cdn.dev.skype.com/uri/skype-analytics.js');
		        return $js_content;
		    },
		    'contentType' => Minify::TYPE_JS,
		    'lastModified' => (
		    	$_SERVER['REQUEST_TIME'] // force cache-refresh
		 	)
		)),
	// jquery ui css
	'jqueryuicss' => array('//stylesheets/black-tie/jquery-ui-1.10.3.custom.css'),
);
