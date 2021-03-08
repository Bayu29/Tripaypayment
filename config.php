<?php

return [
    /**
     * Enter your API Key, Private Key and Merchant Code
     * 
     * To get your API KEY, Private Key and Merchant Code, please go to 
     * https://payment.tripay.co.id/member/merchant
     * 
     * or if you using sandbox mode, you can get your API Key, Private Key and Merchant Code in
     * https://payment.tripay.co.id/simulator/merchant
     * 
     */


    /**
     *Enter your Api Key here or you can set your Api Key in .env 
     */
    'api_key' => env('TRIPAY_PAYMENT_API_KEY','your_api_key_here'),
    
    /**
     * Enter your Private Key here or you can set your Private Key in .env
     */
    'private_key'=> env('TRIPAY_PAYMENT_PRIVATE_KEY','your_private_key_here'),

    /**
     * Enter your Merchant Code here or you can set your Merchant Code in .env
     */
    'merchant_code'=>env('TRIPAY_PAYMENT_MERCHANT_CODE','your_merchant_code_here'),

    /**
     * Set is_production to true if you want to go live and set to false if you want to use sandbox mode
     */
    'is_production'=>env('TRIPAY_PAYMENT_IS_PRODUCTION','set true or false'),
];
?>