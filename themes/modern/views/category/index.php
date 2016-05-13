<?php
/* @var $this CategoryController */
$this->pageTitle = "mPhim - Category";
?>
<br/>
<h1>Thể loại phim</h1>
<!--
<article>
	<h2><?php echo CHtml::link("Phim lẻ", array("/mphim/assets/category/single")); ?></h2>
</article>
<article>
	<h2><?php echo CHtml::link("Phim bộ", array("/mphim/assets/category/series")); ?></h2>
</article>
-->
<article>
	<h2><?php echo CHtml::link("Tất cả", array("/mphim/video/browse")); ?></h2>
</article>
<?php
	/* @var $cat VodCategory */
	foreach ($categories as $cat) { ?>
<article>
	<h2><?php echo CHtml::link($cat->display_name, array("/mphim/video/browse/category/" . $cat->id)); ?></h2>
	<!--<p><?php echo $cat->description; ?></p>-->
</article>
<?php
	}
?>