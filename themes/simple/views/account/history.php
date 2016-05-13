<?php
/** @var $this AccountController */
$this->pageTitle = "mobiphim - Tài khoản";
?>

<?php
if ($this->msisdn != '' && isset($this->subscriber)):?>
<div class="divider"><h3 class="dtitle">Tra cứu giao dịch</h3></div>
<div class="content">
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
		<table border="1" cellspacing="1" cellpadding="2">
				<tr><th>Giao dịch</th><th>Nội dung</th><th>Thời gian</th><th>Giá cước</th></tr>

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

			</table>
			<br/>
					<?php if ($records > 0) { $this->widget("application.widgets.Pager", array('pager' => $pager, 'baseUrl' => Yii::app()->request->url, 'delimiter' => '=')); }?>
	</div>
</div>
<?php endif?>
