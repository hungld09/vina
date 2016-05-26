<div class="breadcrumbs">
    <ul>
        <li><a href="javascript:;">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><span>Kết quả tìm kiếm</span></li>
    </ul>
</div>
<div class="box-news box-result">
    <ul>
        <?php
            $requestUrl = Yii::app()->request->url;
            if (count($result) > 0) {
            foreach ($result as $item):
                ?>
                <li>
                    <div class="thum1"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/img-result.png" alt=""/></div>
                    <div class="infor">
                        <h1><?php echo $item['question'] ?></h1>
                    </div>
                </li>
                <script>
                    $('.checkMoney_<?php echo $item['id'] ?>').click(function () {
                        var fcoin = 10;
                        if (fcoin < '5' || fcoin < 5) {
                            if (confirm("Bạn không đủ tiền, bạn có muốn nạp tiền để tiếp tục xem câu hỏi?")) {
                                window.location.href = "<?php echo Yii::app()->baseUrl . '/account/useCard' ?>";
                                return false;
                            } else {
                                window.location.href = "<?php echo Yii::app()->baseUrl . '/site/' ?>";
                                return false;
                            }
                            return false;
                        } else {
                            window.location.href = "<?php echo Yii::app()->baseUrl . '/questionBank/' . $item['id'] ?>";
                        }
                    });

                </script>
                <?php
            endforeach;
        ?>
    </ul>
</div>

<?php
    $requestUrl = preg_replace('(&page=\d+)', '', $requestUrl);
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
    <a href="<?php echo $requestUrl ?><?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'] + 1;
            echo '&page=' . $page;
        } else {
            echo '&page=1';
        }
    ?>">Xem thêm</a>
</div>
<?php
    } else {
    ?>
    <div class="web_body">
        <p>Chưa cập nhật</p>
    </div>
    <?php
}
?>
<script src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML" type="text/javascript">
    $(document).ready(function () {
        MathJax.Hub.Config({
            tex2jax: {
                inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
                ]
            }
        });
    });
</script>
<script>
    $('.loadmore').click(function () {
        var click = parseInt($('.loadmore').attr('click')) + 1;
        var class_id = <?php echo (isset($class_id)) ? $class_id : 0 ?>;
        var subject_id = <?php echo (isset($subject_id)) ? $subject_id : 0 ?>;
        var chapter_id = <?php echo (isset($chapter_id)) ? $chapter_id : 0 ?>;
        var unit_id = <?php echo (isset($unit_id)) ? $unit_id : 0 ?>;
        var title = '<?php
            if (isset($title)) {
                echo $title;
            }
            ?>';
        $('.loadmore').attr('click', click);
        //        showLoadItem();
        $.ajax({
            type: 'POST',
            url: "<?php echo Yii::app()->request->baseUrl . '/questionBank/loaditem' ?>",
            data: {'click': click, 'subject_id': subject_id, 'title': title, 'unit_id': unit_id, 'class_id': class_id, 'chapter_id': chapter_id},
            dataType: 'html',
            success: function (html) {
                //                hideLoadItem();
                $('#loadtion').append(html);
            }
        });

    });
    function showLoadItem() {
        $('.loadItem').show();
    }
    function hideLoadItem() {
        $('.loadItem').hide();
    }
</script>

