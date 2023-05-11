<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class TaskApiController extends AbstractController
{
    /**
     * @Route("/api/v1/task/create", name="app_api_task_create", methods="POST")
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function taskCreate(
        ManagerRegistry $doctrine, 
        Request $request, 
        ValidatorInterface $validator,
        TranslatorInterface $translator
    ): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $task = new Task();

            $task->setName($request->get('name'));
            $task->setDescription($request->get('description'));

            $errors = $validator->validate($task);

            if (!$errors->count() > 0) {
                $entityManager->persist($task);
                $entityManager->flush();

                return $this->json(['message' => $translator->trans('New task created')]);
            } else {
                return $this->json(['error' => (string) $errors], 500);
            }
        } catch (Throwable $e) {
            return $this->json(['errors' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/api/v1/task/delete/{id}", name="app_api_delete", methods="DELETE")
     * @param ManagerRegistry $doctrine
     * @param TaskRepository $taskRepository
     * @param TranslatorInterface $translator
     * @param int $id
     * @return JsonResponse
     */
    public function taskDelete(
        ManagerRegistry $doctrine, 
        TaskRepository $taskRepository,
        TranslatorInterface $translator,
        int $id
    ): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $task = $taskRepository->find($id);

            $entityManager->remove($task);
            $entityManager->flush();

            return $this->json(['message' => $translator->trans('Task delete', ['{id}' => $id])]);
        } catch (Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/v1/task/findByName", name="app_api_findByName", methods="GET")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function taskFindByName(
        Request $request,
        TaskRepository $taskRepository
    ): JsonResponse
    {
        $name = $request->get('name');

        if (isset($name) && !empty($name)) {
            $task = $taskRepository->findBy(['name' => $name]);
        } else {
            $task = 'Param \'name\' is empty!';
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $serializer->serialize($task, 'json');

        return $this->json(['message' => $task]);
    }

    /**
     * @Route("/api/v1/task/findById", name="app_api_findById", methods="GET")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function taskFindById(
        Request $request, 
        TaskRepository $taskRepository
    ): JsonResponse
    {
        $id = $request->get('id');

        if (isset($id) && !empty($id)) {
            $task = $taskRepository->findBy(['id' => $id]);
        } else {
            $task = 'Param \'id\' is empty!';
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $serializer->serialize($task, 'json');

        return $this->json(['message' => $task]);
    }

    /**
     * @Route("/api/v1/task/findMoreId", name="app_api_findMoreId", methods="GET")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function taskFindMoreId(
        Request $request, 
        TaskRepository $taskRepository
    ): JsonResponse
    {
        $id = $request->get('id');

        if (isset($id) && !empty($id)) {
            $task = $taskRepository->findMoreId($id);
        } else {
            $task = 'Param \'id\' is empty!';
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $serializer->serialize($task, 'json');

        return $this->json(['message' => $task]);
    }

    /**
     * @Route("/api/v1/task/findLessId", name="app_api_findLessId", methods="GET")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function taskFindLessId(
        Request $request, 
        TaskRepository $taskRepository
    ): JsonResponse
    {
        $id = $request->get('id');

        if (isset($id) && !empty($id)) {
            $task = $taskRepository->findLessId($id);
        } else {
            $task = 'Param \'id\' is empty!';
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $serializer->serialize($task, 'json');

        return $this->json(['message' => $task]);
    }
}
