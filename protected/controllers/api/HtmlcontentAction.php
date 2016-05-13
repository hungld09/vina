<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class HtmlcontentAction extends CAction{
    public function run(){
        header('Content-type: application/json');
        $params = $_POST;
        $type = isset($params['type']) ? $params['type'] : '';
        if($type == ''){
            echo json_encode(array('code' => 5, 'message' => 'Missing params type'));
            return;
        }
        if($type == 6){
            $html = HtmlContent::model()->findAllByAttributes(array('type' => $type));
            if(count($html) == 0){
                echo json_encode(array('code' => 5, 'message' => 'Không có dữ liệu'));
                return;
            }
            $arrNews = array();
            for ($j= 0; $j < count($html); $j++){
                $arrNews[$j]['id'] = $html[$j]['id'];
                $arrNews[$j]['title'] = $html[$j]['title'];
            }
            echo json_encode(array('code'=> 0 , 'items' => $arrNews));
            return;
        }elseif($type == 7){
            $id = isset($params['id']) ? $params['id'] : '';
            if($id == ''){
                echo json_encode(array('code' => 5, 'message' => 'Missing params id'));
                return;
            }
            $html = HtmlContent::model()->findByPk($id);
            $content['title'] = '<h3>'. $html->title .'</h3>';
            $content['content'] = $html->content;
            echo json_encode(array('code'=> 0 , 'Class'=> array('title'=>$content['title'], 'items'=>$content)));
            return;

        }else{
            if($type != 8){
                $html = HtmlContent::model()->findByAttributes(array('type' => $type));
                if(count($html) == 0){
                    echo json_encode(array('code' => 5, 'message' => 'Không có dữ liệu'));
                    return;
                }
            }
            $content = array();
            if($type == 1){
                $content['title'] = '<h3>Hướng Dẫn</h3>';
            }elseif($type == 2){
                $content['title'] = '<h3>Mô tả mệnh giá</h3>';
            }elseif($type == 3){
                $content['title'] = '<h3>Mô tả gói</h3>';
            }elseif($type == 4){
                $content['title'] = '<h3>Mô tả KM</h3>';
            }elseif($type == 5){
                $content['title'] = '<h3>Hướng dẫn giáo viên</h3>';
            }elseif($type == 8){
                $content['title'] = '<h3>Quy đổi sms</h3>';
            }elseif($type == 9){
                $content['title'] = '';
            }
            if($type == 8){
                $user_name = isset($params['user_name']) ? $params['user_name'] : '';
//                if($user_name == ''){
//                    echo json_encode(array('code' => 5, 'message' => 'Missing params user_name'));
//                    return;
//                }
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 5.000 VNĐ = 20 OnCash',
                    'syntax_1' => 'BX HD Nap5 '.$user_name,
                    'syntax_2' => 'QK 5000 HD '.$user_name.' 20OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 10.000 VNĐ = 50 OnCash',
                    'syntax_1' => 'BX HD Nap10 '.$user_name,
                    'syntax_2' => 'QK 10000 HD '.$user_name.' 50OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 15.000 VNĐ = 80 OnCash',
                    'syntax_1' => 'BX HD Nap15 '.$user_name,
                    'syntax_2' => 'QK 15000 HD '.$user_name.' 80OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 20.000 VNĐ = 100 OnCash',
                    'syntax_1' => 'BX HD Nap20 '.$user_name,
                    'syntax_2' => 'QK 20000 HD '.$user_name.' 100OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 30.000 VNĐ = 160 OnCash',
                    'syntax_1' => 'BX HD Nap30 '.$user_name,
                    'syntax_2' => 'QK 30000 HD '.$user_name.' 160OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 40.000 VNĐ = 210 OnCash',
                    'syntax_1' => 'BX HD Nap40 '.$user_name,
                    'syntax_2' => 'QK 40000 HD '.$user_name.' 210OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 50.000 VNĐ = 270 OnCash',
                    'syntax_1' => 'BX HD Nap50 '.$user_name,
                    'syntax_2' => 'QK 50000 HD '.$user_name.' 270OnCash HOCDE',
                );
                $content['content'][] = array(
                    'message' => 'Mệnh giá : 100.000 VNĐ = 550 OnCash',
                    'syntax_1' => 'BX HD Nap100 '.$user_name,
                    'syntax_2' => 'QK 100000 HD '.$user_name.' 550OnCash HOCDE',
                );
            }else{
                $content['content'] = $html->content;
            }
            echo json_encode(array('code'=> 0 , 'Class'=> array('title'=>$content['title'], 'items'=>$content)));
            return;
        }
    }
}