<?php

namespace Affluatif\View;

/**
 * Class Error
 *
 * @package Affluatif\View
 */
class Erreur extends BaseTemplate
{
    protected $page_title = "404 - Affluatif";

    /**
     * @var int
     */
    private $code;

    public function __construct(int $code = 404, \PDO $bdd = null)
    {
        parent::__construct($bdd);

        $this->code = $code;

        http_response_code($this->code);

        switch ($this->code) {
            case 403:
                $this->page_title = "403 - Affluatif";
                header('HTTP/1.0 403 Forbidden');
                break;
            case 404:
                $this->page_title = "404 - Affluatif";
                header('HTTP/1.0 404 Not Found');
                break;
            case 500:
                $this->page_title = "500 - Affluatif";
                header('HTTP/1.1 500 Internal Server Error');
                break;
        }
    }

    protected function blockBanner()
    {
        ?>
        <h1 class="text-white black-glow">Erreur</h1>
        <p class="text-white black-glow">
            <?php
            switch ($this->code) {
                case 403:
                    echo "Vous n'avez pas le droit d'accéder à cette page";
                    break;
                case 404:
                    echo "La page demandée est introuvable";
                    break;
                case 500:
                    echo "Le serveur a rencontré une erreur interne";
                    break;
            }
            ?>
        </p>
        <?php
    }
}