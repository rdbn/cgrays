<?php

namespace ApiBundle\Controller;

use AppBundle\Form\NewsCommentTypeForm;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\Post("/news/add/like")
     * @Rest\View()
     * @return Response
     */
    public function postNewsAddLikeAction(Request $request)
    {
        $newsId = (int) $request->request->getInt('news_id');
        if ($newsId <= 0) {
            return $this->handleView($this->view('Bad request', 400));
        }

        $newsLikeService = $this->get('api.service.news_like')
            ->addLike($newsId);

        $view = $this->view(['count_like' => $newsLikeService->getCountLike($newsId)], 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/news/add/comment")
     * @Rest\View()
     * @return Response
     */
    public function postNewsAddCommentAction(Request $request)
    {
        $form = $this->createForm(NewsCommentTypeForm::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //try {
                $comment = $this->get('api.service.news_comment')
                    ->getComment(
                        $form->get('news')->getData()->getId(),
                        $form->get('comment')->getData()
                    );

                $view = $this->view($comment, 200);
//            } catch (\Exception $e) {
//                $view = $this->view('Bad request', 400);
//            }
        } else {
            $view = $this->view($form->getErrors(), 400);
        }

        return $this->handleView($view);
    }
}
