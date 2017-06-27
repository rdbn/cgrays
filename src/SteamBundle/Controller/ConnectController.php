<?php

namespace SteamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConnectController extends Controller
{
    /**
     * @param Request $request
     * @Route("/connect/steam", name="connect_steam")
     */
    public function indexAction(Request $request) {}
}
