<?php

namespace App\Controller;

use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'title' => 'Сергей Фомин aka mpakfm',
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {

        Printu::log('test', 'Index');
        $response = new Response();
        return $response;
    }
}
