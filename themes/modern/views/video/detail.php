<?php
/** @var $asset VodAsset */
/** @var $this VideoController */
$this->pageTitle = "mobiphim - " . $asset->display_name;
$episode_count = count($asset->vodEpisodes);
?>
	<div class="page" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container play" id="fnBody">
<?php $this->widget("application.widgets.Header"); ?>
			<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
			<div class="divider header-bg"><h3 class="dtitle">THÔNG TIN PHIM</h3></div>
			<div class="content" id="fnRTAsset">
				<div class="content-items">
					<div class="emborder"><img src="<?php echo $posterLandUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /></div>
					<h3><?php echo CHtml::encode($asset['display_name']);?></h3>
					<h4><?php if ($asset['is_series']) { echo $episode_count . " tập. "; }?>Giá: <span class="highlight"><?php echo CHtml::encode(sprintf("%dđ", $asset['price']));?></span></h4>
					<ul class="info-des">
						<li class="icon-eye fn-number">&nbsp;<?php echo $asset['view_count'];?></li>
						<li class="icon-thumbs-up fn-number">&nbsp;<?php echo $asset['like_count'];?></li>
					</ul>
					<div style="margin-top:5px;">
						<a href="<?php echo $this->createUrl("/video/watch/id/" . $asset->id) ?>" class="button" style="">Xem phim</a>
<?php $info = CUtils::getDeviceInfo();
if ($info['os'] != 'ios') {?>
						<a href="#" onclick="javascript:void(0);" class="button">Tải về</a>
<?php } ?>
					</div>
				</div>
				<div class="content-items">
					<h3>Đăng ký</h3>
					<p style="color:black;">Quý khách chưa đăng ký gói cước nào của mobiphim. Hãy đăng ký một gói cước bên dưới để xem phim.</p>
					<p align="center"><a class="button" href="javascript:void(0);">PHIM</a> <a class="button" href="javascript:void(0);">PHIM7</a> <a class="button" href="javascript:void(0);">PHIM30</a></p>
				</div>
				<div class="lyric">
					<h3>Nội dung</h3>
					<p id="conLyrics" class="row-5"><?php echo CHtml::encode($asset['description']);?></p>
				</div>
				<a href="#" class="read-more fn-expand" expand-box="conLyrics" expand-class="row-5">Xem thêm <i class="icon-dropdown"></i></a>
				<div class="tabs">
					<ul class="tab-nav">
						<li><a href="#" class="active fn-tab" tab-content="suggestedBox,commentBox">Phim cùng thể loại</a></li>
						<li><a href="#" class="fn-tab" tab-content="commentBox,suggestedBox">Bình luận</a></li>
					</ul>
				
					<div id="suggestedBox" class="tab-content fn-autoload">
<?php foreach ($related as $ra) {
	$rPosterUrl = array_key_exists('poster_land', $ra) ? $ra['poster_land'] : null;
	$this->widget("application.widgets.AssetItem", array('asset' => $ra, 'posterUrl' => $rPosterUrl));
}
$cat = "/video/browse";
foreach($asset->vodCategories as $category) {
	$cat .= "/category/" . $category->id;
	break;
}
?>
						<div><?php echo CHtml::link("Xem thêm &raquo;", array($cat), array('class' => 'read-more'));?></div>
					</div>
					<div id="commentBox" class="tab-content comment none">
						<form class="frm-comment" name="frmComment" id="frmComment" onsubmit="return false;" action="">
							<textarea placeholder="Hãy nhập bình luận của bạn" name="txtComment" id="txtComment"></textarea>
							<input type="submit" class="button-grey btn-comment" value="Gửi">
							<input type="hidden" name="oid" value="ZW6OBCWU">
							<input type="hidden" name="type" value="song">
						</form>
						<div id="commentList">
<?php foreach ($comments as $comment) {
	$daysAgo = CUtils::timeElapsedStringFromMysql($comment->create_date);
?>
							<div class="comment-item">	
								<h3><?php echo $comment->subscriber->user_name ? CHtml::encode($comment->subscriber->user_name) : 'Khách'; ?></h3>
								<span class=""><?php echo CHtml::encode($daysAgo); ?></span>
								<p><?php echo CHtml::encode($comment->comment); ?></p>
							</div>
<?php
}?>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>
