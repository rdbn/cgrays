<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductFilterTypeForm;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\View()
     * @return Response
     */
    public function getProductsAllAction(Request $request)
    {
        $form = $this->createForm(ProductFilterTypeForm::class);
        $form->handleRequest($request);

        $data = [];
        if ($form->isSubmitted()) {
            foreach ($form->getIterator() as $name => $item) {
                $data[$name] = $item->getData();
            }
        }

        $products = $this->getDoctrine()->getRepository(Product::class)
            ->queryProductsByFilters($data);

        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        $products = [];
        foreach ($pagination as $item) {
            $products[] = $item;
        }

        $view = $this->view($products, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Rest\Get("/products/price/{id}", requirements={"id": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getProductsPriceAction(Request $request, $id)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)
            ->queryProductsPrice($id);

        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        $products = [];
        foreach ($pagination as $item) {
            $products[] = $item;
        }

        $view = $this->view($products, 200);
        return $this->handleView($view);
    }

    /**
     * @param int $productId
     * @param int $productPriceId
     *
     * @Rest\Get("/products/{productId}/{productPriceId}", requirements={"productId": "\d+", "productPriceId": "\d+"})
     * @Rest\View(serializerGroups={"product"})
     * @return Response
     */
    public function getProductsAction($productId, $productPriceId)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)
            ->findProductByIdAndPriceId($productId, $productPriceId);

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}
