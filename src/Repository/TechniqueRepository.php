<?php

namespace App\Repository;

use App\Entity\Technique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technique>
 *
 * @method Technique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Technique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Technique[]    findAll()
 * @method Technique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechniqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technique::class);
    }

    public function add(Technique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Technique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Technique[] Returns an array of Technique objects
     */
    public function findAllByName(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEverythingLike($search): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :search')
            ->setParameter(':search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
