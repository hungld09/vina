<?php $this->beginContent('//layouts/main'); ?>
<?php
    if (isset($this->breadcrumbs)) {
        echo "<div class='breadcrumbs'><div class='container'>";
        $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'       => $this->breadcrumbs,
            'separator'   => '<span class="separator">&#10095;</span>',
            'htmlOptions' => array('class' => 'breadcrumb'),
        ));
        echo "</div></div>";
    }
?>

<?php echo $content; ?>
<?php $this->endContent(); ?>
