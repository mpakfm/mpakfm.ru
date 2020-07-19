<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 12.07.2020
 * Time: 14:02.
 */

namespace App\Tests\Controller;

use App\Controller\ErrorsController;
use App\Entity\SiteProperty;
use App\Service\BasePropertizer;
use App\Tests\TestCaseAbstract;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @covers ErrorsController
 *
 * @internal
 */
class ErrorsControllerTest extends TestCaseAbstract
{
    public function testShow404()
    {
        if (static::$booted) {
            $kernel = static::$kernel;
        } else {
            $kernel = static::bootKernel();
        }
        $container  = $kernel->getContainer();
        $request    = Request::create('/notfoundpage');

        $controller = new ErrorsController();
        $controller->setContainer($container);
        $base      = new BasePropertizer();
        $repo      = $container->get('doctrine')->getRepository(SiteProperty::class);
        $exception = new NotFoundHttpException('Page not found');
        $result    = $controller->show($exception, $repo, $base, $request);
        $crawler   = new Crawler($result->getContent());
        assertSame(404, $result->getStatusCode(), 'Неверный ответ сервера на запрос NotFoundHttpException');
        assertSame('Ошибка 404. Страница не найдена.', $crawler->filter('h1.name')->text(), 'Неверный заголовок H1');
        assertNotEmpty($crawler->filter('.copyright')->text(), 'Не найден блок copyright');
    }

    public function testShow403()
    {
        if (static::$booted) {
            $kernel = static::$kernel;
        } else {
            $kernel = static::bootKernel();
        }
        $container  = $kernel->getContainer();
        $request    = Request::create('/notfoundpage');

        $controller = new ErrorsController();
        $controller->setContainer($container);
        $base      = new BasePropertizer();
        $repo      = $container->get('doctrine')->getRepository(SiteProperty::class);
        $exception = new AccessDeniedHttpException('Test access denied exception');
        $result    = $controller->show($exception, $repo, $base, $request);
        $crawler   = new Crawler($result->getContent());
        assertSame(403, $result->getStatusCode(), 'Неверный ответ сервера на запрос AccessDeniedHttpException');
        assertSame('403 Forbidden', $crawler->filter('h1.name')->text(), 'Неверный заголовок H1');
        assertNotEmpty($crawler->filter('.copyright')->text(), 'Не найден блок copyright');
    }

    public function testShow500()
    {
        if (static::$booted) {
            $kernel = static::$kernel;
        } else {
            $kernel = static::bootKernel();
        }
        $container  = $kernel->getContainer();
        $request    = Request::create('/notfoundpage');

        $controller = new ErrorsController();
        $controller->setContainer($container);
        $base      = new BasePropertizer();
        $repo      = $container->get('doctrine')->getRepository(SiteProperty::class);
        $exception = new Exception('Test exception');
        $result    = $controller->show($exception, $repo, $base, $request);
        $crawler   = new Crawler($result->getContent());
        assertSame(500, $result->getStatusCode(), 'Неверный ответ сервера на запрос Exception');
        assertSame('500 Internal Server Error', $crawler->filter('h1.name')->text(), 'Неверный заголовок H1');
        assertNotEmpty($crawler->filter('.copyright')->text(), 'Не найден блок copyright');
    }
}
