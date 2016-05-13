<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class ListQuestionBankAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $title = isset($_GET['namequestion']) ? $_GET['namequestion'] : '';
        $class_id = $_GET['class_id'];
        $subject_id = $_GET['subject_id'];
        $chapter_id = $_GET['chapter_id'];
        $unit_id = isset($_GET['unit_id']) ? $_GET['unit_id'] : 0;
        $tag_id = isset($_GET['tag_id']) ? $_GET['tag_id'] : 0;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : 10;
        if (isset($_GET['page'])) {
            $offset = $page_size * $page;
        } else {
            $offset = 0;
        }
        if($class_id == 0){
            echo json_encode(array('code' => 5, 'message' => 'Missing params class_id'));
            return;
        }
        if($subject_id == 0){
            echo json_encode(array('code' => 5, 'message' => 'Missing params subject_id'));
            return;
        }
        if($chapter_id == 0){
            echo json_encode(array('code' => 5, 'message' => 'Missing params chapter_id'));
            return;
        }
        $arTagId = explode(',',$tag_id);
        $arQuestionId = array();
        foreach($arTagId as $item){
            $mappingQuesTag = TagQuestionMapping::model()->findAllByAttributes(array('tag_id' => $item));
            if(count($mappingQuesTag) > 0){
                foreach($mappingQuesTag as $ite){
                    $arQuestionId[] = $ite->question_id;
                }
            }
        }
        $criteria = new CDbCriteria;
        $criteria->compare('class_id',$class_id);
        $criteria->compare('subject_id',$subject_id);
        $criteria->compare('chapter_id',$chapter_id);
        if(count($arQuestionId) > 0){
            $criteria->addInCondition("id", $arQuestionId);
        }
        if($title != ''){
            $title = CVietnameseTools::removeSigns($title);
            $criteria->compare('question_ascii',$title,true);
        }
        if($unit_id > 0){
            $criteria->compare('bai_id',$unit_id);
        }
        $criteria->addCondition("status != 2");
//        $criteria->compare('status',1);
        $criteria->limit = $page_size;
        $criteria->offset = $offset;
        $result = QuestionLib::model()->findAll($criteria);
        $model = array();
        foreach($result as $i => $item){
            $model[$i]['id'] = $item->id;
            $model[$i]['question'] = $item->question;
        }
        echo json_encode(array('code'=> 0, 'items'=>$model, 'title'=> 'Kết quả tìm kiếm'));
    }
}
?>
