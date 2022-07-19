<?php

namespace App\Repository;

use App\Entity\Recuperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recuperation>
 *
 * @method Recuperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recuperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recuperation[]    findAll()
 * @method Recuperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecuperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recuperation::class);
    }

    public function add(Recuperation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recuperation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByEmail($email): ?Recuperation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function rowCount($email)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function rowCountEmailAndCode($code, $email)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.email = :email')
            ->andWhere('u.code = :code')
            ->setParameter('email', $email)
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
