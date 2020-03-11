<?php


namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;


final class QuestionAdmin extends AbstractAdmin
{

//    protected function configureRoutes(RouteCollection $collection)
//    {
//        $collection
//            ->add('clone', $this->getRouterIdParameter().'/clone');
//    }

    protected function configureListFields(ListMapper $listMapper)
    {

        $translator = $this->getConfigurationPool()->getContainer()->get('translator');
        $listMapper
            ->addIdentifier('question', null, [
                'label' => $translator->trans('key')
            ])
            ->add('user', null)
            ->add('body', null)
            ->add('_action', null,   [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);

    }


}