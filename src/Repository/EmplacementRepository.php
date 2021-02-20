<?php

namespace App\Repository;

use App\Entity\Emplacement;
use EasyRdf\Sparql\Result;
use JetBrains\PhpStorm\ArrayShape;

class EmplacementRepository extends SparQL
{
    public function __construct()
    {
        parent::__construct();
    }

    public function arrayFromResultFast(Result $result): array
    {
        if ($result->numRows() == 0) {
            return [];
        }
        $emplacements = array();

        foreach ($result as $entry) {
            $emplacement = new Emplacement();
            $emplacement->setId(self::getIdFromResourceURI($entry->location));
            $emplacement->setName($entry->name);
            $emplacements[] = $emplacement;
        }
        return $emplacements;
    }

    public function arrayFromResultFull(Result $result): array
    {
        if ($result->numRows() == 0) {
            return [];
        }
        $emplacements = array();

        foreach ($result as $entry) {
            $emplacement = new Emplacement();
            $shortId = self::getIdFromResourceURI($entry->location);
            $emplacement->setId($shortId);
            $emplacement->setName($entry->name);
            $emplacement->setAbstract($entry->abstract ?? null);
            if (isset($entry->latitude)) {
                $emplacement->setLatitude($entry->latitude->getValue());

            }
            if (isset($entry->longitude)) {
                $emplacement->setLongitude($entry->longitude->getValue());
            }
            $museeRepository = new MuseeRepository();
            $musees = $museeRepository->findByLocationFast($shortId);
            foreach ($musees as $musee) {
                $emplacement->addMusee($musee);
            }

            $emplacements[] = $emplacement;
        }
        return $emplacements;
    }

    #[ArrayShape([Emplacement::class])]
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
              (SAMPLE(?location) as ?location)
            WHERE { 
               ?museum rdf:type dbo:Museum.
               ?museum dbp:name ?name.
            OPTIONAL {
               ?museum dbo:abstract ?abstract .
               FILTER(langMatches(lang(?abstract),'en'))
            }
            OPTIONAL { 
               ?museum dbo:thumbnail ?thumbnail
            }
            OPTIONAL { 
                ?museum geo:lat ?latitude
            }
            OPTIONAL {  
                ?museum geo:long ?longitude
              }
            OPTIONAL { 
                ?museum dbp:website ?website
            }
            OPTIONAL { 
                ?museum foaf:homepage ?homepage
            }
            OPTIONAL { 
                ?museum foaf:isPrimaryTopicOf ?wikilink
            }
            OPTIONAL {
               ?museum dbp:director | dbr:director ?director .
            }
            OPTIONAL {
               ?museum dbo:location ?location .
            } 
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?location
            ");

        return $this->arrayFromResultFull($result);
    }

    #[ArrayShape([Emplacement::class])]
    public function findAllFast(): array
    {
        $result = $this->sparql_client->query("
           SELECT DISTINCT ?location
              (MAX(?name) as ?name)
            WHERE { 
               ?museum rdf:type dbo:Museum.
               ?museum dbo:location ?location .
               ?location rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?location
            ");
        return $this->arrayFromResultFast($result);
    }

    #[ArrayShape([Emplacement::class])]
    public function findContainingWordInNameFast(string $word): array
    {
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?location
              (MAX(?name) as ?name)
            WHERE { 
               ?museum rdf:type dbo:Museum.
               ?museum dbo:location ?location .
               ?location rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
              FILTER contains(lcase(str(?name)),lcase(\"${word}\"))
            }
            GROUP BY ?location
            ");
        return $this->arrayFromResultFast($result);
    }

    public function findById(string $id): ?Emplacement
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?location
              (MAX(?name) as ?name)
              (SAMPLE(?abstract) as ?abstract)
              (MAX(?latitude) as ?latitude)
              (MAX(?longitude) as ?longitude)
            WHERE { 
               BIND(<${resource}> as ?location)
               ?location rdfs:label ?name.
               OPTIONAL {
                    ?location dbo:abstract ?abstract .
                    FILTER(langMatches(lang(?abstract),'en'))
               }
               OPTIONAL { 
                    ?location geo:lat ?latitude
               }
               OPTIONAL {  
                    ?location geo:long ?longitude
               }
               
              FILTER (langMatches(lang(?name),'en'))
              }
            ");

        return $this->arrayFromResultFull($result)[0] ?? null;
    }

    public function findByIdFast(string $id): ?Emplacement
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?location
              (MAX(?name) as ?name)
            WHERE { 
               BIND(<${resource}> as ?location)
               ?location rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
              }
            ");

        return $this->arrayFromResultFast($result)[0] ?? null;
    }

}
