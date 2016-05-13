<?php
/**
 * @var $this AccountController
 */
$this->pageTitle = "mobiphim - Tặng gói cước";
?>
<script type="text/javascript">
function validateGiftForm() {
	if (document.gift_form.gift_price.value.length == 0) {
		alert("Xin vui lòng chọn gói cước cần tặng");
		return false;
	}
	if (document.gift_form.gift_number.value.length == 0) {
		alert("Số điện thoại không được để trống");
	} else {
		if (document.gift_form.gift_number.value.match(/^\d+/)) {
			if (document.gift_form.gift_number.value.match(/^(84|0)(90|93|120|121|122|126|128)\d{7}$/)) {
				return confirm("Quý khách đã chọn tặng gói cước với giá "+document.gift_form.gift_price.value+" VNĐ. Đồng ý?");
			} else {
				alert("Số điện thoại không hợp lệ, vui lòng chọn thuê bao của Vinaphone và thử lại");
			}
		} else {
			alert("Số điện thoại không hợp lệ, vui lòng nhập lại");
		}
	}
	return false;
}
function setPrice(price) {
	document.gift_form.gift_price.value = price;
}
</script>
	<div class="home" id="fnPage">
		<?php $this->widget("application.widgets.SearchBox");?>
		<div class="container" id="fnBody">
			<div class="content" id="fnBodyContent">
<?php $this->widget("application.widgets.Header", array('noAccountInfo' => true)); ?>
				<div class="loading none" id="fnLoading"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/loading.gif" />Loading...</div>
			</div>
			
<div class="divider"><h3 class="dtitle">TẶNG GÓI CƯỚC</h3></div>
<div class="content">
	<div class="content-items" style="margin-bottom:10px; color:black;">
		<form name="gift_form" id="gift_form" method="POST" action="<?php echo $this->createUrl("/account/gift"); ?>" onsubmit="return validateGiftForm();">
			<p>Chọn gói cước cần tặng:</p><input type="hidden" value="" name="gift_price" id="gift_price" />
<?php
foreach ($services as $service) {
?>
			<input onclick="setPrice(<?php echo intval($service->price);?>)" type="radio" name="service_id" value="<?php echo $service->id;?>" id="service_id_<?php echo $service->id;?>" />&nbsp;<label for="service_id_<?php echo $service->id;?>"><?php echo CHtml::encode($service->display_name);?></label>&nbsp;
<?php
}
?>
			<p>Nhập số thuê bao Vinaphone cần tặng:</p>
			<img src="<?php echo $this->createUrl("/images/phone.png");?>" alt="phone">
			<input type="text" name="gift_number" id="gift_number" value="" />
			<input type="submit" name="submit" value="Tặng" class="button" />
		</form>
	</div>
</div>

		</div>
		<?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
	</div>