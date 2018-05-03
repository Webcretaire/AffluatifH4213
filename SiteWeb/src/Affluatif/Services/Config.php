<?php

namespace Affluatif\Services;

use Symfony\Component\Yaml\Yaml;

/**
 * Gère les paramètres du site
 *
 * Les paramètres sont lus depuis le fichier de configuration parameters.yml,
 * et ne sont accessible qu'en lecture par des getters
 * @package Affluatif
 */
class Config
{
    // ========== DataBase ==========

    /**
     * @var string
     */
    private $bdd_host;

    /**
     * @var string
     */
    private $bdd_name;

    /**
     * @var string
     */
    private $bdd_login;

    /**
     * @var string
     */
    private $bdd_password;

    // ========== Security ==========

    /**
     * @var string
     */
    private $password_salt;

    /**
     * @var string
     */
    private $aesKey;

    /**
     * @var string
     */
    private $aesIv;

    // ========= RabbitMQ =========

    /**
     * @var string
     */
    private $rabbitmq_host;

    /**
     * @var int
     */
    private $rabbitmq_port;

    /**
     * @var string
     */
    private $rabbitmq_login;

    /**
     * @var string
     */
    private $rabbitmq_password;

    // ========= Google =========

    /**
     * @var string
     */
    private $google_apiMaps;

    // ========= Analizer =========

    /**
     * @var string
     */
    private $analizer_endpoint;

    /**
     * @var string
     */
    private $analizer_login;

    /**
     * @var string
     */
    private $analizer_password;

    // ========= Other shit =========

    /**
     * @var string
     */
    private $projectRootFolder;

    /**
     * @var string
     */
    private $namespaceRootFolder;

    /**
     * @var string
     */
    private $webRootFolder;

    /**
     * Constructeur
     *
     * Les paramètres sont lus automatiquement depuis config/parameters.yml et enregistré dans les attributs
     * correspondants
     */
    public function __construct()
    {
        if (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT'])) {
            $this->webRootFolder = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $this->webRootFolder = __DIR__ . '/../../../html';
        }

        $this->projectRootFolder   = $this->webRootFolder . "/..";
        $this->namespaceRootFolder = $this->projectRootFolder . "/src/Affluatif";
        $parameters                = Yaml::parse(file_get_contents($this->projectRootFolder . "/config/parameters.yml"));
        $this->password_salt       = $parameters['security']['password_salt'];
        $this->aesKey              = $parameters['security']['aes_key'];
        $this->aesIv               = $parameters['security']['aes_iv'];
        $this->bdd_host            = $parameters['database']['host'];
        $this->bdd_name            = $parameters['database']['name'];
        $this->bdd_login           = $parameters['database']['login'];
        $this->bdd_password        = $parameters['database']['password'];
        $this->rabbitmq_host       = $parameters['rabbitmq']['host'];
        $this->rabbitmq_port       = $parameters['rabbitmq']['port'];
        $this->rabbitmq_login      = $parameters['rabbitmq']['login'];
        $this->rabbitmq_password   = $parameters['rabbitmq']['password'];
        $this->analizer_endpoint   = $parameters['analizer']['endpoint'];
        $this->analizer_login      = $parameters['analizer']['login'];
        $this->analizer_password   = $parameters['analizer']['password'];
        $this->google_apiMaps      = $parameters['google']['api_maps'];
    }

    // ========== Getters ==========

    /**
     * @return string
     */
    public function getBddHost(): string
    {
        return $this->bdd_host;
    }

    /**
     * @return string
     */
    public function getBddName(): string
    {
        return $this->bdd_name;
    }

    /**
     * @return string
     */
    public function getBddLogin(): string
    {
        return $this->bdd_login;
    }

    /**
     * @return string
     */
    public function getBddPassword(): string
    {
        return $this->bdd_password;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return $this->password_salt;
    }

    /**
     * @return string
     */
    public function getAesKey(): string
    {
        return $this->aesKey;
    }

    /**
     * @return string
     */
    public function getAesIv(): string
    {
        return $this->aesIv;
    }

    /**
     * @return string
     */
    public function getRabbitmqHost(): string
    {
        return $this->rabbitmq_host;
    }

    /**
     * @return int
     */
    public function getRabbitmqPort(): int
    {
        return $this->rabbitmq_port;
    }

    /**
     * @return string
     */
    public function getRabbitmqLogin(): string
    {
        return $this->rabbitmq_login;
    }

    /**
     * @return string
     */
    public function getRabbitmqPassword(): string
    {
        return $this->rabbitmq_password;
    }

    /**
     * @return string
     */
    public function getProjectRootFolder(): string
    {
        return $this->projectRootFolder;
    }

    /**
     * @return string
     */
    public function getNamespaceRootFolder(): string
    {
        return $this->namespaceRootFolder;
    }

    /**
     * @return string
     */
    public function getWebRootFolder(): string
    {
        return $this->webRootFolder;
    }

    /**
     * @return string
     */
    public function getAnalizerEndpoint(): string
    {
        return $this->analizer_endpoint;
    }

    /**
     * @return string
     */
    public function getAnalizerLogin(): string
    {
        return $this->analizer_login;
    }

    /**
     * @return string
     */
    public function getAnalizerPassword(): string
    {
        return $this->analizer_password;
    }

    /**
     * @return string
     */
    public function getGoogleApiMaps(): string
    {
        return $this->google_apiMaps;
    }
}