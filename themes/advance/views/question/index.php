<?php
    $user_id = Yii::app()->session['user_id'];
?>

<div class="tabs">
    <ul>
        <?php if($this->userName->type == 1) {?>
            <li class="tabs-item" tab_item="1"><a href="#">Câu hỏi đã có câu trả lời</a></li>
            <li class="tabs-item" tab_item="2"><a href="#">Câu hỏi đang chờ xác nhận</a></li>
            <li class="tabs-item" tab_item="3"><a href="#">Câu hỏi chưa có câu trả lời</a></li>
        <?php }else{?>
            <li class="tabs-item" tab_item="2" style=" width: 33% !important;"><a href="#">Câu hỏi đang chờ xác nhận</a></li>
            <li class="tabs-item" tab_item="4" style=" width: 33% !important;"><a href="#">Câu trả lời đúng</a></li>
            <li class="tabs-item" tab_item="5" style=" width: 33% !important;"><a href="#">Câu trả lời sai</a></li>
        <?php } ?>
    </ul>
</div>
<div class ="list-group-item-answer"></div>

<!--<div class="loadItem" style=""><img src="<?php echo Yii::app()->theme->baseUrl .'/img/ajax-loader.gif'?>" /></div>-->
<script>
    $(document).ready(function(){
      loadItem(0,10);
    });

    function loadItem(page,page_size){
        var uid = <?php echo $user_id ?>;
        var page = page;
        var page_size = page_size;  
        <?php if($this->userName->type == 1){?>
            var tab_item = 1;
        <?php }else{?>
            var tab_item = 2;
        <?php } ?>
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
    $('.tabs-item').click(function(){
        var $_this=$(this);
        var uid = <?php echo $user_id ?>;
        var tab_item = $_this.attr('tab_item');
        var page = 0;
        var page_size = 10;
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
    });
    function showLoadItem(){
        $('.loadItem').show();
    }
    function hideLoadItem(){
        $('.loadItem').hide();
    }
</script>