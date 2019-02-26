<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CategorieController extends AbstractController
{
    /**
     * @Route("/administration/categorie", name="administration_categorie")
     */
    public function index(Request $request)
    {
        $categorie = $this
            ->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        $new_categorie = new Categorie();
        $form = $this->createFormBuilder($new_categorie)
            ->add('nom', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Ajouter une categorie'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_categorie = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($new_categorie);
            $entityManager->flush();

            return $this->redirectToRoute('administration_categorie');
        }

        return $this->render('administration/categorie/index.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
            'title' => 'Liste des categories'
        ]);
    }
}
