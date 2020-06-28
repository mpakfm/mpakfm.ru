<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Service\Robokassa;
use Mpakfm\Printu;
use Symfony\Component\Config\Definition\Exception\Exception;
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

        return $this->json(['result' => $payment->setResult]);
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
    public function paymentStatus(string $status, Request $request, PaymentRepository $repository)
    {
        $dt = new \DateTimeImmutable();
        if ($request->query->get('InvId')) {
            Printu::log($request->query->all(), $dt->format('H:i:s')."\t".'PaymentController::paymentStatus $request->query for '.$status, 'file');
            $invId = (int) $request->query->get('InvId');
        } elseif ($request->request->get('InvId')) {
            Printu::log($request->request->all(), $dt->format('H:i:s')."\t".'PaymentController::paymentStatus $request->request for '.$status, 'file');
            $invId = (int) $request->request->get('InvId');
        }
        if (!$invId) {
            throw new Exception('Empty payment id '.$invId);
        }
        $payment = $repository->find($invId);
        if (!$payment) {
            throw new Exception('Unknown payment id '.$invId);
        }
        $payment->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($payment);
        $entityManager->flush();

        if (!in_array($status, $this->status)) {
            throw new HttpException(404, 'Страница не найдена');
        }

        return $this->baseRender('payment/'.$status.'.html.twig', [
            'h1' => 'Сергей Фомин',
            'request' => [
                'status' => $status,
            ],
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function index(Request $request, Robokassa $robokassa)
    {
        return $this->baseRender('payment/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'rate' => '1200',
            'robokassa' => [
                'url' => $robokassa::$url,
                'test' => $robokassa::IS_TEST,
            ],
        ]);
    }
}
