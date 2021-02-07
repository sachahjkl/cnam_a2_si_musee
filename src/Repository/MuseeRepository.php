<?php

namespace App\Repository;

use App\Entity\Musee;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Result;

class MuseeRepository extends SparQL
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Musee::class);
    }

    public function findAll(): ?Result
    {
        return $this->sparql_client->query("
            SELECT * WHERE {
                ?link rdf:type dbo:Museum.
                ?link rdfs:label ?label
                FILTER (lang(?label) = 'fr')
            } ORDER BY ?label
            ");

    }

    public function findContaining(string $word): Result
    {
        $word_lower = strtolower($word);
        return $this->sparql_client->query("
            SELECT * WHERE {
                ?link rdf:type dbo:Museum.
                ?link rdfs:label ?label
                FILTER (lang(?label) = 'fr')
                FILTER contains(lcase(str(?label)),lcase(\"${word_lower}\"))
            } ORDER BY ?label
            ");
    }

    // /**
    //  * @return Musee[] Returns an array of Musee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Musee
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
