<div class="web_body">
    <div class="row col-md-12 col-xs-12 col-lg-12"  style="margin-bottom: 15px;">
        <div class="col-md-6 col-xs-6 col-lg-6 tab-card <?php if($action == 'service'){ echo 'activeHistory' ;}?>">
            <a href="<?php echo Yii::app()->baseUrl .'/account/service'?>">Nạp thẻ</a>
        </div>
        <div class="col-md-6 col-xs-6 col-lg-6 tab-historyService <?php if($action == 'historyService'){ echo 'activeHistory' ;}?>">
             <a href="<?php echo Yii::app()->baseUrl .'/account/historyService'?>">Lịch sử mua gói</a>
        </div>
<!--        <div class="col-md-4 col-xs-4 col-lg-4 tab-historyCard <?php // if($action == 'historyCard'){ echo 'activeHistory' ;}?>">
             <a href="<?php // echo Yii::app()->baseUrl .'/account/historyCard'?>">Lịch sử nạp thẻ</a>
        </div>-->
    </div>
    <?php
        if(count($result) > 0){
    ?>
    <table style="width: 100%;">
        <tr style=" background: #55CAF5; color: #fff;">
            <td>Loại thẻ</td>
            <td>Trạng thái</td>
            <td>Mệnh giá</td>
            <td>kiểu</td>
            <td>Ngày</td>
        </tr>
        <?php
            foreach ($result as $result):
                $name= '';
                switch ($result['service_id']){
                    case 1: $name = 'Gói ngày';                    break;
                    case 2: $name = 'Gói tuần';                    break;
                    case 3: $name = 'Gói tháng';                    break;
                    case 4: $name = 'Gói quý';                    break;
                }
                switch ($result['purchase_type']){
                    case 1: $purchase = 'Đăng ký';                    break;
                    case 3: $purchase = 'Hủy';                    break;
                }
                if($result['status'] == 1){$status = 'Thành công'; }else{ $status = 'Thất bại';}
        ?>
            <tr>
                <td><?php echo $name?></td>
                <td><?php echo $status ?></td>
                <td><?php echo $result['cost']?></td>
                <td><?php echo $purchase?></td>
                <td><?php echo $result['create_date']?></td>
            </tr>
        <?php endforeach;?>
    </table>
    <?php }else{?>
    <p>Chưa cập nhật</p>
    <?php }?>
</div>
<div style="width: 80%;
    height: 40px;
    text-align: center;
    margin: auto;
    background-color: #B3B3B3;
    line-height: 40px;
    border-radius: 5px;
    margin-top: 10px;
    margin-bottom: 15px;
    ">
    <a href="<?php echo Yii::app()->baseUrl .'/account/historyService?page='?><?php if(isset($_GET['page'])){ echo $_GET['page'] + 1; }else{ echo 1; } ?>">Xem thêm</a>
</div>
<style>
    .web_body td{border: 1px solid #ccc; text-align: center;padding: 5px; font-size: 15px;}
    .activeHistory{background: #55CAF5;}
</style>