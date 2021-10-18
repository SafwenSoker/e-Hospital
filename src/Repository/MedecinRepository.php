<?php

namespace App\Repository;

use App\Entity\DemandeRV;
use App\Entity\Medecin;
use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Medecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medecin[]    findAll()
 * @method Medecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedecinRepository extends ServiceEntityRepository
{
    private $rvs = [];
    private $demandeRVRepository;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, DemandeRVRepository $demandeRVRepository, EntityManagerInterface $entityManager)
    {
        $this->rvs[] = new DemandeRV();
        $this->entityManager = $entityManager;
        $this->demandeRVRepository = $demandeRVRepository;
        parent::__construct($registry, Medecin::class);
    }

    /**
     * @return Medecin[] Returns an array of Medecin objects
     */
    public function findMedecinByRV(Patient $patient): array
    {
        $medecins = [];
        $rvs = $this->demandeRVRepository->findRVByPatient($patient->getId());
        foreach ($rvs as $m) {
            array_push($medecins, $m->getMedecin()->getId());
        }
        dump($medecins);
        $qb = $this->createQueryBuilder('m')
            ->where('m.id  NOT IN (:medecins)')
            ->setParameters(['medecins' => [$medecins]]);

        $query = $qb->getQuery();

        return $query->execute();
    }

    /*
    public function findOneBySomeField($value): ?Medecin
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
