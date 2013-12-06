<?php
	require_once dirname(__FILE__) . '/../lib.php';

	$id = $_REQUEST['id'];
	$edited_content = $_REQUEST['html'];

	$result = array('success' => false);

	if (empty($id)) {
		$result['error'] = 'No id provided';
		echo json_encode($result);
		return;
	}

	$parts = explode('_', $id);
	$content_key = $parts[0];
	$content_id = $parts[1];

	try {
		$content_names = array($content_key);
		$lang = $CFG->sitelang;
		$contents = fetchContents($content_names, $lang);
		$c = $contents->$content_key;
		$identifier = $c['_lookups'][$content_id];
		$c[$identifier] = $edited_content;
		$success = saveContents($content_key, $lang, $c);
		if ($success) {
			$result['success'] = true;
			$result['message'] = "Saved contents for [$content_key:$identifier]";
		}
		else {
			$result['error'] = "Could not save contents [$content_key:$identifier]";
		}
	}
	catch (NoSuchContentException $e) {
		$result['error'] = $e->getMessage();
		echo json_encode($result);
		return;
	}

	//sleep(5);//TMP

	echo json_encode($result);