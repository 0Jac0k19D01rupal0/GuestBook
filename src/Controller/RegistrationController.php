<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

//use App\Service\CodeGenerator;
//use App\Service\Mailer;


class RegistrationController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            return $this->redirectToRoute("app_main");
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // encode the plain password
            $find_email = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            $find_username = $entityManager->getRepository(User::class)->findOneBy(['username' => $form->get('username')->getData()]);
            if (is_null($find_email) && is_null($find_username)) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setResetPasswordToken(md5(random_bytes(10)));
                $user->setRoles(['ROLE_EDITOR']);

                $entityManager->persist($user);
                $entityManager->flush();

                $link = getenv('DOMAIN').'/ua/confirm_email/'.$user->getResetPasswordToken();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('oleksandr9.redko@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'security/confirmation.html.twig',
                            [
                                'name' => $user->getUsername(),
                                'link' => $link,
                            ]),

                        'text/html'
                    );
                $mailer->send($message);

                $resetPasswordStatus = 'send.message.success';


                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
            else {
                if (is_null($find_email)) {
                    return $this->render('security/errorToken.html.twig');
                }
                if (is_null($find_username)) {
                    return $this->render('security/errorToken.html.twig');
                }

            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'result'=>$result
        ]);
    }


    public function confirmEmail($token, Request $request)
    {

        $em =$this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['resetPasswordToken' => $token]);

        if ($user->getRoles('ROLE_EDITOR')) {

            $user->setRoles(['ROLE_USER']);
            $user->setEnabled(true);



            $em = $this->getDoctrine()->getManager();
            $em->flush();
           
        }

        else
        {
            return new Response('404');
        }

        return $this->render('security/account_confirm.html.twig', [
            'user' => $user,
        ]);
    }

}