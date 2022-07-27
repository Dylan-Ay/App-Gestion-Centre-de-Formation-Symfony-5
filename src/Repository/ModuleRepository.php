<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function add(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllNotProgrammed(int $session_id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 
        'SELECT *
        FROM module m
        WHERE m.id NOT IN (
            SELECT module_id
            FROM program
            WHERE session_id = :session_id)';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['session_id' => $session_id]);

        return $resultSet->fetchAllAssociative();
    }

    // $conn = $this->getEntityManager()->getConnection();

        // $sql = 
        //     'SELECT * 
        //     FROM intern i
        //     INNER JOIN intern_session s 
        //     ON i.id = s.intern_id
        //     WHERE s.intern_id NOT IN (
        //         SELECT s.intern_id
        //         FROM intern_session s
        //         WHERE session_id = :session_id)';
        // $stmt = $conn->prepare($sql);
        // $resultSet = $stmt->executeQuery(['session_id' => $session_id]);
        
        // return $resultSet->fetchAllAssociative();

//    /**
//     * @return Module[] Returns an array of Module objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Module
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
