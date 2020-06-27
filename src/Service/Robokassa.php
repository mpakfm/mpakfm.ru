<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 27.06.2020
 * Time: 20:44.
 */

namespace App\Service;

use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;

class Robokassa
{
    public static $url = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    private $merchantLogin = 'mpakfm.ru';
    private $pass1 = 'KtyPhp9J904pvt7FAzxb';
    private $pass2 = 'XnjLYbvA6J7iVJ87ge4P';
    private $testPass1 = 'bQ3GT01qRsVka30WKnrG';
    private $testPass2 = 'uv5qN8Tm9w4Vai9HTcrW';
    private $culture = 'ru';
    private $encoding = 'utf-8';
    private $paymentMethod = 'full_payment';
    private $outSumCurrency = 'RUB';
    private $shpItem = '1';
    private $incCurrLabel = 'BANKOCEAN2R';

    public function getPasswordOne($isTest = false)
    {
        if ($isTest) {
            return $this->testPass1;
        }

        return $this->pass1;
    }

    public function getPasswordTwo($isTest = false)
    {
        if ($isTest) {
            return $this->testPass2;
        }

        return $this->pass2;
    }

    public function makePayment(Request $request)
    {
        $inv_id = 0;
        $crc = hash('sha256', "{$this->merchantLogin}:".$request->request->get('money').":{$inv_id}:{$this->outSumCurrency}:".$this->getPasswordOne(true).":Shp_item={$this->shpItem}");
        $crc2 = hash('sha256', "{$this->merchantLogin}:".$request->request->get('money').":{$inv_id}:{$this->outSumCurrency}:".$this->getPasswordOne(true).":Shp_item={$this->shpItem}", true);
        Printu::log($crc, '$crc', 'file');
        Printu::log($crc2, '$crc2', 'file');
        //$crc = md5("{$this->merchantLogin}:".$request->request->get('money').":{$inv_id}:{$this->outSumCurrency}:".$this->getPasswordOne(true).":Shp_item={$this->shpItem}");

        $postData = [
            'MerchantLogin' => $this->merchantLogin,
            'OutSum' => $request->request->get('money'),
            'InvId' => $inv_id,
            'Description' => $request->request->get('comment'),
            'SignatureValue' => $crc,
            'IncCurrLabel' => $this->incCurrLabel,
            'Culture' => $this->culture,
            'Email' => $request->request->get('email'),
            'Encoding' => $this->encoding,
        ];
        Printu::log($postData, 'postData', 'file');
        return $postData;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        $res = curl_exec($curl);
        Printu::log($res, 'curl result', 'file');
        $curlErr = curl_error($curl);
        $curlInfo = curl_getinfo($curl);
        Printu::log($curlInfo, '$curlInfo', 'file');
        Printu::log($curlErr, '$curlErr', 'file');
        curl_close($curl);
    }
}
