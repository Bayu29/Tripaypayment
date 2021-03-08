<?php
namespace Tridi\Tripay\Payment;

use Tridi\Tripay\Payment\Support\Constant;

class Container
{
    protected $apiKey;
    protected $merchantCode;
    protected $privateKey;
    protected $is_production;

    public function __construct()
    {
        $this->apiKey        = config('tripay-payment.api_key','');
        $this->merchantCode  = config('tripay-payment.merchant_code','');
        $this->private_key   = config('tripay-payment.private_key','');
        $this->is_production = config('tripay-payment.is_production');
    }

    /**
     *  Make HTTP request
     * 
     * @param String $endpoint
     * 
     * @param Int Tridi\Tripay\Payment\Support\Constant $method 
     * 
     * @param Array Request Parameters $params
     * 
     * @return Object
     * 
     */

     protected function curl($endpoint, $method = Constant::HTTP_GET, $params = [])
     {
        if($this->is_production == true){
            $url = Constant::API_BASEURL . '/'.ltrim($endpoint,'/');
        }else{
            $url = Constant::API_SANDBOX_BASEURL . '/' .ltrim($endpoint,'/');
        }

        $ch = curl_init();

        if($method == Constant::HTTP_GET)
        {
            $url .= '?'.http_build_query($params);
        }
        else
        {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
        }

        curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            "Authorization: Bearer ".$this->apiKey
        ]);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_TIMEOUT,180);
        curl_setopt($ch,CURLOPT_FAILONERROR,false);
        curl_setopt($ch,CURLOPT_IPRESOLVE,CURL_IPRESOLVE_V4);

        $result     = curl_exec($ch);
        $httpCode   = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $errno      = curl_errno($ch);
        $error      = curl_error($ch);

        curl_close();

        if($errno)
        {
            $result = json_decode([
                'success'=>false,
                'error_message'=>$error
            ]);
        }

        return json_decode($result);
     }
}
?>