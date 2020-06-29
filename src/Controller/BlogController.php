<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    /**
     * @Route("/blog/create", name="blog_create")
     */
    public function post_create(Request $request)
    {
//        if ('POST' == $request->getMethod()) {
//            Printu::log($request->request->all(), 'post_create POST', 'file');
//            return $this->redirectToRoute('blog');
//        }
        $blogPost = new Blog();
        $form = $this->createForm(BlogType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blogPost->setCreated(new \DateTimeImmutable());
            $blogPost->setHidden(false);
            $strInput = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', '\\', '+', '`', '~'];
            $strOutput = ['a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'iy', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'tc', 'ch', 'sh', 'csh', '', 'i', '', 'e', 'yu', 'ya', '_', '_', '_', '-', '-', '-'];
            $code = mb_strtolower(preg_replace("/\s+/", ' ', $blogPost->getName()));
            Printu::log($code, '$code strtolower', 'file');
            $code = str_replace($strInput, $strOutput, $code);
            $blogPost->setCode($code);
            Printu::log($request->request->all(), 'post_create POST', 'file');
            Printu::log($blogPost, 'post_create $blogPost', 'file');
            //return $this->redirectToRoute('blog');
        }

        $error = null;

        return $this->baseRender('blog/create.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

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
            'element' => $elements[0],
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

        return $this->baseRender('blog/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'elements' => $elements,
        ]);
    }
}
