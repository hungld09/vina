<ul style="margin: 0">
    <?php foreach($class as $i => $item){
            $query = 'select sum(count) as total from notifi_question where class_id = '.$item['id'] .' group by class_id';
            $total = NotifiQuestion::model()->findBySql($query);
        ?>
    <div class="web_body">
        <li class="clas"><a href="<?php echo Yii::app()->baseUrl.'/question/notifyQuestion?classId='.$item['id'] ?>"><div style="width: 100%; height: 100%"><?php echo $item['class_name']; ?></div></a><span style="float: right; color: red;margin-top: -15px;"><?php  if($total['total'] > 0) {echo $total['total']; }else{echo  '0' ;}?></span></li>
    </div>
    <?php } ?>
</ul>
<script>
    setTimeout(function(){
       window.location.reload();
    }, 120000);
</script>