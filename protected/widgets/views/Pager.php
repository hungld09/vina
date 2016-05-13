<ul class="pagination" style="text-align:center;">
	<!--Trang: -->
	<?php
	//var_dump($pager);
	$fullUrl = $delimiter == '=' ? (preg_match('/\?/', $requestUrl) ? "$requestUrl&page=" : "$requestUrl?page=") : "$requestUrl?page=";
	if ($pager['page_number'] > 0){
		echo '<li>';
		echo CHtml::link("&laquo;", $fullUrl . $pager['page_number'], array());
		echo '</li>';
		$ignore = false;
	}
	for ($i = 0; $i < $pager['total_page']; $i++) {
		$page = $i + 1;
		if ($i > 1 && $i < $pager['page_number'] - 2) {
			if ($ignore) continue;
			if ($i + 1 < $pager['page_number'] - 2) { // only display `...` if have at least 2 pages in the middle
				echo "<li class=\"pager gradient\">&hellip;</li>";
				$ignore = true;
			} else {
				echo '<li>';
				echo CHtml::link($page, "$fullUrl$page", array());
				echo '</li>';
			}
		} else if ($i == $pager['page_number']) {
			echo "<li class=\"active\"><a>$page</a></li>";
			$ignore = false;
		} else if ($i > $pager['page_number'] + 2 && $i < $pager['total_page'] - 2) {
			if ($ignore) continue;
			if ($i + 1 < $pager['total_page'] - 2) { // only display `...` if have at least 2 pages in the middle
				echo "<li class=\"pager gradient\">&hellip;</li>";
				$ignore = true;
			} else {
				echo '<li>';
				echo CHtml::link($page, "$fullUrl$page", array());
				echo '</li>';
			}
		} else {
			echo '<li>';
			echo CHtml::link($page, "$fullUrl$page", array());
			echo '</li>';
			$ignore = false;
		}
	}
	$next = $pager['page_number'] + 1;
	if ($next < $pager['total_page']){
		echo '<li>';
		echo CHtml::link("&raquo;", "$fullUrl" . ($next + 1), array());
		echo '</li>';
	}
	?>
</ul>