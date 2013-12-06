<?php
require_once dirname(__FILE__) . '/config.php';
require_once $CFG->libdir . '/textlib.class.php';
require_once $CFG->libdir . '/viewfactory.php';

/**
 *
 */
function loadView($view) {
	$view_path = getViewPath($view);
	
	if (!file_exists($view_path)) {
		showError("View file not found: '$view'.");
		return false;
	}
	
	require_once $view_path;
	try {
		$view_class = getViewClass($view);		
	}
	catch (NoSuchViewException $e) {
		showError($e);
		return false;
	}
	
	//$view_obj = $view_class::getInstance();
	//$view_obj = call_user_func(array($view_class, 'getInstance')); // PHP 5.2 workaround
	$view_obj = ViewFactory::getInstance($view_class);
	//print_r($view_obj);//TMP

	return $view_obj;
}

/**
 * 
 * @param unknown $view
 * @param unknown $template
 */
function renderView($view, $template='main') {
	
	$view_obj = loadView($view);
	if (empty($view_obj)) {
		return;
	}

	$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
	
	$view_obj->setTemplate($template, $action);
	
	try {
		$view_obj->display();
	}
	catch (TemplateNotFoundException $e) {
		showError($e->getMessage());
	}
}

/**
 * 
 * @param unknown $view
 * @throws NoSuchViewException
 * @return string
 */
function getViewClass($view) {
	$view_class = ucfirst($view) . "View";
	if (!class_exists($view_class)) {
		throw new NoSuchViewException("View class not found: '$view_class'.");
	}
	//exit("view_class: $view_class");//TMP
	
	return $view_class;
}

/**
 * 
 * @param unknown $view
 * @return string
 */
function getViewPath($view) {
	return dirname(__FILE__) . '/views/' . $view . '.php';
}

/**
 * 
 * @param unknown $template
 * @return string
 */
function getTemplatePath($template) {
	return dirname(__FILE__) . '/templates/' . $template . '.php';
}


/**
 * 
 * @param unknown $content
 * @param string $lang
 * @return string
 */
function getContentPath($content, $lang='en') {
	$path = dirname(__FILE__) . '/content/' . $lang . '/' . $content . '.php';
	//echo __METHOD__ . "($content) path: $path<br/>\n";//TMP
	return $path;
}

/**
 * 
 * @param unknown $msg
 */
function showError($msg) {
	echo "ERROR: " .  $msg;
}

/**
 * 
 * @param unknown $content_names
 * @param string $lang
 * @throws NoSuchContentException
 * @return stdClass
 */
function fetchContents($content_names, $lang='en') {
	global $CFG;

	$contents = new stdClass();
	
	foreach ($content_names as $content_name) {
		$path = getContentPath($content_name, $lang);
		if (!file_exists($path)) {
			throw new NoSuchContentException("Content not found for '$content_name' [lang: $lang].");
		}
		
		ob_start();
		require_once $path;
		ob_end_clean();

		$contents->$content_name = $content;
		if ($CFG->isediting) {
			$c = $contents->$content_name;
			$c['_lookups'] = array();
			foreach ($contents->$content_name as $identifier => $value) {
				$id = generateContentId($identifier);
				//echo "id: $id\n";//TMP
				$c['_lookups'][$id] = $identifier;
			}
			$contents->$content_name = $c;
		}
	}
	
	
	return $contents;
}

/**
 *
 */
function saveContents($content_name, $lang, $contents) {
	//echo __METHOD__ . "($content_name, $lang, [contents])\n";//TMP

	$path = getContentPath($content_name, $lang);
	//$path = $path . '.saved';//TMP

	$array_inner = '';
	foreach($contents as $identifier => $value) {
		if (is_string($value)) {
			$escaped_value = mb_ereg_replace('\'', '\\\'', $value, 'm');
			$array_inner .= "\t'$identifier' => '$escaped_value',\n";
		}
	}

	$content_str = <<<CONTENT_PHP
<?php

\$content = array(
{$array_inner}
);

CONTENT_PHP;

	//exit("<pre>".$content_str."</pre>");//TMP
	//$content_str = utf8_encode($content_str);
	$success = file_put_contents($path, $content_str);
	return $success;
}

/**
 *
 */
function generateContentId($string_id) {
	$id = md5($string_id);
	$id = substr($id, 22);
	return $id;	
}

/**
 * Sends contact form email to $CFG->siteowneremail
 */
function processContactForm() {		
	global $CFG;
	
	$success = true;
	
	$vars = $_POST; // @todo - santize form input
	foreach ($vars as $key => $val) {
		$vars[$key] = strip_tags($val);
		if ($key == 'name' || $key = 'email') {
			$vars[$key] = preg_replace('/[\r\n]+/', '', $vars[$key]);
		}
	}
	
	$csrf = $vars['csrf'];
	if (!csrfValidate($csrf)) { // Cross-site forgery token validation fail
		//echo "CSRF validation fail<br/>\n";//TMP - @todo - log this somewhere
		return false;	
	}	
	
	$date = $vars['date'];
	$date_str = '';
	if (!empty($date)) {
		$date_str = $date;
		if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
			$ts = strtotime($date);
			$date_str = 'on ' . date('l jS F Y', $ts);			
		}
	}
	$days = $vars['days'];
	$name = $vars['name'];
	$email = $vars['email'];
	$phone = $vars['phone'];
	$message = $vars['message'];

	$email_subject = "URGENT: Message for a potential job $date_str";
	
	if (empty($date_str)) {
		$email_subject = "URGENT: Message from the Lara Tours website";
	}

	$sep = str_repeat('-', 70);

	$date_info = '';
	if (!empty($date_str)) {
		$date_info = <<<DATE_INFO

Booking date: $date_str
$sep
DATE_INFO;

	}

	$days_info = '';
	if (!empty($days)) {
		$days_info = <<<DAYS_INFO

Booking length: $days day(s)
$sep
DAYS_INFO;

	}

	$email_msg = <<<EMAIL_MSG
"{$name}" sent you a message via the Lara Tours website.

{$sep}
Phone number: $phone
{$sep}{$date_info}{$days_info}

MESSAGE:

$message

EMAIL_MSG;

	//exit('<pre>SUBJECT: '.$email_subject . "\nMESSAGE:\n" .  $email_msg.'</pre>');//TMP
	
	$from_name = $name;
	$from_email = $email;
	
	// Email priority (1 = High, 3 = Normal, 5 = low).
	$priority = 1;
	$success = send_email($CFG->siteowneremail, $CFG->siteownername, $from_name, $from_email, $email_subject, $email_msg, '', $priority);
	
	// Send a copy to the CC address
	if ($success && isset($CFG->ccaddress) && !empty($CFG->ccaddress)) {
		send_email($CFG->ccaddress, $CFG->siteownername, $from_name, $from_email, $email_subject, $email_msg, '', $priority);
	}

// 	echo "<pre>";//TMP
// 	echo "subject: $email_subject\n";//TMP
// 	echo "from: $from_name\n";//TMP
// 	echo "\n$email_msg\n";//TMP
// 	echo "</pre>";//TMP
	
	return $success;
}


function csrfGenerate() {
	global $CFG;
	return sha1(session_id() . $CFG->csrf_key);
}

function csrfValidate($token) {
	$compare = csrfGenerate();

	return ($token == $compare);
}

/**
 * Send an email to a specified recipient
 *
 * @uses $CFG
 * @param string $email Recipient email address
 * @param string $to Recipient name
 * @param string $fromname Sender name
 * @param string $fromemail Sender email
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, relative to $CFG->dataroot
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param bool $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @param int $wordwrapwidth custom word wrap width
 * @return bool|string Returns "true" if mail was sent OK, "emailstop" if email
 *          was blocked by user and "false" if there was another sort of error.
 */
function send_email($email, $to, $fromname, $fromemail, $subject, $messagetext, $messagehtml='', $priority='',
					$attachment='', $attachname='', $usetrueaddress=true, $replyto='', $replytoname='', $wordwrapwidth=79) {
	global $CFG;
	
	if (empty($email)) {
		return false;
	}

	if (!empty($CFG->divertallemailsto)) {
		$subject = "[DIVERTED {$email}] $subject";
		$email = $CFG->divertallemailsto;
	}
	
	if (isset($CFG->disablemail) && $CFG->disablemail) {
		return false;
	}

	$mail =& get_mailer();
	
	if (isset($CFG->smtpdebug)) {
		$mail->SMTPDebug = $CFG->smtpdebug;
	}

	if (!empty($mail->SMTPDebug)) {
		echo '<pre>' . "\n";
	}

	/// We are going to use textlib services here
	$textlib = textlib_get_instance();

	// make up an email address for handling bounces
	if (!empty($CFG->handlebounces)) {
		$modargs = 'B'.base64_encode(pack('V',rand(0, 100000))).substr(md5($email),0,16);
		$mail->Sender = generate_email_processing_address(0,$modargs);
	} else {
		$mail->Sender = $CFG->supportemail;
	}

	if ($usetrueaddress) {
		$mail->From     = stripslashes($fromemail);
		$mail->FromName = $fromname;
	} else {
		$mail->From     = $CFG->noreplyaddress;
		$mail->FromName = $fromname;
		if (empty($replyto)) {
			$mail->AddReplyTo($CFG->noreplyaddress, $CFG->noreplyname);
		}
	}

	if (!empty($replyto)) {
		$mail->AddReplyTo($replyto,$replytoname);
	}

	$mail->Subject = substr(stripslashes($subject), 0, 900);

	$mail->AddAddress(stripslashes($email), $to );

	//if (isset($CFG->ccaddress)) {
	//	$mail->AddAddress(stripslashes($CFG->ccaddress));
	//}

	$mail->WordWrap = $wordwrapwidth;                   // set word wrap

	/*
	if (!empty($from->customheaders)) {                 // Add custom headers
		if (is_array($from->customheaders)) {
			foreach ($from->customheaders as $customheader) {
				$mail->AddCustomHeader($customheader);
			}
		} else {
			$mail->AddCustomHeader($from->customheaders);
		}
	}
	*/

	if (!empty($priority)) {
		$mail->Priority = $priority;
	}

	if ($messagehtml) { // Don't ever send HTML to users who don't want it
		$mail->IsHTML(true);
		$mail->Encoding = 'quoted-printable';           // Encoding to use
		$mail->Body    =  $messagehtml;
		$mail->AltBody =  "\n$messagetext\n";
	} else {
		$mail->IsHTML(false);
		$mail->Body =  "\n$messagetext\n";
	}

	if ($attachment && $attachname) {
		if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
			$mail->AddAddress($CFG->supportemail, $CFG->supportname);
			$mail->AddStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
		} else {
			require_once($CFG->libdir.'/filelib.php');
			$mimetype = mimeinfo('type', $attachname);
			$mail->AddAttachment($CFG->dataroot .'/'. $attachment, $attachname, 'base64', $mimetype);
		}
	}

	/// If we are running under Unicode and sitemailcharset, convert the email
	/// encoding to the specified one
	if ((!empty($CFG->sitemailcharset))) {
		/// Set it to site mail charset
		$charset = $CFG->sitemailcharset;
		/// If it has changed, convert all the necessary strings
		$charsets = get_list_of_charsets();
		unset($charsets['UTF-8']);
		if (in_array($charset, $charsets)) {
			/// Save the new mail charset
			$mail->CharSet = $charset;
			/// And convert some strings
			$mail->FromName = $textlib->convert($mail->FromName, 'utf-8', $mail->CharSet); //From Name
			foreach ($mail->ReplyTo as $key => $rt) {                                      //ReplyTo Names
				$mail->ReplyTo[$key][1] = $textlib->convert($rt[1], 'utf-8', $mail->CharSet);
			}
			$mail->Subject = $textlib->convert($mail->Subject, 'utf-8', $mail->CharSet);   //Subject
			foreach ($mail->to as $key => $to) {
				$mail->to[$key][1] = $textlib->convert($to[1], 'utf-8', $mail->CharSet);      //To Names
			}
			$mail->Body = $textlib->convert($mail->Body, 'utf-8', $mail->CharSet);         //Body
			$mail->AltBody = $textlib->convert($mail->AltBody, 'utf-8', $mail->CharSet);   //Subject
		}
	}

	//echo '<pre>'.print_r($mail, true).'</pre>';//TMP
	
	
	if ($mail->Send()) {
		//exit('debug: '.$mail->SMTPDebug.' subject: '.$subject.' email: '.$email.' to: '.$to.' fromname: '.$fromname.' fromemail: '.$fromemail."\n");//TMP!!!
		$mail->IsSMTP();                               // use SMTP directly
		if (!empty($mail->SMTPDebug)) {
			echo '</pre>';
		}
		return true;
	} else {
		//mtrace('ERROR: '. $mail->ErrorInfo); //@todo - log this somewhere
		if (!empty($mail->SMTPDebug)) {
			echo '</pre>';
		}
		return false;
	}
	
	return false;
}

function generate_email_processing_address($modid,$modargs) {
	global $CFG;

	$header = $CFG->mailprefix . substr(base64_encode(pack('C',$modid)),0,2).$modargs;
	return $header . substr(md5($header.$CFG->siteidentifier),0,16).'@'.$CFG->maildomain;
}

/**
 * Get mailer instance, enable buffering, flush buffer or disable buffering.
 * @param $action string 'get', 'buffer', 'close' or 'flush'
 * @return reference to mailer instance if 'get' used or nothing
 */
function &get_mailer($action='get') {
	global $CFG;

	static $mailer  = null;
	static $counter = 0;

	if (!isset($CFG->smtpmaxbulk)) {
		$CFG->smtpmaxbulk = 1;
	}

	if ($action == 'get') {
		$prevkeepalive = false;

		if (isset($mailer) and $mailer->Mailer == 'smtp') {
			if ($counter < $CFG->smtpmaxbulk and empty($mailer->error_count)) {
				$counter++;
				// reset the mailer
				$mailer->Priority         = 3;
				$mailer->CharSet          = 'UTF-8'; // our default
				$mailer->ContentType      = "text/plain";
				$mailer->Encoding         = "8bit";
				$mailer->From             = "root@localhost";
				$mailer->FromName         = "Root User";
				$mailer->Sender           = "";
				$mailer->Subject          = "";
				$mailer->Body             = "";
				$mailer->AltBody          = "";
				$mailer->ConfirmReadingTo = "";

				$mailer->ClearAllRecipients();
				$mailer->ClearReplyTos();
				$mailer->ClearAttachments();
				$mailer->ClearCustomHeaders();
				return $mailer;
			}

			$prevkeepalive = $mailer->SMTPKeepAlive;
			get_mailer('flush');
		}

		include_once($CFG->libdir . '/phpmailer/class.phpmailer.php');
		$mailer = new phpmailer();

		$counter = 1;

		$mailer->Version   = 'Lara Tours v0.1';         // mailer version
		$mailer->PluginDir = $CFG->libdir . '/phpmailer/';      // plugin directory (eg smtp plugin)
		$mailer->CharSet   = 'UTF-8';

		// some MTAs may do double conversion of LF if CRLF used, CRLF is required line ending in RFC 822bis
		// hmm, this is a bit hacky because LE should be private
		if (isset($CFG->mailnewline) and $CFG->mailnewline == 'CRLF') {
			$mailer->LE = "\r\n";
		} else {
			$mailer->LE = "\n";
		}

		if ($CFG->smtphosts == 'qmail') {
			$mailer->IsQmail();                              // use Qmail system

		} else if (empty($CFG->smtphosts)) {
			$mailer->IsMail();                               // use PHP mail() = sendmail

		} else {
			$mailer->IsSMTP();                               // use SMTP directly
			if (!empty($CFG->debugsmtp)) {
				$mailer->SMTPDebug = true;
			}
			$mailer->Host          = $CFG->smtphosts;        // specify main and backup servers
			$mailer->SMTPKeepAlive = $prevkeepalive;         // use previous keepalive

			if ($CFG->smtpuser) {                            // Use SMTP authentication
				$mailer->SMTPAuth = true;
				$mailer->Username = $CFG->smtpuser;
				$mailer->Password = $CFG->smtppass;
			}
		}

		return $mailer;
	}

	$nothing = null;

	// keep smtp session open after sending
	if ($action == 'buffer') {
		if (!empty($CFG->smtpmaxbulk)) {
			get_mailer('flush');
			$m =& get_mailer();
			if ($m->Mailer == 'smtp') {
				$m->SMTPKeepAlive = true;
			}
		}
		return $nothing;
	}

	// close smtp session, but continue buffering
	if ($action == 'flush') {
		if (isset($mailer) and $mailer->Mailer == 'smtp') {
			if (!empty($mailer->SMTPDebug)) {
				echo '<pre>'."\n";
			}
			$mailer->SmtpClose();
			if (!empty($mailer->SMTPDebug)) {
				echo '</pre>';
			}
		}
		return $nothing;
	}

	// close smtp session, do not buffer anymore
	if ($action == 'close') {
		if (isset($mailer) and $mailer->Mailer == 'smtp') {
			get_mailer('flush');
			$mailer->SMTPKeepAlive = false;
		}
		$mailer = null; // better force new instance
		return $nothing;
	}
}

/**
 * Returns a list of charset codes. It's hardcoded, so they should be added manually
 * (cheking that such charset is supported by the texlib library!)
 *
 * @return array And associative array with contents in the form of charset => charset
 */
function get_list_of_charsets() {

	$charsets = array(
			'EUC-JP'     => 'EUC-JP',
			'ISO-2022-JP'=> 'ISO-2022-JP',
			'ISO-8859-1' => 'ISO-8859-1',
			'SHIFT-JIS'  => 'SHIFT-JIS',
			'GB2312'     => 'GB2312',
			'GB18030'    => 'GB18030', // gb18030 not supported by typo and mbstring
			'UTF-8'      => 'UTF-8');

	asort($charsets);

	return $charsets;
}

/**
 * Create a directory.
 *
 * @uses $CFG
 * @param string $directory  a string of directory names under $CFG->dataroot eg  stuff/assignment/1
 * param bool $shownotices If true then notification messages will be printed out on error.
 * @return string|false Returns full path to directory if successful, false if not
 */
function make_upload_directory($directory, $shownotices=true) {

	global $CFG;

	$currdir = $CFG->dataroot;

	umask(0000);

	if (!file_exists($currdir)) {
		if (! mkdir($currdir, $CFG->directorypermissions)) {
			if ($shownotices) {
				echo '<div class="notifyproblem" align="center">ERROR: You need to create the directory '.
						$currdir .' with web server write access</div>'."<br />\n";
			}
			return false;
		}
	}

	// Make sure a .htaccess file is here, JUST IN CASE the files area is in the open
	/*
	if (!file_exists($currdir.'/.htaccess')) {
		if ($handle = fopen($currdir.'/.htaccess', 'w')) {   // For safety
			@fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
			@fclose($handle);
		}
	}
	*/

	$dirarray = explode('/', $directory);

	foreach ($dirarray as $dir) {
		$currdir = $currdir .'/'. $dir;
		if (! file_exists($currdir)) {
			if (! mkdir($currdir, $CFG->directorypermissions)) {
				if ($shownotices) {
					echo '<div class="notifyproblem" align="center">ERROR: Could not find or create a directory ('.
							$currdir .')</div>'."<br />\n";
				}
				return false;
			}
			//@chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
		}
	}

	return $currdir;
}


/* Exceptions */

class NoSuchContentException extends Exception {


}


class NoSuchViewException extends Exception {
	
	
}

class TemplateNotFoundException extends Exception {
	
}