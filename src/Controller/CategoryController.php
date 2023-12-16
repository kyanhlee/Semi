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

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'list_category')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository('App\Entity\Category')->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/details/{id}', name: 'details_category')]
    public  function detailsAction(ManagerRegistry $doctrine ,$id)
    {
        $category = $doctrine->getRepository('App\Entity\Category')->find($id);

        return $this->render('category/details.html.twig', ['categories' => $category]);
    }

    #[Route('/category/create', name: 'create_category', methods: ['GET', 'POST'])]
    public function createAction(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category Added'
            );
            return $this->redirectToRoute('list_category');
        }
        return $this->renderForm('category/create.html.twig', ['form' => $form,]);
    }
    #[Route('/category/edit/{id}', name: 'edit_category')]
    public function editAction(ManagerRegistry $doctrine, int $id,Request $request): Response{
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, @$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('list_category', [
                'id' => $category->getId()
            ]);

        }
        return $this->renderForm('category/edit.html.twig', ['form' => $form,]);
    }
    #[Route('/category/delete/{id}', name: 'delete_category')]
    public function deleteAction(ManagerRegistry $doctrine,$id)
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository('App\Entity\Category')->find($id);
        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'error',
            'Category is deleted'
        );

        return $this->redirectToRoute('list_category');
    }

}
