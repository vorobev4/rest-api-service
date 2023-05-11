<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UserApiController extends AbstractController
{
    /**
     * @Route("/api/v1/user/create", name="app_api_user_create", methods="POST")
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $passwordHasher
     * @param RateLimiterFactory $anonymousApiLimiter
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function userCreate(
        ManagerRegistry $doctrine, 
        Request $request, 
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        RateLimiterFactory $anonymousApiLimiter,
        TranslatorInterface $translator
    ): JsonResponse
    {
        try {
            $limiter = $anonymousApiLimiter->create($request->getClientIp());

            if (false === $limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException(null, 'Many requests');
            }

            $entityManager = $doctrine->getManager();

            $user = new User();

            $user->setFirstName($request->get("firstName"));
            $user->setLastName($request->get("lastName"));
            $user->setMiddleName($request->get("middleName"));
            $user->setEmail($request->get("email"));

            $plaintextPassword = $request->get("password");
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            
            $user->setPassword($hashedPassword);

            $errors = $validator->validate($user);

            if (!$errors->count() > 0) {
                $entityManager->persist($user);
                $entityManager->flush();
                
                return $this->json(['message' => $translator->trans('New user created')]);
            } else {
                return $this->json(['error' => (string) $errors], 500);
            }
        } catch (Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/v1/user/edit/{id}", name="app_api_user_edit", methods="PUT")
     * @param ManagerRegistry $doctrine
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $id
     * @return JsonResponse
     */
    public function userEdit(
        ManagerRegistry $doctrine, 
        UserRepository $userRepository, 
        Request $request, 
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        int $id
    ): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $user = $userRepository->find($id);
            
            $firstName = $request->get("firstName");
            $lastName = $request->get("lastName");
            $middleName = $request->get("middleName");

            if (isset($firstName) && !empty($firstName)) {
                $user->setFirstName($firstName);
            }

            if (isset($lastName) && !empty($lastName)) {
                $user->setLastName($lastName);
            }

            if (empty($middleName)) {
                $user->setMiddleName(null);
            } else {
                $user->setMiddleName($middleName);
            }

            $errors = $validator->validate($user);

            if (!$errors->count() > 0) {        
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->json(['message' => $translator->trans('User edit', ['{id}' => $id])]);
            } else {
                return $this->json(['error' => (string) $errors], 500);
            }
        } catch (Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/v1/user/delete/{id}", name="app_api_user_delete", methods="DELETE")
     * @param ManagerRegistry $doctrine
     * @param UserRepository $userRepository
     * @param int $id
     * @return JsonResponse
     */
    public function userDelete(
        ManagerRegistry $doctrine, 
        UserRepository $userRepository,
        TranslatorInterface $translator,
        int $id
    ): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $user = $userRepository->find($id);

            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json(['message' => $translator->trans('User delete', ['{id}' => $id])]);
        } catch (Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
