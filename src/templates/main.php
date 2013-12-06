<?php
	$partial = (empty($this->action) ? 'main' : $this->action);

	require_once dirname(__FILE__) . '/_header.php';
?>
<?php require_once dirname(__FILE__) . '/_tabs.php'; ?>
		
		<div id="maincontent" class="rounded-tr-br-bl">
			<?php require_once dirname(__FILE__) . '/'.$this->name.'/_'.$partial.'.php'; ?>
		</div>
	
<?php require_once dirname(__FILE__) . '/_footer.php'; ?>