<div class="row">
    <div class="col-md-4 col-left no_pad_right">
        <div class="block-question">
            <h3>Ngân hàng câu hỏi</h3>
            <div class="content">
                <ul>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                    <li>
                        <a href="#">x^2-7x+12=0 Simple and best practice solution</a>
                    </li>
                </ul>
            </div>
            <div class="question-more">
                <a href="#">Xem tất cả ngân hàng câu hỏi</a>
            </div>
        </div>
        <div class="block-banner">
            <?php echo CHtml::link(CHtml::image(Yii::app()->theme->baseUrl . '/images/demo/banner.png', ''), Yii::app()->createUrl('/'), array('class' => '')) ?>
        </div>
    </div>

    <div class="col-md-8 col-mid">
        <div class="block-clip">
            <div class="title">
                <h3 class="pull-left">Clip bài giảng mới</h3>
                <div class="pull-right">
                    <a href="#">Xem tất cả các clip</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="content">
                <div class="row">
                    <?php for ($i = 1; $i <= 9; $i++) { ?>
                        <div class="col-md-4">
                            <div class="clip-item">
                                <div class="clip-img">
                                    <a href="<?php echo Yii::app()->baseUrl ?>" title="">
                                        <div class="icon-play">
                                            <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/video-play.png" alt=""/>
                                        </div>
                                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/images/demo/clip.png" alt=""/>
                                    </a>
                                </div>
                                <div class="clip-title">
                                    <a href="#">Giải bài thi toán lớp:6</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="block-news">
            <div class="title">
                <h3>Tin tức mới cập nhật</h3>
            </div>
            <div class="content">
                <div class="new-first">
                    <div class="new">
                        <div
                            class="col-md-4"><?php echo CHtml::link(CHtml::image(Yii::app()->theme->baseUrl . '/images/demo/new.png', ''), Yii::app()->createUrl('/'), array('class' => '')) ?></div>
                        <div class="col-md-8">
                            <a class="new-title" href="#">Nhiều trường 'trắng' học sinh chọn Sử</a>
                            <div class="new-description">Kỳ thi THPT Quốc gia 2016 với ba môn bắt buộc là Toán, Văn, Anh văn và một môn tự chọn trong số các môn
                                Sinh, Sử, Địa, Lý, Hóa.
                                Dù đến 30/4, học sinh mới kết thúc hồ sơ đăng ký dự thi THPT Quốc gia 2016 nhưng thời điểm này, cơ bản các trường THPT đã nắm
                                được danh sách học sinh đăng ký và chọn môn thi tương đối chính xác.
                            </div>
                            <a class="new-more" href="#">Xem chi tiết >></a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="new-list">
                    <ul>
                        <li><a href="#">Chuỗi hội thảo du học, học bổng Đại học Monash (29/2)</a></li>
                        <li><a href="#">Trường đại học tung học bổng hút thí sinh nữ vào ngành kỹ thuật (4/3)</a></li>
                        <li><a href="#">Tháng du học và học bổng tới 400 triệu đồng (29/2)</a></li>
                        <li><a href="#">Lợi thế khi du học từ bậc phổ thông (29/2)</a></li>
                        <li><a href="#">Học bổng trung học Anh và tuyển sinh trực tiếp (1/3)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>