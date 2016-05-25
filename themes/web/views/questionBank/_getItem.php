<?php if (isset($result) && count($result) > 0) { ?>
    <?php
    $requestUrl = Yii::app()->request->url;
    foreach ($result as $item):
        ?>
        <li>
            <div class="thum1"><img src="<?php echo Yii::app()->theme->baseUrl ?>/images/img-result.png" alt=""/></div>
            <div class="infor">
                <h3><?php echo $item['question'] ?></h3>
            </div>
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
        </li>
        <?php
    endforeach;
    ?>
<?php } ?>