<?php
/** @var $asset VodAsset */
/** @var $this VideoController */
$this->pageTitle = "mobiphim - " . $asset->display_name;
$episode_count = count($asset->vodEpisodes);
$hasService = count($this->usingServices) > 0 ? true : false;
?>
<style type="text/css">
table {
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
}
caption { font-size: larger; margin: 1em auto; }
th, td { padding: .75em; }
th {
	background: -webkit-linear-gradient(#3f3f3f,#777);
	background: -moz-linear-gradient(#3f3f3f,#777);
	background: -o-linear-gradient(#3f3f3f,#777);
	background: -ms-linear-gradient(#3f3f3f,#777);
	background: linear-gradient(#3f3f3f,#777);
	color: #fff;
}
th:first-child { border-radius: 9px 0 0 0; }
th:last-child { border-radius: 0 9px 0 0; }
tr:last-child td:first-child { border-radius: 0 0 0 9px; }
tr:last-child td:last-child { border-radius: 0 0 9px 0; }
tr:nth-child(odd) { background: #dedede; }
tr:nth-child(even) { background: #f0f0f0; }
</style>
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
	<div class="page" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container play" id="fnBody">
<?php $this->widget("application.widgets.Header", array('subscriber' => $this->subscriber, 'msisdn' => $this->msisdn, 'usingServices' => $this->usingServices)); ?>
			<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
			<div class="divider header-bg"><h3 class="dtitle">THÔNG TIN PHIM</h3></div>
			<div class="content" id="fnRTAsset">
				<div class="content-items">
					<div class="emborder"><img src="<?php echo $posterLandUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /></div>
					<h3><?php echo CHtml::encode($asset['display_name']);?></h3>
					<h4><?php if ($asset['is_series']) { echo $episode_count . " tập. "; }?></h4>
					<!--<ul class="info-des">
						<li class="icon-eye fn-number">&nbsp;<?php echo $asset['view_count'];?></li>
						<li class="icon-thumbs-up fn-number">&nbsp;<?php echo $asset['like_count'];?></li>
					</ul>-->
					<p style="margin-left:137px;">
						<a style="padding:5px 0" href="<?php if ($this->msisdn == '') { echo "javascript:void(0);"; } else if (!$canWatch) { echo "javascript:confirmPurchase(" . intval($asset->price).", '$watchUrl')"; } else { echo $watchUrl; }?>"><span class="icon-film"> </span>Xem</a> (<?php echo $watchPrice;?>)<br/>
<?php $info = CUtils::getDeviceInfo();
	if ($info['os'] != 'ios' && !$asset['is_series']): ?>
						<a style="padding:5px 0" href="<?php if ($this->msisdn == '') { echo "javascript:void(0);"; } else if (!$canDownload) { echo "javascript:confirmPurchase(".intval($asset->price_download).",'$downloadUrl')"; } else { echo $downloadUrl;}?>"><span class="icon-download"> </span>Tải</a> (<?php echo $downloadPrice;?>)<br/>
<?php endif?>
					</p>
					<form method="post" onsubmit="return validateGiftNumber(<?php echo intval($asset->price_gift);?>);" action="<?php echo $giftUrl?>" name="gift_form" id="gift_form">
					<div style="margin-left:137px;padding:5px 0;">
						<p><span class="icon-gift highlight">&nbsp;Tặng</span> (<?php echo $giftPrice;?>)</p>
						<span class="icon-phone highlight"> </span><input type="text" value="" name="received_number" id="received_number" style="width:80px" />&nbsp;<input type="submit" value="Tặng" class="button" name="submit" <?php if ($this->msisdn == '') { echo "disabled=\"disabled\""; }?>>
					</div>
					</form>
				</div>
<?php // khuyen cao dang ky goi cuoc
	if ($this->msisdn != '' && count($this->usingServices) == 0) {
		$i = intval(rand(0, count($this->services) - 1));
		$s = $this->services[$i];
?>
				<div  style="background:#eda02f; padding: 10px;">
					<a style="color:#fff;" href="<?php echo Yii::app()->baseUrl . "/account/subscribe/service/" . $s->id;?>"><span class="icon-film" style="color:#c2181d;"> </span>Đăng ký xem miễn phí toàn bộ phim (<?php echo /*CHtml::encode($s->display_name) . ", " .*/ intval($s->price) . "đ/" . $s->using_days . "ngày";?>)</a>
				</div>
<?php
	}?>
	
				<div class="lyric">
					<p><span style="font-weight: bold;">Diễn viên:</span> <?php echo (isset($asset['actors']) && $asset['actors'] != '') ? CHtml::encode($asset['actors']) : "Đang cập nhật"?></p>
					<p><span style="font-weight: bold;">Đạo diễn:</span> <?php echo (isset($asset['director']) && $asset['director'] != '') ? CHtml::encode($asset['director']) : "Đang cập nhật"?></p>
					<p><span style="font-weight: bold;">Nội dung:</span></p>
					<p id="conLyrics" class="row-5"><?php echo CHtml::encode($asset['description']);?></p>
				</div>
				<a href="#" class="read-more fn-expand" expand-box="conLyrics" expand-class="row-5">Xem thêm <i class="icon-dropdown"></i></a>
			</div>
			<div class="divider header-bg"><h3 class="dtitle">PHIM CÙNG THỂ LOẠI</h3></div>
			<div class="content">
<?php
if (count($related) > 0) {
	foreach ($related as $i => $vod) {
		$rPosterUrl = array_key_exists('poster_land', $vod) ? $vod['poster_land'] : null;
		$isFree = $hasService || $vod['is_free'] == 1;
		$this->widget("application.widgets.AssetItem", array('asset' => $vod, 'posterUrl' => $rPosterUrl, 'isFree' => $isFree));
	}
} else {?>
					<div class="content-items">
						<p style="color:black;">Không tìm thấy phim liên quan</p>
					</div>
<?php
}?>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>
<script type="text/javascript">
$(document).ready(function() {
	$('.confirmation').click(function(e) {
		if (!confirm("Quý khách muốn đăng ký gói cước "+$(this).text()+", xin bấm OK để xác nhận?")) {
			e.preventDefault();
		}
	});
	$('#purchase_film, #purchase_download').click(function(e) {
		if (!confirm("Quý khách chọn mua nội dung lẻ, xin bấm OK để xác nhận?")) {
			e.preventDefault();
		}
	});
});
</script>
