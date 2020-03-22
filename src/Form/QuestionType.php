<?php

namespace App\Form;

use App\Entity\Question;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('body', CKEditorType::class, [
                'config' => [
                    'uiColor' => "#e2e2e2",
                    'toolbar' => "basic",
                    'required' => true
                ],
                'label' => 'Text'
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image',
                'attr' => ['class' => 'form-control'],
                'data'=> $options['picture'] ?? null,
                'required' => false,
            ])
             ->add('captchaCode', CaptchaType::class, array(
                  'captchaConfig' => 'ExampleCaptchaUserRegistration',
                  'constraints' => [
                      new ValidCaptcha([
                          'message' => 'Invalid captcha, please try again',
                      ]),
                  ],
              ))
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
