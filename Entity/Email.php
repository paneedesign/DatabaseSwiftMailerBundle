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
     * @var ?string
     *
     * @ORM\Column(name="to_email", type="string", length=255, nullable=true)
     */
    private $toEmail;

    /**
     * @var ?string
     *
     * @ORM\Column(name="cc_email", type="string", length=255, nullable=true)
     */
    private $ccEmail;

    /**
     * @var ?string
     *
     * @ORM\Column(name="bcc_email", type="string", length=255, nullable=true)
     */
    private $bccEmail;

    /**
     * @var ?string
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
     * @var ?DateTimeInterface
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var ?DateTimeInterface
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var ?DateTimeInterface
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var ?string
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

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setToEmail(?string $toEmail): void
    {
        $this->toEmail = $toEmail;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setCcEmail(?string $ccEmail): void
    {
        $this->ccEmail = $ccEmail;
    }

    public function getCcEmail(): ?string
    {
        return $this->ccEmail;
    }

    public function setBccEmail(?string $bccEmail): void
    {
        $this->bccEmail = $bccEmail;
    }

    public function getBccEmail(): ?string
    {
        return $this->bccEmail;
    }

    public function setReplyToEmail(?string $replyToEmail): void
    {
        $this->replyToEmail = $replyToEmail;
    }

    public function getReplyToEmail(): ?string
    {
        return $this->replyToEmail;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setMessage(Swift_Mime_SimpleMessage $message): void
    {
        $this->message = base64_encode(serialize($message));
    }

    public function getMessage(): Swift_Mime_SimpleMessage
    {
        return unserialize(base64_decode($this->message));
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setSentAt(DateTimeInterface $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setRetries(int $retries): void
    {
        $this->retries = $retries;
    }

    public function getRetries(): int
    {
        return $this->retries;
    }
}
