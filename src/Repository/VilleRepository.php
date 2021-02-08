<?php

namespace App\Repository;

use App\Entity\Ville;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Result;

/**
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends SparQL
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findContaining(string $word): Result
    {
        $word_lower = strtolower($word);
        return  $this->sparql_client->query("
            SELECT distinct ?label ?link WHERE {
                ?musee rdf:type dbo:Museum.
                ?musee dbo:location ?link.
                ?link dbo:country ?zob.
                ?link rdfs:label ?label.
                FILTER (lang(?label) = 'fr')
                FILTER contains(lcase(str(?label)),\"${word_lower}\")
            } ORDER BY ?label
            ");
    }
}
