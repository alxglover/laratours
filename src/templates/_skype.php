			<script type="text/javascript">
			// ensure skype buttons api is only loaded after everything else - improves pagespeed ranking
			var orig_onload = window.onload;
			window.onload = function() {
				//prevent skype from appending script tags to head - speeds up page load
				if (!document.getElementsByTagName("html")[0].className.match(/ie8/) && typeof Element !== 'undefined') {
					var f = Element.prototype.appendChild;
					var b = document.getElementsByTagName("body")[0];
					Element.prototype.appendChild = function(){
						if (this.nodeName == 'HEAD' && arguments[0].nodeName == 'SCRIPT') {
							arguments[0].src = '/min/g/skypejs.js'; // minified pass-thru wrapper so script can be minified with mod_deflate
							//arguments[0].defer = 'defer';
							b.appendChild(arguments[0]); // add the script tag to the body instead
							//console.log('intercepted appendChild');//TMP
							return;
						}
						f.apply(this, arguments);
					};
					//console.log('redefined appendChild');//TMP
				}

				var js_script = document.createElement('script');
				js_script.type = 'text/javascript';
				js_script.src = '/min/g/skypebuttonsjs.js'; // include api wrapper (allows caching)
				document.getElementsByTagName("body")[0].appendChild(js_script);

				// keep polling for Skype object before attempting to decorate buttons
				function waitForSkypeObj(){
				    if(typeof Skype !== "undefined") {
						if (typeof f !== 'undefined') {
							Element.prototype.appendChild = f; // restore original function
							//console.log('restored appendChild');//TMP
						}

						//console.log('decorating skype buttons');//TMP
						if ($('#SkypeButton_Call_1').length > 0) {					
						    Skype.ui({
						      "name": "call",
						      "element": "SkypeButton_Call_1",
						      "participants": ["princesslarathepink"],
						      "imageColor": "blue",
						      "imageSize": 24
						    });
						}
						if ($('#SkypeButton_Call_2').length > 0) {
							Skype.ui({
							      "name": "call",
							      "element": "SkypeButton_Call_2",
							      "participants": ["princesslarathepink"],
							      "imageColor": "blue",
							      "imageSize": 24
							});
						}
						$('.skype-call-button a').attr('title', '<?php $this->ps('callmewithskype', 'general'); ?>');
				    }
				    else{
				        setTimeout(function(){ waitForSkypeObj();},200);
				    }
				}

				$(document).ready(function(){
					waitForSkypeObj();
				});

				orig_onload();
			};
		    </script>			
