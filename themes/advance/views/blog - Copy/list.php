<div class="web_body" style="float: left">
    <div class="blog-wap">
        <ul>
            <?php
                foreach ($items as $item):
            ?>
            <li>
                <a href="<?php echo Yii::app()->baseUrl . '/blog/'. $item->id?>">
                    <img src="<?php echo IPSERVER . 'web/uploads/' . $item->image_url?>" />
                </a>
                <div class="blog-title">
                    <a href="<?php echo Yii::app()->baseUrl . '/blog/'. $item->id?>"><?php echo $item->title?></a>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>