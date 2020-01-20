<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Service;

use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use Swift_Mime_SimpleMessage;
use Swift_SwiftException;

interface EmailServiceInterface
{
    public function getById(int $id): Email;

    public function paginate(?int $limit = null, ?int $offset = null): array;

    public function count(): int;

    public function add(Swift_Mime_SimpleMessage $message, ?bool $autoFlush = true): void;

    public function getQueue(int $limit = 100, int $maxRetries = 10): array;

    public function retryById(int $id): void;

    public function retry(Email $email): void;

    public function resendById(int $id): void;

    public function resend(Email $email): void;

    public function cancelById(int $id): void;

    public function cancel(Email $email): void;

    public function deleteById(int $id): void;

    public function delete(Email $email): void;

    public function markFailed(Email $email, Swift_SwiftException $ex): void;

    public function markComplete(Email $email): void;
}
