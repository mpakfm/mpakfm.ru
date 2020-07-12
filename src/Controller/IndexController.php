<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $this->preLoad($request);

        return $this->baseRender('index/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer',
        ]);
    }

    /**
     * @Route("/personal-agreement", name="personal agreement")
     */
    public function personalAgreement(Request $request)
    {
        $this->preLoad($request);

        return $this->baseRender('index/agreement.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer',
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
