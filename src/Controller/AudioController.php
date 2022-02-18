<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AudioController extends BaseController
{
    /**
     * @Route("/audio", name="audio")
     */
    public function index(Request $request)
    {
        $this->preLoad($request);

        return $this->baseRender('audio/index.html.twig', [
            'h1'        => 'Аудио',
            'h2'        => 'Северный гамбит',
        ]);
//        return $this->render('audio/index.html.twig', [
//            'controller_name' => 'AudioController',
//        ]);
    }
}
