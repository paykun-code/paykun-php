<?php

namespace Paykun\Checkout;

use Paykun\Checkout\Errors;
use Paykun\Checkout\Validator;
use Paykun\Checkout\Errors\ErrorCodes;
use Paykun\Checkout\Errors\ValidationException;
require_once ('Errors/ErrorCodes.php');
require_once ('Errors/ValidationException.php');
class Payment {

    const GATEWAY_URL_PROD = "https://checkout.paykun.com/payment";
    const GATEWAY_URL_DEV = "https://sandbox.paykun.com/payment";
    const PAGE_TITLE = "Processing Payment...";

    private $merchantId;
    private $accessToken;
    private $encryptionKey;
    private $orderId;
    private $purpose;
    private $amount;
    private $successUrl;
    private $failureUrl;
    private $country;
    private $state;
    private $city;
    private $pinCode;
    private $addressString;
    private $billingCountry;
    private $billingState;
    private $billingCity;
    private $billingPinCode;
    private $billingAddressString;
    private $twig;
    private $isLive;
    private $isPassedValidationForConstructor = false;
    private $isPassedValidationForInitOrder = false;
    private $isPassedValidationForCustomer = false;
    private $isPassedValidationForShipping = false;
    private $isPassedValidationForBilling = false;
    private $isCustomRenderer = false;
    private $currency = 'INR';
    public $udf_1;
    public $udf_2;
    public $udf_3;
    public $udf_4;
    public $udf_5;
    private $isWebView;

    /**
     * Payment constructor.
     * @param string $mid           => Id of the Merchant
     * @param string $accessToken   => Access token
     * @param string $encKey        => Encryption key
     * @param bool $isLive          => Sandbox or production mode flag
     * @throws Errors\ValidationException
     */

    public function __construct($mid, $accessToken, $encKey, $isLive = true, $isCustomTemplate = false, $isWebView=false) {

//        if (Validator::VALIDATE_MERCHANT_ID($mid)) {
//            throw new Errors\ValidationException(ErrorCodes::INVALID_MERCHANT_ID_STRING,
//                ErrorCodes::INVALID_MERCHANT_ID_CODE, null);
//        }

        if (Validator::VALIDATE_ACCESS_TOKEN($accessToken)) {
            throw new Errors\ValidationException(ErrorCodes::INVALID_ACCESS_TOKEN_STRING,
                ErrorCodes::INVALID_ACCESS_TOKEN_CODE, null);
        }

        if (Validator::VALIDATE_ENCRYPTION_KEY($encKey)) {
            throw new Errors\ValidationException(ErrorCodes::INVALID_API_SECRETE_STRING,
                ErrorCodes::INVALID_API_SECRETE_CODE, null);
        }

        $this->merchantId       = $mid;
        $this->accessToken      = $accessToken;
        $this->encryptionKey    = $encKey;
        $this->isLive           = $isLive;
        $this->isPassedValidationForConstructor = true;
        $this->isCustomRenderer = $isCustomTemplate;

        if($this->isCustomRenderer == false) {
            $loader = new \Twig_Loader_Filesystem(__DIR__.'/template');

            $this->twig = new \Twig_Environment($loader);
        }
        $this->isWebView = $isWebView;

    }

    /**
     * @param string $orderId               => Pay for the order id given by the Merchant
     * @param string $purpose               => Detail description for what you are paying
     * @param string $amount                => Amount to be paid
     * @param string $successUrl            => Redirect to the sucsess page once payment is done.
     * @param string $failureUrl            => Redirect to the failed page once payment is not done.
     * @return $this
     * @throws Errors\ValidationException
     */


    public function initOrder ($orderId, $purpose, $amount, $successUrl, $failureUrl, $currency = 'INR') {

        if (Validator::VALIDATE_ORDER_NUMBER($orderId)) {
            throw new Errors\ValidationException(ErrorCodes::INVALID_ORDER_ID_STRING,
                ErrorCodes::INVALID_ORDER_ID_CODE, null);
        }

        if (Validator::VALIDATE_PURPOSE($purpose)) {
            throw new Errors\ValidationException(ErrorCodes::INVALID_PURPOSE_STRING,
                ErrorCodes::INVALID_PURPOSE_CODE, null);
        }

        $this->orderId      = $orderId;
        $this->purpose      = $purpose;
        $this->amount       = $amount;
        $this->successUrl   = $successUrl;
        $this->failureUrl   = $failureUrl;
        $this->isPassedValidationForInitOrder = true;
        $this->currency = $currency;
        return $this;

    }


    /**
     * @param string $customerName
     * @param string $customerEmail
     * @param string $customerMoNo
     * @return $this
     * @throws Errors\ValidationException
     */

    public function addCustomer($customerName, $customerEmail, $customerMoNo) {

        $this->customerName     = $customerName;
        $this->customerEmail    = $customerEmail;
        $this->customerMoNo     = $customerMoNo;
        $this->isPassedValidationForCustomer = true;
        return $this;

    }


    /**
     * @param string $country
     * @param string $state
     * @param string $city
     * @param string $pinCode
     * @param string $addressString
     */

    public function addShippingAddress($country, $state, $city, $pinCode, $addressString) {

        $this->country          = $country;
        $this->state            = $state;
        $this->city             = $city;
        $this->pinCode          = $pinCode;
        $this->addressString    = $addressString;

        $this->isPassedValidationForShipping = true;

    }


    /**
     * @param string $country
     * @param string $state
     * @param string $city
     * @param string $pinCode
     * @param string $addressString
     */

    public function addBillingAddress($country, $state, $city, $pinCode, $addressString) {

        $this->billingCountry   = $country;
        $this->billingState     = $state;
        $this->billingCity      = $city;
        $this->billingPinCode   = $pinCode;
        $this->billingAddressString = $addressString;
        $this->isPassedValidationForBilling = true;

    }


    public function setCustomFields($fields = null) {
        if($fields !== null) {
            $refl = new \ReflectionClass($this);
            foreach ($fields as $key => $value) {
                $property = $refl->getProperty($key);
                if ($property instanceof \ReflectionProperty) {
                    $property->setValue($this, $value);
                }
            }
        }
    }

    /**
     * @param bool $isCustomRender The is render parameter specifies whethere the user want to use custom form for submit or not
     * By default the default template will be used for rendering
     * Set to false for non-composer users
     * @return string
     * @throws Errors\ValidationException
     */
    public function submit() {
        if (
            $this->isPassedValidationForConstructor &&
            $this->isPassedValidationForInitOrder &&
            $this->isPassedValidationForCustomer &&
            $this->isPassedValidationForShipping &&
            $this->isPassedValidationForBilling
        ) {

            $dataArray                      = array();
            $dataArray['order_no']          = $this->orderId;
            $dataArray['product_name']      = $this->purpose;
            $dataArray['amount']            = $this->amount;
            $dataArray['success_url']       = $this->successUrl;
            $dataArray['failure_url']       = $this->failureUrl;
            $dataArray['customer_name']     = $this->customerName;
            $dataArray['customer_email']    = $this->customerEmail;
            $dataArray['customer_phone']    = $this->customerMoNo;
            $dataArray['shipping_address']  = $this->addressString;
            $dataArray['shipping_city']     = $this->city;
            $dataArray['shipping_state']    = $this->state;
            $dataArray['shipping_country']  = $this->country;
            $dataArray['shipping_zip']      = $this->pinCode;
            $dataArray['billing_address']   = $this->billingAddressString;
            $dataArray['billing_city']      = $this->billingCity;
            $dataArray['billing_state']     = $this->billingState;
            $dataArray['billing_country']   = $this->billingCountry;
            $dataArray['billing_zip']       = $this->billingPinCode;
            $dataArray['udf_1']             = $this->udf_1 ? $this->udf_1 : '';
            $dataArray['udf_2']             = $this->udf_2 ? $this->udf_2 : '';
            $dataArray['udf_3']             = $this->udf_3 ? $this->udf_3 : '';
            $dataArray['udf_4']             = $this->udf_4 ? $this->udf_4 : '';
            $dataArray['udf_5']             = $this->udf_5 ? $this->udf_5 : '';
            $dataArray['currency']          = $this->currency;
            $encryptedData = $this->encryptData($dataArray);
            return $this->createForm($encryptedData);

        }

        /*Validation is not passed for all the steps*/

        throw new ValidationException(ErrorCodes::INVALID_DATA_PROVIDED_STRING,
            ErrorCodes::INVALID_DATA_PROVIDED_CODE, null);

    }


    /**
     * @param array $data
     * @return string
     */

    private function encryptData(array $data) {

        $data = array_filter($data);
        ksort($data);

        $dataToPostToPG = "";
        
        foreach ($data as $key => $value)
        {
                if ("" == trim($value) && $value === NULL) {
                } else {
                    $dataToPostToPG = $dataToPostToPG . $key . "::" . ($value) . ";";
                }
        }

        // Removing last 2 characters (::) 
        $dataToPostToPG = substr($dataToPostToPG, 0, -2);
        // Encrypting String
        return Crypto::encrypt($dataToPostToPG, $this->encryptionKey);

    }


    /**
     * @param string $encData
     * @return string
     */

    private function createForm($encData) {

        $formData = array();
        $formData['encrypted_request']  = $encData;
        $formData['merchant_id']        = $this->merchantId;
        $formData['access_token']       = $this->accessToken;

        $extraParam = "";
        if($this->isWebView) {
            $extraParam = "?isWebView=true";
        }
        if ($this->isLive) {
            $formData['gateway_url'] = self::GATEWAY_URL_PROD.$extraParam;
        } else {
            $formData['gateway_url'] = self::GATEWAY_URL_DEV.$extraParam;
        }
        
        $formData['pageTitle'] = self::PAGE_TITLE;

        if($this->isCustomRenderer == false) {

            return $this->render('formTemplate.html', $formData);

        } else {

            return $this->prepareCustomFormTemplate($formData);

        }

    }


    /**
     * @param $templateName
     * @param array $parameters
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */

    public function render($templateName, array $parameters = array()) {

        return $this->twig->render($templateName, $parameters);

    }


    /**
     * @param $formData
     * @return string
     * This function will be used by non-composer users, as they are not using twig or other template parser
     */
    public function prepareCustomFormTemplate ($formData) {
        /*echo "<pre>";
        print_r($formData);
        exit;*/

        $htmlEntity = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html lang="en">
            <head>
                <title>'.$formData["pageTitle"].'</title>
                <meta http-equiv="content-type" content="text/html;charset=utf-8">
            </head>
            <body>
            <div>
                Processing your payment, please wait...
            </div>
            <form  action="'.$formData["gateway_url"].'" method="post" name="server_request" target="_top" >
                <table width="80%" align="center" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><input type="hidden" name="encrypted_request" id="encrypted_request" value="'.$formData['encrypted_request'].'" /></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="merchant_id" id="merchant_id" value="'.$formData['merchant_id'].'" /></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="access_token" id="access_token" value="'.$formData['access_token'].'"></td>
                    </tr>
                </table>
            </form>
            </body>
            <script type="text/javascript">
                document.server_request.submit();
            </script>
            </html>
        ';
        return $htmlEntity;
    }

    public function getTransactionInfo($paymentId) {

        try {

            if($this->isLive == true) {
                $cUrl        = 'https://api.paykun.com/v1/merchant/transaction/' . $paymentId . '/';
            } else {
                $cUrl        = 'https://sandbox.paykun.com/api/v1/merchant/transaction/' . $paymentId . '/';
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $cUrl);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("MerchantId:$this->merchantId", "AccessToken:$this->accessToken"));
            if( isset($_SERVER['HTTPS'] ) ) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            }
            $response       = curl_exec($ch);
            $error_number   = curl_errno($ch);
            $error_message  = curl_error($ch);

            $res = json_decode($response, true);
            curl_close($ch);

            return ($error_message) ? null : $res;

        } catch (Errors\ValidationException $e) {

            throw new Errors\ValidationException("Server couldn't respond, ".$e->getMessage(), $e->getCode(), null);
            return null;

        }

    }
}

?>