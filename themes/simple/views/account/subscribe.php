<?php
/** @var $this DefaultController */
$this->pageTitle = "mobiphim - Đăng ký";
if ($subscribeOK == true) {
	Yii::app()->user->setFlash("responseToUser", $responseToUser);
	$this->redirect(array("/account"));
	exit (0);
}
?>
<?php if ($responseToUser):?>
<div class="content">
	<div class="content-items">
		<p><?php echo CHtml::encode($responseToUser);?></p>
	</div>
</div>
<?php endif?>
<?php if ($this->msisdn != '' && isset($this->subscriber)):?>
<div class="divider"><h3 class="dtitle">Đăng ký dịch vụ</h3></div>
	<div class="content">
		<div class="content-items" style="margin-bottom:10px;">
			<table border="1" cellspacing="1" cellpadding="2">
				<tr><th>Gói cước</th><th>Mô tả</th><th>Giá tiền<th></tr>
<?php		foreach ($services as $s) {?>
				<tr><td><?php echo CHtml::encode($s->display_name);?></td><td><?php echo CHtml::encode($s->description);?></td><td><?php echo CHtml::encode(sprintf("%dđ/ %dngày", $s->price, $s->using_days));?></td><td><?php echo CHtml::link('Đăng ký', array('/account/subscribe?id=' . urlencode($this->crypt->encrypt($s->id))), array('class' => 'confirmation'));?></td></tr>
<?php 		}?>
			</table>
		</div>
	</div>
<?php endif?>