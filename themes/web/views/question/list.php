<div class="question-list">
    <div class="col-md-12">
        <h1>Câu hỏi người dùng</h1>

        <?php
            if (count($questions) > 0) {
                ?>
                <div class="content">
                    <?php
                        $CUtils = new CUtils();
                        foreach ($questions as $s => $question):
                            $time = $CUtils->formatTime($questions[$s]['create_date']);
                            ?>
                            <div class="item-ques">
                                <div class="item-top">
                                    <div class="tel">
                                        <p class="num"><?php echo $questions[$s]['subscriber_name'] ?></p>
                                        <?php echo $time ?>
                                    </div>
                                    <div class="pull-right">
                                        <img src="<?php echo Yii::app()->theme->baseUrl . '/images/f.png' ?>" alt=""/>
                                    </div>
                                </div>
                                <div class="calculator">
                                    <span><?php echo 'Môn ' . $questions[$s]['subject_name'] ?>, <?php echo $questions[$s]['class_name'] ?></span>
                                    <p class="img-can">
                                        <img alt="" src="<?php echo Yii::app()->theme->baseUrl . '/images/can.png' ?>">
                                    </p>
                                    <p class="help">
                                        <?php echo $questions[$s]['title'] ?>
                                    </p>
                                    <p>
                                        <a class="ava" href="<?php echo Yii::app()->baseUrl . '/question/' . $questions[$s]['id'] ?>"><img
                                                src="<?php echo IPSERVER . $questions[$s]['base_url'] ?>" title="" alt=""/></a>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                    <a href="<?php echo Yii::app()->baseUrl . '/question/list?page=' ?><?php
                        if (isset($_GET['page'])) {
                            echo $_GET['page'] + 1;
                        } else {
                            echo 1;
                        }
                    ?>">Xem thêm</a>
                </div>
            <?php } else { ?>
                <div class="content">
                    <div class="web_body">
                        Không có kết quả
                    </div>
                </div>
            <?php } ?>

        <div class="text-center">
            <a class="send-question" href="<?php echo Yii::app()->baseUrl . '/question/upload' ?>">Gửi câu hỏi</a>
            <div class="question-alert">Chỉ khi user đăng ký gói tuần</div>
        </div>
    </div>
</div>