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
     * @Route("/contact/", name="contact_slash")
     */
    public function contactSlash()
    {
        return $this->redirectToRoute('index', [], 301);
    }
}
