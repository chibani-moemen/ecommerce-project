<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
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
        UserRepository $userRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Total revenue
        $commandes = $commandeRepo->findAll();
        $chiffreAffaires = array_sum(array_map(fn($c) => $c->getTotal(), $commandes));

        // Number of clients
        $nbClients = count($userRepo->findAll());

        // Number of orders
        $nbCommandes = count($commandes);

        // Best selling product
        $meubles = $meubleRepo->findAll();

        return $this->render('admin/index.html.twig', [
            'chiffreAffaires' => $chiffreAffaires,
            'nbClients' => $nbClients,
            'nbCommandes' => $nbCommandes,
            'commandes' => $commandes,
        ]);
    }
}