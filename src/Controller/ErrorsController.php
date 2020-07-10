<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 24.06.2020
 * Time: 0:13.
 */

namespace App\Controller;

use App\Repository\SitePropertyRepository;
use App\Service\BasePropertizer;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Error\LoaderError;

class ErrorsController extends AbstractController
{
    public function show(\Throwable $exception, SitePropertyRepository $sitePropertyRepository, BasePropertizer $basePropertizer)
    {
        if ('Access Denied.' == $exception->getMessage()) {
            $exception = new HttpException(403, 'Access Denied');
        }
        $className = get_class($exception);
        switch ($className) {
            case'Symfony\\Component\\HttpKernel\\Exception\\HttpException':
            case'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException':
                $statusCode = $exception->getStatusCode();
                $headers    = $exception->getHeaders();
                $errorText  = $exception->getMessage();
                Printu::obj($exception->getMessage())->dt()->title(' UA: ' . $_SERVER['HTTP_USER_AGENT'] . '; IP: ' . $_SERVER['REMOTE_ADDR'])->response('file')->file('errors.404.log')->show();

                break;
            default:
                $statusCode = '500';
                $headers    = [];
                $errorText  = 'Ошибка сервера';
                Printu::obj($exception->getMessage())->dt()->title('Exception in file ' . $exception->getFile() . ' in line ' . $exception->getLine())->response('file')->file('errors.500.log')->show();
        }

        try {
            $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);
            $response = new Response('', $statusCode, $headers);
            $result   = $this->render('bundles/TwigBundle/Exception/error' . $statusCode . '.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text'  => $errorText,
                'siteProp'    => $siteProp,
                'gtag'        => ('prod' == $_ENV['APP_ENV'] ? true : false),
                'meta'        => [
                    'title'       => $siteProp->getMetaTitle(),
                    'description' => $siteProp->getMetaDescription(),
                    'keywords'    => $siteProp->getMetaKeywords(),
                ],
            ], $response);

            return $result;
        } catch (LoaderError $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text'  => $errorText,
                'siteProp'    => $siteProp,
                'gtag'        => ('prod' == $_ENV['APP_ENV'] ? true : false),
                'meta'        => [
                    'title'       => $siteProp->getMetaTitle(),
                    'description' => $siteProp->getMetaDescription(),
                    'keywords'    => $siteProp->getMetaKeywords(),
                ],
            ], $response);
        } catch (\Throwable $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text'  => $errorText,
                'siteProp'    => $siteProp,
                'gtag'        => ('prod' == $_ENV['APP_ENV'] ? true : false),
                'meta'        => [
                    'title'       => $siteProp->getMetaTitle(),
                    'description' => $siteProp->getMetaDescription(),
                    'keywords'    => $siteProp->getMetaKeywords(),
                ],
            ], $response);
        }
    }
}
