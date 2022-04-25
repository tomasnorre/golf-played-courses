<?php

namespace App\Repository;

use App\Entity\GolfCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GolfCourse>
 *
 * @method GolfCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method GolfCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method GolfCourse[]    findAll()
 * @method GolfCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GolfCourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GolfCourse::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GolfCourse $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GolfCourse $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findWithMarker(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.longitude != :val')
            ->setParameter('val', 0 )
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return GolfCourse[] Returns an array of GolfCourse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GolfCourse
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
