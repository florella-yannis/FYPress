<?php

namespace App\Controller;

use DateTime;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class CategoryController extends AbstractController
{
    #[Route('/ajouter-une-categorie', name:'add_category', methods:['GET','POST'])]
    public function addCategory(Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class,$category) -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());
            
            $category->setAlias($slugger->slug($category->getName()));

            $repository->save($category, true);

            $this->addFlash('succes',"Votre categorie à bien été enregistrée.");
            return $this->redirectToRoute('show_dashboard');
            
        }

        return $this->render('category/add_category.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('modifier-categorie/{id}', name:'update_category', methods:['GET','POST'])]
    public function updateCategory(Request $request, Category $category, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $form= $this->createForm(CategoryFormType::class, $category)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $category->setUpdatedAt(new DateTime());

            $category->setAlias($slugger->slug($category->getName()));

            $repository->save($category,true);

            $this->addFlash('succes', "La modification a bien été enregistré");
            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('category/add_category.html.twig',[
            'form' => $form->createView(),
            'category'=> $category
        ]);
    }

    #[Route('/archiver-une-categorie/{id}', name:'soft_delete_category', methods:['GET'])]
    public function softDeleteCategory(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(new DateTime());

        $repository->save($category, true);

        $this->addFlash('succes', "La catégorie" . $category->getName() . "a bien été archivé");
        return $this->redirectToRoute('show_dashboard');

    }

    #[Route('/restaurer-une-categorie/{id}', name:'restore_category', methods:['GET'])]
    public function restorecategory(Category $category, CategoryRepository $repository)
    {
        $category->setDeletedAt(null);

        $repository->save($category, true);

        $this->addFlash('succes', "La catégorie" . $category->getName() . "a bien été restaurer");
        return $this->redirectToRoute('show_dashboard');
    }

    #[Route('/supprimer-une-categorie/{id}', name:'hard_delete_category', methods:['GET'])]
    public function hardDeleteCategory(Category $category, CategoryRepository $repository): Response
    {

        $repository->remove($category, true);

        $this->addFlash('succes', "La catégorie a bien été supprimé définitivement.");
        return $this->redirectToRoute('show_dashboard');
    }
}


