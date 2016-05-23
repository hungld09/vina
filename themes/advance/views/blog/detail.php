<div class="breadcrumbs">
    <ul>
        <li><a href="javascript:;">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><a href="<?php echo Yii::app()->baseUrl . '/blog' ?>">Tin tức</a></li>
        <li><span>&frasl;</span></li>
        <li><span><?php echo $item->title; ?></span></li>
    </ul>
</div>
<div class="box-news">
    <div class="new-detail">
        <h1><?php echo $item->title; ?></h1>
        <?php
            echo $item->content;
        ?>
    </div>
</div>