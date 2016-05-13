<?php
/** @var $this AccountController */
$this->pageTitle = "mobiphim - Tài khoản";
?>
<script type="text/javascript">
function confirmCancel(url) {
	if (confirm("Ngay sau khi huỷ thì gói cước của quý khách sẽ là gói mặc định. Quý khách có đồng ý không?")) {
		location.href=url;
	}
}
</script>
<div class="divider"><h3 class="dtitle">Thông tin tài khoản</h3></div>
<?php
if (count($this->usingServices) > 0):
	$ss = $this->usingServices[0];
	$eDate = new DateTime($ss->expiry_date);
?>
<div class="content">
	<div class="content-items" style="font-weight:normal;">
		<p>Quý khách đang sử dụng gói cước <span class="highlight"><?php echo CHtml::encode($ss->service->display_name);?></span>: <?php echo intval($ss->service->price) . "đ/" . $ss->service->using_days . "ngày";?>, hết hạn: <span class="highlight"><?php echo $eDate->format("d/m/Y H:i:s") ?></span></p>
		<p>Gói cước sẽ được tự động gia hạn ngay sau khi hết thời gian sử dụng</p>
		<p>- Miễn phí cước data (GPRS/3G) khi sử dụng dịch vụ</p>
		<p>- Miễn phí xem phim.</p><br/>
		<p>Để hủy gói cước kích <a href="javascript:confirmCancel('<?php echo Yii::app()->baseUrl . "/account/cancel";?>');">vào đây</a></p>
	</div>
</div>
<?php
else:
?>
<div class="content">
	<div class="content-items" style="font-weight:normal;">
		<p>Quý khách chưa đăng ký gói cước nào của mobiphim. Hãy đăng ký một gói cước bên dưới để thưởng thức các bộ phim hay hoàn toàn MIỄN PHÍ</p>
	</div>
	<div class="content-items" style="margin-bottom:10px;">
		<table border="1" cellspacing="1" cellpadding="2">
			<tr><th>Gói cước</th><th>Mô tả</th><th>Giá tiền<th></tr>
<?php		foreach ($this->services as $s) {?>
			<tr><td><?php echo CHtml::encode($s->display_name);?></td><td><?php echo CHtml::encode($s->description);?></td><td><?php echo CHtml::encode(sprintf("%dđ/ %dngày", $s->price, $s->using_days));?></td><td><?php echo CHtml::link('Đăng ký', array('/account/subscribe?id=' . urlencode($this->crypt->encrypt($s->id))), array('class' => 'confirmation'));?></td></tr>
<?php 		}?>
		</table>
	</div>
</div>
<?php
endif;?>

<?php
if ($this->msisdn != '' && isset($this->subscriber)):?>
<div class="divider"><h3 class="dtitle">Tra cứu giao dịch</h3></div>
<div class="content">
	<div class="content-items" style="margin:10px;">
		<?php echo CHtml::link('XEM CHI TIẾT', array("account/history"), array('style' => 'color:#fff;text-shadow:none;display:block;background:#4a8019;padding:10px;')); ?>
	</div>
</div>
<?php endif?>
