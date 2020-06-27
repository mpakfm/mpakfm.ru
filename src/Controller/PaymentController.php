<?php

namespace App\Controller;

use App\Service\Robokassa;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends BaseController
{
    public $status = ['success', 'fail'];

    /**
     * @Route("/payment/result", name="payment_result")
     */
    public function result(Request $request)
    {
        $dt = new \DateTimeImmutable();
        Printu::log($request->query, $dt->format('H:i:s')."\t".'PaymentController::result $request->query', 'file');

        return $this->baseRender('payment/result.html.twig', [
            'h1' => 'Сергей Фомин',
            'request' => [
                'all' => Printu::log($request->request->all(), 'all', true),
                'query' => Printu::log($request->query, 'query', true),
            ],
        ]);
    }

    /**
     * @Route("/payment/form", name="payment_form", methods={"POST"})
     */
    public function form(Request $request, Robokassa $robokassa)
    {
        $postFields = $robokassa->makePayment($request);

        return $this->json($postFields);
    }

    /**
     * @Route("/payment/{status}", name="payment_status")
     */
    public function paymentStatus(string $status, Request $request)
    {
        $dt = new \DateTimeImmutable();
        Printu::log($request->query, $dt->format('H:i:s')."\t".'PaymentController::paymentStatus $request->query for '.$status, 'file');

        if (!in_array($status, $this->status)) {
            throw new HttpException(404, 'Страница не найдена');
        }

        return $this->baseRender('payment/'.$status.'.html.twig', [
            'h1' => 'Сергей Фомин',
            'request' => [
                'status' => $status,
                'query' => Printu::log($request->query, 'query', true),
            ],
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function index(Request $request, Robokassa $robokassa)
    {
        if ('POST' == $request->getMethod()) {
            Printu::log($request->request->all(), 'request all', 'file');
            $postFields = $robokassa->makePayment($request);
            //Redirect
            return $this->redirectToRoute('payment');
        }
//        $mrh_login = 'mpakfm.ru';
//        $mrh_pass1 = 'bQ3GT01qRsVka30WKnrG';
//        $inv_id = 0;
//        $inv_desc = 'Техническая документация по ROBOKASSA';
//        $out_summ = '1.00';
//        $crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");

        return $this->baseRender('payment/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'rate' => '1000',
            'robokassa' => [
                'url' => $robokassa::$url,
            ]
        ]);
    }
}
