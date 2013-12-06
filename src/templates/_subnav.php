				<ul class="subnav rounded">
<?php 
	$i = 0;
	$current_label = '';
	foreach ($subnav as $nav_item) {
		$is_current = (isset($nav_item['current']) && $nav_item['current']);
		if ($is_current) {
			$current_label = $nav_item['label'];
		}
		$current_css = ($is_current ? 'current' : '');
		$first_css = ($i == 0 ? 'first' : '');
					$label = '<span>'.$nav_item['label'].'</span>';
					$label = ($is_current ? $label : '<a href="'.$nav_item['url'].'">'.$label.'</a>');
					$nav_item = <<<NAV_ITEM_HTML
					<li class="{$current_css} {$first_css}">{$label}</li>
NAV_ITEM_HTML;

		echo $nav_item;
		$i++;
	}					
?>
				</ul>
<?php if (!empty($current_label) && $current_label != $pagetitle) { // append to page title ?>
<script type="text/javascript">
	var title = document.title;
	title += " - "+<?php echo json_encode($current_label); ?>;
	document.title = title;
</script>
<?php } ?>
