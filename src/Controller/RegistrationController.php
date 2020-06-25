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
use App\Repository\SitePropertyRepository;
use App\Service\BasePropertizer;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/signup", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        throw new HttpException(404, 'Page not found');
        // 1) постройте форму
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) обработайте отправку (произойдёт только в POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Зашифруйте пароль (вы также можете сделать это через слушатель Doctrine)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            Printu::log($user->getPlainPassword(), 'registerAction form $user->getPlainPassword()', 'file');
            Printu::log($user->getPassword(), 'registerAction form $user->getPassword()', 'file');
            Printu::log($user, 'registerAction form $user', 'file');

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
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $dt = new \DateTimeImmutable();
        Printu::log($request->query, $dt->format('d.m H:i:s')."\t".'login::login query', 'file');
        // получить ошибку входа, если она есть
        $error = $authUtils->getLastAuthenticationError();
        if ($error) {
            Printu::log($error->getMessage(), $dt->format('d.m H:i:s')."\t".'SecurityController::login error', 'file', 'error.log');
        }

        // последнее имя пользователя, введенное пользователем
        $lastUsername = $authUtils->getLastUsername();
        Printu::log($lastUsername, $dt->format('d.m H:i:s')."\t".'login::login $lastUsername', 'file');

        $form = $this->createForm(LoginType::class);

        return $this->render('admin/signin.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}
