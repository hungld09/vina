<div class="breadcrumbs">
    <ul>
        <li><a href="javascript:;">Menu</a></li>
        <li><span>&frasl;</span></li>
        <li><span>Tin tức</span></li>
    </ul>
</div>
<div class="box-news">
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <div class="thum1"><img src="<?php echo IPSERVER . 'web/uploads/' . $item->image_url ?>" alt=""/></div>
                <div class="infor">
                    <h1><a href="<?php echo Yii::app()->baseUrl . '/blog/' . $item->id ?>"><?php echo $item->title ?></a></h1>
                    <p><?php echo $item->description ?></p>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>