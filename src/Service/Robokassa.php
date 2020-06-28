<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 27.06.2020
 * Time: 20:44.
 */

namespace App\Service;

use App\Entity\Payment;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;

class Robokassa
{
    const IS_TEST = false;
    const ALGO = 'sha256';

    public static $url = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    private $merchantLogin = 'mpakfm.ru';
    private $pass1 = 'KtyPhp9J904pvt7FAzxb';
    private $pass2 = 'XnjLYbvA6J7iVJ87ge4P';
    private $testPass1 = 'bQ3GT01qRsVka30WKnrG';
    private $testPass2 = 'uv5qN8Tm9w4Vai9HTcrW';
    private $culture = 'ru';
    private $encoding = 'utf-8';
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

    public function verify(Request $request): bool
    {
        if ($request->query->get('SignatureValue')) {
            $str = "{$this->merchantLogin}:".$request->query->get('OutSum').":{$request->query->get('InvId')}:".$this->getPasswordOne(static::IS_TEST);
            $str2 = "{$this->merchantLogin}:".$request->query->get('OutSum').":{$request->query->get('InvId')}:".$this->getPasswordTwo(static::IS_TEST);
            $hash = $request->query->get('SignatureValue');
        } elseif ($request->request->get('SignatureValue')) {
            $str = "{$this->merchantLogin}:".$request->request->get('OutSum').":{$request->request->get('InvId')}:".$this->getPasswordOne(static::IS_TEST);
            $str2 = "{$this->merchantLogin}:".$request->request->get('OutSum').":{$request->request->get('InvId')}:".$this->getPasswordTwo(static::IS_TEST);
            $hash = $request->request->get('SignatureValue');
        }
        Printu::log($str, 'Robokassa::verify $str', 'file');
        Printu::log($str2, 'Robokassa::verify $str2', 'file');
        $crc = hash(static::ALGO, $str);
        $crc2 = hash(static::ALGO, $str2);
        Printu::log($crc, 'Robokassa::verify $crc', 'file');
        Printu::log($crc2, 'Robokassa::verify $crc2', 'file');
        Printu::log($hash, 'Robokassa::verify SignatureValue', 'file');
        if ($hash != $crc) {
            throw new \Exception('Wrong hash');
        }
        return true;
    }

    public function validate(Payment $payment): bool
    {
        if ('' == $payment->getEmail() || 0 == $payment->getMoney()) {
            Printu::log($payment->getEmail(), 'Robokassa::validate $payment->getEmail()', 'file');
            Printu::log($payment->getMoney(), 'Robokassa::validate $payment->getMoney()', 'file');
            throw new \Exception('Wrong parametrs');
        }
        return true;
    }

    public function makePayment(Request $request, $entityManager)
    {
        $money = round(floatval(str_replace(',', '.', $request->request->get('money'))), 2);
        $email = trim($request->request->get('email'));
        $payment = new Payment();
        $payment->setMerchant($this->merchantLogin);
        $payment->setEmail($email);
        $payment->setDescription($request->request->get('comment'));
        $payment->setMoney($money);
        $payment->setIsTest(static::IS_TEST);
        $payment->setCreated(new \DateTimeImmutable());

        if ($request->request->get('organization')) {
            $payment->setOrganization($request->request->get('organization_name'));
            if (!$request->request->get('foreign_organization')) {
                $payment->setOrganizationInn($request->request->get('organization_inn'));
            }
        }

        $this->validate($payment);

        $entityManager->persist($payment);
        $entityManager->flush();

        //$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");
        $str = "{$this->merchantLogin}:".$money.":{$payment->getId()}:".$this->getPasswordOne(static::IS_TEST);
        $crc = hash(static::ALGO, $str);
        Printu::log($this->getPasswordOne(static::IS_TEST), 'getPasswordOne', 'file');
        Printu::log($str, '$str', 'file');
        Printu::log($crc, '$crc', 'file');

        $postData = [
            'MerchantLogin' => $payment->getMerchant(),
            'OutSum' => $payment->getMoney(),
            'InvId' => $payment->getId(),
            'Description' => $payment->getDescription(),
            'SignatureValue' => $crc,
            'IncCurrLabel' => $this->incCurrLabel,
            'Culture' => $this->culture,
            'Email' => $payment->getEmail(),
            'Encoding' => $this->encoding,
        ];
        Printu::log($postData, 'postData', 'file');

        return $postData;
    }
}
