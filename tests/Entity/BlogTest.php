<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 20.07.2020
 * Time: 2:27
 */

namespace App\Tests\Entity;

use App\Entity\Blog;
use App\Entity\Tags;
use App\Tests\TestCaseAbstract;
use DateTime;

/**
 * @covers Blog
 *
 * @internal
 */
class BlogTest extends TestCaseAbstract
{
    public function testAddTag()
    {
        $repoBlog = $this->em->getRepository(Blog::class);
        $repoTags = $this->em->getRepository(Tags::class);

        $tag = new Tags();
        $tag->setName('php');
        $tag->setCode('php');
        $repoTags->saveItem($tag);

        $blog = new Blog();
        $blog->setCreated(new DateTime());
        $blog->setName('Test post');
        $blog->setCode('test_post');
        $blog->setShortText('Test post');
        $blog->setHidden(false);
        $blog->addTag($tag);
        $repoBlog->saveItem($blog);

        assertNotEmpty($blog, 'blog is empty');
        assertSame('php', $blog->getTags()[0]->getName(), 'Неверное имя тэга');
        assertSame('php', $blog->getTags()[0]->getCode(), 'Неверный код тэга');
    }
}
