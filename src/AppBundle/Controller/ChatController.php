<?php

namespace AppBundle\Controller;

use AppBundle\Form\MessageTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    /**
     * @Route("/chat", name="app.chat.chat")
     * @return Response
     */
    public function chatAction()
    {
        $listMessage = $this->get('snc_redis.default')
            ->lrange('chat', 0, -1);

        $listMessage = array_map(function ($item) {
            return json_decode($item, 1);
        }, $listMessage);
        krsort($listMessage);

        $form = $this->createForm(MessageTypeForm::class);

        return $this->render(':default:chat.html.twig', [
            'form' => $form->createView(),
            'listMessage' => $listMessage,
        ]);
    }
}
