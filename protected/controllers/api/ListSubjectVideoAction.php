<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class ListSubjectVideoAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $arrSubject = SubjectVideo::model()->findAll();
        $content = array();
        foreach($arrSubject as $i => $item){
            $content[$i]['id'] = $item->id;
            $content[$i]['subject_name'] = $item->subject_name;
        }
        echo json_encode(array('code' => 0, 'items' => $content));
    }
}
?>
