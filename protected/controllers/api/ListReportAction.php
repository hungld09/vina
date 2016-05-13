<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 09/01/16
 * Time: 16:00 PM
 * To change this template use File | Settings | File Templates.
 */

class ListReportAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $report1 = array(
            'id'=>1,
            'title'=>'Câu hỏi không phù hợp với mức độ',
        );
        $report2 = array(
            'id'=>2,
            'title'=>'Nhiều câu trong một ảnh',
        );
        $report3 = array(
            'id'=>3,
            'title'=>'Câu hỏi sai chuyên môn',
        );
        $report4 = array(
            'id'=>4,
            'title'=>'Câu hỏi chứa nội dung không phù hợp',
        );
        $report5 = array();
        array_push($report5, $report1);
        array_push($report5, $report2);
        array_push($report5, $report3);
        array_push($report5, $report4);
        echo json_encode(array('code'=> 0, 'Report'=> array('title'=> 'Report','items'=>$report5)));
    }
}