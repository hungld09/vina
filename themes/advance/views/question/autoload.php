
<?php
for ($i = 0; $i < count($question); $i++){
    $CUtils = new CUtils();
    $time = $CUtils->formatTime($question[$i]['modify_date']);
    $question_id = $question[$i]['id'];
    $class_id = $question[$i]['class_id'];
    $category_id = $question[$i]['category_id'];
    $subcriber_id = $question[$i]['subscriber_id'];
    $class_name = Class1::model()->findByAttributes(array('id' => $class_id, 'status' => 1));
    $subjectCategory = SubjectCategory::model()->findByAttributes(array('id' => $category_id, 'status' => 1));
    $Subcriber = Subscriber::model()->findByPk($subcriber_id);
    $level = Level::model()->findByPk($question[$i]['level_id']);
    if($Subcriber['url_avatar'] != null){
        if($Subcriber['password'] == 'faccebook' || $Subcriber['password'] == 'Google'){
            $url_avatar = $Subcriber['url_avatar'];
        }else{
            $url_avatar = IPSERVER . $Subcriber['url_avatar'];
        }
    }else{
        $url_avatar = '';
    }
    $question[$i]['url_avatar'] = $url_avatar;
    if($question[$i]['url_avatar'] == ''){
        $avata = Yii::app()->theme->baseUrl .'/FileManager/avata.png';
    }else{
        $avata = $question[$i]['url_avatar'];
    }
?>
 <div class="web_body">  
 <div class="listarticle">  
 <div class="row ">  
 <div class="col-md-12"><div class="row">  
 <div class="col-md-6 col-xs-6 avata">  
 <a href="#"><img src="<?php echo $avata ?>" /></a>  
 <div class="name-title">  
 <a href="#"><?php echo $Subcriber['lastname'].' '.$Subcriber['firstname']; ?></a>  
 <p><?php echo $time ?></p>  
 </div>  
 </div>  
 <div class="col-md-6 col-xs-6 name-detail">  
<?php
if($user_id != -1){
    $checkSub = Subscriber::model()->findByPk($user_id);
    if($checkSub->type == 1){
?>
    <a href="<?php echo Yii::app()->baseUrl .'/question/upload'?>">Gửi câu hỏi</a>
<?php
    }else{
?>
    <a href="<?php echo Yii::app()->baseUrl .'/question/'. $question[$i]['id']?>">Gửi câu trả lời</a>
<?php        
    }
}else{
?>
    <a href="<?php echo Yii::app()->baseUrl .'/question/upload'?>">Gửi câu hỏi</a>
<?php
}
?>  
 <div class="subject-title">  
 <img src="<?php echo Yii::app()->theme->baseUrl .'/FileManager/subject.png' ?>" />  
 <span>Môn <?php echo $subjectCategory['subject_name'].', '.$class_name['class_name'] ?></span> 
 <span><a class="cl"><?php echo isset($level) ? $level->name : ''?></a></span>
 </div>  
 </div>  
 </div></div>  
 <div class="col-md-12">  
 <div class="article-title">  
 <p style="word-wrap:break-word;"><?php echo $question[$i]['title'] ?></p>  
 </div>  
 <div class="articleitem_body">
    <?php
       if($status == 6){
    ?>
        <a class="ava" href="<?php echo Yii::app()->baseUrl.'/question/fail?id='.$question[$i]['id'] ?>"><img src="<?php echo IPSERVER.$question[$i]['base_url'] ?>" title="<?php echo $question[$i]['title']?>" alt="<?php echo $question[$i]['title'] ?>" /></a>
    <?php
       }else{
    ?>
        <a class="ava" href="<?php echo Yii::app()->baseUrl.'/question/'.$question[$i]['id'] ?>"><img src="<?php echo IPSERVER.$question[$i]['base_url'] ?>" title="<?php echo $question[$i]['title']?>" alt="<?php echo $question[$i]['title'] ?>" /></a>
    <?php }?>
 </div>  
 </div>  
 </div>  
 </div>  
 </div>  
<?php
}
?>
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
    <?php if(isset(Yii::app()->session['tab_item']) && Yii::app()->session['tab_item'] == 1){?>
        <a href="#" class="loadMoreComment1" tab_item="1" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
    <?php }else if(isset(Yii::app()->session['tab_item']) && Yii::app()->session['tab_item'] == 2){?>
        <a href="#" class="loadMoreComment2" tab_item="2" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
    <?php }else if(isset(Yii::app()->session['tab_item']) && Yii::app()->session['tab_item'] == 3){?>
        <a href="#" class="loadMoreComment3" tab_item="3" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
    <?php } else if(isset(Yii::app()->session['tab_item']) && Yii::app()->session['tab_item'] == 4){?>
        <a href="#" class="loadMoreComment4" tab_item="4" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
    <?php }else if(isset(Yii::app()->session['tab_item']) && Yii::app()->session['tab_item'] ==5){?>
        <a href="#" class="loadMoreComment5" tab_item="5" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
    <?php }else{?>
        <?php
           if($this->userName->type == 1){
        ?>
            <a href="#" class="loadMoreComment1" tab_item="1" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
        <?php }else{?>
            <a href="#" class="loadMoreComment3" tab_item="3" number = "<?php echo Yii::app()->session['page'] + 1?>" >Xem thêm</a>
        <?php }?>
    <?php }?>
</div>
<div class="loadItem" style=""><img src="<?php echo Yii::app()->theme->baseUrl .'/img/ajax-loader.gif'?>" /></div>
<script>
    $('.loadMoreComment1, .loadMoreComment2, .loadMoreComment3, .loadMoreComment4, .loadMoreComment5').click(function(){ 
        var number = parseInt($(this).attr('number'));
        var tab_item = $(this).attr('tab_item');
        var total = number;
        loadItem(total,10,tab_item);
    });
    function loadItem(page,page_size,tab_item){ 
        var uid = <?php echo $user_id ?>;
        var page = page;
        var page_size = page_size;
        var tab_item = tab_item;
        showLoadItem();
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/loadItem'?>",
            data: {'uid':uid,'tab_item':tab_item,'page':page, 'page_size':page_size},
            dataType:'html',
            success: function(html){
                hideLoadItem();
                $('.list-group-item-answer').html(html);
            }
        });
    }
    
    function showLoadItem(){
        $('.loadItem').show();
    }
    function hideLoadItem(){
        $('.loadItem').hide();
    }
</script>

