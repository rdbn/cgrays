<?php

namespace DotaBundle\Controller;

use DotaBundle\Entity\Skins;
use DotaBundle\Entity\ProductDota;
use DotaBundle\Form\ProductDotaFilterTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/dota")
 */
class ProductDotaController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/", name="app.product_dota.main")
     * @return Response
     */
    public function mainAction(Request $request)
    {
        $productHandler = $this->get('app.service.product_handler')
            ->handler($request, ProductDota::class, ProductDotaFilterTypeForm::class);

        return $this->render('default/products.html.twig', [
            'form' => $productHandler->getFormView(),
            'pagination' => $productHandler->getPaginationProduct(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/products/{id}", name="app.product_dota.products_price")
     * @return Response
     */
    public function productsPriceAction(Request $request, $id)
    {
        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        $form = $this->createForm(ProductDotaFilterTypeForm::class);
        $products = $this->getDoctrine()->getRepository(Skins::class)
            ->queryProductsPrice($id, $sort, $order);

        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        return $this->render(":default:products.html.twig", [
            "form" => $form->createView(),
            "pagination" => $pagination,
        ]);
    }

    /**
     * @param int $productPriceId
     *
     * @Route("/products/price/{productPriceId}", name="app.product_dota.product")
     * @return Response
     */
    public function productAction($productPriceId)
    {
        $product = $this->getDoctrine()->getRepository(Skins::class)
            ->findProductByIdAndPriceId($productPriceId);

        return $this->render('default/product.html.twig', [
            'product' => $product,
        ]);
    }
}
