<ul class="main_total" style="margin: 0">

</ul>
<script>
    var classId = <?php echo $class_id; ?>;
    $.ajax({
        type: 'POST',
        url: '<?php echo Yii::app()->createUrl('question/showsubject')?>',
        data: 'classId=' + classId,
        cache: false,
        success: function (data) {
            $('.main_total').html(data).parent().fadeIn('slow');
        }
    });
    setInterval(function () {
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('question/showsubject')?>',
            data: 'classId=' + classId,
            cache: false,
            success: function (data) {
                $('.main_total').html(data).parent().fadeIn('slow');
            }
        });
    }, 12000);
</script>
