<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 25.06.2020
 * Time: 0:29.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserType;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends BaseController
{
    /**
     * @Route("/signup", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        throw new HttpException(404, 'Page not found');
        $this->preLoad($request);
        // 1) постройте форму
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) обработайте отправку (произойдёт только в POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Зашифруйте пароль (вы также можете сделать это через слушатель Doctrine)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) сохраните Пользователя!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... сделайте любую другую работу - вроде отправки письма и др
            // может, установите "флеш" сообщение об успешном выполнении для пользователя

            return $this->redirectToRoute('/admin');
        }

        return $this->render(
            'admin/signup.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/login", name="login")
     * @throws \Exception
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $this->preLoad($request);
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $dt = new \DateTimeImmutable();
        // Получить ошибку входа, если она есть
        $error = $authUtils->getLastAuthenticationError();
        if ($error) {
            Printu::obj($error->getMessage())->dt()->title('SecurityController::login error')->response('file')->file('error')->show();
        }

        // Последнее имя пользователя, введенное пользователем
        $lastUsername = $authUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->baseRender('index/signin.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer',
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}
