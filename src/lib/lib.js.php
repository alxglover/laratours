   <?php 
   		$lib_path = dirname(__FILE__).'/../lib.php';
   		require_once($lib_path);

		header('Content-Type: text/javascript'); // send javascript header

   		$view = (isset($_GET['view']) ? $_GET['view'] : false);
   		$view_obj = null;

   		if (!empty($view)) {
			$view_obj = loadView($view);
			if (empty($view_obj)) {
				return;
			}   		   			
   		}

   		function js_ps($view_obj, $identifier, $content_name, $noeditwrap=true) {
   			if ($view_obj) {
   				$view_obj->ps($identifier, $content_name, $noeditwrap);
   			}
   			else {
   				echo '[not found]';
   			}
   		}

   		echo "var self = '".$_SERVER['REQUEST_URI']."';\n";
   ?>

if (typeof WebFont !== 'undefined') {
	WebFont.load({
	    custom: {
	        families: ['england_hand_dbregular'],//, 'liberationsansregular', 'gillsansmt'],
	        urls: ['/stylesheets/main.css']
	    }
	});
}
   
$(document).ready(function(){	
	//alert($('html').attr('class'));//TMP

	// IE rounded corners
	$('.oldie .rounded-tr-br-bl, .ie8 .rounded-tr-br-bl').corner('round tr br bl');
	$('.oldie .rounded-tl-tr-br, .ie8 .rounded-tl-tr-br').corner('round tl tr br');
	$('.oldie .rounded-tl-tr, .ie8 .rounded-tl-tr').corner('round tl tr');
	$('.oldie .rounded, .ie8 .rounded').corner();

	// IE warning for old IE
	$('#ie-dialog').dialog({
		dialogClass: 'alert-dialog',
		modal: true,
		closeOnEscape: true,
		autoOpen: false,
		buttons: [
		{
			text: "<?php js_ps($view_obj, 'iewarning_okbutton', 'general'); ?>",
			click: function() {
				$(this).dialog("close");
				// set cookie to remember dialog viewed
				$.cookie('iewarning_viewed', 1);
			}
		}
		]				
	});	

	if ($.cookie('iewarning_viewed') != 1 && $('html').hasClass('oldie')) {
		$('#ie-dialog').dialog('open');
	}

	/*** image galleries ***/

	// start strip slip up
	var gallery_slide_ms = 1000;
	$('.gallery-container').css({
		'background-position-y': '-100px'
	});
	$('.gallery-img').css({
		'top' : '-100px'
	});

	// add enlarge buttons & shadow effect
	decorateGalleryImages(slideDownGallery, gallery_slide_ms);

	// slide images down - called after images have been decorated
	var slidDown = false;
	function slideDownGallery(slide_ms) {
		if (slidDown) {
			return;
		}
		slidDown = true;
		$('.gallery-container').animate({
			'background-position-y': '0px'
	  	}, slide_ms, function() { // animation complete
		});	
		$('.gallery-img').animate({
	    	top: "+=100"
	  	}, slide_ms, function() { // animation complete	    	
		});	
	}

	// add enlarge buttons and dialogs for displaying larger images
	function decorateGalleryImages(callback, callback_arg) {
		$('.gallery-img').each(function(){
			var container = $(this);
			var img = $(this).find('img').first();
			var shadow = $(this).find('.shadow').first();

			var classes = $(this)[0].className.split(/\s+/);
			var img_dir = classes[1];

			// get attribution info from json file
			var url = "/images/"+img_dir+"/info.json"; 
			$.getJSON(url, function(data) {
				img.css('cursor', 'pointer');
				shadow.css('cursor', 'pointer');
				var sitelang = '<?php echo $CFG->sitelang; ?>';
				var title = data['title'][sitelang];
				var author = data['author'];
				var source_link = data['source_link'];
				var license_name = data['license_name'];
				var license_link = data['license_link'];
				img.attr('title', title);
				shadow.attr('title', title);

				var zoomimg = $('<img class="zoomimg clean" title="+" alt="+" src="/images/zoom_in.png" />');
				container.append(zoomimg);

				// add dialogs and handlers
				var str_close = "<?php js_ps($view_obj, 'close', 'general'); ?>";

				var copyright = '<small class="smaller"><a target="_blank" class="copyright" href="'+source_link+'">© '+author+'</a>. <a target="_blank" href="'+license_link+'">'+license_name+'</a>.</small>';
				var imgdialog = $('<div class="gallery-img-dialog"><input type="hidden" autofocus="autofocus" /><img class="img-large" title="'+str_close+'" alt="'+title+'" src="/images/'+img_dir+'/large_1024.jpg" /><div class="clear"></div>'+copyright+'</div>');
				//imgdialog.css('visibility', 'hidden');
				imgdialog.find('img').css('cursor', 'pointer').click(function(){ imgdialog.dialog('close'); });
				container.append(imgdialog);
				imgdialog.find('*').css('visibility', 'visible');

				$(imgdialog).dialog({
					title: title,
					dialogClass: 'alert-dialog',
					modal: true,
					closeOnEscape: true,
					autoOpen: false,
					buttons: [
					{
						text: str_close,
						click: function() {
							$(this).dialog("close");
						}
					}
					],
					open: function() {},
					close: function() {}				
				});	

				zoomimg.click(function(){ $(imgdialog).dialog('open'); });
				if ($(shadow).length > 0) {
					shadow.click(function() { $(imgdialog).dialog('open'); });
				}
				else {
					img.click(function() { $(imgdialog).dialog('open'); });
				}				
				

				if (callback) {
					callback(callback_arg);
				}
			});
		});
	}

	/*** contact form validation ***/
	var cf = $('form#contactform');
	if (cf.length > 0) {
		cf.submit(function(evt){
			var result = validateContactForm();
			if (!result) {
				evt.preventDefault();
			}
		});
		
		$(cf).find('input', 'textarea').each(function(){
			var name = $(this).attr('name');
			$(this).blur(function(evt){
				//validateContactForm();
			});
		});	

		//$(cf).find('input[type="tel"]').each(function() {
		//	$(this)[0].setCustomValidity("Please enter a valid telephone number");
		//});
	}
	
	function validateContactForm() {
		var required = ['name', 'email', 'phone', 'message'];
		var is_error = false;
		$(required).each(function(i,val) {			
			var ff = $(cf).find('input[name='+val+'], textarea[name='+val+']').first();
			if (ff.val().length == 0) {
				is_error = true;
				//ff.parent().addClass('error');
				cf.find('label[for='+ff.attr('id')+']').addClass('error');
			}
			else {
				//ff.parent().removeClass('error');
				cf.find('label[for='+ff.attr('id')+']').removeClass('error');
			}
		});
		
		cf.find('#validation-msg').hide();
		if (is_error) {
			cf.find('#validation-msg').text('<?php js_ps($view_obj, 'contactvalidationwarning', '', true); ?>');
			cf.find('#validation-msg').show();							
		}		
		
		return !is_error;
	}
	
	// debugging	
	//$('div').attr('style', 'border: dashed 1px #CCC');//TMP

	<?php
		/*** page editing ***/
		if ($CFG->isediting) {
	?>
			// live editing message
			var exit_url = window.location.pathname + '?edit=0';
			var editstatus = $('<div id="editstatus">Live editing for this page is active. [<a title="stop editing" href="'+exit_url+'">exit</a>]</div>');
			editstatus.css({
				'position': 'absolute',
				'z-index':'1000',
				'top':'2px',
				'left':'0',
				'background-color':'#FFF',
				'color':'#000',
				'padding':'4px',
				'border':'dashed 2px #F55',
				'font-weight':'bold'
			});
			$('body').append(editstatus);
			var doc_w = $(document).width();
			editstatus.css('left', (doc_w / 2) - editstatus.width() / 2);

			// editing status message
			var editmsg = $('<div id="editmsg"></div>');
			var editmsg_top = 40;
			editmsg.css({
				'position': 'absolute',
				'z-index':'1000',
				'top': editmsg_top,
				'left':'0',
				'background-color':'#CCC',
				'color':'#000',
				'padding':'4px',
				'border':'solid 1px #222',				
				'font-weight':'normal',
				'visibility':'hidden'
			});
			$('body').append(editmsg);

			// view source container
			var viewsrc = $('<div id="viewsource"></div>');
			var viewsrc_top = 80;
			viewsrc.css({
				'position': 'absolute',
				'width' : '500px',
				'z-index':'1000',
				'top': viewsrc_top,
				'left':'0',
				'background-color':'#EEE',
				'color':'#444',
				'padding':'10px',
				'border':'solid 1px #222',				
				'font-size':'11px',
				'font-family':'Courier New',
				'font-weight':'normal',
				'visibility':'hidden',
				'display':'none'
			});
			$('body').append(viewsrc);

			// change editing status message
			function showEditingMessage(msg, fade) {
				editmsg.stop(true, true);
				editmsg.html('');
				editmsg.html(msg);
				var doc_w = $(document).width();
				editmsg.css('left', (doc_w / 2) - editmsg.width() / 2);
				editmsg.css('visibility', 'visible');
				editmsg.show();

				if (fade) {
					editmsg.fadeOut(fade);
				}
			}

			// keep messages fixed to top of page when scrolling
			$(document).scroll(function(){
				editstatus.css('top', $(this).scrollTop()+2);
				editmsg.css('top', $(this).scrollTop()+editmsg_top);
				viewsrc.css('top', $(this).scrollTop()+viewsrc_top);
			});

			// Source editing dialog
			var current_editor = null;
			var ignorefocuslost = false;
			var restore_previous = false;
			var previous_html = null;

			var editsrc_top = 80;

			$('#edit-src-dialog').dialog({
				title: 'Edit HTML source',
				position: ['center', editsrc_top],
				dialogClass: 'alert-dialog',
				modal: true,
				closeOnEscape: true,
				autoOpen: false,

				buttons: [
				{
					// Apply source edit button
					text: "Apply changes",
					click: function() {
						ignorefocuslost = false;
						// replace editable content with contents of source textarea and trigger save
						if ($(current_editor).length > 0) {
							//current_editor.html()
							var html = $(this).find('textarea').val();
							console.log('current editor: '+current_editor.attr('id'));//TMP
							console.log('applying html:\n'+html);//TMP
							current_editor.html(html);
							restore_previous = false;
   							//current_editor.focus();
							current_editor.blur();
						}							
						$(this).dialog("close");
					}
				},
				{
					// Cancel source edit button
					text: "Cancel",
					click: function() {
						// close dialog and restore focus to current editor
						ignorefocuslost = false;
						$(this).dialog("close");
   						if ($(current_editor).length > 0) {
							console.log('edit source closed, restoring focus');
   							current_editor.focus();
						}
					}
				},				
				]				
			});	

			// define handlers for editable content areas
			$('span.editable').each(function(){
				$(this).attr('contenteditable', 'true');
				$(this).css({
					'padding': '0',
					'margin':'0',
					'top': '0',
					'right': '0',
					'bottom' : '0',
					'left' : '0',
					'position': 'relative',
					'border': 'none',
					'background-color': 'none'
				});

				$(this).focus(function(){
					current_editor = $(this);
					previous_html = $(this).html();

					// set editing status message and provide controls
					var viewsrclabel = (viewsrc.is(':visible') ? 'Hide source' : 'View source');
					showEditingMessage('Editing content... [<a id="saveedit" href="#">Save</a>] [<a id="cancelediting" href="#">Cancel</a>] [<a id="viewsource" href="#">'+viewsrclabel+'</a>] [<a id="editsource" href="#">Edit source</a>]');
					$('a#cancelediting').mousedown(function(){
						restore_previous = true;
					});

					$('a#viewsource').mousedown(function(){ // on choosing to view source, ensure lost focus on editable area doesn't trigger save
						console.log('mousedown');//TMP
						ignorefocuslost = true;
						toggleViewSource();
						$(this).text(viewsrc.is(':visible') ? 'Hide source' : 'View source');
					});

					$('a#viewsource').mouseup(function(){ // restore focus to to content editor after clicking view source
						ignorefocuslost = false;
						current_editor.focus();
					});

					$('a#editsource').mousedown(function(){ // on choosing to edit source, ensure lost focus doesn't trigger save
						console.log('editsource mousedown');//TMP
   						if ($(current_editor).length > 0) {
							ignorefocuslost = true;
						}
					});

					$('a#editsource').click(function(evt){ // on clicking edit source, display source editor popup dialog
						evt.preventDefault();
						console.log('editsource click');//TMP
   						if ($(current_editor).length > 0) { // open source editor
							var html = current_editor.html();
							//alert(html);//TMP
							$('#edit-src-dialog textarea').val(html);							
							//$('#edit-src-dialog').dialog('option', 'position', { my: "center", at: "center", of: window });
							$('#edit-src-dialog').dialog('open');
						}
					});

				})

				$(this).keyup(function(evt){
					var code = (evt.keyCode || evt.which);
					if (code == 27) { // esc pressed
						restore_previous = true;
						$(this).blur(); // remove focus
					}
				});
				$(this).blur(function(){
					if (ignorefocuslost) {
						return true;
					}
					toggleViewSource(false);

					current_editor = null;

					// quit without saving
					if (restore_previous) {
						$(this).html(previous_html);
						restore_previous = false;
						showEditingMessage('Edit cancelled - content unchanged.', 2500);
						return true;
					}
					// save changes
					var current = $(this).html();
					if (current != previous_html) {
						saveContentEdit($(this).attr('id'), current);
						previous_html = current;
					}
					else {
						showEditingMessage('Content unchanged.', 2500);						
					}
				});
			});

   			function toggleViewSource(visibility) {
					viewsrc.stop(true, true);
					var is_visible = viewsrc.is(':visible');
					console.log('viewsrc is visible? '+is_visible);//TMP

					if (is_visible || (typeof visibility !== 'undefined' && !visibility)) {
						viewsrc.fadeOut(1500, function(){
							$('a#viewsource').text('View source');							
						});
						return;
   					}

   					if (!is_visible || (typeof visibility !== 'undefined' && visibility)) {
   						if ($(current_editor).length > 0) {
							var html = current_editor.html();
							viewsrc.text(html);
							viewsrc.html('<pre style="white-space: pre-wrap;">'+viewsrc.html()+'</pre>');
							var doc_w = $(document).width();
							viewsrc.css('left', (doc_w / 2) - viewsrc.width() / 2);
							viewsrc.css('visibility', 'visible');
							viewsrc.show();
							$('a#viewsource').text('View source');
						}

   					}
					//console.log(current_editor.html());//TMP
			}

			/**
			 * do ajax call passing content_id and html to update content
			 */
			function saveContentEdit(content_id, html) {
				var data = {
					'id' : content_id,
					'html': html
				};
				$('body').css('cursor', 'wait');
				showEditingMessage('Saving content...');

				$.post("/ajax/saveeditedcontent.php", data, function(data){
					$('body').css('cursor', 'auto');
					if (data['success']) {
						showEditingMessage(data['message'], 2500);						
					}
					else {
						showEditingMessage(data['error']);						
					}
				}, 'json');
			}
	<?php
		}
	?>
});