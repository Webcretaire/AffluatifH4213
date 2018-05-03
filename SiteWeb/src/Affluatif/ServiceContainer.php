<?php

namespace Affluatif;

use Affluatif\Services\Config;
use Affluatif\Services\Functions;
use Affluatif\Services\Notify;
use Affluatif\Services\RabbitMQ;
use Affluatif\Services\Securite;

/**
 * Gère les classes de services
 *
 * Les services doivent être récupérés via cette classe (et pas juste instanciés)
 * @package Affluatif
 */
class ServiceContainer
{
    /**
     * @var \PDO Base de données passée à chaque service lors de sa création
     */
    private $bdd;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Securite
     */
    private $securite;

    /**
     * @var Notify
     */
    private $notify;

    /**
     * @var RabbitMQ
     */
    private $rabbitMQ;

    /**
     * @var Functions
     */
    private $functions;

    /**
     * Constructeur
     *
     * Contrairement aux autres classes du site, la connexion à la base de données doit obligatoirement être passée au
     * ServiceContainer à sa création (et donc être établie au préalable)
     * @param \PDO $bdd Connexion à la base de données
     */
    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    /**
     * Récupère un service quelconque
     * @param string $service   Le nom du service
     * @param string|null $fqcn Le nom de la classe correspondant au service (Full Qualified Class Name)
     * @return mixed            L'objet instance du service demandé
     */
    private function getService($service, $fqcn)
    {
        if (is_null($this->$service)) {
            $this->$service = new $fqcn($this->bdd);
        }

        return $this->$service;
    }

    /*
     * Les fonctions suivantes sont des cas particuliers pour chaque service. On aurait pu faire un cas général qui aurait
     * rendu la classe beaucoup plus courte et efficace, mais cela aurait empêché la bonne reconnaissance du type d'objet
     * par PhpStorm (ou tout autre IDE). Cela signifie que l'autocomplétion ne fonctionnerait pas pour le service retourné
     * (Ce qui n'a l'air de rien mais rallonge considérablement le temps de développement). Cette classe est donc un
     * compromis : il y a à la fois une fonction générale pour récupérer un service quelconque, et une fonction particulière
     * pour chaque service, pour indiquer proprement le type de retour
     */

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->getService('config', Config::class);
    }

    /**
     * @return Securite
     */
    public function getSecurite(): Securite
    {
        return $this->getService('securite', Securite::class);
    }

    /**
     * @return Notify
     */
    public function getNotify(): Notify
    {
        return $this->getService('notify', Notify::class);
    }

    /**
     * @return RabbitMQ
     */
    public function getRabbitMQ(): RabbitMQ
    {
        return $this->getService('rabbitMQ', RabbitMQ::class);
    }

    /**
     * @return Functions
     */
    public function getFunctions(): Functions
    {
        return $this->getService('functions', Functions::class);
    }

}