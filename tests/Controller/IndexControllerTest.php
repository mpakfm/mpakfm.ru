<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 13:09.
 */

namespace App\Tests\Controller;

use App\Controller\IndexController;
use App\Tests\TestCaseAbstract;

/**
 * @internal
 * @covers IndexController
 */
class IndexControllerTest extends TestCaseAbstract
{
    /**
     * @covers IndexController::index
     */
    public function testIndex()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин', $crawler->filter('h1')->text(), 'Ошибка значения h1');
        assertSame('Web Developer', $crawler->filter('h2')->text(), 'Ошибка значения h2');
    }

    /**
     * @covers IndexController::personalAgreement
     */
    public function testAgreement()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/personal-agreement');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин', $crawler->filter('h1')->text(), 'Ошибка значения h1');
        assertSame('Web Developer', $crawler->filter('h2.desc')->text(), 'Ошибка значения h2.desc');
        assertSame('Согласие пользователя сайта на обработку персональных данных', $crawler->filter('h2.heading')->text(), 'Ошибка значения h2.heading');
        assertSame('Политика конфиденциальности', $crawler->filter('h5')->text(), 'Ошибка значения h5');
    }

    /**
     * @covers IndexController::portfolio
     */
    public function testPortfolio()
    {
        $client  = static::createClient();
        $client->request('GET', '/portfolio');
        assertSame(301, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('/', $client->getResponse()->headers->get('location'), 'Ошибка location');
    }

    /**
     * @covers IndexController::contact
     */
    public function testContact()
    {
        $client  = static::createClient();
        $client->request('GET', '/contact');
        assertSame(301, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('/', $client->getResponse()->headers->get('location'), 'Ошибка location');
    }
}
