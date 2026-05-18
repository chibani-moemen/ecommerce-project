<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\MeubleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
        CommandeRepository $commandeRepo,
        MeubleRepository $meubleRepo,
        UserRepository $userRepo,
        LigneCommandeRepository $ligneRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commandes = $commandeRepo->findAll();
        $chiffreAffaires = array_sum(array_map(fn($c) => $c->getTotal(), $commandes));
        $nbClients = count($userRepo->findAll());
        $nbCommandes = count($commandes);

        // Revenue by month
        $revenueByMonth = [];
        foreach ($commandes as $commande) {
            $month = $commande->getDateCommande()->format('M Y');
            $revenueByMonth[$month] = ($revenueByMonth[$month] ?? 0) + $commande->getTotal();
        }

        // Top selling meubles
        $lignes = $ligneRepo->findAll();
        $meubleSales = [];
        foreach ($lignes as $ligne) {
            $nom = $ligne->getMeuble()->getNom();
            $meubleSales[$nom] = ($meubleSales[$nom] ?? 0) + $ligne->getQuantite();
        }
        arsort($meubleSales);
        $topMeubles = array_slice($meubleSales, 0, 5, true);

        return $this->render('admin/index.html.twig', [
            'chiffreAffaires' => $chiffreAffaires,
            'nbClients' => $nbClients,
            'nbCommandes' => $nbCommandes,
            'commandes' => $commandes,
            'revenueLabels' => array_keys($revenueByMonth),
            'revenueData' => array_values($revenueByMonth),
            'topMeublesLabels' => array_keys($topMeubles),
            'topMeublesData' => array_values($topMeubles),
        ]);
    }
}