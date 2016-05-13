<?php
if(Yii::app()->session['user_id']){
    $user_id = Yii::app()->session['user_id'];
//        $user_name = Subscriber::model()->findByPk($user_id);
}else{
    $user_id = -1;
}
if(count($questions)> 0){
    ?>
    <?php
    $CUtils = new CUtils();
    foreach ($questions as $s=>$question):
        $time = $CUtils->formatTime($questions[$s]['modify_date']);
        if($questions[$s]['url_avatar'] == ''){
            $avata = Yii::app()->theme->baseUrl .'/FileManager/avata.png';
        }else{
            $avata = $questions[$s]['url_avatar'];
        }
        ?>
        <div class="web_body">
            <div class="listarticle">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 avata">
                                <a href="#"><img src="<?php echo $avata ?>" /></a>
                                <div class="name-title">
                                    <a href="#"><?php echo $questions[$s]['subscriber_name'] ?></a>
                                    <p><?php echo $time?></p>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6 name-detail">
                                <?php
                                if($user_id != -1){
                                    if($this->userName->type == 1){
                                ?>
                                    <a href="<?php echo Yii::app()->baseUrl .'/question/upload'?>">Gửi câu hỏi</a>
                                <?php
                                    }else{
                                ?>
                                    <a href="<?php echo Yii::app()->baseUrl .'/question/'. $questions[$s]['id']?>">Gửi câu trả lời</a>
                                <?php        
                                    }
                                }else{
                                ?>
                                    <a href="<?php echo Yii::app()->baseUrl .'/question/upload'?>">Gửi câu hỏi</a>
                                <?php
                                }
                                ?>
                                <div class="subject-title">
                                    <img src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/subject.png" />
                                    <span><?php echo 'Môn '.$questions[$s]['subject_name']?>, <?php echo $questions[$s]['class_name']?></span>
                                    <span><a class="cl"><?php echo $questions[$s]['level'] ?></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="article-title">
                            <p style="word-wrap:break-word;"><?php echo $questions[$s]['title']?></p>
                        </div>
                        <div class="articleitem_body">
                            <a class="ava" href="<?php echo Yii::app()->baseUrl .'/question/'. $questions[$s]['id']?>"><img src="<?php echo IPSERVER.$questions[$s]['base_url']?>" title="" alt="" /></a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="notify">
                                <div class="col-md-3 col-xs-3 like">
                                    <?php if($questions[$s]['check_like'] == 0){?>
                                        <a uid ="<?php echo $user_id?>" question_id ="<?php echo $questions[$s]['id'] ?>" status ="<?php echo $questions[$s]['check_like'] ?>" class="checkLike_<?php echo $questions[$s]['id'] ?>">
                                            <img src="<?php echo Yii::app()->theme->baseUrl . '/FileManager/like.png' ?>" />
                                            <span>Like</span>
                                        </a>
                                    <?php }else{?>
                                        <a uid ="<?php echo $user_id?>" question_id ="<?php echo $questions[$s]['id'] ?>" status ="<?php echo $questions[$s]['check_like'] ?>" class="checkLike_<?php echo $questions[$s]['id'] ?>">
                                            <img src="<?php echo Yii::app()->theme->baseUrl . '/FileManager/ic_like.png' ?>" />
                                            <span>Like</span>
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="col-md-4 col-xs-4 comment">
                                    <a href="<?php echo Yii::app()->baseUrl .'/question/'. $questions[$s]['id']?>" class="comment-home">
                                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/comment.png" />
                                        <span>Bình luận</span>
                                    </a>
                                </div>
                                <div class="col-md-5 col-xs-5 time">
                                    <a href="#">
                                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/FileManager/time.png" />
                                        <span>
                                            <?php if($questions[$s]['status'] == 2){
                                                echo 'Chờ xác nhận';
                                            }elseif($questions[$s]['status'] == 1){
                                                echo 'Chưa có câu trả lời';
                                            }elseif($questions[$s]['status'] == 3){
                                                echo 'Đã có câu trả lời';
                                            }elseif($questions[$s]['status'] == 4){
                                                echo 'Có câu trả lời sai';
                                            } ?>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(".checkLike_<?php echo $questions[$s]['id'] ?>").click(function(){
                var $_this = $(this);
                var status = $_this.attr('status');
                var uid = $_this.attr('uid');
                var question_id = $_this.attr('question_id');
                if(uid == -1){
                    return false;
                }
                if(status == 0){
                    $(".checkLike_<?php echo $questions[$s]['id'] ?> img").attr('src',"<?php echo Yii::app()->theme->baseUrl . '/FileManager/ic_like.png' ?>");
                    $_this.attr('status',"1");
                }else{
                    $(".checkLike_<?php echo $questions[$s]['id'] ?> img").attr('src',"<?php echo Yii::app()->theme->baseUrl . '/FileManager/like.png' ?>");
                    $_this.attr('status',"0");
                }
                $.ajax({
                    type: 'POST',
                    url: "<?php echo Yii::app()->baseUrl . '/site/checklike'?>",
                    data: {'status':status,'uid':uid, 'question_id':question_id},
                    dataType:'html',
                    success: function(html){
                        return false;
                    }
                });
            });
            //    $(".comment-home").click(function(){
            //        window.location.href = "<?php echo Yii::app()->baseUrl .'/question/'. $questions[$s]['id']?>";
            //        $(".comment-text").css('display','block');
            //    });
        </script>
    <?php endforeach;?>
        
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
    <a href="<?php echo Yii::app()->baseUrl .'/question/questionSubject?id='.$subjectId.'&page='?><?php if(isset($_GET['page'])){ echo $_GET['page'] + 1; }else{ echo 1; } ?>">Xem thêm</a>
</div>
<?php }else{?>
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
<?php
if($user_id != -1){
    if($this->userName->type == 1){
?>
<div class="uploadQuestion" style="position: fixed;z-index: 999999;bottom: 2%;right:15%;">
    <a class="" href="<?php echo Yii::app()->baseUrl .'/question/upload'?>"><img src="<?php echo Yii::app()->theme->baseUrl .'/img/friend_add.png'?>" /></a>
</div>
<?php 
    }
    }else{
?>
    <div class="uploadQuestion" style="position: fixed;z-index: 999999;bottom: 2%;right: 15%;">
        <a href="<?php echo Yii::app()->baseUrl .'/question/upload'?>"><img src="<?php echo Yii::app()->theme->baseUrl .'/img/friend_add.png'?>" /></a>
    </div>
<?php }?>