<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index()
    {
        $produit = $this
            ->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
            'produit' => $produit,
            'title' => 'Liste des produits'
        ]);
    }

    /**
     * @Route("/produit/voir/{id}", name="produit_voir")
     */
    public function voir_produit($id)
    {
        $produit = $this
            ->getDoctrine()
            ->getRepository(Produit::class)
            ->find($id);

        return $this->render('produit/voir.html.twig', [
            'produit' => $produit,
            'title' => 'Voir produit'
        ]);
    }

    /**
     * @Route("/produit/ajouter", name="produit_ajouter")
     */
    public function ajouter_produit(Request $request)
    {
        $produit = new Produit();

        $form = $this->createFormBuilder($produit)
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', MoneyType::class)
            ->add('categorie', EntityType::class,
                array(
                    'class' => Categorie::class,
                    'query_builder' => function(EntityRepository $repository){
                        return $repository->createQueryBuilder('c')
                            ->select('c');
                    }
                ))
            ->add('save', SubmitType::class, ['label' => 'Ajouter un produit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit');
        }

        return $this->render('produit/ajouter.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter un produit'
        ]);
    }

    /**
     * @Route("/produit/modifier/{id}", name="produit_modifier")
     */
    public function modifier_produit(Request $request, Produit $produit)
    {
        $form = $this->createForm(ProduitType::class, $produit)
            ->add('save', SubmitType::class, ['label' => 'Modifier le produit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit');
        }
        return $this->render('produit/modifier.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier un produit'
        ]);
    }

    /**
     * @Route("/produit/supprimer/{id}", name="produit_supprimer")
     */
    public function supprimer_produit($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException(
                'Pas de produit avec l\'id : '.$id
            );
        }

        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('produit');
    }
}
