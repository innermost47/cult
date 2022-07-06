<?php

namespace App\Repository;

use App\Entity\Reporting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reporting>
 *
 * @method Reporting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reporting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reporting[]    findAll()
 * @method Reporting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reporting::class);
    }

    public function add(Reporting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reporting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findEverythingLike($search): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.description LIKE :search')
            ->orWhere('r.reporter LIKE :search')
            ->orWhere('r.createdAt LIKE :search')
            ->leftJoin('r.praticien', 'p')
            ->orWhere('p.firstName LIKE :search')
            ->orWhere('p.lastName LIKE :search')
            ->leftJoin('r.groupe', 'g')
            ->orWhere('g.name LIKE :search')
            ->setParameter(':search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
