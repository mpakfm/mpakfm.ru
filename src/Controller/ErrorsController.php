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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Error\LoaderError;

class ErrorsController extends AbstractController
{
    public function show(\Throwable $exception, DebugLoggerInterface $logger = null, Request $request, SitePropertyRepository $sitePropertyRepository, BasePropertizer $basePropertizer)
    {
        $dt = new \DateTimeImmutable();
        if ('Access Denied.' == $exception->getMessage()) {
            $exception = new HttpException(403, 'Access Denied');
        }
        $className = get_class($exception);
        switch ($className) {
            case'Symfony\\Component\\HttpKernel\\Exception\\HttpException':
            case'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException':
                $statusCode = $exception->getStatusCode();
                $headers = $exception->getHeaders();
                $errorText = $exception->getMessage();
                Printu::log($exception->getMessage(), $dt->format('d.m H:i:s')."\t".' UA: '.$_SERVER['HTTP_USER_AGENT'].'; IP: '.$_SERVER['REMOTE_ADDR'], 'file', 'errors.404.log');
                break;
            default:
                $statusCode = '500';
                $headers = [];
                $errorText = 'Ошибка сервера';
                Printu::log($exception->getMessage(), $dt->format('d.m H:i:s')."\t".'Exception in file '.$exception->getFile().' in line '.$exception->getLine(), 'file', 'errors.500.log');
        }

        try {
            $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);
            $response = new Response('', $statusCode, $headers);
            $result = $this->render('bundles/TwigBundle/Exception/error'.$statusCode.'.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $errorText,
                'siteProp' => $siteProp,
                'gtag' => ('prod' == $_ENV['APP_ENV'] ? true : false),
            ], $response);

            return $result;
        } catch (LoaderError $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $errorText,
                'siteProp' => $siteProp,
                'gtag' => ('prod' == $_ENV['APP_ENV'] ? true : false),
            ], $response);
        } catch (\Throwable $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $errorText,
                'siteProp' => $siteProp,
                'gtag' => ('prod' == $_ENV['APP_ENV'] ? true : false),
            ], $response);
        }
    }
}
