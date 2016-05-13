<?php if(count($subject) > 0){ ?>
    <?php foreach($subject as $i => $item){
        $total = NotifiQuestion::model()->findByAttributes(array(
            'class_id' => $class_id,
            'subject_id' => $item['id']
        ));
        ?>
        <div class="web_body">
            <li class="clas"><a href="<?php echo Yii::app()->baseUrl.'/question/questionSubject?id='.$item['id'] ?>"><div style="width: 100%; height: 100%"><?php echo $item['subject_name']; ?></div></a><span style="float: right; color: red;margin-top: -15px;"><?php echo $total['count']; ?></span></li>
        </div>
    <?php } ?>
<?php }else{ ?>
    <div class="web_body">
        <li class="clas">Không có câu hỏi nào chưa có câu trả lời!</li>
    </div>
<?php } ?>
