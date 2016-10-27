<?php

namespace aharen;

use Symfony\Component\HttpFoundation\ParameterBag;

class MPG
{

    protected $url;

    protected $parameters;

    protected $PurchaseCurrency;

    protected $PurchaseCurrencyExponent;

    protected $Version;

    protected $Signature;

    protected $SignatureMethod;

    protected $PurchaseAmt;

    protected $AcqID;

    protected $MerID;

    protected $MerPassword;

    protected $OrderID;

    public function __construct(
        $PurchaseCurrency = '462',
        $PurchaseCurrencyExponent = '2',
        $Version = '1.1',
        $SignatureMethod = 'SHA1') {

        $this->PurchaseCurrency         = $PurchaseCurrency;
        $this->PurchaseCurrencyExponent = $PurchaseCurrencyExponent;
        $this->Version                  = $Version;
        $this->SignatureMethod          = $SignatureMethod;

        $this->parameters = new ParameterBag(compact('Version', 'PurchaseCurrency', 'PurchaseCurrencyExponent', 'SignatureMethod'));

    }

    public function initialize($url, $AcqID, $MerID, $MerPassword)
    {
        $this->AcqID       = $AcqID;
        $this->MerID       = $MerID;
        $this->MerPassword = $MerPassword;

        $this->parameters->set('AcqID', $AcqID);
        $this->parameters->set('MerID', $MerID);

        return $this;
    }

    public function setTransaction($amount, $transactionId)
    {
        $this->makeAmount($amount);
        $this->parameters->set('PurchaseAmt', $this->PurchaseAmt);

        $this->OrderID = $transactionId;
        $this->parameters->set('OrderID', $this->OrderID);

        $this->makeSignature();
        $this->parameters->set('Signature', $this->Signature);

        return $this;
    }

    public function getFormValues()
    {
        return $this->parameters->all();
    }

    public function getForm()
    {
        $form = '<form method="post" action="' . $this->url . '">';
        foreach ($this->parameters as $key => $value) {
            $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }
        $form .= '<input type="submit" name="submit" value="Proceed to Make Payment" /></form>';
        return $form;
    }

    protected function makeAmount($amount)
    {
        $this->PurchaseAmt = str_pad(str_replace('.', '', $amount), 12, 0, STR_PAD_LEFT);
    }

    protected function makeSignature()
    {
        $signatureValue  = $this->MerPassword . $this->MerID . $this->AcqID . $this->OrderID . $this->PurchaseAmt . $this->PurchaseCurrency;
        $this->Signature = base64_encode(sha1($signatureValue, true));
    }

    public static function response($response)
    {
        $responseCode     = $response['ResponseCode'];
        $responseCodeText = self::responseCodeText($responseCode);

        return array_merge(['message' => $responseCodeText], $response);
    }

    protected static function responseCodeText($code)
    {
        switch ($code) {
            case 1:
                return 'Payment Processed';
                break;

            case 2:
                return 'Payment Declined';
                break;

            case 3;
                return 'Error Occured';
                break;
        }
    }
}
