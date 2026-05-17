<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MeubleController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/meuble', name: 'app_meuble')]
    public function index(
        Request $request,
        MeubleRepository $meubleRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $search = $request->query->get('search');
        $categorieId = $request->query->get('categorie');

        $queryBuilder = $meubleRepository->createQueryBuilder('m')
            ->leftJoin('m.categorie', 'c')
            ->addSelect('c');

        if ($search) {
            $queryBuilder
                ->andWhere('m.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($categorieId) {
            $queryBuilder
                ->andWhere('c.id = :categorieId')
                ->setParameter('categorieId', $categorieId);
        }

        $meubles = $queryBuilder
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('meuble/index.html.twig', [
            'meubles' => $meubles,
            'categories' => $categorieRepository->findAll(),
            'search' => $search,
            'categorieId' => $categorieId,
        ]);
    }

    #[Route('/meuble/{id}', name: 'app_meuble_show')]
    public function show(int $id, MeubleRepository $meubleRepository): Response
    {
        $meuble = $meubleRepository->find($id);

        if (!$meuble) {
            throw $this->createNotFoundException('Meuble introuvable');
        }

        return $this->render('meuble/show.html.twig', [
            'meuble' => $meuble,
        ]);
    }
}