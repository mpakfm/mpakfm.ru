<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Mpakfm\Printu;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    /**
     * @Route("/blog/fileupload", name="blog_fileupload")
     */
    public function fileupload(Request $request)
    {
        $dt = new \DateTimeImmutable();
        try {
            $file = $this->base64ToImage($request->request->get('a'), __DIR__.'/../../upload/img');
            Printu::log($file, $dt->format('H:i:s')."\t".'fileupload $file', 'file');
            return $this->json(['result' => $file]);
        } catch (\Throwable $exception) {
            Printu::log($exception->getMessage(), $dt->format('H:i:s')."\t".'fileupload $exception getMessage', 'file');
            return $this->json(['result' => $exception->getMessage()]);
        }
    }

    private function base64ToImage(string $base64String, string $outputFile)
    {
        $dt = new \DateTimeImmutable();
        $type0 = explode(';', $base64String);
        $type1 = explode('/', $type0[0]);
        $globalType = $type1[1];

        $data = explode(',', $base64String);
        $source = imagecreatefromstring(base64_decode($data[1]));
        Printu::log($source, $dt->format('H:i:s')."\t".'base64ToImage $source', 'file');
        Printu::log($globalType, $dt->format('H:i:s')."\t".'base64ToImage $globalType', 'file');
        //Printu::log(getimagesize($source), $dt->format('H:i:s')."\t".'base64ToImage getimagesize', 'file');

        if ('png' == $globalType) {
            $res = imagepng($source, $outputFile.'.png');
            exec('optipng -o7 img.png');
        } elseif ('jpg' == $globalType || 'pjpeg' == $globalType || 'jpeg' == $globalType || 'plain' == $globalType) {
            $res = imagejpeg($source, $outputFile.'.'.$globalType);
            exec('jpegoptim img');
        } else {
            echo 'This file type is not supported, or the input data is corrupted! ('.$globalType.')';
            $res = false;
        }
        Printu::log($res, $dt->format('H:i:s')."\t".'base64ToImage $res', 'file');
        imagedestroy($source);

        return $outputFile;
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
            $blogPost->setHidden(false);
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
        $criteria = ['hidden' => 0];
        $order = ['id' => 'desc'];
        $limit = 20;
        $elements = $repository->findBy($criteria, $order, $limit, $offset);

        return $this->baseRender('blog/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer / Блог',
            'elements' => $elements,
            'meta' => [
                'title' => $this->siteProperties->getMetaTitle().' / Блог',
                'description' => $this->siteProperties->getMetaDescription().' / Блог, журнал программиста',
                'keywords' => $this->siteProperties->getMetaKeywords().', блог, журнал программиста',
            ],
        ]);
    }
}
