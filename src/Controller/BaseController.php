<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 27.06.2020
 * Time: 12:41.
 */

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\SiteProperty;
use App\Service\BasePropertizer;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    public $isCached = true;
    public $cacheTime = 3600;

    public function __construct()
    {
    }

    public function preLoad($name = 'main')
    {
    }

    public function baseRender(string $view, array $parameters = [], Response $response = null, $last_modified = null): Response
    {
        $sitePropertyRepository = $this->getDoctrine()->getRepository(SiteProperty::class);
        $blogRepository = $this->getDoctrine()->getRepository(Blog::class);
        $basePropertizer = new BasePropertizer();
        $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);
        $blogListCount = $blogRepository->getCount();
        $parameters['siteProp'] = $siteProp;
        $parameters['gtag'] = ('prod' == $_ENV['APP_ENV'] ? true : false);
        $parameters['user'] = $this->getUser();
        $parameters['bloglist'] = $blogListCount;
        if (null === $response) {
            $response = new Response();
        }

        if ($this->isCached && $this->cacheTime) {
            $response->setSharedMaxAge($this->cacheTime);
        }
        // (необязательно) установите пользовательскую директиву
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $dt = new \DateTimeImmutable();
        $dtMonth = $dt->add(new \DateInterval('P1M'));
        //$dtMonthString = gmdate('D, d M Y H:i:s T', $dtMonth->format('U'));
        $response->setExpires($dtMonth);
        // устанавливает заголовки для кэширования одним вызовом
        $response->setCache([
            'last_modified' => $last_modified ? $last_modified : $siteProp->getLastUpdate(),
            'max_age' => $this->cacheTime ? $this->cacheTime : 0,
            's_maxage' => $this->cacheTime ? $this->cacheTime : 0,
            'private' => true,
        ]);

        return $this->render($view, $parameters, $response);
    }
}
