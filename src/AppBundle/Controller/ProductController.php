<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProductFilterTypeForm;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/", name="main")
     * @return Response
     */
    public function mainAction(Request $request)
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

        return $this->render('default/products.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/products/{id}", name="products")
     * @return Response
     */
    public function productsPriceAction(Request $request, $id)
    {
        $form = $this->createForm(ProductFilterTypeForm::class);
        $products = $this->getDoctrine()->getRepository(Product::class)
            ->queryProductsPrice($id);

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
     * @param int $productId
     * @param int $productPriceId
     *
     * @Route("/product/{productId}/{productPriceId}", name="product")
     * @return Response
     */
    public function productAction($productId, $productPriceId)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)
            ->findProductByIdAndPriceId($productId, $productPriceId);

        return $this->render('default/product.html.twig', [
            'product' => $product,
        ]);
    }
}
