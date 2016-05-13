<?php
/** @var $this DefaultController */
$this->pageTitle = "mobiphim - Đăng ký";
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
	<div class="home" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container" id="fnBody">
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('noAccountInfo' => true)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
				
				<div class="divider"><h3 class="dtitle">Thông tin tài khoản</h3></div>
<?php
if ($this->msisdn != '') { ?>
				<div class="tab-content fnRTAccountInfo" id="fnRTAccountInfo">
				
<?php if ($responseToUser != '') { echo '<div class="content-items" style="color:#000;">' . $responseToUser . '</div>'; } ?>

					<div class="content-items">
						<p style="color:#000">Xin chào thuê bao <b><?php echo $this->msisdn?></b></p>
<?php if ($this->subscriber == null || count($usingServices) == 0) {?>
						<p style="color:#000">Quý khách chưa đăng ký sử dụng dịch vụ. Quý khách có thể đăng ký bằng cách chọn một gói cước bên dưới.</p>
					</div>
				</div>
				<div class="divider"><h3 class="dtitle">Đăng ký dịch vụ</h3></div>
				<div class="lyric" style="margin-bottom:10px;">
					<table>
						<thead>
							<tr><th>Gói cước<th>Mô tả<th>Giá tiền<th>&nbsp;</tr>
						</thead>
						<tbody>
<?php		foreach ($services as $s) {?>
							<tr><td><?php echo CHtml::encode($s->display_name);?></td><td><?php echo CHtml::encode($s->description);?></td><td><?php echo CHtml::encode(sprintf("%dđ/ %dngày", $s->price, $s->using_days));?></td><td><?php echo CHtml::link('Đăng ký', array('/account/subscribe?id=' . urlencode($this->crypt->encrypt($s->id))), array('class' => 'confirmation'));?></td></tr>
<?php 		}?>
						</tbody>
					</table>
				</div>
<?php 	} else {
			$service = $usingServices[0];
			$expdate = new DateTime($service->expiry_date);
			if ($service->isExpired()) {
				echo "<p style=\"color:#000\">Gói cước đang sử dụng: <b>".CHtml::encode($service->service->display_name)."</b>, đã hết hạn ngày: <span class=\"highlight\">".$expdate->format('d/m/Y h:i:s')."</span><br/></p>";
			} else {
				echo "<p style=\"color:#000\">Gói cước đang sử dụng: <b>".CHtml::encode($service->service->display_name)."</b>, ngày hết hạn: <b>".$expdate->format('d/m/Y h:i:s')."</b><br/></p>";
			}
		}
} else { ?>
				<div class="tab-content fnRTAccountInfo" id="fnRTAccountInfo">
					<div class="content-items">
						<p style="color: #000">Hệ thống không thể nhận diện được thuê bao của bạn. Xin vui lòng truy cập bằng 3G/EDGE của Vinaphone để sử dụng được dịch vụ.</p>
					</div>
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
		if (!confirm("Quý khách có chắc chắn muốn đăng ký gói cước?")) {
			e.preventDefault();
		}
	});
});
</script>
