<?php


namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

final class UserAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('_action', null,   [
                'actions' => [
                    'show' => ['question_show'],
                    'edit' => ['question_edit'],
                    'delete' => ['question_delete'],
                ],
            ]);


        ;
    }

}