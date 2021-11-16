<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Service\JsonDataResponse;
use DateTimeImmutable;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    use JsonDataResponse;

    /**
     * @Route("/blog/action", name="blog_action")
     */
    public function action(Request $request)
    {
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_USER', $user->getRoles())) {
            $this->jsonResult  = false;
            $this->jsonError[] = 'Access denied';

            return $this->makeJsonResult([
                'id' => $request->request->get('id'),
            ]);
        }
        $em   = $this->getDoctrine()->getManager();
        if ($request->request->get('id')) {
            $repo = $em->getRepository(Blog::class);
            $blog = $repo->findOneBy(['id' => $request->request->get('id')]);
        }
        if (!$blog) {
            throw new \Exception('Unknown post');
        }
        switch ($request->request->get('action')) {
            case'hidden':
                $data = $this->hiddenAction($blog);

                break;
            case'delete':
                $data = $this->deleteAction($blog);

                break;
            default:
                throw new \Exception('Unknown action');
        }
        $this->jsonResult = true;

        return $this->makeJsonResult($data);
    }

    /**
     * @Route("/blog/create", name="blog_create")
     */
    public function postCreate(Request $request)
    {
        $this->preLoad($request);
        if (!$this->getUser()) {
            throw new NotFoundHttpException();
        }
        $blogPost = new Blog();
        $form     = $this->createForm(BlogType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $blogPost->setCreated(new \DateTimeImmutable());
            $strInput  = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', '\\', '+', '`', '~'];
            $strOutput = ['a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'iy', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'tc', 'ch', 'sh', 'csh', '', 'i', '', 'e', 'yu', 'ya', '_', '_', '_', '-', '-', '-'];
            $code      = mb_strtolower(preg_replace('/\\s+/', ' ', $blogPost->getName()));
            $code      = str_replace($strInput, $strOutput, $code);
            $blogPost->setCode($code);
            $repo      = $em->getRepository(Blog::class);
            $tryByCode = $repo->findOneBy(['code' => $blogPost->getCode()]);

            if (!$tryByCode) {
                $em->persist($blogPost);
                $em->flush();

                return $this->redirectToRoute('blog');
            }
            $form->addError(new FormError('Необходимо изменить название, дублирование кода.'));
        }

        $error = null;

        return $this->baseRender('blog/create.html.twig', [
            'h1'      => 'Сергей Фомин',
            'h2'      => 'Web Developer / Блог',
            'error'   => $error,
            'form'    => $form->createView(),
            'element' => $blogPost,
        ]);
    }

    /**
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function edit(Request $request, int $id, BlogRepository $repository)
    {
        $this->preLoad($request);
        $user = $this->getUser();
        if (!$user || !$user->isAdmin()) {
            throw new AccessDeniedHttpException();
        }
        $criteria = [
            'id' => $id,
        ];
        $element = $repository->findOneBy($criteria, []);

        $form = $this->createForm(BlogType::class, $element);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $element->setUpdated(new DateTimeImmutable());
            $repository->saveItem($element);

            return $this->redirect('/blog/' . $element->getCode());
        }

        $error = null;

        return $this->baseRender('blog/create.html.twig', [
            'h1'      => 'Сергей Фомин',
            'h2'      => 'Web Developer / Блог',
            'error'   => $error,
            'form'    => $form->createView(),
            'element' => $element,
        ]);
    }

    /**
     * @Route("/blog/{post}", name="blog_post")
     */
    public function element(Request $request, string $post, BlogRepository $repository)
    {
        $this->preLoad($request);

        $criteria = [
            'code' => $post,
        ];
        $elements = $repository->findBy($criteria, [], 1);

        if (!$elements) {
            $criteria = [
                'alias' => $post,
            ];
            $elements = $repository->findBy($criteria, [], 1);
            if (!$elements) {
                throw new NotFoundHttpException('Страница не найдена');
            }
        }

        $user = $this->getUser();

        $lastModified = $elements[0]->getUpdated() ? $elements[0]->getUpdated() : $elements[0]->getCreated();
        $response     = new Response();

        return $this->baseRender('blog/element.html.twig', [
            'h1'          => 'Сергей Фомин',
            'h2'          => 'Web Developer / Блог',
            'element'     => $elements[0],
            'access_edit' => ($user && $user->isAdmin() ? true : false),
            'meta'        => [
                'description' => $this->siteProperties->getMetaDescription() . ' / Блог, журнал программиста',
                'keywords'    => $this->siteProperties->getMetaKeywords() . ', блог, журнал программиста',
            ],
        ], $response, $lastModified);
    }

    /**
     * @Route("/blog/{offset}", name="blog")
     */
    public function index(Request $request, BlogRepository $repository, int $offset = null)
    {
        $this->preLoad($request);
        $criteria = [];
        $user     = $this->getUser();
        if (!$user) {
            $criteria['hidden'] = 0;
        }
        $order    = ['id' => 'desc'];
        $limit    = 20;
        $elements = $repository->findBy($criteria, $order, $limit, $offset);

        $lastModified = $elements[0]->getUpdated() ? $elements[0]->getUpdated() : $elements[0]->getCreated();
        $response     = new Response();

        return $this->baseRender('blog/index.html.twig', [
            'h1'          => 'Сергей Фомин',
            'h2'          => 'Web Developer / Блог',
            'elements'    => $elements,
            'access_edit' => ($user && $user->isAdmin() ? true : false),
            'meta'        => [
                'title'       => $this->siteProperties->getMetaTitle() . ' / Блог',
                'description' => $this->siteProperties->getMetaDescription() . ' / Блог, журнал программиста',
                'keywords'    => $this->siteProperties->getMetaKeywords() . ', блог, журнал программиста',
            ],
        ], $response, $lastModified);
    }

    private function hiddenAction(Blog $blog): array
    {
        $blog->toggleHidden();
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Blog::class);
        $repo->saveItem($blog);

        return [
            'id'     => $blog->getId(),
            'hidden' => $blog->getHidden(),
        ];
    }

    private function deleteAction(Blog $blog): array
    {
        $id = $blog->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();

        return [
            'id'      => $id,
            'deleted' => true,
        ];
    }
}
