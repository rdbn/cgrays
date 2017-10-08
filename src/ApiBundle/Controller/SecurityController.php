<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends FOSRestController
{
    /**
     * @param Request $request
     * @Rest\Post("/login_check")
     */
    public function postLoginCheckAction(Request $request) {}
}
