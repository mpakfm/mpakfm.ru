<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 15.07.2020
 * Time: 10:20
 */

namespace App\Tests\Controller;

use App\Controller\PaymentController;
use App\Tests\TestCaseAbstract;

/**
 * @internal
 * @covers PaymentController
 */
class PaymentControllerTest extends TestCaseAbstract
{
    public function testIndex()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/payment');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин', $crawler->filter('h1')->text(), 'Ошибка значения h1');
        assertSame('Web Developer / Оплата', $crawler->filter('h2')->text(), 'Ошибка значения h2');
    }
}
