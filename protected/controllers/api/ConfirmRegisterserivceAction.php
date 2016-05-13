<?php

/**
 * Created by JetBrains PhpStorm.
 * User: hungld
 * Date: 1/7/15
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
class ConfirmRegisterserivceAction extends CAction
{
    public function run()
    {
        header('Content-type: application/json');
        $serviceId = isset($_POST['service_id']) ? $_POST['service_id'] : null;

        if ($serviceId == null) {
            echo json_encode(array('code' => 5, 'message' => 'Missing params service_id'));
            return;
        }
        $service = Service::model()->findByPk($serviceId);
        if (count($service) > 0) {
            echo json_encode(array(
                'code' => 0,
                'service_id' => $serviceId,
                'service_expiry_date' => date('Y-m-d', (time() + 60 * 60 * 24 * $service['using_days'])),
            ));
            return;
        }else{
            echo json_encode(array('code' => 1, 'message' => 'service_id is not exist'));
            return;
        }
    }
}