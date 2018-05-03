<?php

namespace Affluatif;

use Affluatif\View\Erreur;
use Exception;
use Affluatif\Services\Config;
use PDO;

/**
 * Connection et gestion très basique de la base de données
 *
 * @package GdM
 */
class BDDConnector
{
    /**
     * Connexion à la base de données
     *
     * Cette fonction se base sur les paramètres fournis par la classe Config
     * @return PDO La connexion a la base de données
     */
    function connectBDD()
    {
        $config = new Config();
        try {
            $host = $config->getBDDHost();
            $database = $config->getBDDName();
            $username = $config->getBDDLogin();
            $password = $config->getBDDPassword();
            $bdd = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $bdd->exec("SET @@global.time_zone = 'Europe/Paris';");
            return $bdd;
        } catch (Exception $ex) {
            $errorPage = new Erreur(500);
            $errorPage->render();
            die();
        }
    }

    /**
     * Récupère le dernier id inséré dans la base de données pour la table passée en paramètre
     *
     * @param PDO    $bdd La connexion à utiliser
     * @param string $table La table considérée
     * @return int          Le dernier id inséré
     */
    public static function lastInsertedId(\PDO $bdd, string $table): int
    {
        $req = $bdd->query('SELECT id FROM ' . $table . ' ORDER BY id DESC LIMIT 1');
        $row = $req->fetch();

        return $row['id'];
    }
}