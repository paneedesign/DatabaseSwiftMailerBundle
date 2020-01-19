<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\Query;

interface EmailRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getAllEmails(?int $limit = null, ?int $offset = null): Query;

    public function getEmailQueue(?int $limit = 100, ?int $maxRetries = 10): Query;
}
