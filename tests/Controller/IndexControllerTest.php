<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 13:09.
 */

namespace App\Tests\Controller;

use App\Controller\IndexController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @covers IndexController
 */
class IndexControllerTest extends WebTestCase
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
