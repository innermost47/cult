<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Praticien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Praticien>
 *
 * @method Praticien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Praticien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Praticien[]    findAll()
 * @method Praticien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PraticienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Praticien::class);
    }

    public function add(Praticien $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Praticien $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Praticien[] Returns an array of Praticien objects
     */
    public function findAllByLastName(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.zip', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEverythingLike($search): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.lastName LIKE :search')
            ->orWhere('p.firstName LIKE :search')
            ->setParameter(':search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
