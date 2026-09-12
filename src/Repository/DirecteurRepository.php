<?php

namespace App\Repository;

use App\Entity\Directeur;
use EasyRdf\Sparql\Result;
use JetBrains\PhpStorm\ArrayShape;


class DirecteurRepository extends SparQL
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
        $directeurs = array();

        foreach ($result as $entry) {
            $directeur = new Directeur();
            $directeur->setId(self::getIdFromResourceURI($entry->director));
            $directeur->setName($entry->name);
            $directeurs[] = $directeur;
        }
        return $directeurs;
    }

    public function arrayFromResultFull(Result $result): array
    {
        if ($result->numRows() == 0) {
            return [];
        }
        $directeurs = array();

        foreach ($result as $entry) {
            $directeur = new Directeur();
            $shortId = self::getIdFromResourceURI($entry->director);
            $directeur->setId($shortId);
            $directeur->setName($entry->name);
            if (isset($entry->abstract)) {
                $directeur->setAbstract($entry->abstract);
            }
            $museeRepository = new MuseeRepository();
            $musee = $museeRepository->findByDirectorFast($shortId);
            $directeur->setMusee($musee);
            $directeurs[] = $directeur;
        }
        return $directeurs;
    }


    public function findById(string $id): ?Directeur
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?director
              (MAX(?name) as ?name)
              (SAMPLE(?abstract) as ?abstract)
            WHERE {
            BIND(<${resource}> as ?director)
              ?director rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
              OPTIONAL {
                ?director dbo:abstract ?abstract .
                FILTER(langMatches(lang(?abstract),'en'))
              }
            }
            GROUP BY ?director
            ");

        return $this->arrayFromResultFull($result)[0] ?? null;
    }

    public function findByIdFast(string $id): ?Directeur
    {
        $resource = self::$prefix . $id;
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?director
              (MAX(?name) as ?name)
            WHERE {
            BIND(<${resource}> as ?director)
               ?director rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?director
            ");

        return $this->arrayFromResultFast($result)[0] ?? null;
    }

    public function findContainingWordInNameFast(string $word): array
    {
        $result = $this->sparql_client->query("
        SELECT DISTINCT ?director
              (MAX(?name) as ?name)
            WHERE {
               ?museum rdf:type dbo:Museum.
               ?museum dbp:director | dbr:director ?director .
               ?director rdfs:label ?name.
               FILTER (langMatches(lang(?name),'en'))
              FILTER contains(lcase(str(?name)),lcase(\"${word}\"))
            }
            GROUP BY ?director
            ");
        return $this->arrayFromResultFast($result);
    }

    #[ArrayShape([Directeur::class])]
    public function findAllFast(): array
    {
        $result = $this->sparql_client->query("
            SELECT DISTINCT ?director
              (MAX(?name) as ?name)
            WHERE {
               ?museum rdf:type dbo:Museum.
               ?museum dbp:director | dbr:director ?director .
               ?director rdfs:label ?name.
              FILTER (langMatches(lang(?name),'en'))
            }
            GROUP BY ?director
            ");

        return $this->arrayFromResultFast($result);
    }

}
