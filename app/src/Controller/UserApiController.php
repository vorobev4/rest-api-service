<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

class UserApiController extends AbstractController
{
    /**
     * @Route("/api/user/create", name="app_api_user_create", methods="GET")
     */
    public function userCreate(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $user = new User();

        if (isset($_GET['firstName']) && !empty($_GET['firstName'])) {
            $user->setFirstName($_GET['firstName']);
        } else {
            return $this->json(['error' => 'Param \'firstName\' is empty'], 500);
        }

        if (isset($_GET['lastName']) && !empty($_GET['lastName'])) {
            $user->setLastName($_GET['lastName']);
        } else {
            return $this->json(['error' => 'Param \'lastName\' is empty'], 500);
        }

        if (isset($_GET['middleName'])) {
            $user->setMiddleName($_GET['middleName']);
        }

        $entityManager->persist($user);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'New user created!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/user/edit/{id}", name="app_api_user_edit", methods="GET")
     */
    public function userEdit(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (isset($_GET['firstName']) && !empty($_GET['firstName'])) {
            $user->setFirstName($_GET['firstName']);
        }
        if (isset($_GET['lastName']) && !empty($_GET['lastName'])) {
            $user->setLastName($_GET['lastName']);
        }
        if (isset($_GET['middleName'])) {
            $user->setMiddleName($_GET['middleName']);
        }

        $entityManager->persist($user);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'User '.$id.' edit!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/user/delete/{id}", name="app_api_user_delete", methods="DELETE")
     */
    public function userDelete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'User '.$id.' delete!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }
}
