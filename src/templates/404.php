<?php
	$title = $this->getStr('title_404');
	require_once dirname(__FILE__) . '/_header.php';
?>
		
		<div id="maincontent" class="error404">
			<div class="content-section">
				<div class="img-col med-col">
					<p><img src="/images/404_cat_pink.png" alt="404 cat" /></p>
				</div>
				<div class="text-col wider-col">
				<?php $this->ps('error_404'); ?>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	
<?php require_once dirname(__FILE__) . '/_footer.php'; ?>