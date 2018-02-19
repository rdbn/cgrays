<?php

namespace SteamBundle\Security\Firewall;

use AppBundle\Entity\CasesDomain;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use SteamBundle\Security\Token\SteamToken;

class SteamListener implements ListenerInterface
{
    use TargetPathTrait;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var
     */
    private $rememberMeServices;

    /**
     * @var
     */
    private $providerKey;

    /**
     * @var
     */
    private $defaultRoute;

    /**
     * @var JWTManager
     */
    private $JWTManager;

    /**
     * SteamListener constructor.
     * @param $defaultRoute
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param Router $router
     * @param JWTManager $JWTManager
     * @param EntityManager $em
     */
    public function __construct(
        $defaultRoute,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        Router $router,
        JWTManager $JWTManager,
        EntityManager $em
    )
    {
        $this->defaultRoute = $defaultRoute;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->router = $router;
        $this->JWTManager = $JWTManager;
        $this->em = $em;
    }

    /**
     * @param $providerKey
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;
    }

    /**
     * @param $providerKey
     * @return mixed
     */
    public function getProviderKey($providerKey)
    {
        return $this->providerKey;
    }

    /**
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function setRememberMeServices(RememberMeServicesInterface $rememberMeServices)
    {
        $this->rememberMeServices = $rememberMeServices;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->get('_route');
        if ($routeName != 'connect_steam' && $routeName != 'cases_connect_steam') {
            return;
        }

        $token = new SteamToken();
        $token->setSteamId(str_replace("http://steamcommunity.com/openid/id/", "", $request->query->get('openid_claimed_id')));
        $token->setAttributes($request->query->all());

        $authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authToken);
        $targetPath = $this->getTargetPath($request->getSession(), $this->providerKey);

        if ($targetPath !== null) {
            $this->removeTargetPath($request->getSession(), $this->providerKey);
        } else {
            $targetPath = $this->router->generate($this->defaultRoute);
        }

        if ($routeName == 'connect_steam') {
            $response = new RedirectResponse($targetPath);
            if ($this->rememberMeServices !== null) {
                $this->rememberMeServices->loginSuccess($request, $response, $token);
            }
            $event->setResponse($response);
        } else if ($routeName == 'cases_connect_steam') {
            $casesDomain = $this->em->getRepository(CasesDomain::class)
                ->findOneBy(['uuid' => $request->headers->get('x-domain-id')]);

            $token = $this->JWTManager->create($token->getUser());

            $response = new RedirectResponse(
                "{$casesDomain->getDomain()}/login_check?token={$token}",
                301,
                ['x-origin-domain-name' => parse_url($casesDomain->getDomain())['host']]
            );
            $event->setResponse($response);
        }

        return;
    }
}