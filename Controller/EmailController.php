<?php

namespace PaneeDesign\DatabaseSwiftMailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;

/**
 * Email controller.
 *
 * @Route("/email-spool")
 */
class EmailController extends Controller
{
    const MAX_PAGE_ROWS = 30;

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
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entities = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')
            ->getAllEmails(EmailController::MAX_PAGE_ROWS, ($page - 1) * EmailController::MAX_PAGE_ROWS)
            ->getResult();

        return [
            'entities' => $entities,
            'page' => $page,
            'max_page_rows' => EmailController::MAX_PAGE_ROWS,
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
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entity = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')->find($id);

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
     * @throws \Exception
     */
    public function retryAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entity = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_FAILED);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new \DateTime());

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
     * @throws \Exception
     */
    public function resendAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entity = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_READY);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new \DateTime());

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
     * @throws \Exception
     */
    public function cancelAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entity = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_CANCELLED);
        $entity->setUpdatedAt(new \DateTime());

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
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);
        $entity = $em->getRepository('PedDatabaseSwiftMailerBundle:Email')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }
}
