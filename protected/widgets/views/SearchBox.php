		<header>
			<div class="top-head">
				<a href="#" class="main-menu-link icon-menu fn-nav-show"></a>
				<form name="frmSearch" id="frmSearch" class="frm-search" action="<?php echo CHtml::encode(Yii::app()->baseUrl . "/video/search");?>">
					<p class="text-box">
						<input autocomplete="off" type="text" id="q" name="q" placeholder="Tìm kiếm" class="search-text-box" value="" />
						<a href="#" class="delete-btn icon-cancel none" id="fnSearchReset"></a>
						<a href="#" class="search-btn icon-search" id="fnSearchSubmit"></a>
					</p>
				</form>
			</div>
		</header>