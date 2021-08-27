<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */

    public function findByPrix($prix)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.prix <= :prix')
            ->setParameter('prix', $prix)
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByMatiere($matiere)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.matieres','m')
            ->andWhere('m.id = :matiere')
            ->setParameter('matiere', $matiere)
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPrixCategory($prix, $cat)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.prix <= :prix')
            ->setParameter('prix', $prix)
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $cat)
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }



    public function findByPrixMatiere($prix, $matiere)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.matieres','m')
            ->andWhere('p.prix <= :prix')
            ->setParameter('prix', $prix)
            ->andWhere('m.id = :matiere')
            ->setParameter('matiere', $matiere)
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPrixCatMatiere($prix, $cat, $matiere)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.matieres','m')
            ->andWhere('p.prix <= :prix')
            ->setParameter('prix', $prix)
            ->andWhere('m.id = :matiere')
            ->setParameter('matiere', $matiere)
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $cat)
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }



    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
