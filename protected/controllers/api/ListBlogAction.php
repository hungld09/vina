<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dangtd
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 *///
class ListBlogAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $params = $_POST;
        $category_id = isset($params['category_id']) ? $params['category_id'] : 0;
        $page = isset($params['page']) ? $params['page'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;
        if (isset($params['page'])) {
            $offset = $page_size * $page;
        } else {
            $offset = 0;
        }
        if($category_id == 0){
            echo json_encode(array('code' => 5, 'message' => 'Missing params category_id'));
            return;
        }
        $criteria = new CDbCriteria;
        $criteria->compare('category_id',$category_id);
        $criteria->compare('status',1);
        $criteria->order = 'id desc';
        $criteria->limit = $page_size;
        $criteria->offset = $offset;
        $arrBlog = Blog::model()->findAll($criteria);
        $content = array();
        foreach($arrBlog as $i => $item){
            $content[$i]['id'] = $item->id;
            $content[$i]['title'] = $item->title;
            $content[$i]['url_images'] = IPSERVER.'web/uploads/'.$item['image_url'];
        }
        echo json_encode(array('code'=> 0, 'items'=>$content, 'title'=> 'Kết quả tìm kiếm'));
    }
}
?>
