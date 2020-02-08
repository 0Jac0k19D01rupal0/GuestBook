<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'username', 'readonly' => 'readonly'],
                'data' => $options['user'] ? $options['user']->getUsername() : null
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'question', 'readonly' => 'readonly'],
                'data' => $options['user'] ? $options['user']->getEmail() : null
            ])
            ->add('question', null)
            ->add('body', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5
                ],
                'label' => 'Body(Optional)'
            ])
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (PDF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024m',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
//            ->add('captchaCode', CaptchaType::class, array(
//                'captchaConfig' => 'ExampleCaptchaUserRegistration',
//                'constraints' => [
//                    new ValidCaptcha([
//                        'message' => 'Invalid captcha, please try again',
//                    ]),
//                ],
//            ))
            ->add('submit', SubmitType::class, [
                'label' => "Ask Question"
            ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Question::class
        ]);
        $resolver->setRequired(['user', 'picture']);
    }
}
