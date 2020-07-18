# checkout-php
[Latest Stable Version](https://packagist.org/packages/paykun/checkout) *** [License](https://packagist.org/packages/paykun/checkout)


# Installation

- If your project uses composer, run the below command
```
composer require paykun/checkout
```

- If you are not using composer, download the latest release from [the releases section](https://github.com/paykun-code/paykun-php/releases/).
**You should download the `Source code.zip` file**.
After that include `Payment.php` in your application and you can use the API as usual.

# How To Generate Access token and API Secret
You can generate Or Regenerate Access token and API Secret from login into your paykun admin panel, Then Go To : Settings -> Security -> API Keys. There you will find the generate button if you have not generated api key before.

If you have generated api key before then you will see the date of the api key generate, since you will not be able to retrieve the old api key (For security reasons) we have provided the re-generate option, so you can re-generate api key in case you have lost the old one.

Note : Once you re-generate api key your old api key will stop working immediately. So be cautious while using this option.

# Usage (Composer project)

```php


use Paykun\Checkout\Payment;

$obj = new Payment('<merchantId>', '<accessToken>', '<encryptionKey>');

// Initializing Order
// default currency is 'INR'
$obj->initOrder('<orderId>', '<Purpose or ProductName>', "<amount>", '<successUrl.example.com>',  '<failUrl.example.com>', 'INR');

// Add Customer
$obj->addCustomer('<customerName>', '<customerEmail>', '<customerContactNo>');

// Add Shipping address
$obj->addShippingAddress('<country>', '<state>', '<city>', '<postalCode>', '<fullAddress>');

// Add Billing Address
$obj->addBillingAddress('<country>', '<state>', '<city>', '<postalCode>', '<fullAddress>');

echo $obj->submit();

/* Check for transaction status
 * Once your success or failed url called then create an instance of Payment same as above and then call getTransactionInfo like below
 *  $obj = new Payment('merchantUId', 'accessToken', 'encryptionKey');
 *  $transactionData = $obj->getTransactionInfo(Get payment-id from the success or failed url);
 *  Process $transactionData as per your requirement
 *
 * */

```

# Usage (Non-composer project)

```php

require 'src/Payment.php';
require 'src/Validator.php';
require 'src/Crypto.php';

/**
 *  Parameters requires to initialize an object of Payment are as follow.
 *  mid => Merchant Id provided by Paykun
 *  accessToken => Access Token provided by Paykun
 *  encKey =>  Encryption provided by Paykun
 *  isLive => Set true for production environment and false for sandbox or testing mode
 *  isCustomTemplate => Set true for non composer projects, will disable twig template
 */

$obj = new \Paykun\Checkout\Payment('<merchantId>', '<accessToken>', '<encryptionKey>', true, true);

// Initializing Order
// default currency is 'INR'
$obj->initOrder('<orderId>', '<Purpose or ProductName>', "<amount>", '<successUrl.example.com>',  '<failUrl.example.com>', 'INR');

// Add Customer
$obj->addCustomer('<customerName>', '<customerEmail>', '<customerContactNo>');

// Add Shipping address
$obj->addShippingAddress('<country>', '<state>', '<city>', '<postalCode>', '<fullAddress>');

// Add Billing Address
$obj->addBillingAddress('<country>', '<state>', '<city>', '<postalCode>', '<fullAddress>');

//Render template and submit the form
echo $obj->submit();

/* Check for transaction status
 * Once your success or failed url called then create an instance of Payment same as above and then call getTransactionInfo like below
 *  $obj = new Payment('merchantUId', 'accessToken', 'encryptionKey', true, true); //Second last false if sandbox mode
 *  $transactionData = $obj->getTransactionInfo(Get payment-id from the success or failed url);
 *  Process $transactionData as per your requirement
 *
 * */

```


For further help, see our documentation on <https://paykun.com/docs>.

[composer-install]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx


## License

The Paykun PHP SDK is released under the MIT License.

## Release
