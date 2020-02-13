<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Spool;

use PaneeDesign\DatabaseSwiftMailerBundle\Service\EmailServiceInterface;
use Swift_ConfigurableSpool;
use Swift_Mime_SimpleMessage;
use Swift_SwiftException;
use Swift_Transport;

class DatabaseSpool extends Swift_ConfigurableSpool
{
    public const MESSAGE_LIMIT = 10;

    /**
     * @var EmailServiceInterface
     */
    private $emailService;

    /**
     * @var array|null
     */
    private $parameters;

    public function __construct(EmailServiceInterface $emailService, ?array $parameters = [])
    {
        $this->emailService = $emailService;
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
     * @return bool Whether the operation has succeeded
     */
    public function queueMessage(Swift_Mime_SimpleMessage $message)
    {
        $this->emailService->add($message, $this->parameters['auto_flush']);

        return true;
    }

    /**
     * Sends messages using the given transport instance.
     *
     * @param Swift_Transport $transport        A transport instance
     * @param string[]        $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent e-mail's
     */
    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        if (!$transport->isStarted()) {
            $transport->start();
        }

        $sentEmails = 0;
        $messageLimit = $this->getMessageLimit() ?? self::MESSAGE_LIMIT;

        $emails = $this->emailService->getQueue($messageLimit, $this->parameters['max_retries']);

        foreach ($emails as $email) {
            /* @var Swift_Mime_SimpleMessage $message */
            $message = $email->getMessage();

            try {
                $sent = $transport->send($message, $failedRecipients);

                if ($sent > 0) {
                    if ($this->parameters['delete_sent_messages']) {
                        $this->emailService->delete($email);
                    } else {
                        $this->emailService->markComplete($email);
                    }

                    $sentEmails += $sent;
                } else {
                    throw new Swift_SwiftException('The email was not sent.');
                }
            } catch (Swift_SwiftException $ex) {
                $this->emailService->markFailed($email, $ex);
            }
        }

        return $sentEmails;
    }
}
