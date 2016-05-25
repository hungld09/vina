<div class="question-list">
    <div class="col-md-12">
        <h1>Tin tức</h1>
        <div class="content">
            <div class="box-news">
                <ul>
                    <?php foreach ($items as $item): ?>
                        <li>
                            <div class="thum1"><img src="<?php echo IPSERVER . 'web/vina/blog/' . $item->image_url ?>" alt=""/></div>
                            <div class="infor">
                                <h3><a href="<?php echo Yii::app()->baseUrl . '/blog/' . $item->id ?>"><?php echo $item->title ?></a></h3>
                                <p><?php echo $item->description ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>