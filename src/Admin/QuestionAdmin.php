<?php


namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



final class QuestionAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('question', null)
            ->add('user', null)
            ->add('body', null)
            ->add('_action', null,   [
                'actions' => [
                    'show' => ['question_show'],
                    'edit' => ['question_edit'],
                    'delete' => ['question_delete'],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('question', null)
            ->add('body', CKEditorType::class)
            ->add('validate', ChoiceType::class, [
                'choices' => [
                    'Accept' => true,
                    'Dismiss' => false
                ]


            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('question')
            ->add('body')
            ->add('username')
            ->add('email')
        ;
    }

}
