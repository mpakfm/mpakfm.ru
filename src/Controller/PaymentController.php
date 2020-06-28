<?php

namespace App\Controller;

use App\Repository\PaymentRepository;
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
    public function result(Request $request, PaymentRepository $repository, Robokassa $robokassa)
    {
        $dt = new \DateTimeImmutable();
        $payment = $repository->find((int) $request->query->get('InvId'));

        try {
            Printu::log($request->query, $dt->format('H:i:s')."\t".'PaymentController::result $request->query', 'file');
            Printu::log($payment, $dt->format('H:i:s')."\t".'PaymentController::result $payment', 'file');

            $robokassa->verify($request);
            $payment->setResult(1);

        } catch (\Throwable $exception) {
            $payment->setResult(0);
            $payment->setError($exception->getMessage());
        }
        $payment->setPaymented(new \DateTimeImmutable());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($payment);
        $entityManager->flush();

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
        $entityManager = $this->getDoctrine()->getManager();
        $postFields = $robokassa->makePayment($request, $entityManager);

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
            ],
        ]);
    }
}
