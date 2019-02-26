<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $produit = $this
            ->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('home/index.html.twig', [
            'produit' => $produit,
            'title' => 'Liste des produits'
        ]);
    }
}
