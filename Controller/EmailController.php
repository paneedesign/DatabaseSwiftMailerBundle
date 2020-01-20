<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Controller;

use Exception;
use PaneeDesign\DatabaseSwiftMailerBundle\Service\EmailServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

/**
 * Email controller.
 */
class EmailController extends AbstractController
{
    public const MAX_PAGE_ROWS = 30;

    /**
     * @var EmailServiceInterface
     */
    private $emailService;

    /**
     * @var int
     */
    private $maxPageRows;

    public function __construct(EmailServiceInterface $emailService, ?int $maxPageRows = self::MAX_PAGE_ROWS)
    {
        $this->emailService = $emailService;
        $this->maxPageRows = $maxPageRows;
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
        $limit = $this->maxPageRows;
        $offset = ($page - 1) * $this->maxPageRows;

        $emails = $this->emailService->paginate($limit, $offset);
        $count = $this->emailService->count();

        return $this->render('PedDatabaseSwiftMailerBundle:Email:index.html.twig', [
            'entities' => $emails,
            'page' => $page,
            'max_page_rows' => $this->maxPageRows,
            'from' => $offset,
            'to' => min($limit + $offset, $count),
            'total' => $count,
        ]);
    }

    /**
     * Finds and displays a Email entity.
     *
     * @Route("/{id}/show", name="email-spool_show", methods={"GET"}, requirements={"id" = "\d+"})
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        Assert::integer($id);

        $email = $this->emailService->getById($id);

        return $this->render('PedDatabaseSwiftMailerBundle:Email:show.html.twig', [
            'entity' => $email,
        ]);
    }

    /**
     * Retry to send an email.
     *
     * @Route("/{id}/retry", name="email-spool_retry", methods={"GET"}, requirements={"id" = "\d+"})
     *
     * @param $id
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function retryAction($id)
    {
        Assert::integer($id);

        $this->emailService->retryById($id);

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
        $this->emailService->resendById($id);

        return $this->redirectToRoute('email-spool');
    }

    /**
     * Cancel an email sending.
     *
     * @Route("/{id}/cancel", name="email-spool_cancel", methods={"GET"}, requirements={"id" = "\d+"})
     *
     * @param $id
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function cancelAction($id)
    {
        Assert::integer($id);

        $this->emailService->cancelById($id);

        return $this->redirectToRoute('email-spool');
    }

    /**
     * Deletes a Email entity.
     *
     * @Route("/{id}/delete", name="email-spool_delete", methods={"GET"}, requirements={"id" = "\d+"}))
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        Assert::integer($id);

        $this->emailService->deleteById($id);

        return $this->redirectToRoute('email-spool');
    }
}
