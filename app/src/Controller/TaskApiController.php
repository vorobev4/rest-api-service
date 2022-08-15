<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

class TaskApiController extends AbstractController
{
    /**
     * @Route("/api/task/create", name="app_api_task_create", methods="GET")
     */
    public function taskCreate(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();

        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $task->setName($_GET['name']);
        } else {
            return $this->json(['error' => 'Param \'name\' is empty'], 500);
        }

        if (isset($_GET['desc']) && !empty($_GET['desc'])) {
            $task->setDescription($_GET['desc']);
        } else {
            return $this->json(['error' => 'Param \'desc\' is empty'], 500);
        }

        $entityManager->persist($task);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'New task created!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/task/edit/{id}", name="app_api_edit", methods="GET")
     */
    public function taskEdit(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $task->setName($_GET['name']);
        }
        if (isset($_GET['desc']) && !empty($_GET['desc'])) {
            $task->setDescription($_GET['desc']);
        }

        $entityManager->persist($task);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'Task ' . $id . ' edit!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/task/delete/{id}", name="app_api_delete", methods="DELETE")
     */
    public function taskDelete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        $entityManager->remove($task);

        try {
            $entityManager->flush();

            return $this->json(['message' => 'Task ' . $id . ' delete!']);
        } catch (\Throwable $ex) {
            return $this->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/task/findByName", name="app_api_findByName", methods="GET")
     */
    public function taskFindByName(ManagerRegistry $doctrine, TaskRepository $taskRepository): JsonResponse
    {
        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $task = $taskRepository->findBy(['name' => $_GET['name']]);
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
     * @Route("/api/task/findById", name="app_api_findById", methods="GET")
     */
    public function taskFindById(ManagerRegistry $doctrine, TaskRepository $taskRepository): JsonResponse
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $task = $taskRepository->findBy(['id' => $_GET['id']]);
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
     * @Route("/api/task/findMoreId", name="app_api_findMoreId", methods="GET")
     */
    public function taskFindMoreId(ManagerRegistry $doctrine, TaskRepository $taskRepository): JsonResponse
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $task = $taskRepository->findMoreId($_GET['id']);
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
     * @Route("/api/task/findLessId", name="app_api_findLessId", methods="GET")
     */
    public function taskFindLessId(ManagerRegistry $doctrine, TaskRepository $taskRepository): JsonResponse
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $task = $taskRepository->findLessId($_GET['id']);
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
