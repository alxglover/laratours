		<!-- Google Analytics -->
		<script type="text/javascript">
<?php 
	// Google Analytics
	$base_url = preg_replace('/^https?:\/\//', '', $CFG->wwwroot);
	$ga_tracking_ids = array(
		'laratours.co.uk' => 'UA-46070308-1',
		'laratours.it' => 'UA-46070308-2',
	);
	$ga_tracking_id = (isset($ga_tracking_ids[$base_url]) ? $ga_tracking_ids[$base_url] : '');

	if (!empty($ga_tracking_id)) {
?>				
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo $ga_tracking_id; ?>', '<?php echo $base_url; ?>');
		ga('send', 'pageview');
<?php 
	} 
?>
		</script>
		<!-- End Google Analytics -->
