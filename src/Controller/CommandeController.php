<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\MeubleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/create', name: 'app_commande_create')]
    public function create(
        SessionInterface $session,
        MeubleRepository $meubleRepo,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return $this->redirectToRoute('app_cart');
        }

        $commande = new Commande();
        $commande->setUser($this->getUser());
        $commande->setDateCommande(new \DateTime());
        $commande->setStatut('en attente');

        $total = 0;

        foreach ($cart as $id => $quantity) {
            $meuble = $meubleRepo->find($id);
            if ($meuble) {
                $ligne = new LigneCommande();
                $ligne->setMeuble($meuble);
                $ligne->setQuantite($quantity);
                $ligne->setPrixUnitaire($meuble->getPrix());
                $ligne->setCommande($commande);
                $em->persist($ligne);

                $total += $meuble->getPrix() * $quantity;
            }
        }

        $commande->setTotal($total);
        $em->persist($commande);
        $em->flush();

        $session->remove('cart');

        return $this->redirectToRoute('app_commande_history');
    }

    #[Route('/commande/history', name: 'app_commande_history')]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $commandes = $this->getUser()->getCommandes();

        return $this->render('commande/history.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}