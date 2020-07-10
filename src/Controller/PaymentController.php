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
        $this->preLoad($request);
        $dt = new \DateTimeImmutable();

        try {
            if ($request->query->get('InvId')) {
                $payment = $repository->find((int) $request->query->get('InvId'));
            } elseif ($request->request->get('InvId')) {
                $payment = $repository->find((int) $request->request->get('InvId'));
            }

            $robokassa->verify($request, 2);
            $payment->setResult(1);
        } catch (\Throwable $exception) {
            $payment->setResult(0);
            $payment->setError($exception->getMessage());
            Printu::obj($exception->getMessage())->dt()->title('Payment::result exception in file ' . $exception->getFile() . ' in line ' . $exception->getLine())->response('file')->file('error')->show();
        }
        $payment->setPaymented(new \DateTimeImmutable());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($payment);
        $entityManager->flush();

        return $this->json(['result' => $payment->getResult()]);
    }

    /**
     * @Route("/payment/form", name="payment_form", methods={"POST"})
     */
    public function form(Request $request, Robokassa $robokassa)
    {
        $this->preLoad($request);
        $entityManager = $this->getDoctrine()->getManager();
        $postFields    = $robokassa->makePayment($request, $entityManager);

        return $this->json($postFields);
    }

    /**
     * @Route("/payment/{status}", name="payment_status")
     */
    public function paymentStatus(string $status, Request $request, PaymentRepository $repository, Robokassa $robokassa)
    {
        $this->preLoad($request);
        $dt    = new \DateTimeImmutable();
        $invId = null;
        if ($request->query->get('InvId')) {
            $invId = (int) $request->query->get('InvId');
        } elseif ($request->request->get('InvId')) {
            $invId = (int) $request->request->get('InvId');
        }
        if (!$invId) {
            return $this->redirectToRoute('payment');
        }
        $payment = $repository->find($invId);
        if (!$payment) {
            return $this->redirectToRoute('payment');
        }

        if (!in_array($status, $this->status)) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $paymentData = null;

        try {
            $robokassa->verify($request, 1);
            $paymentData = [
                'id'          => $payment->getId(),
                'email'       => $payment->getEmail(),
                'money'       => $payment->getMoney(),
                'description' => $payment->getDescription(),
                'created'     => $payment->getCreated(),
            ];
        } catch (\Throwable $exception) {
            Printu::obj($exception->getMessage())->dt()->title('PaymentController::paymentStatus verify exception')->response('file')->file('error')->show();
        }

        if (is_null($payment->getStatus()) || '' == $payment->getStatus()) {
            $payment->setStatus($status);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();
        }

        return $this->baseRender('payment/' . $status . '.html.twig', [
            'h1'      => 'Сергей Фомин',
            'h2'      => 'Web Developer / Оплата',
            'request' => [
                'status' => $status,
            ],
            'payment' => $paymentData,
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function index(Request $request, Robokassa $robokassa)
    {
        $this->preLoad($request);

        return $this->baseRender('payment/index.html.twig', [
            'h1'        => 'Сергей Фомин',
            'h2'        => 'Web Developer / Оплата',
            'rate'      => '1200',
            'robokassa' => [
                'url'  => $robokassa::$url,
                'test' => $robokassa::IS_TEST,
            ],
        ]);
    }
}
