<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $logger->info('Send Email');

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('appbooks@example.com', 'App Books'))
                    ->to((string) $user->getEmail())
                    ->subject('Por Favor confirme seu Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $security->login($user);

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email/send', name: 'app_send_verify_email', methods: ['GET'])]
    #[IsGranted('ROLE_USER_NOT_VERIFY')]
    public function sendVerifyEmail(): Response
    {
        /** @var User */
        $user = $this->getUser();

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
        (new TemplatedEmail())
            ->from(new Address('appbooks@example.com', 'App Books'))
            ->to((string) $user->getEmail())
            ->subject('Por Favor confirme seu Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->redirectToRoute('app_book_index');
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, Security $security): Response
    {
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $security->login($user);

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_book_index');
    }

    #[Route('/user/seed')]
    public function createExampleUsers(UserPasswordHasherInterface $hash, EntityManagerInterface $entityManager): Response
    {
        $user = new User;
        $user->setEmail('user@example.com');
        $user->setPassword('user1234');
        $user->setVerified(true);

        $passwordHash = $hash->hashPassword($user, $user->getPassword());
        $user->setPassword($passwordHash);

        $admin = new User;
        $admin->setEmail('admin@example.com');
        $admin->setPassword('admin1234');
        $admin->setVerified(true);
        $admin->setRoles(['ROLE_ADMIN']);

        $passwordHash = $hash->hashPassword($admin, $admin->getPassword());
        $admin->setPassword($passwordHash);

        $entityManager->persist($user);
        $entityManager->persist($admin);
        $entityManager->flush();

        return $this->redirectToRoute('app_book_index');
    }
}
