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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Error\LoaderError;

class ErrorsController extends AbstractController
{
    public function show(HttpException $exception, DebugLoggerInterface $logger = null, Request $request, SitePropertyRepository $sitePropertyRepository, BasePropertizer $basePropertizer)
    {
        $statusCode = $exception->getStatusCode();
        $headers = $exception->getHeaders();

        $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);

        $response = new Response('', $statusCode, $headers);

        try {
            $result = $this->render('bundles/TwigBundle/Exception/error'.$statusCode.'.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $exception->getMessage(),
                'siteProp' => $siteProp,
            ], $response);
            return $result;
        } catch (LoaderError $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $exception->getMessage(),
                'siteProp' => $siteProp,
            ], $response);
        } catch (\Throwable $exceptionRender) {
            return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
                'status_code' => $statusCode,
                'status_text' => Response::$statusTexts[$statusCode],
                'error_text' => $exception->getMessage(),
                'siteProp' => $siteProp,
            ], $response);
        }
    }
}
