<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\EmailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Email controller.
 */
class EmailController extends AbstractController
{
    const MAX_PAGE_ROWS = 30;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EmailRepository
     */
    private $repository;

    public function __construct(EntityManager $manager, EmailRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

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
     *
     * @param $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page)
    {
        $entities = $this->repository
            ->getAllEmails(self::MAX_PAGE_ROWS, ($page - 1) * self::MAX_PAGE_ROWS)
            ->getResult();

        return $this->render('PedDatabaseSwiftMailerBundle:Email:index.html.twig', [
            'entities' => $entities,
            'page' => $page,
            'max_page_rows' => self::MAX_PAGE_ROWS,
        ]);
    }

    /**
     * Finds and displays a Email entity.
     *
     * @Route("/{id}/show", name="email-spool_show", methods={"GET"})
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        return $this->render('PedDatabaseSwiftMailerBundle:Email:show.html.twig', [
            'entity' => $entity,
        ]);
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
        /* @var Email $entity */
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_FAILED);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new DateTime());

        $this->manager->persist($entity);
        $this->manager->flush();

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
        /* @var Email $entity */
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_READY);
        $entity->setRetries(0);
        $entity->setUpdatedAt(new DateTime());

        $this->manager->persist($entity);
        $this->manager->flush();

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
        /* @var Email $entity */
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $entity->setStatus(Email::STATUS_CANCELLED);
        $entity->setUpdatedAt(new DateTime());

        $this->manager->persist($entity);
        $this->manager->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }

    /**
     * Deletes a Email entity.
     *
     * @Route("/{id}/delete", name="email-spool_delete", methods={"GET"})
     *
     * @param $id
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $this->manager->remove($entity);
        $this->manager->flush();

        return $this->redirect($this->generateUrl('email-spool'));
    }
}
