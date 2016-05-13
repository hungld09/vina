
<?php
foreach ($category as $item):
    ?>
    <div class="web_body">
        <ul style="margin: 0">
            <li class="clas"><a href="<?php echo Yii::app()->baseUrl . '/blog/cate/' . $item->id ?>"><?php echo $item->title ?></a></li>

        </ul>
    </div>
<?php endforeach; ?>
