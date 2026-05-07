<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;

use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $user->setRoles([UserRole::CLIENT]);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email de confirmation
            $email = (new TemplatedEmail())
                ->from(new Address('dfumex2004@gmail.com', 'Les parenthèses dorées'))
                ->to((string) $user->getEmail())
                ->subject('Confirmation de votre inscription')
                ->htmlTemplate('registration/email.html.twig')
                ->context([
                    'user' => $user,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Merci de vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
