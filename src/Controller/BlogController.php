<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    /**
     * @Route("/blog/{post}", name="blog_post")
     */
    public function element(string $post, BlogRepository $repository)
    {
        $criteria = [
            'code' => $post,
        ];
        $elements = $repository->findBy($criteria, [], 1);

        return $this->baseRender('blog/element.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'element' => $elements[0]
        ]);
    }
    /**
     * @Route("/blog/{offset}", name="blog")
     */
    public function index(Request $request, BlogRepository $repository, int $offset = null)
    {
        $criteria = ['hidden' => 0];
        $order = ['id' => 'desc'];
        $limit = 20;
        $elements = $repository->findBy($criteria, $order, $limit, $offset);

        Printu::log($elements, 'elements', 'file');

        return $this->baseRender('blog/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'elements' => $elements,
        ]);
    }
}
