<?php 
	$pagetitle = (isset($title) ? $title : $this->getStr('sectiontitle', '', true));
	$lang = $CFG->sitelang;
?><!DOCTYPE html>
<!--[if IEMobile 7 ]> 
<html dir="ltr" class="iem7" lang="<?php echo $lang; ?>">
<![endif]-->
<!--[if lt IE 7 ]>
<html dir="ltr" class="ie ie6 oldie" lang="<?php echo $lang; ?>">
<![endif]-->
<!--[if IE 7 ]>       
<html dir="ltr" class="ie ie7 oldie" lang="<?php echo $lang; ?>">
<![endif]-->
<!--[if IE 8 ]>
<html dir="ltr" class="ie ie8" lang="<?php echo $lang; ?>"> 
<![endif]-->
<!--[if IE 9 ]>
<html dir="ltr" class="ie ie9" lang="<?php echo $lang; ?>"> 
<![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<html dir="ltr" class="other-browser" lang="<?php echo $lang; ?>">
<!--<![endif]-->
	<head itemscope itemtype="http://schema.org/Organization" id="laratours" itemref="contactdetails">
		<title><?php $this->ps('laratourstitle', 'general', true); ?> - <?php echo $pagetitle; ?></title>
		<meta itemprop="name" content="<?php $this->ps('laratourstitle', 'general', true); ?>">

		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="<?php $this->ps('metadata_keywords', 'general'); ?>, <?php $this->ps('metadata_keywords'); ?>">
		<meta name="description" content="<?php $this->ps('metadata_description', 'general'); ?>">
		<meta itemprop="description" content="<?php $this->ps('metadata_description', 'general'); ?>">		
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />

		<?php // force inlining of CSS - helps pagespeed
			require_once $CFG->libdir . '/css_inliner.php';
			$css_files = array('/stylesheets/main.css');
			$css_inliner = new CssInliner($css_files);
			$css_inliner->printStyles();
		?>

	</head>
	<body class="main">
		<div id="ie-dialog" title="<?php $this->ps('iewarning_title', 'general'); ?>">
			<?php $this->ps('iewarning_text', 'general'); ?>
		</div>
		<?php if ($CFG->isediting) { ?>
		<div id="edit-src-dialog" title="">
			<textarea></textarea>
		</div>
		<?php } ?>
		<div id="header">
			<div id="headings">
				<a id="home" title="Site home" href="/"><h1 id="logo-text"><?php $this->ps('laratours', 'general'); ?></h1></a>
				<div style="clear: both;"></div>
				<div id="subheading">
					<div id="subheading-text">
						<?php $this->ps('mainsubheading', 'general'); ?>
					</div>
					<div id="sitelang"><!-- <?php echo $CFG->wwwroot . $_SERVER['REQUEST_URI']; ?> -->
						<span><small><?php $this->ps('language', 'general'); ?></small></span>
						<?php
							$self = $_SERVER['REQUEST_URI'];
							$self = str_replace(".php", "", $self);   // clean URL
							$self = str_replace("/index", "", $self); //
							$self = preg_replace('/(\?|&)?lang=[a-z]{2}/', '', $self); // remove lang param
							$en_url = $CFG->wwwroot . $self . '?lang=en';
							$it_url = $CFG->wwwroot . $self . '?lang=it';
							if (preg_match('/laratours\.co\.uk/', $CFG->wwwroot)) {
								$it_url = 'http://laratours.it'.$self.'?lang=it';
							}
							else if (preg_match('/laratours\.it/', $CFG->wwwroot)) {
								$en_url = 'http://laratours.co.uk'.$self.'?lang=en';
							}
						?>
						<a title="View site in English" href="<?php echo $en_url;  ?>"><img alt="gb flag" class="flag" src="/images/gb.png" /></a>
						<a title="Il sito in Italiano" href="<?php echo $it_url;  ?>"><img alt="it flag" class="flag" src="/images/it.png" /></a>
					</div>					
				</div>
			</div>
			<img class="logo" alt="logo" height="80" src="/images/sil5.png" />
			<div style="clear: both;"></div>			
		</div>
		<div id="header-mask"></div>
		<div style="clear: both;"></div>
		<div id="pagewrapper">
		