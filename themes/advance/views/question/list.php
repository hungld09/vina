
<?php
if ($this->msisdn != '') {
    $user_id = $this->id;
    ?>
    <?php
} else {
    $user_id = -1;
}
?>
<div class="breadcrumbs">
    <ul>
        <li><a href="javascript:;">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><span>Câu hỏi</span></li>
    </ul>
</div>
<?php
if (count($questions) > 0) {
    ?>
    <?php
    $CUtils = new CUtils();
    foreach ($questions as $s => $question):
        $time = $CUtils->formatTime($questions[$s]['create_date']);
        ?>
        <div class="box-ques">
            <div class="item-ques">
                <div class="item-top">
                    <div class="tel">
                        <p class="num"><?php echo $questions[$s]['subscriber_name'] ?></p>
                        <?php echo $time ?>
                    </div>
                    <div class="f">
                        <img src="<?php echo Yii::app()->theme->baseUrl . '/images/f.png'?>" alt="" />
                    </div>
                </div>
                <div class="calculator">
                    <p>
                        <img alt="" src="<?php echo Yii::app()->theme->baseUrl . '/images/can.png'?>">
                        <?php echo 'Môn ' . $questions[$s]['subject_name'] ?>, <?php echo $questions[$s]['class_name'] ?>
                    </p>
                    <p class="help">
                        <?php echo $questions[$s]['title'] ?>
                    </p>
                    <p>
                        <a class="ava" href="<?php echo Yii::app()->baseUrl . '/question/' . $questions[$s]['id'] ?>"><img src="<?php echo IPSERVER . $questions[$s]['base_url'] ?>" title="" alt="" /></a>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

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
        <a href="<?php echo Yii::app()->baseUrl . '/question/list?page=' ?><?php
        if (isset($_GET['page'])) {
            echo $_GET['page'] + 1;
        } else {
            echo 1;
        }
        ?>">Xem thêm</a>
    </div>
<?php } else { ?>
    <div class="web_body">
        <div class="listarticle">
            <div class="row">
                <div class="col-md-12"><div class="row">
                        <div class="col-md-6 col-xs-6 avata">
                            Không có kết quả
                        </div>
                    </div></div>
            </div>
        </div>
    </div>
<?php } ?>