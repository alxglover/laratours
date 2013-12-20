<?php
?>
		</div> <!-- end #pagewrapper -->
		<div id="footer-mask">
		</div>
		<div id="footer">
			<div id="contactdetails">
				<?php $this->ps('contactdetails', 'general'); ?>				
			</div>						
		</div>
		<?php // analytics
			require_once dirname(__FILE__) . '/_google_analytics.php';
			//require_once dirname(__FILE__) . '/_hosting24_analytics.php';
		?>

	</body>
</html>
<!-- defer render-blocking JS includes -->
<!--script src="http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script-->

<script src="/lib/jquery-1.10.2.min.js"></script>
<script src="/lib/jquery-ui-1.10.3.custom.min.js"></script>		

<script type="text/javascript">
	// defer loading particular stylesheets until body has loaded
	window.onload = function() {
		//console.log('adding deferred css');//TMP

    	var deferred_css = [
    		//'/min/g/jqueryuicss.css'
    		'/stylesheets/black-tie/jquery-ui-1.10.3.custom.min.css'
    	];

        if(document.getElementsByTagName("head").length > 0){
        	for (var i = 0; i < deferred_css.length; i++) {
                if (document.createStyleSheet){
                    document.createStyleSheet(deferred_css[i]);
                }
                else {
                	var css_link = document.createElement('link');
                	css_link.type = 'text/css';
                	css_link.rel = 'stylesheet';
                	css_link.media = 'screen';
                	css_link.href = deferred_css[i];
                	document.getElementsByTagName("head")[0].appendChild(css_link);
                }
        	}
        }
   	};
</script>

<script async="false" src="/lib/jquery.cookie.min.js"></script>
<script async="false" src="/lib/jquery.corner.min.js"></script>

<?php // skype buttons
	require_once dirname(__FILE__) . '/_skype.php';				
?>

<!--script src="/lib/lib.js.php?view=<?php echo $this->name; ?>"></script-->
<?php 
if ($CFG->isediting) { 
?>
<script async="false" src="/lib/lib.js.php?view=<?php echo $this->name . '&ts=' . time(); ?>"></script>
<?php
} else {
?>
<script async="false" src="/min/g/libjs.js/<?php echo $this->name; ?>"></script>
<?php    
}
	require_once $CFG->libdir . '/tracking.php';