<?php

namespace App\Repository;

use App\Entity\Musee;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Sparql\Result;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;

class MuseeRepository extends SparQL
{

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Musee::class);
        $this->logger = $logger;
    }

    public function arrayFromResultFast(Result $result): array
    {
        if ($result->numRows() == 0) {
            return [];
        }
        $musees = array();

        foreach ($result as $entry) {
            $musee = new Musee();
            $musee->setId(self::getIdFromResourceURI($entry->museum));
            $musee->setName($entry->name);
            $musees[] = $musee;
        }
        return $musees;
    }

    public function arrayFromResultFull(Result $result): array
    {
        if ($result->numRows() == 0) {
            return [];
        }
        $musees = array();

        foreach ($result as $entry) {
            $musee = new Musee();
            $musee->setId(self::getIdFromResourceURI($entry->museum));
            $musee->setName($entry->name);
            $musee->setAbstract($entry->abstract ?? null);
            if (isset($entry->latitude)) {
                $musee->setLatitude($entry->latitude->getValue());

            }
            if (isset($entry->longitude)) {
                $musee->setLongitude($entry->longitude->getValue());
            }
            $musees[] = $musee;
        }
        return $musees;
    }

    #[ArrayShape([Musee::class])]
    public function findAll(): array
    {
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
              (SAMPLE(?abstract) as ?abstract)
              (SAMPLE(?thumbnail) as ?thumbnail)
              (MAX(?latitude) as ?latitude)
              (MAX(?longitude) as ?longitude)
              (SAMPLE(?website) as ?website)
              (SAMPLE(?homepage) as ?homepage)
              (SAMPLE(?wikilink) as ?wikilink)
              (SAMPLE(?director) as ?director)
            WHERE { 
              ?museum a dbo:Museum ; 
                      dbp:name ?name ; 
                      dbo:abstract ?abstract ; 
                      dbo:thumbnail ?thumbnail ; 
                      geo:lat ?latitude ;  
                      geo:long ?longitude ; 
                      dbp:website ?website ; 
                      foaf:homepage ?homepage ; 
                      foaf:isPrimaryTopicOf ?wikilink.
            OPTIONAL {
                     ?museum dbp:director | dbr:director ?director .
            }   
              FILTER(langMatches(lang(?abstract),'en')) 
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");

        return $this->arrayFromResultFull($result);
    }

    #[ArrayShape([Musee::class])]
    public function findAllFast(): array
    {
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
              ?museum a dbo:Museum ; 
                      dbp:name ?name.
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");
        return $this->arrayFromResultFast($result);
    }

    #[ArrayShape([Musee::class])]
    public function findContainingWordInNameFast(string $word): array
    {
        $word_lower = strtolower($word);
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
              ?museum a dbo:Museum ; 
                      dbp:name ?name.
              FILTER (langMatches(lang(?name),'en'))
              FILTER contains(lcase(str(?name)),lcase('${word_lower}'))
            }
            GROUP BY ?museum
            ");
        return $this->arrayFromResultFast($result);
    }

    #[ArrayShape([Musee::class])]
    public function findById(string $id): ?Musee
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
              (SAMPLE(?abstract) as ?abstract)
              (SAMPLE(?thumbnail) as ?thumbnail)
              (MAX(?latitude) as ?latitude)
              (MAX(?longitude) as ?longitude)
              (SAMPLE(?website) as ?website)
              (SAMPLE(?homepage) as ?homepage)
              (SAMPLE(?wikilink) as ?wikilink)
              (SAMPLE(?director) as ?director)
            WHERE { 
            BIND( <${resource}> as ?museum)
               ?museum dbp:name ?name. 
            OPTIONAL {
               ?museum dbo:abstract ?abstract ; 
                      dbo:thumbnail ?thumbnail ; 
                      geo:lat ?latitude ;  
                      geo:long ?longitude ; 
                      dbp:website ?website ; 
                      foaf:homepage ?homepage ; 
                      foaf:isPrimaryTopicOf ?wikilink.
                      ?museum dbp:director | dbr:director ?director .
            }   
              FILTER(langMatches(lang(?abstract),'en')) 
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");
        return $this->arrayFromResultFull($result)[0];
    }

}
