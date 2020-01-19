<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Controller;

use Exception;
use PaneeDesign\DatabaseSwiftMailerBundle\Service\EmailServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Email controller.
 */
class EmailController extends AbstractController
{
    public const MAX_PAGE_ROWS = 30;

    /**
     * @var EmailServiceInterface
     */
    private $service;

    public function __construct(EmailServiceInterface $service)
    {
        $this->service = $service;
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
     * @return Response
     */
    public function indexAction($page)
    {
        $emails = $this->service
            ->paginate(self::MAX_PAGE_ROWS, ($page - 1) * self::MAX_PAGE_ROWS);

        return $this->render('PedDatabaseSwiftMailerBundle:Email:index.html.twig', [
            'entities' => $emails,
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
     * @return Response
     */
    public function showAction($id)
    {
        $email = $this->service->getById((int) $id);

        return $this->render('PedDatabaseSwiftMailerBundle:Email:show.html.twig', [
            'entity' => $email,
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
        $this->service->retryById((int) $id);

        return $this->redirectToRoute('email-spool');
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
        $this->service->resendById((int) $id);

        return $this->redirectToRoute('email-spool');
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
        $this->service->cancelById((int) $id);

        return $this->redirectToRoute('email-spool');
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
        $this->service->deleteById((int) $id);

        return $this->redirectToRoute('email-spool');
    }
}
