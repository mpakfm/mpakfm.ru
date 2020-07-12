<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 12.07.2020
 * Time: 9:51.
 */

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Controller\IndexController;
use App\Tests\TestCaseAbstract;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BaseController
 *
 * @internal
 */
class BaseControllerTest extends TestCaseAbstract
{
    /**
     * @covers BaseController::baseRender
     */
    public function testBaseRender()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин aka mpakfm', $crawler->filter('title')->text(), 'Ошибка значения META title');
        assertNotEmpty($crawler->filter('meta[name="description"]')->attr('content'), 'Пустой META description сайта');
        assertNotEmpty($crawler->filter('meta[name="keywords"]')->attr('content'), 'Пустой META keywords сайта');
        assertNotEmpty($crawler->filter('meta[name="author"]')->attr('content'), 'Пустой META author сайта');
        assertTrue((strpos($crawler->filter('link[rel="canonical"]')->attr('href'), $client->getRequest()->server->get('SERVER_NAME')) !== false), 'Неверно передается каноничный uri');
        assertNotEmpty($crawler->filter('.copyright')->text(), 'Пустой copyright');
    }

    /**
     * @covers BaseController::preLoad
     *
     * @throws \Exception
     */
    public function testPreLoad()
    {
        if (static::$booted) {
            $kernel = static::$kernel;
        } else {
            $kernel = static::bootKernel();
        }
        $container  = $kernel->getContainer();
        $request    = Request::create('/');
        $controller = new IndexController();
        $controller->setContainer($container);
        $controller->index($request);
        assertSame('Сергей Фомин aka mpakfm', $controller->siteProperties->getName(), 'Неверное имя сайта');
        assertSame('Сергей Фомин aka mpakfm', $controller->siteProperties->getMetaTitle(), 'Неверный META title сайта');
        assertNotEmpty($controller->siteProperties->getMetaDescription(), 'Пустой META description сайта');
        assertNotEmpty($controller->siteProperties->getMetaKeywords(), 'Пустой META keywords сайта');
        assertNotEmpty($controller->siteProperties->getFooterCopyright(), 'Пустой copyright сайта');
        assertInstanceOf(DateTime::class, $controller->siteProperties->getLastUpdate(), 'Отсутствует дата последнего обновления или она в неверном формате');
        assertTrue((strpos($controller->canonical, $request->server->get('SERVER_NAME')) !== false), 'Неверно передается каноничный uri');
    }
}
