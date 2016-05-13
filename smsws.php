<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
  include 'lib/nusoap.php';
    $servername = "123.30.200.84";
    $username = "hocde";
    $password = "hocde@2015";
    // Create connection
    $conn = mysql_connect($servername, $username, $password);   
    // Check connection
    mysql_select_db('hocde', $conn) or die('Could not select database.');
  $server = new soap_server();
  $server->configureWSDL('hocde', 'urn:hocde');
  function Sms_hocde($userId, $smsShortNumber, $mobileNumber, $orderId, $signature, $SMSvalue, $errcode, $returntext)
  {
    if($userId == '' || empty($userId)){
        return array( FALSE,'01','userId rong.');
    }
    if($userId !=''){
        $query = "select * from subscriber where username = '$userId'";
        $result = mysql_num_rows(mysql_query($query));
        if($result == 0){
            return array(FALSE,'02', 'Tai khoan khong tai.');
        }  
    }
    if($mobileNumber =='' || empty($userId)){
        return array(FALSE,'03', 'So dien thoai khong hop le.'); 
    }
    if($orderId =='' || empty($orderId)){
        return array(FALSE, '04', 'orderId khong ton tai.'); 
    }
    $signature_check =MD5($orderId.$userId.'9029'.$mobileNumber.$SMSvalue);
    if($signature_check != $signature){
        return array(FALSE, '05', 'chu ky khong hop le.'); 
    }
    $time = date('Y-m-d H:i:s');
    $oncash = '';
    switch (intval($SMSvalue)){
        case '5000': $oncash = '20';            break;
        case '10000': $oncash = '50';            break;
        case '15000': $oncash = '80';            break;
        case '20000': $oncash = '100';            break;
        case '30000': $oncash = '160';            break;
        case '40000': $oncash = '210';            break;
        case '50000': $oncash = '270';            break;
        case '100000': $oncash = '550';            break;
    }
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
        if($row){
            $subscriber_id = $row['id'];
            $partner_id = $row['partner_id'];
            $fcoin = $row['fcoin'];
        }
    }
    $query_insert = "INSERT INTO subscriber_transaction (create_date, status, description, subscriber_id,purchase_type,cost, oncash, partner_id, channel_type) VALUES ('$time', '1', 'Giao dich thanh cong', '$subscriber_id', '1', '$SMSvalue', '$oncash', '$partner_id', 'SMS')";
    $retval = mysql_query($query_insert);
    $money = $oncash+$fcoin;
    $query_insert_sub = "update subscriber set fcoin = $money where id = $subscriber_id";
    $retval = mysql_query($query_insert_sub);
    $description = "Ban da nap thanh cong $SMSvalue VND vao tai khoan $userId. Xin cam on. Chuc ban thanh cong voi hocde.";
    return array(TRUE, '00',$description);
  }
  $server->register('Sms_hocde', array('userId' => 'xsd:string','smsShortNumber' => 'xsd:string','mobileNumber' => 'xsd:string','orderId' => 'xsd:string','signature' => 'xsd:string','SMSvalue' => 'xsd:string','errcode' => 'xsd:string','returntext' => 'xsd:string'), array('Response' => 'xsd:boolean', 'Errcode' => 'xsd:string', 'Returntext'=> 'xsd:string'));
  $query = isset($HTTP_RAW_POST_DATA)? $HTTP_RAW_POST_DATA : '';
  $server->service($query);
?>
