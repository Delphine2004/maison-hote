<?php

namespace App\Controller;

use App\Entity\User;

use App\Enum\UserRole;

use App\Form\UserType;

use App\Repository\UserRepository;
use App\Repository\RoomRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted(UserRole::ADMIN->value)]
final class AdminController extends AbstractController
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    #[Route('', name: 'app_settings_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/settings_general.html.twig', []);
    }

    #[Route('/hotel', name: 'app_hotel_settings', methods: ['GET'])]
    public function renderHotelInfos(
        RoomRepository $roomRepository
    ): Response {
        // Récupération des chambres
        $rooms = $roomRepository->findAllRooms();
        return $this->render('admin/settings_hotel.html.twig', ['rooms' => $rooms,]);
    }

    #[Route('/staff', name: 'app_staff_index', methods: ['GET'])]
    public function renderStaff(UserRepository $userRepository): Response
    {
        // Récupération des utilisateurs
        $users = $userRepository->findUsersWithoutRoles([UserRole::ADMIN->value, UserRole::CLIENT->value, UserRole::ANONYMIZED->value]);
        return $this->render('admin/settings_staff.html.twig', ['users' => $users]);
    }


    #[Route('/staff/new', name: 'app_staff_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $user->setRoles([UserRole::EMPLOYE]);
        $form = $this->createForm(UserType::class, $user, ['mode' => 'createUser']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succés.');
            return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/staff/{id}/edit', name: 'app_user_edit_by_admin', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser() === $user) {
            $mode = 'updateAdmin';
        } else {
            $mode = 'updateUserByAdmin';
        }

        $form = $this->createForm(UserType::class, $user, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Modification(s) réalisées avec succés.');
            return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/staff/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $userConnected = $this->getUser();

        // Vérification que l'utilisateur est connecté
        if (!$userConnected) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }


        $user->setEmail('anonyme_' . $user->getId() . '@example.com');
        $user->setLogin('anonyme' . $user->getId());
        $user->setRoles([UserRole::ANONYMIZED]);
        $hashedPassword = $this->hasher->hashPassword($user, 'Anonymised12*');
        $user->setPassword($hashedPassword);

        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('app_staff_index', [], Response::HTTP_SEE_OTHER);
    }
}
