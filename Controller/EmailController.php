<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Controller;

use DateTime;
use Exception;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Email controller.
 *
 * @Route("/email-spool")
 */
class EmailController extends AbstractController
{
    const MAX_PAGE_ROWS = 30;

    /**
     * Lists all Email entities.
     *
     * @Route(
     *     "/{page}",
     *     name="email-spool",
     *     defaults={"page" = 1},
     *     requirements={"page" = "\d+"},
     *     methods={"GET"}
     * )
     * @Template
     *
     * @param $page
     *
     * @return array
     */
    public function indexAction($page)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entities = $em->getRepository(Email::class)
            ->getAllEmails(self::MAX_PAGE_ROWS, ($page - 1) * self::MAX_PAGE_ROWS)
            ->getResult();

        return [
            'entities' => $entities,
            'page' => $page,
            'max_page_rows' => self::MAX_PAGE_ROWS,
        ];
    }

    /**
     * Finds and displays a Email entity.
     *
     * @Route("/{id}/show", name="email-spool_show", methods={"GET"})
     * @Template
     *
     * @param $id
     *
     * @return array
     */
    public function showAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        $entity = $em->getRepository(Email::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        return [
            'entity' => $entity,
        ];
    }

    /**
     * Retry to send an email.
     *
     * @Route("/{id}/retry", name="email-spool_retry", methods={"GET"})
     *
     * @param $id
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function retryAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        /* @var Email $entity */
        $entity = $em->getRepository(Email::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_FAILED);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new DateTime());

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Resend an email.
     *
     * @Route("/{id}/resend", name="email-spool_resend", methods={"GET"})
     *
     * @param $id
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function resendAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        /* @var Email $entity */
        $entity = $em->getRepository(Email::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_READY);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new DateTime());

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Cancel an email sending.
     *
     * @Route("/{id}/cancel", name="email-spool_cancel", methods={"GET"})
     *
     * @param $id
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function cancelAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);

        /* @var Email $entity */
        $entity = $em->getRepository(Email::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_CANCELLED);
        $entity->setUpdatedAt(new DateTime());

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Deletes a Email entity.
     *
     * @Route("/{id}/delete", name="email-spool_delete", methods={"GET"})
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $entityManagerName = $this->container->getParameter('ped_database_swift_mailer.entity_manager');
        $em = $this->container->get($entityManagerName);
        $entity = $em->getRepository(Email::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }
}
