<?php

namespace App\Repository;

use App\Entity\Pays;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Result;

/**
 * @method Pays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pays[]    findAll()
 * @method Pays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaysRepository extends SparQL
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pays::class);
    }

    public function findContaining(string $word): Result
    {
        $word_lower = strtolower($word);
        return $this->sparql_client->query("
            SELECT * WHERE {
                ?link rdf:type dbo:Museum.
                ?link rdfs:label ?label
                FILTER (lang(?label) = 'fr')
                FILTER contains(lcase(str(?label)),\"${word_lower}\")
            } ORDER BY ?label
            ");
    }
}
