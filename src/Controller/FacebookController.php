<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 12.07.2020
 * Time: 23:02.
 */

namespace App\Controller;

use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends BaseController
{
    /**
     * Link to this controller to start the "connect" process.
     *
     * @Route("/connect/facebook", name="connect_facebook_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        Printu::obj(getenv('OAUTH_FACEBOOK_ID'))->title('getenv OAUTH_FACEBOOK_ID');
        Printu::obj($_ENV['OAUTH_FACEBOOK_ID'])->title('$_ENV OAUTH_FACEBOOK_ID');
        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'email', // the scopes you want to access
            ])
            ;
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml.
     *
     * @Route("/connect/facebook/check", name="connect_facebook_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        Printu::obj($request->request->all())->title('FacebookController::connectCheckAction request');
        Printu::obj($request->query->all())->title('FacebookController::connectCheckAction query');

        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $clientRegistry->getClient('facebook_main');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
            $user = $client->fetchUser();

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            Printu::obj($user)->title('FacebookController::connectCheckAction $user');

            return $this->redirectToRoute('index');
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            Printu::obj($e->getMessage())->title('FacebookController::connectCheckAction $e->getMessage()');

            throw new Exception($e->getMessage());
        }

        return $this->baseRender('index/index.html.twig', [
            'h1' => 'Сергей Фомин',
            'h2' => 'Web Developer',
        ]);
    }
}
