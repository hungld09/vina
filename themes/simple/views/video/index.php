<?php
/** @var $asset VodAsset */
/** @var $this VideoController */
$this->pageTitle = "mobiphim - " . $asset->display_name;
$episode_count = count($asset->vodEpisodes);
?>
<script type="text/javascript">
function validateGiftNumber(price) {
	if (document.gift_form.received_number.value.length == 0) {
		alert("Số điện thoại không được để trống");
	} else {
		if (document.gift_form.received_number.value.match(/^\d+/)) {
			if (document.gift_form.received_number.value.match(/^(84|0)(90|93|120|121|122|126|128)\d{7}$/)) {
<?php if ($canSendGift) {?>
				return true;
<?php } else {?>
				return confirm("Quý khách đã chọn tặng phim với giá "+price+" VNĐ. Đồng ý?");
<?php }?>
			} else {
				alert("Số điện thoại không hợp lệ, vui lòng chọn thuê bao của Vinaphone và thử lại");
			}
		} else {
			alert("Số điện thoại không hợp lệ, vui lòng nhập lại");
		}
	}
	return false;
}
function confirmPurchase(price, url) {
	if (confirm("Quý khách đã chọn mua nội dung với giá "+price+" VNĐ. Thời gian sử dụng 24 giờ. Đồng ý?")) {
		location.href=url;
	}
}
</script>
<div class="divider header-bg"><h3 class="dtitle">THÔNG TIN PHIM</h3></div>
<div class="content">
	<div class="content-items">
		<div class="emborder"><img src="<?php echo $posterLandUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /></div>
		<h3><?php echo CHtml::encode($asset['display_name']);?></h3>
		<h4><?php if ($asset['is_series']) { echo $episode_count . " tập. "; }?></h4>
		<!--<p class="info-des" style="margin-left:137px;">
			<?php echo $asset['view_count'];?> lượt xem, <?php echo $asset['like_count'];?> thích
		</p>-->
		<p style="margin-left:137px;">
			<a style="padding:10px 0" href="<?php if ($this->msisdn == '') { echo "javascript:void(0);"; } else if (!$canWatch) { echo "javascript:confirmPurchase(" . intval($asset->price).", '$watchUrl')"; } else { echo $watchUrl; }?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo $watchPrice;?>)<br/>
<?php
	$info = CUtils::getDeviceInfo();
	if ($info['os'] != 'ios' && !$asset['is_series']) {
?>
			<a style="padding:10px 0" href="<?php if ($this->msisdn == '') { echo "javascript:void(0);"; } else if (!$canDownload) { echo "javascript:confirmPurchase(".intval($asset->price_download).",'$downloadUrl')"; } else { echo $downloadUrl;}?>"><img src="<?php echo Yii::app()->baseUrl?>/images/download3.png" /> Tải</a> (<?php echo $downloadPrice;?>)<br/>
<?php };?>
		</p>
		<form method="post" onsubmit="return validateGiftNumber(<?php echo intval($asset->price_gift);?>);" action="<?php echo $giftUrl?>" name="gift_form" id="gift_form">
		<div style="margin-left:137px;padding:5px 0;">
			<p><span class="highlight"><img src="<?php echo Yii::app()->baseUrl?>/images/gift1.png" /> Tặng</span> (<?php echo $giftPrice;?>)</p>
			&nbsp;<img src="<?php echo Yii::app()->baseUrl?>/images/phone.png" alt="Số ĐT"/> <input type="text" value="" name="received_number" id="received_number" style="width:80px" />&nbsp;<input type="submit" value="Tặng" class="button" name="submit" <?php if ($this->msisdn == '') { echo "disabled=\"disabled\""; }?>>
		</div>
		</form>
	</div>
	<div class="clear_fix" style="border-bottom: 1px solid #eee; padding-bottom: 5px;"></div>
</div>

<?php // khuyen cao dang ky goi cuoc
	if ($this->msisdn != '' && count($this->usingServices) == 0) {
		$i = intval(rand(0, count($this->services) - 1));
		$s = $this->services[$i];
?>
<div class="content" style="background:#eda02f; padding: 10px;">
	<div class="content-items">
		<a style="color:#fff;" href="<?php echo Yii::app()->baseUrl . "/account/subscribe/service/" . $s->id;?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" alt="Đăng ký" /> Đăng ký xem miễn phí toàn bộ phim (<?php echo /*CHtml::encode($s->display_name) . ", " .*/ intval($s->price) . "đ/" . $s->using_days . "ngày";?>)</a>
	</div>
</div>
<?php
	}?>
<div class="content">
	<div class="content-items">
		<p><span style="font-weight: bold;">Diễn viên:</span> <?php echo (isset($asset['actors']) && $asset['actors'] != '') ? CHtml::encode($asset['actors']) : "Đang cập nhật"?></p>
		<p><span style="font-weight: bold;">Đạo diễn:</span> <?php echo (isset($asset['director']) && $asset['director'] != '') ? CHtml::encode($asset['director']) : "Đang cập nhật"?></p>
		<p><span style="font-weight: bold;">Nội dung:</span></p>
		<p id="conLyrics" class="row-5"><?php echo CHtml::encode($asset['description']);?></p>
	</div>
</div>
<div class="divider header-bg"><h3 class="dtitle">PHIM CÙNG THỂ LOẠI</h3></div>
<?php
if (count($related) > 0) {
	foreach ($related as $i => $asset):
		$posterUrl = array_key_exists('poster_land', $asset) ? $asset['poster_land'] : null;
?>
<div class="content">
<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div style="padding:10px;">
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:130px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><img src="<?php echo Yii::app()->baseUrl?>/images/play2.png" /> Xem</a> (<?php echo count($this->usingServices) > 0 || $asset['is_free'] == 1 ? 'miễn phí' : intval($asset['price']) . 'đ';?>)</p>
	</div>
</div>
<div class="clear_fix"></div>
</div>
<?php
	endforeach;
} else {
	echo "<p>Không tìm thấy phim cùng thể loại</p>";
}?>
