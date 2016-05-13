<div class="content-items">
	<div class="emborder"><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>"><?php if (isset($posterUrl) && $posterUrl != "") { ?><img src="<?php echo $posterUrl;?>" alt="<?php echo CHtml::encode($asset['display_name']);?>" class="poster-img" /><?php } ?></a></div>
	<div>
		<h3><a href="<?php echo Yii::app()->baseUrl . "/video/" . $asset['id'];?>" style="color:#000;"><?php echo CHtml::encode($asset['display_name']);?></a></h3>
		<p style="font-style:italic;color:#000;font-weight:normal;margin-right:20px;"><?php echo $asset['duration'] . " phút, " . $asset['view_count'] . " lượt xem"?></p>
		<p style="margin-left:140px;"><a href="<?php echo Yii::app()->baseUrl . "/video/purchase?type=watch&id=" . urlencode($asset['encrypted_id']);?>"><span class="icon-film"> </span>Xem</a> (<?php echo $isFree ? "miễn phí" : intval($asset['price']) . 'đ';?>)</p>
	</div>
	<span class="right-arrow"></span>
</div>