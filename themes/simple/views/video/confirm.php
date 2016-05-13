<?php
/** @var $this VideoController */
$this->pageTitle = "mobiphim - Xác nhận thanh toán";
?>
<div class="content">
	<div class="content-items">
		<p>Quý khách chọn mua nội dung <b><?php echo CHtml::encode($asset->display_name);?></b> với giá <b><?php echo intval($price);?> VNĐ</b></p>
		<a href="<?php echo $nextUrl?>">Đồng ý</a>
	</div>
</div>