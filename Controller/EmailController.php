<?php

namespace Citrax\Bundle\DatabaseSwiftMailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Citrax\Bundle\DatabaseSwiftMailerBundle\Entity\Email;

/**
 * Email controller.
 *
 * @Route("/email-spool")
 */
class EmailController extends Controller
{
    /**
     * Lists all Email entities.
     *
     * @Route("/{page}", name="email-spool", defaults={"page" = 1}, requirements={"page" = "\d+"})
     * @Method("GET")
     * @Template()
     * @param $page
     * @return array
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->getAllEmails()->getResult();

        //TODO: add limit and pagination

        return [
            'entities' => $entities,
            'page' => $page,
        ];
    }

    /**
     * Finds and displays a Email entity.
     *
     * @Route("/{id}/show", name="email-spool_show")
     * @Method("GET")
     * @Template()
     * @param $id
     * @return array
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        return [
            'entity' => $entity,
        ];
    }

    /**
     * Retry to send an email
     *
     * @Route("/{id}/retry", name="email-spool_retry")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function retryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_FAILED);
        $entity->setRetries(0);

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Resend an email
     *
     * @Route("/{id}/resend", name="email-spool_resend")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resendAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_READY);
        $entity->setRetries(0);

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Cancel an email sending
     *
     * @Route("/{id}/cancel", name="email-spool_cancel")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_CANCELLED);

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Deletes a Email entity.
     *
     * @Route("/{id}/delete", name="email-spool_delete")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CitraxDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }
}
