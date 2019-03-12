<?php

namespace Paykun\Checkout;
use Paykun\Checkout\Errors\ErrorCodes;
/**
 * Class Validator
 * @package Paykun\Checkout
 */

class Validator {

    const ORDER_ID_MIN_LENGTH       = 10;
    const MERCHANT_ID_MIN_LENGTH    = 15;
    const ACCESS_TOKEN_MIN_LENGTH   = 32;
    const ENC_KEY_MIN_LENGTH        = 32;
    const MIN_AMOUNT_REQUIRE        = 10;
    const MOBILE_NO_MIN_LENGTH      = 10;
    const MOBILE_NO_MAX_LENGTH      = 10;
    const ADDRESS_MIN_LENGTH        = 10;


    public static function VALIDATE_MERCHANT_ID($mid) {

        return strlen($mid) !== Validator::MERCHANT_ID_MIN_LENGTH;

    }

    public static function VALIDATE_ACCESS_TOKEN($accessToken) {

        return (strlen($accessToken) !== Validator::ACCESS_TOKEN_MIN_LENGTH);

    }

    public static function VALIDATE_ENCRYPTION_KEY($encKey) {

        return (strlen($encKey) !== Validator::ENC_KEY_MIN_LENGTH);

    }

    public static function VALIDATE_ORDER_NUMBER($orderId) {

        return (strlen($orderId) < Validator::ORDER_ID_MIN_LENGTH);

    }

    public static function VALIDATE_PURPOSE($purpose) {

        return !isset($purpose) || empty($purpose);

    }

    public static function VALIDATE_AMOUNT($amount) {

        return (double)$amount < Validator::MIN_AMOUNT_REQUIRE;

    }


    public static function VALIDATE_URL($url) {

        filter_var($url, FILTER_VALIDATE_URL);

    }

    public static function VALIDATE_CUSTOMER_NAME($name) {

        $errorDetail = array();
        if (!isset($name) || empty($name)) {
            $errorDetail["message"] = ErrorCodes::MISSING_CUSTOMER_NAME_STRING;
            $errorDetail["code"]    = ErrorCodes::MISSING_CUSTOMER_NAME_CODE;
        }

        if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
            $errorDetail["message"] = ErrorCodes::INVALID_CUSTOMER_NAME_STRING;
            $errorDetail["code"]    = ErrorCodes::INVALID_CUSTOMER_NAME_CODE;
        }

        return $errorDetail;

    }

    public static function VALIDATE_CUSTOMER_EMAIL($email) {

        $errorDetail = array();
        if (!isset($email) || empty($email)) {
            $errorDetail["message"] = ErrorCodes::MISSING_CUSTOMER_EMAIL_STRING;
            $errorDetail["code"]    = ErrorCodes::MISSING_CUSTOMER_EMAIL_CODE;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorDetail["message"] = ErrorCodes::INVALID_CUSTOMER_EMAIL_STRING;
            $errorDetail["code"]    = ErrorCodes::INVALID_CUSTOMER_EMAIL_CODE;
        }

        return $errorDetail;

    }

    public static function VALIDATE_MOBILE_NO($mobileNo) {

        return !isset($mobileNo) || empty($mobileNo) || strlen($mobileNo) < Validator::MOBILE_NO_MIN_LENGTH ||
            strlen($mobileNo) > Validator::MOBILE_NO_MAX_LENGTH;

    }

    public static function VALIDATE_COMMON_FIELD($name) {

        return  (preg_match("/^[a-zA-Z]{2,}/", $name));

    }

    public static function VALIDATE_ADDRESS_FIELD($address) {

        return  !strlen($address) < Validator::ADDRESS_MIN_LENGTH;

    }

    public static function VALIDATE_PINCODE($pin) {

        return  (preg_match("/^\d{6}$/", $pin));

    }

}
