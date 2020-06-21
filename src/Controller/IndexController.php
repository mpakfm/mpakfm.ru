<?php

namespace App\Controller;

use App\Repository\SitePropertyRepository;
use App\Service\BasePropertizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(LoggerInterface $logger, SitePropertyRepository $sitePropertyRepository, BasePropertizer $basePropertizer)
    {
        $logger->info('index');
        $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);

        return $this->render('index/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'siteProp' => $siteProp,
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(LoggerInterface $logger)
    {
        $logger->info('test');
        $response = new Response();

        return $response;
    }
}
