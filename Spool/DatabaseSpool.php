<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Rafael
 * Date: 02/05/2015
 * Time: 22:16.
 */

namespace PaneeDesign\DatabaseSwiftMailerBundle\Spool;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\EmailRepository;
use Swift_ConfigurableSpool;
use Swift_Mime_SimpleMessage;
use Swift_SwiftException;
use Swift_Transport;

class DatabaseSpool extends Swift_ConfigurableSpool
{
    /**
     * @var EmailRepository
     */
    private $repository;

    private $parameters;

    public function __construct(EmailRepository $repository, $parameters)
    {
        $this->repository = $repository;
        $this->parameters = $parameters;
    }

    /**
     * Starts this Spool mechanism.
     */
    public function start(): void
    {
        // TODO: Implement start() method.
    }

    /**
     * Stops this Spool mechanism.
     */
    public function stop(): void
    {
        // TODO: Implement stop() method.
    }

    /**
     * Tests if this Spool mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Queues a message.
     *
     * @param Swift_Mime_SimpleMessage $message The message to store
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return bool Whether the operation has succeeded
     */
    public function queueMessage(Swift_Mime_SimpleMessage $message)
    {
        $email = new Email();
        $email->setFromEmail(implode('; ', array_keys($message->getFrom())));

        if (null !== $message->getTo()) {
            $email->setToEmail(implode('; ', array_keys($message->getTo())));
        }
        if (null !== $message->getCc()) {
            $email->setCcEmail(implode('; ', array_keys($message->getCc())));
        }
        if (null !== $message->getBcc()) {
            $email->setBccEmail(implode('; ', array_keys($message->getBcc())));
        }
        if (null !== $message->getReplyTo()) {
            $email->setReplyToEmail($message->getReplyTo());
        }

        $email->setBody($message->getBody());
        $email->setSubject($message->getSubject());
        $email->setMessage($message);

        $this->repository->addEmail($email);

        return true;
    }

    /**
     * Sends messages using the given transport instance.
     *
     * @param Swift_Transport $transport        A transport instance
     * @param string[]        $failedRecipients An array of failures by-reference
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return int The number of sent e-mail's
     */
    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        if (!$transport->isStarted()) {
            $transport->start();
        }

        $count = 0;

        /** @var Email[] $emails */
        $emails = $this->repository->getEmailQueue($this->getMessageLimit());

        foreach ($emails as $email) {
            /* @var Swift_Mime_SimpleMessage $message */
            $message = $email->getMessage();

            try {
                $count_ = $transport->send($message, $failedRecipients);
                if ($count_ > 0) {
                    $this->repository->markCompleteSending($email);
                    $count += $count_;
                } else {
                    throw new Swift_SwiftException('The email was not sent.');
                }
            } catch (Swift_SwiftException $ex) {
                $this->repository->markFailedSending($email, $ex);
            }
        }

        return $count;
    }
}
