<?php

/*
 * Response object charging
*/

class VCGWResponse
{
    public $request_id;
    public $requestid;
    public $name;
    public $extra_information;
    public $error;
    public $return;
    public $msisdn;
    public $error_desc = "";
    public $price;
    public $promotion;
    public $note = "";
}

class VCGW
{
    protected $chargingUrl;
    protected $chargingVasprovisioning;
    protected $name;
    protected $user;
    protected $pass;
    protected $ch;

    function __construct($chargingUrl = '', $name = '', $user = '', $pass = '')
    {

        $chargingConfig = Yii::app()->params['chargingProxy'];
        $chargingVasConfig = Yii::app()->params['chargingProxy_test'];

        $this->chargingUrl = $chargingUrl != '' ? $chargingUrl : $chargingConfig['url'];
        $this->chargingVasprovisioning = $chargingUrl != '' ? $chargingUrl : $chargingVasConfig['url'];
        $this->name = $name != '' ? $name : $chargingConfig['name'];
        $this->user = $user != '' ? $user : $chargingConfig['user'];
        $this->pass = $pass != '' ? $pass : $chargingConfig['pass'];

        $this->ch = new MyCurl();
        $this->ch->headers = array('Content-Type: text/xml');
    }

    /*
     * Charging normal
    * @param: int $transaction_id (Id cua giao dich)
    * @param: string $msisdn (So dien thoai vinaphone)
    * @param: int $debit_amount (So tien thanh toan)
    * @param: int $original_amount (gia goc)
    * @param: boolean $promotion (co khuyen mai ko)
    * @param: int $command (register hoac mua phim le)
    * @return: object charging response
    */
    function debitAccount($transaction_id, $msisdn, $have_promotion, $debit_amount, $original_price, $reason, $note = "", $channel = CHANNEL_TYPE_WAP)
    {
        if ($have_promotion) {
            $promotion = 1;
        } else {
            $promotion = 0;
        }

        $xml_data = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><VAS request_id="' . $transaction_id . '"><REQ name="' . $this->name . '" user="' . $this->user . '" password="' . $this->pass . '"><SUBSCRIBER><SUBID>' . $msisdn . '</SUBID><PRICE>' . $debit_amount . '</PRICE><REASON>' . $reason . '</REASON><ORIGINALPRICE>' . $original_price . '</ORIGINALPRICE><PROMOTION>' . $promotion . '</PROMOTION><NOTE>' . $note . '</NOTE><CHANNEL>' . $channel . '</CHANNEL></SUBSCRIBER></REQ></VAS>';
        Yii::log("debug REQUEST:" . $xml_data);
        $response = $this->ch->post($this->chargingUrl, $xml_data);
        Yii::log("debug RESPONSE:\n" . $response . "\n");
        return $this->getResponse($response);
    }

    function debitAccount2($user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $reason, $trial = 0, $bundle = 0, $note = "", $channel = CHANNEL_TYPE_WAP)
    {
        Yii::log("debug VCWG:debitAccount2 msisdn = $msisdn\n");
        $requestid = Date('ymdHis');
        Yii::log("debug VCWG:debitAccount2 requestId = $requestid\n");
        if ($reason == ChargingProxy::CHARGING_REGISTER) {
            $subscribe = 'subscribe';
        } elseif ($reason == ChargingProxy::CHARGING_CANCEL) {
            $subscribe = 'unsubscribe';
        }
        $service = 'HD';
        $package = 'HD';
        $application = 'CP_SCT';
        $policy = '';
        Yii::log("debug Start xml data param  = $user_name, $user_ip, $transaction_id, $msisdn, $promotion, $debit_amount, $original_price, $reason, $trial, $bundle , $note, $channel, $subscribe\n");
        if ($reason == ChargingProxy::CHARGING_REGISTER) {
            $xml_data = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><RQST><name>' . $subscribe . '</name><requestid>' . $transaction_id . '</requestid><msisdn>' . $msisdn . '</msisdn><service>' . $service . '</service><package>' . $package . '</package><promotion>' . $promotion . '</promotion><trial>' . $trial . '</trial><bundle>' . $bundle . '</bundle><note>' . $note . '</note><application>' . $application . '</application><channel>' . $channel . '</channel><username>' . $user_name . '</username><userip>' . $user_ip . '</userip></RQST>';
        } elseif ($reason == ChargingProxy::CHARGING_CANCEL) {
            $xml_data = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><RQST><name>' . $subscribe . '</name><requestid>' . $transaction_id . '</requestid><msisdn>' . $msisdn . '</msisdn><service>' . $service . '</service><package>' . $package . '</package><policy>' . $policy . '</policy><note>' . $note . '</note><application>' . $application . '</application><channel>' . $channel . '</channel><username>' . $user_name . '</username><userip>' . $user_ip . '</userip></RQST>';
        }
        Yii::log("debug REQUEST: " . $xml_data);
        $response = $this->ch->post($this->chargingVasprovisioning, $xml_data);
        Yii::log("debug RESPONSE: " . $response);
        return $this->getResponse2($response);
    }


    /*
     * Parser respone XML from server:
    * <?xml version="1.0" encoding="utf-8" standalone="yes"?>
    <VAS request_id="20101204121212"  version="1.0">
    <SUBID>84xxxxxxxx</SUBID>
    <ERROR>0</ERROR>
    <ERROR_DESC>Successfull</ERROR_DESC>
    <PRICE>15000</PRICE>
    <PROMOTION>0</PROMOTION>
    <NOTE></NOTE>
    </VAS>
    @return: object VCGResponse
    */
    private function getResponse($data)
    {
        try {
            $VAS = new SimpleXMLElement($data);
        } catch (Exception $e) {
            $VAS = NULL;
        }

        if ($VAS == null) {
            return null;
        }

        $response = new VCGWResponse();
        $response->request_id = isset($VAS['request_id']) ? $VAS['request_id'] : '0';
        $response->msisdn = isset($VAS->SUBID) ? $VAS->SUBID : '';
        $response->return = isset($VAS->ERROR) ? $VAS->ERROR : '';
        $response->error_desc = isset($VAS->ERROR_DESC) ? $VAS->ERROR_DESC : '';
        $response->price = isset($VAS->PRICE) ? $VAS->PRICE : '';
        $response->promotion = isset($VAS->PROMOTION) ? $VAS->PROMOTION : '';
        $response->note = isset($VAS->NOTE) ? $VAS->NOTE : '';

        return $response;
    }

    private function getResponse2($data)
    {
        try {
            $VAS = new SimpleXMLElement($data);
        } catch (Exception $e) {
            $VAS = NULL;
        }

        if ($VAS == null) {
            return null;
        }

        $response = new VCGWResponse();
        $response->name = isset($VAS->name) ? $VAS->name : '';
        $response->requestid = isset($VAS['requestid']) ? $VAS['requestid'] : '0';
        $response->error = isset($VAS->error) ? $VAS->error : '';
        $response->error_desc = isset($VAS->error_desc) ? $VAS->error_desc : '';
        $response->extra_information = isset($VAS->extra_information) ? $VAS->extra_information : '';

        return $response;
    }
}
