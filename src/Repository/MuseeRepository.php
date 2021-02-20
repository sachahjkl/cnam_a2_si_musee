<?php

namespace App\Repository;

use App\Entity\Musee;
use EasyRdf\Sparql\Result;
use JetBrains\PhpStorm\ArrayShape;

class MuseeRepository extends SparQL
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
            if (isset($entry->director)) {
                $directeurRepository = new DirecteurRepository();
                $id = self::getIdFromResourceURI($entry->director);
                $directeur = $directeurRepository->findByIdFast($id);
                $musee->setDirecteur($directeur);
            }
            if (isset($entry->thumbnail)) {
                $musee->setThumbnailUri($entry->thumbnail);
            }
            if (isset($entry->website)) {
                $musee->setWebsite($entry->website);
            }
            if (isset($entry->wikilink)) {
                $musee->setWikilink($entry->wikilink);
            }
            if (isset($entry->location)) {
                $emplacementRepository = new EmplacementRepository();
                $id = self::getIdFromResourceURI($entry->location);
                $emplacement = $emplacementRepository->findByIdFast($id);
                $musee->setLocation($emplacement);
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
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
              ?museum a dbo:Museum ; 
                      dbp:name ?name.
              FILTER (langMatches(lang(?name),'en'))
              FILTER contains(lcase(str(?name)),lcase(\"${word}\"))
            }
            GROUP BY ?museum
            ");
        return $this->arrayFromResultFast($result);
    }

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
              (SAMPLE(?wikilink) as ?wikilink)
              (SAMPLE(?director) as ?director)
              (SAMPLE(?location) as ?location)
            WHERE { 
            BIND(<${resource}> as ?museum)
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
            GROUP BY ?museum
            ");

        return $this->arrayFromResultFull($result)[0] ?? null;
    }

    public function findByIdFast(string $id): ?Musee
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
            BIND(<${resource}> as ?museum)
               ?museum dbp:name ?name.
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");

        return $this->arrayFromResultFast($result)[0] ?? null;
    }

    public function findByDirectorFast(string $directorId): ?Musee
    {
        $resource = self::$prefix . $directorId;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
               ?museum rdf:type dbo:Museum.
               ?museum dbp:name ?name.
               ?museum dbp:director | dbr:director <${resource}> .
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");

        return $this->arrayFromResultFast($result)[0] ?? null;
    }

    public function findByLocationFast(string $locationId): array
    {
        $resource = self::$prefix . $locationId;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?museum
              (MAX(?name) as ?name)
            WHERE { 
               ?museum rdf:type dbo:Museum.
               ?museum dbp:name ?name.
               ?museum dbo:location <${resource}> .
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?museum
            ");

        return $this->arrayFromResultFast($result);
    }

}
