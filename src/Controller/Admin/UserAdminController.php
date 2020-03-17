<?php


namespace App\Controller\Admin;


use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAdminController extends CRUDController
{
    public function deleteAction($id)
    {
        $object = $this->admin->getObject($id);
        $currentUser = $this->getUser();
        if ($currentUser == $object) {
            $this->addFlash(
                'sonata_flash_error',
                'You cannot delete your own account.'
            );

            return $this->redirectTo($object);
        }

        return parent::deleteAction($id);
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $request       = $this->getRequest();
        $currentUserId = $this->getUser();
        $selectedUsers = $query->execute();

        foreach ($selectedUsers as $selectedUser) {
            if ($selectedUser->getId() == $currentUserId) {
                $this->addFlash(
                    'sonata_flash_error',
                    'You cannot delete your own account.'
                );

                return new RedirectResponse(
                    $this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
                );
            }
        }

        return parent::batchActionDelete($query);
    }

}