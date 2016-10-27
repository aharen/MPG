# MPG
Maldives Payment Gateway (MPG) by Bank of Maldives

## Installation

	composer require aharen/mpg

or update your `composer.json` as follows and run `composer update`

	require: {
		"aharen/mpg": "1.0.*"
	}

## Usage

	$pay = new MPG();

	$url         = [Gateway URL from BML];
	$MerID       = [Merchant ID];
	$AcqID       = [Acquirer ID];
	$MerPassword = [Merchant Account Password];

	$pay->initialize($url, $MerID, $AcqID, $MerPassword);

	/* 
	* setup transanction
	* $amount = amount to debit eg: 100.01
	* $transactionId = your transaction id eg: TRN/001
	*/
	$pay->setTransaction($amount, $transactionId);

You can use one of the following methods to get the fields.

Get form values, which will give you an array of all the form values.

	$form_values = $pay->getFormValues();

Or, Get Form, which will give you an HTML form.
	
	$form = $pay->getForm();

You may use the following method to format the response from the gateway.

	Pay::response($_REQUEST);
