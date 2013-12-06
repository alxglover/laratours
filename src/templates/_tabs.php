<?php
	$current_view = $this->name;
	//echo "current view: $current_view<br/>\n";//TMP

	$tabs = array(
		'guiding', 'tourorganising', 'interpreting', 'contact'
	);
?>
		<!--div id="main-nav-tabs"-->
			<ul id="mainnavtabs" class="tabs">
<?php 
	$i = 0;
	$count = count($tabs);
	foreach ($tabs as $tab) {
		$first_css = ($i == 0 ? 'first' : '');
		$last_css = ($i == $count - 1 ? 'last' : '');
		$selected_css = ($tab == $current_view ? 'selected' : 'unselected');
		$tab_label = $this->getStr($tab, 'general');
		$html = <<<TAB_HTML
				<li id="$tab" class="tab $selected_css $first_css $last_css rounded-tl-tr">
					<a href="/{$tab}">$tab_label</a>
				</li>
TAB_HTML;

		echo $html;
		$i++;
	}
?>
			</ul>			
		<!--/div-->