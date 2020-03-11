<?php


namespace App\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

 class QuestionAdminController extends CRUDController
{
    /**
     * @param $id
     */
    public function cloneAction($id)
    {
        $object = $this->admin->getSubject();

         if (!$object) {
        throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
    }

        $clonedObject = clone $object;
        $clonedObject->setName($object->getName().' (Clone)');
        $this->admin->create($clonedObject);
        $this->addFlash('sonata_flash_success', 'Cloned successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }


}