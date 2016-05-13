<?php

class VideoController extends ApiController {
	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
				array('allow', // allow all users to perform 'index' and 'view' actions
						'actions' => array('index', 'view'),
						'users' => array('*'),
				),
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
						'actions' => array('create', 'update'),
						'users' => array('@'),
				),
				array('allow', // allow admin user to perform all actions
						//'actions' => array('create', 'delete'),
						//TODO: authen, author
						//'users' => array('admin'),
				),
				array('deny', // deny all users
						'users' => array('*'),
				),
		);
	}

// 	public function beforeAction($action) {
// 		if($this->accessType == Controller::$ACCESS_VIA_WIFI) {
// 			if(($this->wifi_number != -1) && ($this->subscriber == NULL)) {
// 				$this->msisdn = CUtils::validatorMobile($this->wifi_number);
// 				$this->subscriber = Subscriber::newSubscriber($this->msisdn);
// 			}
// 		}
// 		return parent::beforeAction($action);
// 	}

	public function actionGetHomePage() {
		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";

		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", 0));
		$result = $root->appendChild($xmlDoc->createElement("result"));

		$order = 'top_new';
		$groupTopNew = $result->appendChild($this->createVodGroup($order, $result, $xmlDoc, VodImage::ORIENTATION_LANDSCAPE));

		$order = 'top_rated';
		$groupTopRated = $result->appendChild($this->createVodGroup($order, $result, $xmlDoc, VodImage::ORIENTATION_PORTRAIT));

		$order = 'top_kinhdien';
		$groupTopRated = $result->appendChild($this->createVodGroup($order, $result, $xmlDoc, VodImage::ORIENTATION_PORTRAIT));

		$order = 'top_free';
		$groupTopRated = $result->appendChild($this->createVodGroup($order, $result, $xmlDoc, VodImage::ORIENTATION_PORTRAIT));

		$order = 'most_viewed';
		$groupMostViewed = $result->appendChild($this->createVodGroup($order, $result, $xmlDoc, VodImage::ORIENTATION_PORTRAIT));

		echo $xmlDoc->saveXML();
		Yii::app()->end();
	}

	public function actionGetRelatedAssets(){
		// order = {default|featured|top_new|most_viewed|top_rated|most_discussed}
		$order = isset($_REQUEST['order'])?$_REQUEST['order']:"";
		$vod_id = isset($_REQUEST['vod'])?$_REQUEST['vod']:null;
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;
		$image_width = isset($_REQUEST['image_width'])?$_REQUEST['image_width']:null;

		if ($vod_id != null) {
			$vodModel = VodAsset::model()->findByPk($vod_id);
			if (VodAsset::model()->findByPk($vod_id) == null) {
				$this->responseError(1, "ERROR_", "VOD #$vod_id does note exist!");
			}
		}
		else {
			$this->responseError(1, "ERROR_", "No VOD specified!");
		}

		$db_order = "";
		switch($order)
		{
			case "top_new":
				$db_order = 't.modify_date DESC';
				break;
			case "most_viewed":
				$db_order = 't.view_count DESC';
				break;
			case "top_rated":
				//$db_order = '(rating_count*3 + rating*7) DESC';
				$db_order = 't.rating_count DESC';
				break;
			case "most_discussed":
				$db_order = 't.comment_count DESC';
				break;
			case "featured"://not support now
				// $models = Asset::model()->findAll();
				//break;
			default ://case "default":
				$order = 'default';
				$db_order = "t.modify_date DESC";
				break;
		}

		//echo $this->_format;

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			//$this->_categoriesToXML($xmlDoc, $result, VodCategory::getSubCategories(null,false));
			/* @var $vodModel VodAsset */
			$res = $vodModel->getRelatedVODs($db_order, $page, $page_size);

			$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
			$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
			$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
			$result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

			$this->createXmlVodList($res, $xmlDoc, $result);

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetAssets(){
		// order = {default|featured|top_new|most_viewed|top_rated|most_discussed}
		$order = isset($_REQUEST['order'])?$_REQUEST['order']:"";
		$cat_id = isset($_REQUEST['category'])?$_REQUEST['category']:null;
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;
		//        $image_width = isset($_REQUEST['image_width'])?$_REQUEST['image_width']:null;
		$keyword = isset($_REQUEST['keyword'])?$_REQUEST['keyword']:"";
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:0;

		if ($cat_id!=null && VodCategory::model()->findByPk($cat_id) == null) {
			$this->responseError(1,"ERROR_","VOD Category #$cat_id does note exist!");
		}

		// add keyword to history
		if (!empty($keyword)) {
			$keyword = CVietnameseTools::makeSearchableStr($keyword);
			$subscriber = Subscriber::model()->findByAttributes(array("user_name"=>$this->_username));
			if($subscriber != NULL) {
				$old_val = VodSearchHistory::model()->findByAttributes(array(
						'subscriber_id' => $subscriber->id,
						'keyword' => $keyword
				));
				/* @var $old_val VodSearchHistory */
				if ($old_val != null) {
					$old_val->hit_count++;
					$old_val->last_date = new CDbExpression('NOW()');
					$old_val->update();
				}
				else {
					$new_val = new VodSearchHistory();
					$new_val->subscriber_id = $subscriber->id;
					$new_val->last_date = new CDbExpression('NOW()');
					$new_val->keyword = $keyword;
					$new_val->hit_count = 1;
					if ($cat_id != null) {
						$new_val->vod_category_id = $cat_id;
					}
					$new_val->save();
				}
			}
		}

		$db_order = "";
		switch($order)
		{
			case "top_new":
				$db_order = 't.modify_date DESC';
				break;
			case "most_viewed":
				$db_order = 't.view_count DESC';
				break;
			case "top_rated":
				//$db_order = '(rating_count*3 + rating*7) DESC';
				$db_order = 't.rating_count DESC';
				break;
			case "most_discussed":
				$db_order = 't.comment_count DESC';
				break;
			case "featured"://not support now
				// $models = Asset::model()->findAll();
				//break;
			default ://case "default":
				$order = 'default';
				//                $db_order = "t.modify_date DESC"; // tam thoi comment lai, sap xep theo ten phim
				$db_order = "t.modify_date DESC, t.honor, t.code_name";
				break;
		}

		//echo $this->_format;

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			//$this->_categoriesToXML($xmlDoc, $result, VodCategory::getSubCategories(null,false));
			$res = VodAsset::findVODs($cat_id, $db_order, $page, $page_size, $keyword, $type);

			// xu ly doi voi app version < 2 *** begin
			// nhung version cu hon thi episode cung la vodAsset
			$listAssets = array();
			$assetCount = 0;
			$assetCount = $res['total_result'];

			$result->appendChild($xmlDoc->createElement('keyword', CHtml::encode($res['keyword'])));
			$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
			$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
			$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
			$result->appendChild($xmlDoc->createElement('total_result', $assetCount));
			//            $result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

			$result = $this->createXmlVodList($res, $xmlDoc, $result);

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetCategories(){
		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$this->_categoriesToXML($xmlDoc, $result, VodCategory::getSubCategories(null,false));

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}

	}

	/**
	 * to be used in actionGetVODCategories
	 * @param type $_dom
	 * @param type $_parentNode
	 * @param type $data
	 */
	private function _categoriesToXML($_dom, $_parentNode, $data) {
		foreach($data as $node) {
			/* @var $node VodCategory */
			if ($node->status==VodCategory::VOD_CAT_STATUS_ACTIVE) {
				$cat = $_dom->createElement("category");
				$cat->appendChild($_dom->createAttribute("id"))
				->appendChild($_dom->createTextNode($node->id));
				$cat->appendChild($_dom->createAttribute("create_date"))
				->appendChild($_dom->createTextNode($node->create_date));
				$cat->appendChild($_dom->createElement("name"))
				->appendChild($_dom->createTextNode($node->display_name));
				$cat->appendChild($_dom->createElement("description"))
				->appendChild($_dom->createTextNode($node->description));
				$cat->appendChild($_dom->createElement("image"))
				->appendChild($_dom->createTextNode($node->image_url));
				$_parentNode->appendChild($cat);

				$this->_categoriesToXML($_dom, $cat, VodCategory::getSubCategories($node->id,false));
			}
		}
	}

	public function chooseVODPoster($vod_id, $posterOrientation = -1) {
		if($posterOrientation == -1) {
			$posterOrientation = $this->_isTablet;
		}
		$images = VodAsset::getVODImages($vod_id);

		if (empty($images)) {
			return null;
		}
	
		$image = $images[0];
		foreach ($images as $anImage) {
			if ($anImage->orientation == $posterOrientation) {
				$image = $anImage;
				break;
			}
		}
		return $image;
	}

	public function actionGetAssetInfo(){
		$isEpisode = 0;
		$vod_id = isset($_REQUEST['vod'])?$_REQUEST['vod']:null;
		$protocol = isset($_REQUEST['protocol'])?$_REQUEST['protocol']:-1;
		if ($vod_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}

		$vod = VodAsset::model()->findByPk($vod_id);
		/* @var $vod VodAsset */

		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$result->appendChild($xmlDoc->createElement("id",CHtml::encode($vod['id'])));
			$result->appendChild($xmlDoc->createElement('name'))
			->appendChild($xmlDoc->createCDATASection($vod['display_name']));
			$result->appendChild($xmlDoc->createElement('original_title'))
			->appendChild($xmlDoc->createCDATASection($vod['original_title']));
			if($isEpisode == 0) {
				if($vod['is_series'] == 1) {
					$episodeCount = count($vod->vodEpisodes);
					if($vod->episode_count != $episodeCount) {
						$vod->episode_count = $episodeCount;
						$vod->update();
					}
					$result->appendChild($xmlDoc->createElement("episode_count",CHtml::encode($vod->episode_count)));
				}
			}
			$result->appendChild($xmlDoc->createElement('short_description'))
			->appendChild($xmlDoc->createCDATASection($vod['short_description']));
			$result->appendChild($xmlDoc->createElement('description'))
			->appendChild($xmlDoc->createCDATASection($vod['description']));
			$result->appendChild($xmlDoc->createElement('duration', CHtml::encode($vod['duration'])));
			$result->appendChild($xmlDoc->createElement("create_date",CHtml::encode($vod['create_date'])));
			$result->appendChild($xmlDoc->createElement("view_count",CHtml::encode($vod['view_count'])));
			$result->appendChild($xmlDoc->createElement("favorite_count",CHtml::encode($vod['favorite_count'])));
			$result->appendChild($xmlDoc->createElement("comment_count",CHtml::encode($vod['comment_count'])));
			$result->appendChild($xmlDoc->createElement("like_count",CHtml::encode($vod['like_count'])));
			$result->appendChild($xmlDoc->createElement("dislike_count",CHtml::encode($vod['dislike_count'])));
			$result->appendChild($xmlDoc->createElement("rating",CHtml::encode($vod['rating'])));
			$result->appendChild($xmlDoc->createElement("rating_count",CHtml::encode($vod['rating_count'])));
			$result->appendChild($xmlDoc->createElement("tags",CHtml::encode($vod['tags'])));
			$result->appendChild($xmlDoc->createElement("is_free",CHtml::encode($vod['is_free'])));
			
			$price = 0;
			if($vod['is_free'] == 0) {
				$price = $vod['price'];
				if($this->subscriber != NULL) {
					if($this->subscriber->isUsingService() == true) {
						$price = 0;
					}
					else if ($this->subscriber->hasVodAsset($vod, USING_TYPE_WATCH)){
						$price = 0;
					}
				}
			}
			$result->appendChild($xmlDoc->createElement("price",CHtml::encode($price)));
			
			$result->appendChild($xmlDoc->createElement("actor",CHtml::encode($vod->actors)));
			$result->appendChild($xmlDoc->createElement("director",CHtml::encode($vod->director)));

			$cats = $xmlDoc->createElement('category');
			foreach ($vod->vodCategories as $cat) {
				/* @var $cat VodCategory */
				$cats->appendChild($xmlDoc->createElement('category'))
				->appendChild($xmlDoc->createCDATASection($cat->display_name));
			}
			$result->appendChild($cats);

			$images = $xmlDoc->createElement('images');
			foreach ($vod->vodImages as $image) {
				$imageUrl = $image->url;
				if(strpos($imageUrl, 'http://') === FALSE) {
					$imageUrl = 'http://vfilm.vn/posters/'.$imageUrl;
				}
				/* @var $image VodImage */
				$node = $images->appendChild($xmlDoc->createElement('image'));
				$node->appendChild($xmlDoc->createCDATASection($imageUrl));
				//                 $node->appendChild($xmlDoc->createAttribute('w'))
				//                         ->appendChild($xmlDoc->createTextNode($image->width));
				//                 $node->appendChild($xmlDoc->createAttribute('h'))
				//                         ->appendChild($xmlDoc->createTextNode($image->height));
				$imageOrientation = ($image->orientation == 2)?'land':'port';
				$node->appendChild($xmlDoc->createAttribute('format'))
				->appendChild($xmlDoc->createTextNode($imageOrientation));
			}
			$result->appendChild($images);

			$trailers = $xmlDoc->createElement('trailers');
			$trailerStreams = VodStream::model()->findAllByAttributes(array("vod_asset_id" => $vod->id, "status" => 1, "stream_type" => 2));
			$streams = array();
			foreach($trailerStreams as $vodStream) {
				/* @var $vodStream VodStream */
				$streams = array_merge($streams, $vodStream->generateStreams($protocol));
			}
			foreach ($streams as $stream) {
				/* @var $stream VodStream */
				$node = $xmlDoc->createElement('trailer');
				$streamUrl = CUtils::getSecuredStreamUrl($stream,$this->msisdn);
				$node->appendChild($xmlDoc->createCDATASection($streamUrl));
				$trailers->appendChild($node);
			}
			$result->appendChild($trailers);
			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetEpisodes() {
		// dua vao vod_asset_id de get episode
		$vod_id = (isset($_REQUEST['vod']) && !empty($_REQUEST['vod']))?$_REQUEST['vod']:null;

		if ($vod_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}
		$vod = VodAsset::model()->findByPk($vod_id);
		/* @var $vod VodAsset */
		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$arrEpisodes = array();
			/* @var $vod VodAsset */
			$countEpisodes = 0;
			$criteria=new CDbCriteria;
			$criteria->distinct = true;
			$criteria->select=array('*');

			$criteria->addCondition('t.status = "1"');
			$criteria->addCondition('t.vod_asset_id = "'.$vod_id.'"');
			$criteria->order = "episode_order asc";
			$vodEpisodes = VodEpisode::model()->findAll($criteria);
			$countEpisodes = sizeof($vodEpisodes);

			$vodNode = $result->appendChild($xmlDoc->createElement("vod"));
			$vodNode->appendChild($xmlDoc->createAttribute("id"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['id'])));

			$cats = VodAsset::getVODCategories($vod['id']);

			/* @var $episode VodEpisode */
			foreach ($vodEpisodes as $episode) {
				if($episode->status==0) continue;
				$episodeNode = $result->appendChild($xmlDoc->createElement('episode'));
				$episodeNode->appendChild($xmlDoc->createAttribute('id'))
				->appendChild($xmlDoc->createTextNode($episode->id));
				$episodeNode->appendChild($xmlDoc->createAttribute('episode_number'))
				->appendChild($xmlDoc->createTextNode($episode->episode_order));
				$episodeNode->appendChild($xmlDoc->createAttribute("create_date"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->create_date)));
				$episodeNode->appendChild($xmlDoc->createAttribute("view_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->view_count)));
				$episodeNode->appendChild($xmlDoc->createAttribute("favorite_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->favorite_count)));
				$episodeNode->appendChild($xmlDoc->createAttribute("like_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->like_count)));
				$episodeNode->appendChild($xmlDoc->createAttribute("dislike_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->dislike_count)));
				$episodeNode->appendChild($xmlDoc->createAttribute("rating"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->rating)));
				$episodeNode->appendChild($xmlDoc->createAttribute("rating_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($episode->rating_count)));

				$episodeNode->appendChild($xmlDoc->createElement('name'))
				->appendChild($xmlDoc->createCDATASection($episode->display_name));

				$episodeNode->appendChild($xmlDoc->createElement('duration',CHtml::encode($episode->duration)));
				if (!empty($cats)) {
					$episodeNode->appendChild($xmlDoc->createElement('category'))
					->appendChild($xmlDoc->createCDATASection($cats[0]->display_name));
				}
				$image = $this->chooseVODPoster($vod['id']);
				if (!empty($image)) {
					$imageUrl = $image->url;
					if(strpos($imageUrl, 'http://') === FALSE) {
						$imageUrl = 'http://vfilm.vn/posters/'.$imageUrl;
					}
					$vodimage = $xmlDoc->createElement('image', CHtml::encode($imageUrl));
					$vodimage->appendChild($xmlDoc->createAttribute("w"))
					->appendChild($xmlDoc->createTextNode($image->width));
					$vodimage->appendChild($xmlDoc->createAttribute("h"))
					->appendChild($xmlDoc->createTextNode($image->height));
					$episodeNode->appendChild($vodimage);
				}
			}

			$xmlDoc->formatOutput = true;
			$vod->view_count++;
			$vod->update();
			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function getEpisodeStreamUrl($episode, $protocol) {
		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$result->appendChild($xmlDoc->createElement("episode"))
			->appendChild($xmlDoc->createAttribute("id"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($episode['id'])));

			$streams = array();
			$vodStreams = VodStream::model()->findAllByAttributes(array("vod_episode_id" => $episode->id, "status" => 1, "stream_type" => 1));
			foreach($vodStreams as $vodStream) {
				$streams = array_merge($streams, $vodStream->generateStreams($protocol));
			}

			if(count($streams) == 0) {
				$this->responseError(1,"ERROR_","stream of this episode not found.");
			}
			$arrStreamNode = $result->appendChild($xmlDoc->createElement('streams'));
			foreach ($streams as $stream) {
				$streamNode = $arrStreamNode->appendChild($xmlDoc->createElement('stream'));
				$streamUrl = CUtils::getSecuredStreamUrl($stream,$this->msisdn);
				$streamNode->appendChild($xmlDoc->createCDATASection($streamUrl));
			}

			$xmlDoc->formatOutput = true;
			$episode->view_count++;
			$episode->update();
			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetEpisodeStreamUrl() {
		$vod_episode_id = (isset($_REQUEST['episode']) && !empty($_REQUEST['episode']))?$_REQUEST['episode']:null;
		$protocol = (isset($_REQUEST['protocol']) && !empty($_REQUEST['protocol']))?$_REQUEST['protocol']:2;

		if ($vod_episode_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}
		$episode = VodEpisode::model()->findByPk($vod_episode_id);
		/* @var $episode VodEpisode */
		if ($episode == NULL) {
			$this->responseError(1,"ERROR_","Requested episode not found.");
		}
		$vod = VodAsset::model()->findByPk($episode->vod_asset_id);
		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}
		if($vod->is_free == 0) {
			$this->checkPermission($vod); //neu ko permission de xem thi responseError trong nay luon roi
		}
		$this->getEpisodeStreamUrl($episode, $protocol);
	}

	private function checkPermission($vod, $using_type = USING_TYPE_WATCH) {
		$noPermission = true;
		if($this->subscriber != NULL) {
			if($this->subscriber->isUsingService()) {
				$noPermission = false;
			}
			else if ($this->subscriber->hasVodAsset($vod, $using_type)){
				$noPermission = false;
			}
		}
		else {
			$this->responseError(1,1, "Subscriber ".$this->msisdn." is null.");
		}
		if($noPermission) {
			$vodName = $vod->display_name." (giá 2000đ)";
			$this->responseError(1,1, "Bạn chưa mua phim, bấm \"Mua\" để xem phim $vodName, hoặc đăng ký gói cước (10.000đ) để xem miễn phí tất cả các phim.");
		}
	}
	
	public function actionGetStreamUrl(){
		$vod_id = (isset($_REQUEST['vod']) && !empty($_REQUEST['vod']))?$_REQUEST['vod']:null;
		$protocol = (isset($_REQUEST['protocol']) && !empty($_REQUEST['protocol']))?$_REQUEST['protocol']:2;

		if ($vod_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}

		$vod = VodAsset::model()->findByPk($vod_id);
		/* @var $vod VodAsset */
		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}

		if($vod->is_free == 0) {
			$this->checkPermission($vod); //neu ko permission de xem thi responseError trong nay luon roi
		}
		
		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$result->appendChild($xmlDoc->createElement("vod"))
			->appendChild($xmlDoc->createAttribute("id"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['id'])));

			$streams = array();
			$vodStreams = VodStream::model()->findAllByAttributes(array("vod_asset_id" => $vod->id, "status" => 1, "stream_type" => 1));
			foreach($vodStreams as $vodStream) {
				$streams = array_merge($streams, $vodStream->generateStreams($protocol));
			}
			
			$token = '';
			if(($this->subscriber != NULL) && (count($vodStreams) > 0)) {
				$token = StreamingLog::saveGetLinkLog($this->subscriber->id,$vodStreams[0]['id'], CHANNEL_TYPE_APP);
			}
			
			foreach ($streams as $stream) {
				$streamNode = $result->appendChild($xmlDoc->createElement('stream'));
				$streamUrl = $stream."?token=$token";
// 				$streamUrl = CUtils::getSecuredStreamUrl($stream,$this->msisdn); lam theo ben ifilm
				$streamNode->appendChild($xmlDoc->createCDATASection($streamUrl));
			}
			
			
			$xmlDoc->formatOutput = true;
			$vod->view_count++;
			$vod->update();
			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetComments(){
		$vod_id = isset($_REQUEST['vod'])?$_REQUEST['vod']:null;
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;

		if ($vod_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}

		$vod = VodAsset::model()->findByPk($vod_id);
		/* @var $vod VodAsset */

		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$res = $vod->getComments($page, $page_size);

			$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
			$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
			$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
			$result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

			foreach ($res['data'] as $comment) {
				/* @var $comment VodComment */
				$commentNode = $xmlDoc->createElement('comment');
				$commentNode->appendChild($xmlDoc->createAttribute('id'))
				->appendChild($xmlDoc->createTextNode($comment->id));
				$commentNode->appendChild($xmlDoc->createAttribute('create_date'))
				->appendChild($xmlDoc->createTextNode($comment->create_date));
				$commentNode->appendChild($xmlDoc->createElement('poster'))
				->appendChild($xmlDoc->createCDATASection($comment->subscriber->user_name));
				$commentNode->appendChild($xmlDoc->createElement('title'))
				->appendChild($xmlDoc->createCDATASection($comment->title));
				$commentNode->appendChild($xmlDoc->createElement('content'))
				->appendChild($xmlDoc->createCDATASection($comment->comment));
				$result->appendChild($commentNode);
			}

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}

	}

	public function actionSendFeedback(){
		$vod_id = isset($_REQUEST['vod']) ? $_REQUEST['vod'] : null;
		if ($vod_id=="") $vod_id = null;
		$like = isset($_REQUEST['like']) ? $_REQUEST['like'] : null;
		if ($like=="") $like = null;
		$rating = isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null;
		if ($rating=="") $rating = null;
		$favorite = isset($_REQUEST['favorite']) ? $_REQUEST['favorite'] : null;
		if ($favorite=="") $favorite = null;
		$comment_title = isset($_REQUEST['comment_title']) ? $_REQUEST['comment_title'] : null;
		if ($comment_title=="") $comment_title = null;
		$comment_content = isset($_REQUEST['comment_content']) ? $_REQUEST['comment_content'] : null;
		if ($comment_content=="") $comment_content = null;

		$subscriber = $this->subscriber;
		if ($subscriber == NULL) {
			$this->responseError(1,1,"Xin vui lòng truy cập dịch vụ bằng 3G của VinaPhone hoặc đăng nhập bằng wifi");
			
		}

		if (is_null($like) && is_null($rating)
		&& is_null($favorite)&& is_null($comment_content)) {
			$this->responseError(1,"ERROR_","No actions specified!");
		}

		if ($vod_id === null) {
			$this->responseError(1,"ERROR_","No vod specified!");
		}

		if($vod_id < 0) {
			$vod_id = -$vod_id;
		}
		$vod = VodAsset::model()->findByPk($vod_id);
		/* @var $vod VodAsset */

		if (empty ($vod)) {
			$this->responseError(1,"ERROR_","Requested vod not found.");
		}

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$message = array();

			if (!is_null($like)) {
				$like = $like==0?0:1;
				// kiem tra da vote chua
				$old_val = VodLikeDislike::model()->findByAttributes(
						array(
								'subscriber_id' => $subscriber->id,
								'vod_asset_id' => $vod->id,
						));
				$new_val = new VodLikeDislike();
				$new_val->create_date = new CDbExpression('NOW()');
				$new_val->like = $like;
				$new_val->subscriber_id = $subscriber->id;
				$new_val->vod_asset_id = $vod_id;
				if ($new_val->like != 0) {
					$vod->like_count++;
					$tmp = 'Đã nhận phản hồi. Xin cảm ơn!';
				}
				else {
					$vod->dislike_count++;
					$tmp = 'Đã nhận phản hồi. Xin cảm ơn!';
				}
				if ($new_val->save()) {
					$vod->update();
					$message[] = $tmp;
				}
			}

			if (!is_null($rating)) {
				$rating = $rating % 6;
				// kiem tra da vote chua
				$old_val = VodRating::model()->findByAttributes(
						array(
								'subscriber_id' => $subscriber->id,
								'vod_asset_id' => $vod->id,
						));
				{
					$new_val = new VodRating();
					$new_val->create_date = new CDbExpression('NOW()');
					$new_val->rating = $rating;
					$new_val->subscriber_id = $subscriber->id;
					$new_val->vod_asset_id = $vod_id;
					$vod->rating = ($vod->rating*$vod->rating_count + $rating)/($vod->rating_count +1);
					$vod->rating_count++;

					if ($new_val->save()) {
						$vod->update();
						$message[] = "Bạn đã chấm $rating sao cho phim này. Xin cảm ơn!";
					}
				}
			}

			if (!is_null($favorite)) {
				// kiem tra da vote chua
				$old_val = VodSubscriberFavorite::model()->findByAttributes(
						array(
								'subscriber_id' => $subscriber->id,
								'vod_asset_id' => $vod->id,
						));
				if ($old_val != null) {
					if ($favorite!=0) {
						$message[] = "Phim đã có trong danh sách yêu thích!";
					}
					else {
						$vod->favorite_count--;
						if ($old_val->delete()) {
							$vod->update();
							$message[] = "Phim đã được xóa khỏi danh sách yêu thích!"; ;
						}
					}
				}
				else {
					if ($favorite!=0) {
						$new_val = new VodSubscriberFavorite();
						$new_val->create_date = new CDbExpression('NOW()');
						$new_val->subscriber_id = $subscriber->id;
						$new_val->vod_asset_id = $vod_id;
						$vod->favorite_count++;

						if ($new_val->save()) {
							$vod->update();
							$message[] = "Successfully add to your favorite!";
						}
					}
					else {
						$message[] = "Phim này không còn trong danh sách yêu thích!";
					}
				}
			}

			if (!is_null($comment_content)) {
				$new_val = new VodComment();
				$new_val->create_date = new CDbExpression('NOW()');
				$new_val->subscriber_id = $subscriber->id;
				$new_val->vod_asset_id = $vod_id;
				$new_val->comment = $comment_content;
				$new_val->title = $comment_title;
				$vod->comment_count++;

				if ($new_val->save()) {
					$vod->update();
					$message[] = "Your comment saved!";
				}
				else {
					$this->responseError(1,"ERROR_","Unable to save your comment");
				}
			}

			$msgs = $result->appendChild($xmlDoc->createElement("messages"));
			foreach ($message as $msg) {
				$msgs->appendChild($xmlDoc->createElement("message"))
				->appendChild($xmlDoc->createCDATASection($msg));
			}

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}
	}

	public function actionGetAutoComplete(){
		$currentInput = isset($_REQUEST['token'])?$_REQUEST['token']:null;
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;

		$subscriber = $this->subscriber;
		if ($subscriber == NULL) {
			$this->responseError(1,1,"Xin vui lòng truy cập dịch vụ bằng 3G của VinaPhone hoặc đăng nhập bằng wifi");
		}

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			/* @var $subscriber Subscriber */
			$res = $subscriber->getVodAutoComplete($page, $page_size, $currentInput);

			$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
			$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
			$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
			$result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

			foreach ($res['data'] as $word) {
				/* @var $word VodSearchHistory */
				$node = $xmlDoc->createElement('keyword');
				$node->appendChild($xmlDoc->createAttribute('count'))
				->appendChild($xmlDoc->createTextNode($word->hit_count));
				$node->appendChild($xmlDoc->createCDATASection($word->keyword));
				$result->appendChild($node);
			}

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}

	}

	public function actionGetFavAssets(){
		if (isset($_REQUEST['tablet']) && ($_REQUEST['tablet']==1)) {
			$this->_isTablet = 1;
		}

		$page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
		$page_size = isset($_REQUEST['page_size'])?$_REQUEST['page_size']:10;
		$image_width = isset($_REQUEST['image_width'])?$_REQUEST['image_width']:null;

		$subscriber = $this->subscriber;
		if ($subscriber == null) {
			$this->responseError(1,1,"Xin vui lòng truy cập dịch vụ bằng 3G của VinaPhone hoặc đăng nhập bằng wifi");
		}

		$db_order = 't.modify_date DESC';

		if ($this->_format === 'xml') {
			header("Content-type: text/xml; charset=utf-8");
			$xmlDoc = new DOMDocument();
			$xmlDoc->encoding = "UTF-8";
			$xmlDoc->version = "1.0";

			//TODO: authen, session, error handle
			$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
			$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
			$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
			$root->appendChild($xmlDoc->createElement("action", $this->action->id));
			$root->appendChild($xmlDoc->createElement("error_no", "0"));
			$result = $root->appendChild($xmlDoc->createElement("result"));

			$res = $subscriber->getFavoritedVods($page, $page_size);

			$result->appendChild($xmlDoc->createElement('page_number', CHtml::encode($res['page_number'])));
			$result->appendChild($xmlDoc->createElement('page_size', CHtml::encode($res['page_size'])));
			$result->appendChild($xmlDoc->createElement('total_page', CHtml::encode($res['total_page'])));
			$result->appendChild($xmlDoc->createElement('total_result', CHtml::encode($res['total_result'])));

			foreach ($res['data'] as $vod) {
				/* @var $vod VodAsset */
				$vodnode = $xmlDoc->createElement("vod");
				$vodnode->appendChild($xmlDoc->createAttribute("id"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['id'])));

				$vodnode->appendChild($xmlDoc->createAttribute("create_date"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['create_date'])));
				$vodnode->appendChild($xmlDoc->createAttribute("view_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['view_count'])));
				$vodnode->appendChild($xmlDoc->createAttribute("favorite_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['favorite_count'])));
				$vodnode->appendChild($xmlDoc->createAttribute("comment_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['comment_count'])));
				$vodnode->appendChild($xmlDoc->createAttribute("like_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['like_count'])));
				$vodnode->appendChild($xmlDoc->createAttribute("dislike_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['dislike_count'])));
				$vodnode->appendChild($xmlDoc->createAttribute("rating"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['rating'])));
				$vodnode->appendChild($xmlDoc->createAttribute("rating_count"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['rating_count'])));
				//$vodnode->appendChild($xmlDoc->createElement('name',CHtml::encode($vod->display_name)));

				$vodnode->appendChild($xmlDoc->createElement('name'))
				->appendChild($xmlDoc->createCDATASection($vod['display_name']));

				$vodnode->appendChild($xmlDoc->createElement('duration',CHtml::encode($vod['duration'])));
				$vodnode->appendChild($xmlDoc->createElement('add_favorite_date',CHtml::encode($vod['favorite_date'])));

				$cats = VodAsset::getVODCategories($vod['id']);
				if (!empty($cats)) {
					$vodnode->appendChild($xmlDoc->createElement('category'))
					->appendChild($xmlDoc->createCDATASection($cats[0]->display_name));
				}

				$image = $this->chooseVODPoster($vod['id']);
				if (!empty($image)) {
					$imageUrl = $image->url;
					if(strpos($imageUrl, 'http://') === FALSE) {
						$imageUrl = 'http://vfilm.vn/posters/'.$imageUrl;
					}
					$vodimage = $xmlDoc->createElement('image', CHtml::encode($imageUrl));
					$vodimage->appendChild($xmlDoc->createAttribute("w"))
					->appendChild($xmlDoc->createTextNode($image->width));
					$vodimage->appendChild($xmlDoc->createAttribute("h"))
					->appendChild($xmlDoc->createTextNode($image->height));
					$vodnode->appendChild($vodimage);
				}

				$result->appendChild($vodnode);
			}

			$xmlDoc->formatOutput = true;

			$content = $xmlDoc->saveXML();

			echo $content;
		}
		else {
			//TODO
			$this->responseError(1,"ERROR_","not supported output format");
		}

	}

	/**
	 *
	 * @param type $error_no
	 * @param type $error_code
	 * @param type $message
	 */
	public function responseError($error_no, $error_code, $message) {
		header("Content-type: text/xml; charset=utf-8");
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = "UTF-8";
		$xmlDoc->version = "1.0";

		//TODO: authen, session, error handle
		$root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
		$root->appendChild($xmlDoc->createElement("phone_number", $this->msisdn));
		$root->appendChild($xmlDoc->createElement("session", $this->_sessionID?$this->_sessionID:""));
		$root->appendChild($xmlDoc->createElement("action", $this->action->id));
		$root->appendChild($xmlDoc->createElement("error_no", $error_no));
		$root->appendChild($xmlDoc->createElement("error_code", $error_code));
		$root->appendChild($xmlDoc->createElement("error_message", CHtml::encode($message)));

		echo $xmlDoc->saveXML();
		Yii::app()->end();
	}

	private function createVodGroup($order, $resultParent, $xmlDoc, $posterOrientation = 1) {
		$result = $resultParent->appendChild($xmlDoc->createElement("group"));
		$db_order = "";
		$order_name = "";
		$cat_id = null;
		switch($order)
		{
			case "top_new":
				$db_order = "t.modify_date DESC, t.code_name";
				$order_name = 'Mới nhất';
				$more_params = "order=top_new";
				break;
			case "top_rated":
				$db_order = "t.modify_date DESC, t.code_name";
				$cat_id=15;
				$more_params = "category=15";
				$order_name = 'Phim tuyển chọn';
				break;
			case "top_kinhdien":
				$db_order = "t.modify_date DESC, t.code_name";
				$cat_id = 33;
				$more_params = "category=33";
				$order_name = 'Phim kinh điển';
				break;
			case "top_free" ://case "default":
				$db_order = "t.modify_date DESC, t.code_name";
				$order_name = 'Phim miễn phí';
				$cat_id = 27;
				$more_params = "category=27";
				//                $db_order = "t.modify_date DESC"; // tam thoi comment lai, sap xep theo ten phim
				$db_order = "t.modify_date DESC, t.honor, t.code_name";
				break;
			case "most_viewed":
				$db_order = 't.view_count DESC';
				$order_name = 'Xem nhiều nhất';
				$more_params = "order=most_viewed";
				break;
		}

		$result->appendChild($xmlDoc->createAttribute("more_params"))
		->appendChild($xmlDoc->createTextNode(CHtml::encode($more_params)));
		$result->appendChild($xmlDoc->createAttribute("name"))
		->appendChild($xmlDoc->createTextNode(CHtml::encode($order_name)));
		//echo $this->_format;

		$page = 0;
		$page_size = 6;
		$keyword = '';
		$res = VodAsset::findVODs($cat_id, $db_order, $page, $page_size, $keyword);

		$result = $this->createXmlVodList($res, $xmlDoc, $result, $posterOrientation);
		return $result;
	}

	private function createXmlVodList($res, $xmlDoc, $result, $posterOrientation = 1) {
		$listAssets = array();
		$assetCount = 0;
		$listAssets = $res['data'];
		$assetCount = $res['total_result'];

		foreach ($listAssets as $vod) {
			/* @var $vod VodAsset */
			$vodnode = $xmlDoc->createElement("vod");
			$vodnode->appendChild($xmlDoc->createAttribute("id"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['id'])));
			if($vod['id'] > 0) {
				$vodnode->appendChild($xmlDoc->createAttribute("is_serie"))
				->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['is_series'])));
				if($vod['is_series'] == 1) {
					$vodnode->appendChild($xmlDoc->createAttribute("episode_count"))
					->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['episode_count'])));
					$info = VodAsset::model()->getDetailInfoOfSerie($vod['id']);
					$vod['view_count'] = $info['view_count'];
					$vod['like_count'] = $info['like_count'];
					$vod['dislike_count'] = $info['dislike_count'];
				}
			}

			$vodnode->appendChild($xmlDoc->createAttribute("create_date"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['create_date'])));
			$vodnode->appendChild($xmlDoc->createAttribute("view_count"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['view_count'])));
			$vodnode->appendChild($xmlDoc->createAttribute("favorite_count"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['favorite_count'])));
			//                $vodnode->appendChild($xmlDoc->createAttribute("comment_count"))
			//                        ->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['comment_count'])));
			$vodnode->appendChild($xmlDoc->createAttribute("like_count"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['like_count'])));
			$vodnode->appendChild($xmlDoc->createAttribute("dislike_count"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['dislike_count'])));
			$vodnode->appendChild($xmlDoc->createAttribute("rating"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['rating'])));
			$vodnode->appendChild($xmlDoc->createAttribute("rating_count"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['rating_count'])));
			
			$price = 0;
			if($vod['is_free'] == 0) {
				$price = $vod['price'];
				if($this->subscriber != NULL) {
					if($this->subscriber->isUsingService() == true) {
						$price = 0;
					}
				}
			}
			$vodnode->appendChild($xmlDoc->createAttribute("watching_price"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($price)));
			$vodnode->appendChild($xmlDoc->createAttribute("is_free"))
			->appendChild($xmlDoc->createTextNode(CHtml::encode($vod['is_free'])));

			$vodnode->appendChild($xmlDoc->createElement('name'))
			->appendChild($xmlDoc->createCDATASection($vod['display_name']));
			$vodnode->appendChild($xmlDoc->createElement('original_title'))
			->appendChild($xmlDoc->createCDATASection($vod['original_title']));

			$vodnode->appendChild($xmlDoc->createElement('duration',CHtml::encode($vod['duration'])));

			$cats = VodAsset::getVODCategories($vod['id']);
			if (!empty($cats)) {
				$vodnode->appendChild($xmlDoc->createElement('category'))
				->appendChild($xmlDoc->createCDATASection($cats[0]->display_name));
			}

			$image = $this->chooseVODPoster($vod['id'], $posterOrientation);

			if (!empty($image)) {
				$imageUrl = $image->url;
				if(strpos($imageUrl, 'http://') === FALSE) {
					$imageUrl = 'http://vfilm.vn/posters/'.$imageUrl;
				}
				$vodimage = $xmlDoc->createElement('image', CHtml::encode($imageUrl));
				$vodimage->appendChild($xmlDoc->createAttribute("w"))
				->appendChild($xmlDoc->createTextNode($image->width));
				$vodimage->appendChild($xmlDoc->createAttribute("h"))
				->appendChild($xmlDoc->createTextNode($image->height));
				$vodnode->appendChild($vodimage);
			}

			$result->appendChild($vodnode);
		}
		return $result;
	}
	
	public function actionPayVod() {
		$vod_id = isset($_REQUEST['vod'])?$_REQUEST['vod']:-1;
		$vod = VodAsset::model()->findByPk($vod_id);
		if($vod == NULL) {
			$this->responseError(1,1, "Vod $vod_id is not existed");
		}
		
		if($this->subscriber == NULL) {
			$this->responseError(1,1, "Không nhận diện được thuê bao");
		}

		$payVodStatus = ChargingProxy::processPayVod(CHANNEL_TYPE_APP, $vod, $this->subscriber);
		if($payVodStatus == ChargingProxy::PROCESS_ERROR_NONE) {
			$this->responseError(0, 0, "Mua phim thành công");
		}
		else {
			$this->responseError($payVodStatus, $payVodStatus, ChargingProxy::getNameErrorCode($payVodStatus));
		}
	}
}
