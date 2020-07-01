<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 27.06.2020
 * Time: 20:44.
 */

namespace App\Service;

use App\Entity\Payment;
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

    public function verify(Request $request, int $passType = 1): bool
    {
        $password = ($passType == 1 ? $this->getPasswordOne(static::IS_TEST) : $this->getPasswordTwo(static::IS_TEST) );
        if ($request->query->get('SignatureValue')) {
            $str = $request->query->get('OutSum').":{$request->query->get('InvId')}:".$password;
            $hash = $request->query->get('SignatureValue');
        } elseif ($request->request->get('SignatureValue')) {
            $str = $request->request->get('OutSum').":{$request->request->get('InvId')}:".$password;
            $hash = $request->request->get('SignatureValue');
        }
        $crc = hash(static::ALGO, $str);
        $crc = strtoupper($crc);
        $hash = strtoupper($hash);
        if ($hash != $crc) {
            throw new \Exception('Wrong hash');
        }
        return true;
    }

    public function validate(Payment $payment): bool
    {
        if ('' == $payment->getEmail() || 0 == $payment->getMoney()) {
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

        $str = "{$this->merchantLogin}:".$money.":{$payment->getId()}:".$this->getPasswordOne(static::IS_TEST);
        $crc = hash(static::ALGO, $str);

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

        return $postData;
    }
}
