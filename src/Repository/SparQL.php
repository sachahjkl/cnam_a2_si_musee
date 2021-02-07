<?php


namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Client;

abstract class SparQL extends ServiceEntityRepository
{
    protected Client $sparql_client;
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $this->sparql_client = new Client("https://dbpedia.org/sparql","");
        parent::__construct($registry, $entityClass);
    }

}