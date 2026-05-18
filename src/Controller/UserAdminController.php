<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserAdminController extends AbstractController
{
    #[Route('/admin/users', name: 'app_user_admin')]
    public function index(UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepo->findAll();

        return $this->render('user_admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'app_user_admin_delete')]
    public function delete(int $id, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepo->find($id);
        if ($user) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_user_admin');
    }
}