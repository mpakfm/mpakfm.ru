<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 14.07.2020
 * Time: 22:55.
 */

namespace App\Tests\Controller;

use App\Controller\BlogController;
use App\Entity\Blog;
use App\Tests\TestCaseAbstract;
use DateTime;

/**
 * @internal
 * @covers BlogController
 */
class BlogControllerTest extends TestCaseAbstract
{
    /**
     * @covers BlogController::index
     */
    public function testIndex()
    {
        $repo = $this->em->getRepository(Blog::class);
        $blog = new Blog();
        $blog->setCreated(new DateTime());
        $blog->setName('Test post');
        $blog->setCode('test_post');
        $blog->setShortText('Test post');
        $blog->setHidden(false);
        $repo->saveItem($blog);

        $client  = static::createClient();
        $crawler = $client->request('GET', '/blog');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин', $crawler->filter('h1')->text(), 'Ошибка значения h1');
        assertSame('Web Developer / Блог', $crawler->filter('h2')->text(), 'Ошибка значения h2');
    }

    /**
     * @covers BlogController::element
     */
    public function elementIndex()
    {
        $repo = $this->em->getRepository(Blog::class);
        $blog = new Blog();
        $blog->setCreated(new DateTime());
        $blog->setName('Test post');
        $blog->setCode('test_post');
        $blog->setShortText('Test post');
        $blog->setHidden(false);
        $repo->saveItem($blog);

        $client  = static::createClient();
        $crawler = $client->request('GET', '/blog/test_post');
        assertSame(200, $client->getResponse()->getStatusCode(), 'Ошибка StatusCode');
        assertSame('Сергей Фомин', $crawler->filter('h1')->text(), 'Ошибка значения h1');
        assertSame('Web Developer / Блог', $crawler->filter('h2')->text(), 'Ошибка значения h2');
        assertSame('Test post', $crawler->filter('h2.heading')->text(), 'Ошибка значения h2');
    }
}
