<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AdministrationController extends AbstractController
{
    /**
     * @Route("/administration", name="administration")
     */
    public function index()
    {
        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
        ]);
    }
    /**
     * @Route("/administration/utilisateur", name="administration_utilisateur")
     */
    public function utilisateur()
    {
        $utilisateur = $this
            ->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        return $this->render('administration/utilisateur/index.html.twig', [
            'utilisateur' => $utilisateur,
            'title' => 'Liste des utilisateurs'
        ]);
    }

    /**
     * @Route("/administration/utilisateur/ajouter", name="administration_utilisateur_ajouter")
     */
    public function ajouter_utilisateur(Request $request)
    {
        $utilisateur = new Utilisateur();
        //$utilisateur->setTask('Write a blog post');
        //$utilisateur->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($utilisateur)
            ->add('pseudo', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('mail', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('role_id', EntityType::class,
                array(
                    'class' => Role::class,
                    'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('u')
                            ->select('u');
                    }
                ))
            ->add('save', SubmitType::class, ['label' => 'Ajouter un utilisateur'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $utilisateur = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('administration_utilisateur');
        }

        return $this->render('administration/utilisateur/ajouter.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un utilisateur'
        ]);
    }

    /**
     * @Route("/administration/utilisateur/modifier/{id}", name="administration_utilisateur_modifier")
     */
    public function modifier_utilisateur(Request $request, Utilisateur $utilisateur)
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur)
            ->add('save', SubmitType::class, ['label' => 'Modifier un utilisateur']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('administration_utilisateur');
        }
        return $this->render('administration/utilisateur/modifier.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier un utilisateur'
        ]);
    }
}
