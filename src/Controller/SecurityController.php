<?php

namespace App\Controller;

use App\Entity\User;
use PharIo\Manifest\Email;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/forgot-password', name: 'app_forgot_password_request')]
    public function forgotPasswordRequest(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
    
            // Find the user by email
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
            if ($user) {
                // Generate a reset token (e.g., using random_bytes)
                $user->setResetToken(bin2hex(random_bytes(32)));
                $user->setResetTokenExpiresAt(new \DateTimeImmutable('+1 hour')); // Token expires in 1 hour
                $entityManager->flush();
    
                // Generate the reset URL
                $resetUrl = 'http://127.0.0.1:8000' . $this->generateUrl('app_reset_password', ['token' => $user->getResetToken()]);
    
                // Send the email
                $email = (new TemplatedEmail())
                    ->from('aymen.zargouni1996@gmail.com') // Your Gmail
                    ->to($user->getEmail()) // User's email from form
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('emails/resetPassword.html.twig')
                    ->context([
                        'resetUrl' => $resetUrl,
                    ]);
    
                $mailer->send($email);
    
                // Add a success flash message
                $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
            } else {
                // Add an error flash message if the email doesn't exist
                $this->addFlash('error', 'Aucun compte trouvé avec cette adresse email.');
            }
    
            // Redirect back to the forgot password page
            return $this->redirectToRoute('app_forgot_password_request');
        }
    
        return $this->render('security/forgotPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // src/Controller/SecurityController.php
#[Route('/reset-password/{token}', name: 'app_reset_password')]
public function resetPassword(string $token, Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
{
    // Find the user by the reset token
    $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

    if (!$user || $user->getResetTokenExpiresAt() < new \DateTimeImmutable()) {
        $this->addFlash('error', 'Invalid or expired reset token.');
        return $this->redirectToRoute('app_forgot_password_request');
    }

    $form = $this->createForm(ResetPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Update the password
        $user->setPassword(
            $passwordHasher->hashPassword($user, $form->get('password')->getData())
        );
        $user->setResetToken(null); // Clear the reset token
        $user->setResetTokenExpiresAt(null); // Clear the expiration
        $entityManager->flush();

        $this->addFlash('success', 'Your password has been reset successfully.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/resetPassword.html.twig', [
        'form' => $form->createView(),
    ]);
}
    
}
