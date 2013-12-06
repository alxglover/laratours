<?php
	$subnav = array(
		'main' => array('url' => '/guiding', 'label' => $this->getStr('sectiontitle')),
		'walkingtours' => array('url' => '/guiding/walkingtours', 'label' => $this->getStr('walkingtours')),
		//'dramatours' => array('url' => '/guiding/dramatours', 'label' => $this->getStr('dramatours')),																
	);
	if (isset($current)) {
		$subnav[$current]['current'] = true;
	}
	require_once dirname(__FILE__) . '/../_subnav.php'; 
?>
