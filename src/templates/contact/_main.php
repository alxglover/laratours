<?php 
	$self = basename($_SERVER['PHP_SELF']);
	$self = preg_replace('/\.php$/', '', $self);

	$is_submitted = isset($_POST['submit']);
	
	$vars = array(
		'name' => '', 'email' => '', 'phone' => '', 'date' => '', 'days' => '', 'message' => ''	
	);
	
	if ($is_submitted) {
		$result = processContactForm();		
		if ($result) {
			$msg = $this->getStr('messagesent');
		}
		else {
			$msg = $this->getStr('messageerror');
			foreach ($vars as $key => $val) {
				$vars[$key] = $_POST[$key];
			}
		}
		$error_css = ($result ? 'error' : 'success');
	}
	
	$csrf_token = csrfGenerate();
?>
			<div id="contact-content">
				<?php 
					if ($is_submitted) {
						$msg_html = <<<MSG_HTML
							<div id="submit-message" class="$error_css rounded">
								<p>$msg</p>
							</div>
							<div style="clear: both;"></div>
MSG_HTML;
						echo $msg_html;
					}
				?>
				<h2><?php $this->ps('phone'); ?></h2>
				<p><?php $this->ps('phonecontact', 'general'); ?></p>
				<h2><?php $this->ps('email'); ?></h2>
				<p class="instructions"><?php $this->ps('forminstructions'); ?></p>
				<form name="contact-form" id="contactform" method="post" action="/<?php echo $self; ?>">
					<input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>" />
					<div class="slim-col text-col">
						<label for="fe-name"><?php $this->ps('name'); ?></label>
						<label for="fe-email"><?php $this->ps('youremail'); ?></label>
						<label for="fe-phone"><?php $this->ps('yourphone'); ?></label>
						<label for="fe-date"><?php $this->ps('startdate'); ?></label>																	
						<label for="fe-message"><?php $this->ps('message'); ?></label>
					</div>
					<div class="wide-col text-col">					
						<div class="input"><input id="fe-name" class="text" type="text" name="name" required value="<?php echo $vars['name']; ?>" /></div>
						<div class="input"><input id="fe-email" class="text" type="email" name="email" required value="<?php echo $vars['email']; ?>"/></div>					
						<div class="input"><input id="fe-phone" class="text" type="tel" name="phone" required value="<?php echo $vars['phone']; ?>"/></div>					
						<div class="input"><input id="fe-date" type="date" name="date" value="<?php echo $vars['date']; ?>"/> 
							<?php $this->ps('for'); ?> <input type="number" name="days" min="1" max = "30" step="1" value="<?php echo $vars['days']; ?>" /> <?php $this->ps('days'); ?></div>
						<div class="input">
							<textarea id="fe-message" name="message" required><?php echo $vars['message']; ?></textarea>
						</div>
						<div class="submit-buttons">
							<div id="validation-msg"></div>
							<input type="submit" name="submit" value="<?php $this->ps('formsubmit'); ?>" />
						</div>
					</div>
					<div style="clear: both;"></div>							
				</form>
			</div>
			<div style="clear: both;"></div>			