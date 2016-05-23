<?php // echo "<pre>"; print_r($question);die;   ?>
<?php
    if (Yii::app()->session['user_id']) {
        $user_id      = Yii::app()->session['user_id'];
        $subcriberPop = Subscriber::model()->findByPk($user_id);
    } else {
        $user_id = -1;
    }
?>
<?php
    $CUtils = new CUtils();
    $time   = $CUtils->formatTime($question['modify_date']);
?>
<div class="question-list">
    <div class="col-md-12">
        <h1>Câu hỏi người dùng</h1>
        <div class="content">
            <div class="item-ques">
                <div class="item-top item-top1">
                    <div class="tel">
                        <p class="num"><?php echo $question['subscriber_name'] ?></p>
                        <?php echo $time ?>
                    </div>
                    <div class="pull-right">
                        <a data-toggle="modal" data-target="#myModal3" href="#"><img src="<?php echo Yii::app()->theme->baseUrl . '/images/f.png' ?>"
                                                                                     alt=""/></a>
                    </div>
                </div>
                <div class="calculator">
                    <div class="cal">
                        <span><?php echo 'Môn ' . $question['subject_name'] ?>, <?php echo $question['class_name'] ?></span>
                        <p class="img-can">
                            <img alt="" src="<?php echo Yii::app()->theme->baseUrl . '/images/can.png' ?>">
                        </p>
                        <p class="help">
                            <?php echo $question['title'] ?>
                        </p>
                    </div>
                    <div><img src="<?php echo IPSERVER . $question['base_url'] ?>" title="<?php echo $question['title'] ?>"
                              alt="<?php echo $question['title'] ?>"/>
                    </div>
                </div>
            </div>
            <?php
                $answer_id = -1;
                if ($answer != '') {
                $timeAnswer = $CUtils->formatTime($answer['modify_date']);
                $answer_id  = $answer['id'];
            ?>
            <div class="item-ques">
                <div class="item-top">
                    <div class="tel">
                        <p class="num"><span>Giáo viên:</span> <?php echo $subUser['subscriber_number'] ?></p>
                        <?php echo $timeAnswer ?>
                    </div>

                </div>
                <div class="calculator">
                    <p class="help">
                        Đáp án của em đây nhé
                    </p>
                    <?php
                        if (count($answer['url_images']) > 0):
                            foreach ($answer['url_images'] as $item) {
                                ?>
                                <div>
                                    <img src="<?php echo $item['images'] ?>" title="" alt="">
                                </div>
                                <?php
                            }
                        endif;
                    ?>
                    <?php } else { ?>
                        <h2>Chưa có câu trả lời</h2>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        loadComment();
    });

    $(".comment a").click(function () {
        $('.comment-text').fadeIn();
        $('#comment-text').focus();
    });
    $(".submit-comment-text").click(function () {
        var comment_text = $('#comment-text').val();
        var uid = <?php echo $user_id ?>;
        var question_id = <?php echo $question['id'] ?>;
        if (comment_text.length < 3 || comment_text.trim().length < 3) {
            $('#comment-text').focus();
            return;
        }
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/insertComment' ?>",
            data: {'comment_text': comment_text, 'uid': uid, 'question_id': question_id},
            dataType: 'html',
            success: function (html) {
                $('#comment-text').val('');
                $('#comment-text').focus();
                loadComment();
            }
        });
    });
    function loadComment() {
        var question_id = <?php echo $question['id'] ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/loadComment' ?>",
            data: {'question_id': question_id},
            dataType: 'html',
            success: function (html) {
                $('.comment-answer-list').html(html);
            }
        });
    }
    $('.close-comment-text').click(function () {
        $('.comment-text').fadeOut();
    });
    $('.statusSuccess').click(function () {
        var answer_id = <?php echo $answer_id ?>;
        var id = <?php echo $id ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/answer/statusSuccess' ?>",
            data: {'answer_id': answer_id, 'id': id},
            dataType: 'html',
            success: function (html) {
                window.location.reload();
            }
        });
    });
    $('.statusFail').click(function () {
        var answer_id = <?php echo $answer_id ?>;
        var id = <?php echo $id ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/answer/statusFail' ?>",
            data: {'answer_id': answer_id, 'id': id},
            dataType: 'html',
            success: function (html) {
                window.location.replace("<?php echo Yii::app()->homeurl . '/question/' ?>");
                return false;
            }
        });
    });
    $('#gymQuestion_<?php echo $question['id'] ?>').click(function () {
        var questionId = <?php echo $question['id'] ?>;
        var user_id = <?php echo $user_id ?>;
//        $('.gymQuestion_<?php // echo $question['id']   ?>').html('bỏ ghim');
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/holdQuestion' ?>",
            data: {'questionId': questionId, 'user_id': user_id},
            dataType: 'html',
            success: function (html) {
                if (html == 0) {
                    if (confirm("Bạn phải đăng nhập để sử dụng")) {
                        window.location.href = "<?php echo Yii::app()->baseUrl . '/account/' ?>";
                    }
                    return;
                }
                if (html == 1) {
                    alert('Câu hỏi đang được trả lời');
                    return;
                }
                if (html == 4) {
                    alert('Bạn vui lòng trả lời câu hỏi bạn đã ghim trước khi bạn muốn ghim câu hỏi này.');
                    return;
                }
//                var msg = {
//                        question_id: questionId, //id cau hoi
//                        type: '15', //dang tra loi: 1, tra loi xong: 2
//                };
//                websocket.send(JSON.stringify(msg));
                countdown("timer_<?php echo $question['id'] ?>", 0, html);
                location.reload();
//                countdown( "timer_<?php // echo $question['id']   ?>", 0, html );
            }
        });
    });

    function countdown(elementName, minutes, seconds) {
        var element, hours, endTime, mins, msLeft, time;
        element = document.getElementById(elementName);
        endTime = (+new Date) + 1000 * (60 * minutes + seconds) + 500;
        updateTimer();
//        endTime.setTime(endTime.getTime()+ (1* 60 *7 * 60 * 60 * 1000));
        updateTimer();

        function twoDigits(n) {
            return (n <= 9 ? "0" + n : n);
        }

        function updateTimer() {
            msLeft = endTime - (+new Date)
            if (msLeft <= 0) {
                element.innerHTML = "Hết giờ";
                location.reload();
//                location.href = '<?php // echo Yii::app()->baseUrl .'/question/deleteholdQuestion/'.$question['id']   ?>';
            } else {
                time = new Date(msLeft);
                hours = time.getUTCHours();
                mins = time.getUTCMinutes();
                element.innerHTML = (hours ? hours + ':' : '') + twoDigits(mins) + ':' + twoDigits(time.getUTCSeconds());
                setTimeout(updateTimer, time.getUTCMilliseconds() + 500);
            }
        }

    }
    $('.UngymQuestion_<?php echo $question['id'] ?>').click(function () {
        var questionId = <?php echo $question['id'] ?>;
        var user_id = <?php echo $user_id ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/unholdQuestion' ?>",
            data: {'questionId': questionId, 'user_id': user_id},
            dataType: 'html',
            success: function (html) {
                if (html == 0) {
                    if (confirm("Bạn phải đăng nhập để sử dụng")) {
                        window.location.href = "<?php echo Yii::app()->baseUrl . '/account/' ?>";
                    }
                    return;
                }
                if (html == 1) {
                    alert('Bạn bỏ ghim thành công');
                    location.reload();
                }
            }
        });
    });
    $('.changeLevel').click(function () {
        var level_id = <?php echo $question['level_id'] ?>;
        var user_id = <?php echo $user_id ?>;
        var question_id = <?php echo $question['question_id'] ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/changeLevel' ?>",
            data: {'level_id': level_id, 'user_id': user_id, 'question_id': question_id},
            dataType: 'html',
            success: function (html) {
                if (html == 0) {
                    alert('Bạn đã đề xuất tăng thêm 1 mức độ cho câu hỏi này thành công, Bạn đợi học sinh xác nhận.');
                    location.reload();
                }
                if (html == 1) {
                    alert('Đã có người đề xuất tăng mức độ.');
                    return false;
                }
                if (html == 2) {
                    alert('mức độ hiện tại là cao nhất, xin cảm ơn!.');
                    return false;
                }
            }
        });
    });
    $('.levelFail').click(function () {
        var level_id = <?php echo $question['level_id'] ?>;
        var question_id = <?php echo $question['question_id'] ?>;
        var user_id = <?php echo $user_id ?>;
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl . '/question/levelFail' ?>",
            data: {'level_id': level_id, 'user_id': user_id, 'question_id': question_id},
            dataType: 'html',
            success: function (html) {
                if (html == 1) {
                    location.reload();
                }
                location.reload();
            }
        });
    });
</script>
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="reportQuestion" style="padding: 10px; text-align: center">
                <h3>Báo cáo vi phạm của câu hỏi</h3>
                <div class="" style="text-align: left">
                    <input type="radio" name="report" id="report2" value="2"/>
                    <label>Nhiều câu trong một ảnh</label><br/>
                    <input type="radio" name="report" id="report3" value="3"/>
                    <label>Câu hỏi sai chuyên môn</label><br/>
                    <input type="radio" name="report" id="report4" value="4"/>
                    <label>Câu hỏi chứa nội dung không phù hợp</label><br/><br/>
                </div>
                <button type="button" class="btn btn-primary sub-reportQuestion" style="">Đồng ý</button>
            </div>
        </div>
    </div>
</div>
<style>
    .reportQuestion label {
        margin-right: 10px
    }
</style>
<script>
    $('.sub-reportQuestion').click(function () {
        var type;
        var url;
        var question_id = <?php echo $question['id'] ?>;
        var level_id = <?php echo $question['level_id'] ?>;
        var user_id = <?php echo $user_id ?>;
        $('input:checked').each(function () {
            type = $(this).val();
        });
        url = '/report/report';
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->baseUrl ?>" + url,
            data: {'type': type, 'user_id': user_id, 'question_id': question_id, 'level_id': level_id},
            dataType: 'html',
            success: function (html) {
                if (html == 5) {
                    if (confirm("Bạn phải đăng nhập để sử dụng")) {
                        window.location.href = "<?php echo Yii::app()->baseUrl . '/account/' ?>";
                    }
                    return;
                }
                if (html == 0) {
                    alert('Bạn đã đề xuất tăng mức độ cho câu hỏi này thành công, Bạn đợi học sinh xác nhận.');
                    location.reload();
                }
                if (html == 1) {
                    alert('Đã có người đề xuất tăng mức độ.');
                    return false;
                }
                if (html == 2) {
                    alert('mức độ hiện tại là cao nhất, xin cảm ơn!.');
                    return false;
                }
                if (html == 11) {
                    alert('Bạn báo cáo thành công.');
                    return false;
                }
//                alert(html);
                location.reload();
            }
        });
    });
    $('.call_fail').click(function () {
        alert('Hiện tại chưa thể kết nối với giáo viên này. Bạn vui lòng gọi lại sau.');
        return false;
    });

</script>