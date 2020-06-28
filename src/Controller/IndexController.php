<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->baseRender('index/index.html.twig', [
            'h1' => 'Сергей Фомин',
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->redirectToRoute('index', [], 301);
    }

    /**
     * @Route("/portfolio", name="portfolio")
     */
    public function portfolio()
    {
        return $this->redirectToRoute('index', [], 301);
    }
}
