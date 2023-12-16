<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'list_product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        return $this->render('product/user_index.html.twig', [
            'products' => $products,
        ]);

    }
    #[Route('/product/product byCategory/{id}', name: 'product_by_category')]
    public  function productByCatAction(ManagerRegistry $doctrine ,$id):Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        $products = $category->getProducts();
        $categories = $doctrine->getRepository('App\Entity\Category')->findAll();
        return $this->render('product/index.html.twig', ['products' => $products,
            'categories'=>$categories]);
    }
    #[Route('/product/create', name: 'create_product', methods: ['GET', 'POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload file
            $productImage = $form->get('Image')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }


            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('list_product');
        }
        return $this->renderForm('product/create.html.twig', ['form' => $form,]);
    }
    #[Route('/product/edit', name: 'edit_product', methods: ['GET', 'POST'])]
    public function editAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload file
            $productImage = $form->get('Image')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }


            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('list_product');
        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form,]);
    }
}
