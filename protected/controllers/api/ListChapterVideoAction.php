<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class ListChapterVideoAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $subject_id = isset($params['subject_id']) ? $params['subject_id'] : 0;
        $criteria = new CDbCriteria();
        $criteria->compare('subject_id',$subject_id);
        $arrChapter = ChapterVideo::model()->findAll($criteria);
        $content = array();
        foreach($arrChapter as $i => $item){
            $content[$i]['id'] = $item->id;
            $content[$i]['chapter_name'] = $item->chapter_name;
        }
        echo json_encode(array('code' => 0, 'items' => $content));
    }
}
?>
