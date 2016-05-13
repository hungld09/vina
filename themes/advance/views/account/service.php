<div class="web_body container">
    <div class=" col-md-12 col-xs-12 col-lg-12" style="margin-bottom: 15px;">
        <div class="col-md-6 col-xs-6 col-lg-6 tab-card">
            <a href="<?php echo Yii::app()->baseUrl .'/account/service'?>">Nạp thẻ</a>
        </div>
        <div class="col-md-6 col-xs-6 col-lg-6 tab-historyService ">
             <a href="<?php echo Yii::app()->baseUrl .'/account/historyService'?>">Lịch sử mua gói</a>
        </div>
    </div>
</div>
<?php 
if(isset($responseMessage)){
?>
<div class="web_body">
    <div class="message">
        <?php  echo $responseMessage;?>
    </div>
</div>
<?php } ?>
<?php
    if(count($usingService) > 0){
        foreach ($usingService as $usingService):
        $services = Service::model()->findByPk($usingService['service_id']);
?>
<div class="web_body">
    <div class="" style="background: url(<?php echo Yii::app()->theme->baseUrl . '/img/service1.png'?>) no-repeat; background-size: 50% 100%">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-xs-3">
                <img src="<?php // echo Yii::app()->theme->baseUrl . '/img/service.png'?>" />
            </div>
            <div class="col-lg-9 col-md-9 col-xs-9 content-service">
                <p style="font-size: 20px;"><a class="#" href="#">Bạn đang sử dụng gói <span><?php echo $services['using_days']?></span> ngày</a></p>            
                <p style="font-size: 11px;">Ngày hêt hạn <span><?php echo date('d/m/Y', strtotime($usingService['expiry_date']))?></span></p>
                <p style="float: right; color: #f00"> <?php echo $services['price'] . ' ONCASH'?></p><br>
                <p style="float: right; color: #f00"> <a href="<?php echo Yii::app()->baseUrl ?>/account/cancelService?id=<?php echo base64_encode($services['id']); ?>">Hủy gói cước</a></p>
            </div>
        </div>
    </div>
</div>
    <?php
            endforeach;
        }else{
    ?>
<?php 
    $services = Service::model()->findAllByAttributes(array('status'=>1));
    foreach ($services as $service):
        $coin = $service['price'];
?>
<div class="web_body">
    <div class="" style="background: url(<?php echo Yii::app()->theme->baseUrl . '/img/service.png'?>) no-repeat; background-size: 50% 100%">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-xs-3">
                <img src="<?php // echo Yii::app()->theme->baseUrl . '/img/service.png'?>" />
            </div>
            <div class="col-lg-9 col-md-9 col-xs-9 content-service">
                <p style="font-size: 20px;"><a class="confirmRegister" href="<?php echo Yii::app()->baseUrl ?>/account/registerService?id=<?php echo base64_encode($service->id); ?>">Gói <span><?php echo $service['using_days']?></span> ngày</a></p>
                <p style="font-size: 11px;">Mỗi ngày <span>Miễn phí 3</span> câu hỏi</p>
                <p style="font-size: 11px;">Quá 3 câu tính <span>50 ONCASH</span>/câu</p>
                <p style="float: right; color: #f00"> <?php echo $coin . ' ONCASH'?></p>
            </div>
        </div>
    </div>
</div>
<?php endforeach;?>
<?php }?>
<style>
    .content-service p{
    font-weight: bold;
    /*padding: 1px 0px;*/
    line-height: 20px;
    }
    .content-service span{color: #f00}
</style>
<script>
    $('.confirmRegister').click(function(){
        if(confirm("Bạn chắc chắn muốn mua gói cước")){
            var href = $(this).attr('href');
            window.location.href = href;
        }
        return false;
    });
</script>

<style>
    .web_body td{border: 1px solid #ccc; text-align: center;padding: 5px; font-size: 15px;}
    .activeHistory{background: #55CAF5;}
</style>