<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Entity\NewsComment;
use AppBundle\Form\NewsCommentTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/news", name="app.news.list")
     * @return Response
     */
    public function listAction(Request $request)
    {
        $news = $this->getDoctrine()->getRepository(News::class)
            ->queryNews();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $news,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render(':default:news_list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param int $id
     *
     * @Route("/news/{id}", requirements={"id": "\d+"}, name="app.news.news")
     * @return Response
     */
    public function newsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)
            ->findOneBy(['id' => $id]);

        $newsLikes = $em->getRepository(News::class)
            ->findCountLikeNewsByNewsId($id);

        $newsComment = $em->getRepository(NewsComment::class)
            ->findBy(['news' => $id]);

        $form = $this->createForm(NewsCommentTypeForm::class);

        return $this->render(':default:news.html.twig', [
            'form' => $form->createView(),
            'newsComment' => $newsComment,
            'newsLikes' => $newsLikes,
            'news' => $news,
        ]);
    }
}
