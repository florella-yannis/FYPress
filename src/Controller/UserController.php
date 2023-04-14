<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\ChangePasswordFormType;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'app_register',  methods:['GET', 'POST'])]
    public function register(Request $request, UserRepository $repository, UserPasswordHasherInterface $passwordHasher ): Response
    {

        $user = new User();

        $form = $this->createForm(RegisterFormType::class,$user) -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());
            
            $user->setRoles(['ROLE_USER']);

            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $repository->save($user, true);

            $this->addFlash('succes',"Votre inscription a été correctement enregistrée.");
            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('user/register_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/voir-mon-compte', name: 'show_profile',  methods:['GET'])]
    public function showProfile(EntityManagerInterface $entityManager):Response
    {

        //$user = new User();
        $articles = $entityManager->getRepository(Article::class)->findBy(['author' =>$this-> getUser()]);
        $commentaries =  $entityManager->getRepository(Commentary::class)->findBy(['author' =>$this-> getUser()]);

        

        return $this->render('user/show_profile.html.twig', [
            'articles'=> $articles,
            'commentaries'=>$commentaries
        ]);
    }

    #[Route('/changer-mon-mot-de-passe', name:'change_password', methods:['GET','POST'])]
    public function changePassword(Request $request, UserRepository $repository, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // recuperer le user en bdd
            $user = $repository->find($this->getUser());

            /*listing des étapes:
            1- un nouvel input => changepasswordFormType
            2- récupérer la valeur de cet input
            3- Hasher le currentPassword pour la comparaison en BDD
            4- condition de vérification
            5-si la verification est vérifiée, alors on exécute le code
              */

              //----------------------------VERIFICATION DU MDP----------------------------//

            $currentPassword = $form->get('currentPassword')->getData();

            if(! $hasher->isPasswordValid($user, $currentPassword)){
                $this->addFlash('warning', "Le mot de passe actuel n'est pas valide.");
                return $this->redirectToRoute('show_profile');
            }

            $user->setUpdatedAt(new DateTime());

            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($hasher->hashPassword($user,$plainPassword));

            $repository->save($user,true);

            $this->addFlash('succes',"Votre mot de passe a bien été modifié !");
            return $this->redirectToRoute('show_profile');
        }

        return $this->render('user/change_password_form.html.twig',[
            'form'=> $form->createView(),
        ]);
    }
}
