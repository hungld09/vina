<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class SearchQuestionAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $subject= array();
        $chapter= array();
        $unit= array();
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
        $subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;
        $chapter_id = isset($_GET['chapter_id']) ? $_GET['chapter_id'] : null;
        $connection = Yii::app()->db2;
        $query = 'select id, class_name from class where status = 1';
        $command = $connection->createCommand($query);
        $class = $command->queryAll();
        //subject
        if($class_id != null && $subject_id == null){
            $querySubject = 'select id, subject_name, class_id from subject_category where status = 1 and class_id = '.$class_id.'';
            $command = $connection->createCommand($querySubject);
            $subject = $command->queryAll(); 
        }
        if($class_id != null && $subject_id != null && $chapter_id == null){
            $queryChapter = 'select id, chapter_name, chapter_name_ascii, class_id, subject_id from chapter where class_id = '.$class_id.' and subject_id = '.$subject_id.'';
            $command = $connection->createCommand($queryChapter);
            $chapter = $command->queryAll(); 
        }
        if($chapter_id != null){
            $queryUnit = 'select id, title, class_id, subject_id, chapter_id from bai where class_id = '.$class_id.' and subject_id = '.$subject_id.' and chapter_id = '.$chapter_id.'';
            $command = $connection->createCommand($queryUnit);
            $unit = $command->queryAll(); 
        }
        $queryTag = 'select id, name from question_tag';
        $command = $connection->createCommand($queryTag);
        $tag = $command->queryAll(); 
        echo json_encode(array('code'=> 0, 'Class'=> array('title'=> 'Lớp','items'=>$class), 'Subject'=>array('title'=>'Môn', 'items'=>$subject), 'Chapter'=>array('title'=>'Chương', 'items'=>$chapter), 'Unit'=>array('title'=>'Bài', 'items'=>$unit), 'Tag'=>array('title'=>'Tag', 'items'=>$tag)));
    }
}