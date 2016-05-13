<?php
/** @var $this AccountController */
$this->pageTitle = "mobiphim - Tài khoản";
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
function confirmCancel(url) {
	if (confirm("Ngay sau khi huỷ thì gói cước của quý khách sẽ là gói mặc định. Quý khách có đồng ý không?")) {
		location.href=url;
	}
}
</script>
	<div class="home" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox", array('searchUrl' => "#"));?>
		<div class="container" id="fnBody">
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('noAccountInfo' => true, 'msisdn' => $this->msisdn, 'subscriber' => $this->subscriber)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
				
				<div class="divider"><h3 class="dtitle">Thông tin tài khoản</h3></div>
<?php if ($this->msisdn != '') { ?>
				<div class="tab-content fnRTAccountInfo" id="fnRTAccountInfo">

					<div class="content-items" style="color:#000;">
						<p>Xin chào thuê bao <b><?php echo $this->msisdn?></b></p>
<?php
		if ($this->subscriber == null || count($usingServices) == 0) {
?>
						<p>Quý khách chưa đăng ký gói cước nào của mobiphim. Hãy đăng ký một gói cước bên dưới để thưởng thức các bộ phim hay hoàn toàn MIỄN PHÍ</p>
<?php
		} else {
			$service = $usingServices[0];
			$expdate = new DateTime($service->expiry_date);
			?>

						<p>Quý khách đang sử dụng gói cước <span class="highlight"><?php echo CHtml::encode($service->service->display_name);?></span>: <?php echo intval($service->service->price) . "đ/" . $service->service->using_days . "ngày";?>, hết hạn: <span class="highlight"><?php echo $expdate->format("d/m/Y H:i:s") ?></span></p>
						<p>Gói cước sẽ được tự động gia hạn ngay sau khi hết thời gian sử dụng</p>
						<p>- Miễn phí cước data (GPRS/3G) khi sử dụng dịch vụ</p>
						<p>- Miễn phí xem phim.</p><br/>
						<p>Để hủy gói cước kích <a href="javascript:confirmCancel('<?php echo Yii::app()->baseUrl . "/account/cancel";?>');">vào đây</a></p>

			<?php
		}
?>
						
					</div>
<?php if ($this->subscriber == null || count($usingServices) == 0) {?>
					<div class="lyric" style="margin-bottom:10px;">
					<table>
						<thead>
							<tr><th>Gói cước<th>Mô tả<th>Giá tiền<th>&nbsp;</tr>
						</thead>
						<tbody>
<?php		foreach ($this->services as $s) {?>
							<tr><td><?php echo CHtml::encode($s->display_name);?></td><td><?php echo CHtml::encode($s->description);?></td><td><?php echo CHtml::encode(sprintf("%dđ/ %dngày", $s->price, $s->using_days));?></td><td><?php echo CHtml::link('Đăng ký', array('/account/subscribe?id=' . urlencode($this->crypt->encrypt($s->id))), array('class' => 'confirmation'));?></td></tr>
<?php 		}?>
						</tbody>
					</table>
					</div>
<?php }?>
				</div>
				<div class="divider"><h3 class="dtitle">Tra cứu giao dịch</h3></div>
				<div class="tab-content" id="fnRTAccountHistory">
					<div class="content-items"><?php echo CHtml::link('<span class="icon-search"> </span>XEM CHI TIẾT', array("account/history"), array('style' => 'color:#fff;text-shadow:none;display:block;background:#4a8019;padding:10px;'));?></div>
				</div>
<?php } else { ?>
				<div class="tab-content fnRTAccountInfo" id="fnRTAccountInfo">
					<div class="content-items">
						<p style="color: #000">Hệ thống không thể nhận diện được thuê bao của bạn. Xin vui lòng truy cập bằng 3G/EDGE của Vinaphone để sử dụng được dịch vụ.</p>
					</div>
				</div>
<?php }?>
			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>
