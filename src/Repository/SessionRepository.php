<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function add(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Récupère les stagiaires qui ne sont pas inscrit dans la session
    public function findAllNotSubscribed(int $session_id)
    {
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

        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        $qb->select('s')
            ->from('App\Entity\Intern', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');
        
            $sub = $em->createQueryBuilder();
            $sub->select('it')
                ->from('App\Entity\Intern', 'it')
                ->where($sub->expr()->notIn('it.id', $qb->getDQL()))
                ->setParameter('id', $session_id)
                ->orderBy('it.lastname');
        $query = $sub->getQuery();
        return $query->getResult();
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
