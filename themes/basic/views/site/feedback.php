<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - Gửi phản hồi';
$this->breadcrumbs = array(
    'Gửi phản hồi',
);
?>

<script type="text/javascript">
    function validateFeedbackForm() {
        title = $.trim(document.feedback_form.title.value);
        content = $.trim(document.feedback_form.content.value);
        //Validate mobile
        if(title.length <= 0 && content.length <= 0){
            alert('Xin vui lòng nhập tiêu đề hoặc nội dung phản hồi');
            return false;
        }else{
            return true;
        }
        
        return false;
    }
    
</script>


<div  style ="text-shadow: none;"id="main_page" data-theme="a">
    <div id="detail" align="center">
        
        <img width="115" src="<?php echo Yii::app()->theme->baseUrl ?>/images/feedback.png" />
        <?php if($this->msisdn != ''){ ?>
        <form name="feedback_form" id="feedback_form" method="POST" action="<?php echo $this->createUrl("/site/feedback"); ?>" onsubmit="return validateFeedbackForm();">
            <p style="color: white;">Tiêu đề</p>
            <input style="margin-top: 1px; color: black;" type="text" name="title" id="title" value="" />
            <div class="clear"></div>
            <p style="margin-top: 10px; color: white;">Nội dung phản hồi</p>
            <textarea style="margin-top: 1px; color: black; height: 100px;" type="text" name="content" id="content" rows="10" cols="50"></textarea>
            <div style="margin-top: 10px;" >
                <input type="submit" name="submit_feedback" value="Gửi phản hồi" class="button" />
            </div>
        </form>
        <?php } else { ?>
        <p style="color: #8D8D8D;font-size: 14px;margin-bottom: 14px;">Bạn phải truy cập qua dịch vụ 3G của Vinaphone hoặc đăng nhập bằng <a style="color: white;" href="<?php echo Yii::app()->request->baseUrl; ?>/account/login">wifi tại đây </a> mới có thể gửi phản hồi được</p>
        <?php } ?>
    </div>
    <div id="detail" align="center"></div>
    <?php $this->widget("application.widgets.Footer", array('categories' => $this->categories)); ?>
</div>


