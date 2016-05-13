<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class ListVideoAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $subject_id = isset($params['subject_id']) ? $params['subject_id'] : 0;
        $chapter_id = isset($params['chapter_id']) ? $params['chapter_id'] : 0;

        $page = isset($params['page_number']) ? $params['page_number'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;
        if (isset($params['page_number'])) {
            $offset = $page_size * $page;
        } else {
            $offset = 0;
        }

        $criteria = new CDbCriteria();
        if($subject_id > 0){
            $criteria->compare('subject_id',$subject_id);
        }
        if($chapter_id > 0){
            $criteria->compare('chapter_id',$chapter_id);
        }
        $criteria->order = 'id desc';
        $criteria->limit = $page_size;
        $criteria->offset = $offset;
        $arrVideo = Video::model()->findAll($criteria);
        $content = array();
        foreach($arrVideo as $i => $item){
            $content[$i]['id'] = $item->id;
            $content[$i]['title'] = $item->title;
            $content[$i]['image_thump'] = $item->image_thump;
            $content[$i]['url_video'] = 'https://www.youtube.com/watch?v='.$item->url_video;
        }
        echo json_encode(array('code' => 0, 'items' => $content));
    }
}
?>
