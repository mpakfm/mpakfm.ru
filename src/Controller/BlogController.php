<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Service\JsonDataResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
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
        $em = $this->getDoctrine()->getManager();
        $blog = null;
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
        }
        $this->jsonResult = true;

        return $this->makeJsonResult($data);
    }

    private function hiddenAction(Blog $blog): array
    {
        $blog->toggleHidden();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Blog::class);
        $repo->saveItem($blog);

        return [
            'id' => $blog->getId(),
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
            'id' => $id,
            'deleted' => true,
        ];
    }

    /**
     * @Route("/blog/create", name="blog_create")
     */
    public function post_create(Request $request)
    {
        $this->preLoad();
        if (!$this->getUser()) {
            throw new NotFoundHttpException();
        }
        $blogPost = new Blog();
        $form = $this->createForm(BlogType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $blogPost->setCreated(new \DateTimeImmutable());
            $strInput = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', '\\', '+', '`', '~'];
            $strOutput = ['a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'iy', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'tc', 'ch', 'sh', 'csh', '', 'i', '', 'e', 'yu', 'ya', '_', '_', '_', '-', '-', '-'];
            $code = mb_strtolower(preg_replace("/\s+/", ' ', $blogPost->getName()));
            $code = str_replace($strInput, $strOutput, $code);
            $blogPost->setCode($code);
            $repo = $em->getRepository(Blog::class);
            $tryByCode = $repo->findOneBy(['code' => $blogPost->getCode()]);

            if (!$tryByCode) {
                $em->persist($blogPost);
                $em->flush();

                return $this->redirectToRoute('blog');
            } else {
                $form->addError(new FormError('Необходимо изменить название, дублирование кода.'));
            }
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
        $this->preLoad();

        $criteria = [
            'code' => $post,
        ];
        $elements = $repository->findBy($criteria, [], 1);

        if (!$elements) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->baseRender('blog/element.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'element' => $elements[0],
            'meta' => [
                'description' => $this->siteProperties->getMetaDescription().' / Блог, журнал программиста',
                'keywords' => $this->siteProperties->getMetaKeywords().', блог, журнал программиста',
            ],
        ]);
    }

    /**
     * @Route("/blog/{offset}", name="blog")
     */
    public function index(Request $request, BlogRepository $repository, int $offset = null)
    {
        $this->preLoad();
        $criteria = [];
        $user = $this->getUser();
        if (!$user) {
            $criteria['hidden'] = 0;
        }
        $order = ['id' => 'desc'];
        $limit = 20;
        $elements = $repository->findBy($criteria, $order, $limit, $offset);

        return $this->baseRender('blog/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'elements' => $elements,
            'access_edit' => ($user && in_array('ROLE_USER', $user->getRoles()) ? true : false),
            'meta' => [
                'title' => $this->siteProperties->getMetaTitle().' / Блог',
                'description' => $this->siteProperties->getMetaDescription().' / Блог, журнал программиста',
                'keywords' => $this->siteProperties->getMetaKeywords().', блог, журнал программиста',
            ],
        ]);
    }
}
