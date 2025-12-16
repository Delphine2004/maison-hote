<?php

namespace App\Controller;

use App\Entity\Room;
use App\Enum\RoomStatus;
use App\Form\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/room')]
#[IsGranted('ROLE_ADMIN')]
final class RoomController extends AbstractController
{

    public function __construct(private string $uploadsRoomsDirectory) {}

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $room = new Room();
        $room->setStatus(RoomStatus::AVAILABLE);
        $form = $this->createForm(RoomType::class, $room, ['mode' => 'create']);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('picture')->getData();
            if ($uploadedFile) {
                $fileName = uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move($this->uploadsRoomsDirectory, $fileName);
                $room->setPicture($fileName);
            }

            $room->setCreatedBy($this->getUser());

            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $oldPicture = $room->getPicture();

        $form = $this->createForm(RoomType::class, $room, ['mode' => 'update']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('picture')->getData();

            if ($uploadedFile) {
                $fileName = uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move($this->uploadsRoomsDirectory, $fileName);
                $room->setPicture($fileName);

                // Suppression de l'ancienne image
                if ($oldPicture) {
                    $oldPath = $this->uploadsRoomsDirectory . '/' . $oldPicture;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $room->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($room);

            // Suppression du fichier
            $filePath = $this->uploadsRoomsDirectory . '/' . $room->getPicture();
            if ($room->getPicture() && file_exists($filePath)) {
                unlink($filePath);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
    }
}
