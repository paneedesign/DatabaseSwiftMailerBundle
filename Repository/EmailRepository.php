<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use PaneeDesign\DatabaseSwiftMailerBundle\Entity\Email;

class EmailRepository extends ServiceEntityRepository implements EmailRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function getAllEmails(?int $limit = null, ?int $offset = null): Query
    {
        $qb = $this->createQueryBuilder('e');
        $qb->addOrderBy('e.createdAt', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery();
    }

    public function getEmailQueue(?int $limit = 100, ?int $maxRetries = 10): Query
    {
        $qb = $this->createQueryBuilder('e');

        $qb->where($qb->expr()->in('e.status', ':status'));
        $qb->andWhere($qb->expr()->lt('e.retries', ':retries'));
        $qb->setParameters([
            'status' => [Email::STATUS_READY, Email::STATUS_FAILED],
            'retries' => $maxRetries,
        ]);

        $qb->addOrderBy('e.retries', 'ASC');
        $qb->addOrderBy('e.createdAt', 'ASC');

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }
}
