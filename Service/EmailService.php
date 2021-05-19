<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use PaneeDesign\DatabaseSwiftMailerBundle\Repository\EmailRepository;
use Swift_Mime_SimpleMessage;
use Swift_SwiftException;

class EmailService implements EmailServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var EmailRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager, EmailRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getById(int $id): Email
    {
        /* @var Email $email */
        $email = $this->repository->find($id);

        if (!$email) {
            throw new EntityNotFoundException('Unable to find Email entity.');
        }

        return $email;
    }

    /**
     * @param ?int $limit
     * @param ?int $offset
     *
     * @return Email[]
     */
    public function getAll(?int $limit = null, ?int $offset = null): array
    {
        return $this->repository
            ->getAllEmails($limit, $offset)
            ->getResult();
    }

    public function count(): int
    {
        return $this->repository
            ->count([]);
    }

    /**
     * @param ?bool $autoFlush
     *
     * @throws Exception
     */
    public function add(Swift_Mime_SimpleMessage $message, ?bool $autoFlush = true): void
    {
        $email = new Email();

        if (\is_array($message->getFrom()) && \count($message->getFrom())) {
            $email->setFromEmail(implode('; ', array_keys($message->getFrom())));
        }

        if (\is_array($message->getTo()) && \count($message->getTo())) {
            $email->setToEmail(implode('; ', array_keys($message->getTo())));
        }

        if (\is_array($message->getCc()) && \count($message->getCc())) {
            $email->setCcEmail(implode('; ', array_keys($message->getCc())));
        }

        if (\is_array($message->getBcc()) && \count($message->getBcc())) {
            $email->setBccEmail(implode('; ', array_keys($message->getBcc())));
        }

        /** @var array $replyTo */
        $replyTo = $message->getReplyTo();

        if (\is_array($replyTo) && \count($replyTo)) {
            $email->setReplyToEmail(implode('; ', array_keys($replyTo)));
        }

        $email->setBody($message->getBody());
        $email->setSubject($message->getSubject());
        $email->setMessage($message);
        $email->setStatus(Email::STATUS_READY);

        if ($autoFlush) {
            $this->manager->persist($email);
            $this->manager->flush();
        } else {
            $canFlush = $this->isDbChangesScheduled();
            $this->manager->persist($email);

            // Flush only if there are not other db changes
            if ($canFlush) {
                $this->manager->flush();
            }
        }
    }

    /**
     * @return Email[]
     */
    public function getQueue(int $limit = 100, int $maxRetries = 10): array
    {
        /** @var Email[] $emails */
        $emails = $this->repository
            ->getEmailQueue($limit, $maxRetries)
            ->getResult();

        if (\count($emails) > 0) {
            foreach ($emails as $email) {
                $email->setStatus(Email::STATUS_PROCESSING);

                $this->manager->persist($email);
            }

            $this->manager->flush();
        }

        return $emails;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function retryById(int $id): void
    {
        $this->retry($this->getById($id));
    }

    public function retry(Email $email): void
    {
        $email->setStatus(Email::STATUS_FAILED);
        $email->setRetries(0);
        $email->setUpdatedAt(new DateTime());

        $this->manager->persist($email);
        $this->manager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function resendById(int $id): void
    {
        $this->resend($this->getById($id));
    }

    public function resend(Email $email): void
    {
        $email->setStatus(Email::STATUS_READY);
        $email->setRetries(0);
        $email->setUpdatedAt(new DateTime());

        $this->manager->persist($email);
        $this->manager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function cancelById(int $id): void
    {
        $this->cancel($this->getById($id));
    }

    public function cancel(Email $email): void
    {
        $email->setStatus(Email::STATUS_CANCELLED);
        $email->setUpdatedAt(new DateTime());

        $this->manager->persist($email);
        $this->manager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->getById($id));
    }

    public function delete(Email $email): void
    {
        $this->manager->remove($email);
        $this->manager->flush();
    }

    /**
     * @throws Exception
     */
    public function markFailed(Email $email, Swift_SwiftException $ex): void
    {
        $email->setErrorMessage($ex->getMessage());
        $email->setStatus(Email::STATUS_FAILED);
        $email->setRetries((int) ($email->getRetries()) + 1);
        $email->setUpdatedAt(new DateTime());

        $this->manager->persist($email);
        $this->manager->flush();
    }

    /**
     * @throws Exception
     */
    public function markComplete(Email $email): void
    {
        $now = new DateTime();

        $email->setStatus(Email::STATUS_COMPLETE);
        $email->setSentAt($now);
        $email->setErrorMessage(null);
        $email->setUpdatedAt($now);

        $this->manager->persist($email);
        $this->manager->flush();
    }

    private function isDbChangesScheduled(): bool
    {
        $uow = $this->manager->getUnitOfWork();

        $count = 0;
        $count += \count($uow->getScheduledEntityInsertions());
        $count += \count($uow->getScheduledEntityUpdates());
        $count += \count($uow->getScheduledEntityDeletions());
        $count += \count($uow->getScheduledCollectionUpdates());
        $count += \count($uow->getScheduledCollectionUpdates());

        return $count === 0;
    }
}
