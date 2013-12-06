<?php
session_start();

//echo(__FILE__);//TMP

global $CFG;

$CFG = new stdClass();

$CFG->dirroot = dirname(__FILE__);
$CFG->libdir = dirname(__FILE__) . '/lib';
$CFG->dataroot = '.'; //TMP
$CFG->wwwroot = 'http://' . $_SERVER['HTTP_HOST'];
//echo "wwwroot: $CFG->wwwroot<br/>\n";//TMP

//$CFG->directorypermissions = 00770;  // try 02777 on a server in Safe Mode
$CFG->directorypermissions = 02777;
$CFG->siteidentifier = md5('laraglover') .  $_SERVER['HTTP_HOST'];

$CFG->csrf_key = '9724cd61b23da31504729cdbbbe7011b';

$CFG->safedir = dirname(__FILE__) . '/../safe';

// email
$CFG->siteownername = 'Lara';
//$CFG->siteowneremail = 'lara.lacasarosa@hotmail.co.uk';
$CFG->siteowneremail = 'info@laratours.co.uk'; // mail forwarding set up on hostinger.co.uk

//$CFG->smtpdebug = true; //TMP
$CFG->smtphosts = '';
$CFG->smtpuser = '';
$CFG->smtppass = '';

if (preg_match('/local/', $_SERVER["HTTP_HOST"])) { // doesn't work on web-hosting
	$CFG->smtphosts = 'smtp.gmail.com:465';
	$CFG->smtpuser = 'laragloverguide';
	$CFG->smtppass = 'ulgsbznrhnxgqcju';
}

$CFG->sitemailcharset = 'UTF-8';
$CFG->supportemail = 'glubbah@gmail.com';
$CFG->ccaddress = 'alexglover3000@gmail.com';
$CFG->supportname = 'Alex G';
//$CFG->divertallemailsto = 'laragloverguide@gmail.com';//TMP
//$CFG->divertallemailsto = $CFG->supportemail;//TMP
$CFG->handlebounces = true;
$CFG->noreplyaddress = 'noreply@example.com';
$CFG->noreplyname = 'Do not reply';

$CFG->maildomain = 'laratours.co.uk';
$CFG->mailprefix = 'laratours';

$CFG->disablemail = true;


/**** SETUP TASKS ****/

/*** live editing ***/

$CFG->editingpass = '42d388f8b1db997faaf7dab487f11290'; // hashed

$CFG->isediting = (isset($_SESSION['editing']) ? $_SESSION['editing'] : false);
if (isset($_REQUEST['edit'])) {
	$edit = $_REQUEST['edit'];
	$enable = ($edit == '1' || $edit == 'true');
	if (!$enable) {
		$CFG->isediting = false;
		$_SESSION['editing'] = $CFG->isediting;
	}
	else if ($enable && isset($_REQUEST['pw']) && md5($_REQUEST['pw']) == $CFG->editingpass) {
		$edit = $_REQUEST['edit'];
		$CFG->isediting = true;
		$_SESSION['editing'] = $CFG->isediting;
	}
}
//exit('editing: '.$CFG->isediting);//TMP

/*** choose site language ***/
$CFG->sitelang = 'en'; // default to english

// check host name, if ends with .it change site lang accordingly
if(preg_match('/^(www\.)?[^.]+\.([a-z]{2})$/', $_SERVER['HTTP_HOST'], $matches)) {
	$CFG->sitelang = $matches[2];
	//echo "set sitelang based on domain: $CFG->sitelang<br/>";//TMP
}

// check cookie
$current_lang = (isset($_COOKIE['sitelang']) ? $_COOKIE['sitelang'] : '');
$CFG->sitelang = (empty($current_lang) ? $CFG->sitelang : $current_lang);
//echo "cookie sitelang: $current_lang<br/>\n";//TMP

// check url param (override cookie and lang setting based on host)
$lang = (isset($_GET['lang']) ? $_GET['lang'] : '');
if (preg_match('/[a-z]{2}/', $lang)) {
	$CFG->sitelang = $lang;
	
	if ($current_lang != $lang) {	
		//$expire = time() + 60 * 60 * 24 * 365 * 10;
		$expire = 0;
		$result = setcookie('sitelang', $lang, $expire, '/', $_SERVER['HTTP_HOST']);
		//echo "set cookie 'sitelang = $lang' success? [$result]<br/>\n";//TMP
	}
}

//echo "FINAL SITELANG: $CFG->sitelang<br/>\n";//TMP