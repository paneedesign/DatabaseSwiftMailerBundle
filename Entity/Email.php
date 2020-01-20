<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Swift_Mime_SimpleMessage;

/**
 * @ORM\Table(name="ped_email_spool")
 * @ORM\Entity(repositoryClass="PaneeDesign\DatabaseSwiftMailerBundle\Repository\EmailRepository")
 */
class Email
{
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_READY = 'READY';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_COMPLETE = 'COMPLETE';
    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="from_email", type="string", length=255)
     */
    private $fromEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="to_email", type="string", length=255, nullable=true)
     */
    private $toEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="cc_email", type="string", length=255, nullable=true)
     */
    private $ccEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc_email", type="string", length=255, nullable=true)
     */
    private $bccEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="reply_to_email", type="string", length=255, nullable=true)
     */
    private $replyToEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="retries", type="integer", options={"default" = 0})
     */
    private $retries = 0;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var string
     *
     * @ORM\Column(name="error_message", type="text", nullable=true)
     */
    private $errorMessage;

    /**
     * Email constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set fromEmail.
     *
     * @param string $fromEmail
     *
     * @return Email
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * Get fromEmail.
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Set toEmail.
     *
     * @param string $toEmail
     *
     * @return Email
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * Get toEmail.
     *
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * @return string
     */
    public function getCcEmail()
    {
        return $this->ccEmail;
    }

    /**
     * @param string $ccEmail
     */
    public function setCcEmail($ccEmail): void
    {
        $this->ccEmail = $ccEmail;
    }

    /**
     * @return string
     */
    public function getBccEmail()
    {
        return $this->bccEmail;
    }

    /**
     * @param string $bccEmail
     */
    public function setBccEmail($bccEmail): void
    {
        $this->bccEmail = $bccEmail;
    }

    /**
     * @return string
     */
    public function getReplyToEmail()
    {
        return $this->replyToEmail;
    }

    /**
     * @param string $replyToEmail
     *
     * @return Email
     */
    public function setReplyToEmail($replyToEmail)
    {
        $this->replyToEmail = $replyToEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return Swift_Mime_SimpleMessage
     */
    public function getMessage()
    {
        return unserialize(base64_decode($this->message));
    }

    public function setMessage(Swift_Mime_SimpleMessage $message): void
    {
        $this->message = base64_encode(serialize($message));
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeInterface $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getRetries(): int
    {
        return $this->retries;
    }

    public function setRetries(int $retries): void
    {
        $this->retries = $retries;
    }
}
