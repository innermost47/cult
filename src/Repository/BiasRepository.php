<?php

namespace App\Repository;

use App\Entity\Bias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bias>
 *
 * @method Bias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bias|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bias[]    findAll()
 * @method Bias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bias::class);
    }

    public function add(Bias $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bias $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Bias[] Returns an array of Bias objects
     */
    public function findAllByName(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Bias
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
