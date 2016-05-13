<?php
$this->title = 'Hướng dẫn giải bài tập toán, lý, hóa online | Học Dễ OnEdu';
$this->description = $category['description'];
$this->keywords = $category['keywords'];
//echo Yii::app()->baseUrl.'/giai-bai-tap-'.mb_strtolower(CVietnameseTools::removeSigns($category['title'])).'-'.$category['id'];die;
?>
<style>
    .col-sm-12{
        margin-bottom: 20px;
    }
    .col-sm-4 img{
        height: 180px;
        width: 100%;
    }
</style>
<div style="width: 70%; margin: auto;text-align: center">
    <h1 style="margin-bottom: 40px;margin-top: 0px"><?php echo $category['title']; ?></h1>
    <?php $e = 0; foreach($topicals as $i => $item){ $e++; ?>
    <?php if($i == 0){ ?>
        <div class="col-sm-12">
    <?php } ?>
            <div class="col-sm-4">
                <div style="text-align: center;width: 100%">
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-'.mb_strtolower(CVietnameseTools::makeCodeName2($category['title'])).'/'.$item->title_code; ?>">
                        <img class="img-thumbnail" src="<?php echo IPSERVER; ?>web/uploads/<?php echo $item->image_url; ?>" alt="" />
                    </a>
                    <a href="<?php echo Yii::app()->baseUrl.'/giai-bai-tap-'.mb_strtolower(CVietnameseTools::makeCodeName2($category['title'])).'/'.$item->title_code; ?>">
                        <h2 style="font-size: 20px;"><?php echo $item->title; ?></h2>
                    </a>
                </div>
            </div>
    <?php if($e == 3){ echo '</div><div class="col-sm-12">'; } ?>
    <?php } ?>
        </div>
    <?php
    if($total_result > 6){
        $this->widget("application.widgets.Pager", array('pager' => $pager, 'baseUrl' => Yii::app()->baseUrl.'/giai-bai-tap-'.mb_strtolower(CVietnameseTools::removeSigns($category['title']))));
    }
    ?>
</div>