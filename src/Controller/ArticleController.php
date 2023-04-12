<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class ArticleController extends AbstractController
{
    #[Route('/ajouter-un-article', name: 'create_article',methods:['GET', 'POST'])]
    public function createArticle(Request $request, ArticleRepository $repository, SluggerInterface $slugger): Response
    {

        $article = new Article();

        $form = $this->createForm(ArticleFormType::class,$article)
        ->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());

            $article->setAlias($slugger->slug($article->getTitle()));

            #set de la relarion entre article et user
            $article->setAuthor($this->getUser());

            $photo = $form->get('photo')->getdata();

            if($photo){

            //     $this->handleFile($article, $photo, $slugger);
            // }

            // $repository->save($article, true);

            // $this->addFlash('succes', "L'article est en ligne avec succès.");
            // return $this->redirectToRoute('show_dashboard');
        }
    }


        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
