<?php
/** @var $this AccountController */
$this->pageTitle = "mobiphim - Chi tiết giao dịch";
?>
<style type="text/css">
table {
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
}
caption { font-size: larger; margin: 1em auto; }
th, td { padding: .75em; }
td { color: #000; font-weight: normal; }
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
		<?php $this->widget("application.widgets.SearchBox", array('searchUrl' => "#"));?>
		<div class="container" id="fnBody">
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('msisdn' => $this->msisdn, 'subscriber' => $this->subscriber, 'usingServices' => $this->usingServices)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
				
				
				<div class="divider"><h3 class="dtitle">Tra cứu giao dịch</h3></div>
				<div class="lyric" style="margin-bottom:10px;">
					<form method="get" action="" name="transactionSearch" id="transactionSearch">
						<div style="margin: 3px;">
							<div style="float: left; width: 80px; color:black;"><label>Từ ngày</label></div>
							<div style="margin-left:90px;"><input type="text" value="<?php echo $from_date;?>" style="max-width: 160px;" name="from_date"/></div>
						</div>
						<div style="margin: 3px;">
							<div style="float: left; width: 80px; color:black;"><label>Đến ngày</label></div>
							<div style="margin-left:90px;"><input type="text" value="<?php echo $to_date;?>" style="max-width: 160px;" name="to_date"/></div>
						</div>
						<div style="margin: 5px 10px 5px 90px;"><input class="button" type="submit" value="Xem" /></div>
					</form>
					<table>
						<thead>
							<tr><th>Giao dịch</th><th>Nội dung</th><th>Thời gian</th><th>Giá cước</th></tr>
						</thead>
						<tbody>
<?php
$records = count($transaction);
if ($records > 0) {
	$pt = array("", "Đăng ký", "GIA HẠN", "HỦY", "BỊ HỦY");
	foreach($transaction as $t) {
		$type = isset($pt[$t['purchase_type']]) ? $pt[$t['purchase_type']] : "";
		if ($t['using_type'] > USING_TYPE_REGISTER && $t['purchase_type'] == PURCHASE_TYPE_NEW)
			$type = "Mua";
		$tDate = DateTime::createFromFormat('Y-m-d H:i:s', $t['create_date']);
?>
							<tr><td><?php echo CHtml::encode($type);?></td><td><?php echo CHtml::encode($t['description']);?></td><td><?php echo CHtml::encode($tDate->format('d/m/Y H:i:s'));?></td><td><?php echo intval($t['cost']).'đ';?></td></tr>
<?php
	}
} else {
?>
							<tr><td colspan="4">Không tìm thấy giao dịch nào.</td></tr>
<?php
}
?>
						</tbody>
					</table>
					<br/>
					<?php if ($records > 0) { $this->widget("application.widgets.Pager", array('pager' => $pager, 'baseUrl' => Yii::app()->request->url, 'delimiter' => '=')); }?>
				</div>

			</div>
		</div>
<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>
