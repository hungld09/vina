<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
		<meta name="format-detection" content="telephone=no" />
		<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl ?>/css/reset.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl ?>/css/style.css" type="text/css" />
		<!--[if IE]>
		<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl ?>/css/iemobile.css" type="text/css" />
		<![endif]-->
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl ?>/js/zt/zepto.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl ?>/js/zt/event.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl ?>/js/zt/ajax.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl ?>/js/zt/form.js"></script>
		<script type="text/javascript">
			var MO = {
				MO_URL:'<?php echo Yii::app()->request->baseUrl ?>',
				ACCOUNT_NAME:''
			};
		</script>
		<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl;?>/images/mobiphim_icon.png">
	</head>
	<body class="mobile">
		<div id="fnWrapper" class="division">
<?php
			$this->widget("application.widgets.NavMenu", array('categories' => $this->categories));
			echo $content;
?>
		</div><!-- end fnWrapper -->
		
		<div class="sugguest-mask none" id="fnSuggestContainer">
			<div class="sugguest none" id="fnSuggest">
			</div>
			<div id="fnSuggestHistory" class="search-history none">
			</div>
			<div class="btn-option">
				<a href="#" class="button page-prev fnSuggestClose">Quay lại</a>
				<a href="#" class="button delete-history fnSuggestClear none">Xóa lịch sử tìm kiếm</a>
			</div>
		</div>
		<div class="popup-mask none" id="fnPopup">
			<div class="flyout popup-finish none" id="fnMsgBox">
				<div class="glist">
					<div class="flyout-title"><span>Thông báo</span><a href="#" class="close icon-delete fnCloseBox" close-box="fnMsgBox"></a></div>
					<div class="popup-content">
						<span class="popup-alert" id="fnMsgError"></span>
						<a href="#" class="button desktop-btn fnCloseBox" close-box="fnMsgBox">Đóng</a>
					</div>
				</div>
			</div>
			<div class="flyout popup-report none" id="fnFbBox">
				<div class="glist">
					<div class="flyout-title">
						<span>Góp ý - báo lỗi</span>
						<a href="#" class="close icon-delete fnCloseBox" close-box="fnFbBox"></a>
					</div>
					<div class="popup-content">
						<form action="/ajax/feed-back/send" name="frmFb" id="frmFb">
							<div class="outside-element">
								<span class="popup-alert">Chủ đề:&#42;</span>
								<p class="text-box icon-dropdown">
									<span class="txt-selected" id="fnFbSubjectLabel">Vui lòng chọn loại phản hồi</span>
									<select name="subject" id="fnFbSubject" class="select">
										<option value="">Vui lòng chọn loại phản hồi</option><option value="1">Báo lỗi</option><option value="2">Góp ý nội dung</option><option value="3">Yêu cầu chức năng</option><option value="5">Nhạc chờ</option><option value="4">Khác</option>
									</select>
								</p>
							</div>
							<div class="outside-element">
								<span class="popup-alert">Link:</span>
								<p class="text-box">
									<input name="link" id="fnFbLink" type="text" class="trpr-textbox" />
								</p>
							</div>
							<div class="outside-element">
								<span class="popup-alert">Mô tả:</span>
								<p class="text-box text-area">
									<textarea name="content" id="fnFbContent" class="trpr-textbox"></textarea>
								</p>
							</div>
							<input type="hidden" name="flVer" value="html5" />
							<input type="hidden" name="browser" value="" />
							<a href="#" class="button desktop-btn fnFbSubmit">Gửi</a>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl ?>/js/mo.common.js"></script>		
	</body>
</html>