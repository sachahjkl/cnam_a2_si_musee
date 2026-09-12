<?php


namespace App\Repository;

use EasyRdf\Sparql\Client;

abstract class SparQL
{
    protected Client $sparql_client;
    protected static string $prefix = "http://dbpedia.org/resource/";

    public function __construct()
    {
        $this->sparql_client = new Client("https://dbpedia.org/sparql", "");
    }

    public static function getIdFromResourceURI(string $URI): ?string
    {
        $split = preg_split("/\//", $URI);
        if (count($split) == 0)
            return null;
        return str_replace(" ", "_", $split[count($split) - 1]);

    }

}
