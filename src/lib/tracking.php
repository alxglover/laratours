<?php
	if (!file_exists($CFG->safedir)) {
		@mkdir($CFG->safedir);
	}

	if (!file_exists($CFG->safedir)) {
		//echo "<!-- no safe dir: $CFG->safedir -->\n";//TMP
		return;
	}

	$log_fields = array(
		'ts' =>  $_SERVER['REQUEST_TIME'],
		'datetime' =>  date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		'domain' => $_SERVER['HTTP_HOST'],
		'requesturi' =>  $_SERVER['REQUEST_URI'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'referer' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
		'useragent' => $_SERVER['HTTP_USER_AGENT'],
		'pathinfo' => (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ''),
	);

	$log_path = $CFG->safedir . '/access_log.csv';

	if (!file_exists($log_path)) {
		$cols = array_keys($log_fields);
		$header = '"' . join('","', $cols) . '"' . "\n";
		file_put_contents($log_path, $header);
	}

	if (!file_exists($log_path)) {
		//echo "<!-- no log file: $log_path -->\n";//TMP
		return;
	}

	$entry = '"' . join('","', $log_fields) . '"' . "\n";

	file_put_contents($log_path, $entry, FILE_APPEND | LOCK_EX);
