<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordEmailType;
use App\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            return $this->redirectToRoute("app_main");
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logout()
    {}

    public function resetPasswordEmail(Request $request, \Swift_Mailer $mailer)
    {

        $em =$this->getDoctrine()->getManager();
        $form = $this->createForm(ResetPasswordEmailType::class);
        $form->handleRequest($request);
        $resetPasswordStatus = false;

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $em->getRepository(User::class)->findOneBy([
                'email'=>$form->getData()['email']
            ]);
            if ($user)
            {

                $user->setResetPasswordToken(md5(random_bytes(10)));
                $em->persist($user);
                $em->flush();

                //Send Email//
                //////////////
                $link = getenv('DOMAIN').'/ua/forget/'.$user->getResetPasswordToken();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('oleksandr9.redko@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'security/sendEmail.html.twig',
                        [
                            'name' => $user->getUsername(),
                            'link' => $link,
                        ]),

                        'text/html'
                    );
                $mailer->send($message);

                $resetPasswordStatus = 'send.message.success';
            }

            else {
                return $this->render('security/errorToken.html.twig');
            }
        }


        return $this->render('security/emailRender.html.twig', [
            'form'=>$form->createView(),
            'resetPasswordStatus' => $resetPasswordStatus
        ]);



    }

    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'resetPasswordToken'=>$token
        ]);
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        $displayForm=false;
        if ($user)
        {
            if ($form->isSubmitted() && $form->isValid())
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->getData()['password']
                    )
                );
                $user->setResetPasswordToken(null);
                $em->persist($user);
                $em->flush();
                $displayForm ='reset.password.success';
            }

        }

        else {
            return $this->render('security/errorToken.html.twig');
        }

        return $this->render('security/resetPassword.html.twig', [

            'form'=>$form->createView(),
            'displayForm'=> $displayForm
        ]);
    }
}