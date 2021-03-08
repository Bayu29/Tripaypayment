<?php
namespace Tridi\Tripay\Payment;

use Tridi\Tripay\Payment\Support\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Payment extends Container
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load Payment Channel
     * 
     * @param String
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function getPaymentChannel($code = null)
    {
        return $this->curl('/payment/channel',Constant::HTTP_GET,$code);
    }

    /**
     * Load Payment Instruction 
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function getPaymentInstruction($data = [])
    {
        return $this->curl('/payment/instruction',Constant::HTTP_GET,$data);
    }

    /**
     * Get fee calculation Detail for every payment channel
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function feeCalculation($data = [])
    {
        return $this->curl('/merchant/fee-calculator',Constant::HTTP_GET,$data);
    }

    /**
     * Create Signature for Request Transaction
     * 
     * set is_closed_payment to false to Generate Signature for open payment
     */
    public function Signature($data = [],$is_closed_payment = true)
    {
        $privateKey        = $this->private_key;
        $merchantCode      = $this->merchantCode;
        $merchantRef       = $data['merchant_ref'];
        $channel           = $data['method'];
        $amount            = '';

        if($is_closed_payment == true){
            $amount    = $data['amount'];
            $signature = has_hmac('sha256',$merchantCode.$merchantRef.$amount);
        }else{
            $signature = hash_hmac('sha256',$merchantCode.$channel.$merchantRef,$privateKey);
        }

        return $signature;
    }

    /**
     * Request Transaction Closed Payment
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function TrxClosedPayment($data = [],$is_closed_payment = true)
    {
        if($is_closed_payment == false){
            return 'Parameter Not Valid';
        }

        $itemDetails = array([
            'name'=>'Invoice #'.$data['merchant_ref'].' '.$data['customer_name'],
            'price'=>$data['amount'],
            'quantity'=>1,
        ]); 

        $data['order_items']  = $itemDetails;
        $data['expired_time'] = (time()+(24*60*60));
        $data['signature']    = $this->Signature($data,true);

        return $this->curl('/transaction/create',Constant::HTTP_POST,$data);
    }

    /**
     * create new transactions or generate payment codes for Open Payment types
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function TrxOpenPayment($data = [],$is_closed_payment = false)
    {
        if($is_closed_payment == true){
            return 'Parameter Not Valid';
        }

        $data['signature'] = $this->Signature($data,false);

        return $this->curl('/open-payment/create',Constant::HTTP_POST,$data);
    }

    /**
     * used to retrieve details of transactions that have been made. 
     * Can also be used to check payment status in Closed Payment
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function DetailTrxClosedPayment($data = [])
    {
        return $this->curl('/transaction/detail',Constant::HTTP_GET,$data);
    }

    /**
     * used to retrieve details of open payment transactions that have been made
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function DetailTrxOpenPayment($data = [])
    {
        return $this->curl('/open-payment/'.$data['uuid'].'/detail',Constant::HTTP_GET);
    }


    /**
     * Used to retrive a list of payments that are included in open payments
     * 
     * @param Array
     * 
     * @return Object Tridi\Tripay\Payment
     */
    public function OpenPaymentList($data = [])
    {
        // $data['reference'] = $param['reference'];
        // $data['merchant_ref'] = $param['merchant_ref'];
        // $data['start_date'] = $param['start_date'];
        // $data['end_date'] = $param['end_date'];
        return $this->curl('/open-payment/'.$data['uuid'].'/transaction',Constant::HTTP_GET,$data);
    }


}
?>