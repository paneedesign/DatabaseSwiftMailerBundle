<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;
use Swift_SwiftException;

interface EmailRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function addEmail(Email $email, ?bool $autoFlush = true): void;

    public function getAllEmails($limit = null, $offset = null);

    public function getEmailQueue($limit = 100, $maxRetries = 10);

    public function markFailedSending(Email $email, Swift_SwiftException $ex);

    public function markCompleteSending(Email $email): void;

    public function deleteSentMessages(Email $email): void;
}
