<?php


namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Client;

abstract class SparQL extends ServiceEntityRepository
{
    protected Client $sparql_client;
    protected static string $prefix = "http://dbpedia.org/resource/";

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $this->sparql_client = new Client("https://dbpedia.org/sparql", "");
        parent::__construct($registry, $entityClass);
    }

    public static function getIdFromResourceURI(string $URI): ?string
    {
        $split = preg_split("/\//", $URI);
        if (count($split) == 0)
            return null;
        return $split[count($split) - 1];

    }

}