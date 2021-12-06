<?php

// Stripe singleton
require(dirname(__FILE__) . '/stripe/Stripe.php');

// Utilities
require(dirname(__FILE__) . '/stripe/Util/AutoPagingIterator.php');
require(dirname(__FILE__) . '/stripe/Util/RequestOptions.php');
require(dirname(__FILE__) . '/stripe/Util/Set.php');
require(dirname(__FILE__) . '/stripe/Util/Util.php');

// HttpClient
require(dirname(__FILE__) . '/stripe/HttpClient/ClientInterface.php');
require(dirname(__FILE__) . '/stripe/HttpClient/CurlClient.php');

// Errors
require(dirname(__FILE__) . '/stripe/Error/Base.php');
require(dirname(__FILE__) . '/stripe/Error/Api.php');
require(dirname(__FILE__) . '/stripe/Error/ApiConnection.php');
require(dirname(__FILE__) . '/stripe/Error/Authentication.php');
require(dirname(__FILE__) . '/stripe/Error/Card.php');
require(dirname(__FILE__) . '/stripe/Error/InvalidRequest.php');
require(dirname(__FILE__) . '/stripe/Error/RateLimit.php');

// Plumbing
require(dirname(__FILE__) . '/stripe/ApiResponse.php');
require(dirname(__FILE__) . '/stripe/JsonSerializable.php');
require(dirname(__FILE__) . '/stripe/StripeObject.php');
require(dirname(__FILE__) . '/stripe/ApiRequestor.php');
require(dirname(__FILE__) . '/stripe/ApiResource.php');
require(dirname(__FILE__) . '/stripe/SingletonApiResource.php');
require(dirname(__FILE__) . '/stripe/AttachedObject.php');
require(dirname(__FILE__) . '/stripe/ExternalAccount.php');

// Stripe API Resources
require(dirname(__FILE__) . '/stripe/Account.php');
require(dirname(__FILE__) . '/stripe/AlipayAccount.php');
require(dirname(__FILE__) . '/stripe/ApplicationFee.php');
require(dirname(__FILE__) . '/stripe/ApplicationFeeRefund.php');
require(dirname(__FILE__) . '/stripe/Balance.php');
require(dirname(__FILE__) . '/stripe/BalanceTransaction.php');
require(dirname(__FILE__) . '/stripe/BankAccount.php');
require(dirname(__FILE__) . '/stripe/BitcoinReceiver.php');
require(dirname(__FILE__) . '/stripe/BitcoinTransaction.php');
require(dirname(__FILE__) . '/stripe/Card.php');
require(dirname(__FILE__) . '/stripe/Charge.php');
require(dirname(__FILE__) . '/stripe/Collection.php');
require(dirname(__FILE__) . '/stripe/CountrySpec.php');
require(dirname(__FILE__) . '/stripe/Coupon.php');
require(dirname(__FILE__) . '/stripe/Customer.php');
require(dirname(__FILE__) . '/stripe/Dispute.php');
require(dirname(__FILE__) . '/stripe/Event.php');
require(dirname(__FILE__) . '/stripe/FileUpload.php');
require(dirname(__FILE__) . '/stripe/Invoice.php');
require(dirname(__FILE__) . '/stripe/InvoiceItem.php');
require(dirname(__FILE__) . '/stripe/Order.php');
require(dirname(__FILE__) . '/stripe/Plan.php');
require(dirname(__FILE__) . '/stripe/Product.php');
require(dirname(__FILE__) . '/stripe/Recipient.php');
require(dirname(__FILE__) . '/stripe/Refund.php');
require(dirname(__FILE__) . '/stripe/SKU.php');
require(dirname(__FILE__) . '/stripe/Subscription.php');
require(dirname(__FILE__) . '/stripe/Token.php');
require(dirname(__FILE__) . '/stripe/Transfer.php');
require(dirname(__FILE__) . '/stripe/TransferReversal.php');
