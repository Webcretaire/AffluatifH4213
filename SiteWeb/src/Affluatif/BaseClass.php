<?php

namespace Affluatif;

use Affluatif\Services\Functions;

/**
 * Class BaseClasse
 *
 * Classe globale fournissant des fonctions réutilisables à peu près partout
 * @package Affluatif
 */
abstract class BaseClass
{
    /**
     * @var \PDO
     */
    protected $bdd;

    /**
     * @var ServiceContainer
     */
    protected $services;

    /**
     * Constructeur
     *
     * Se connecte à la base de données si ce n'est pas déjà fait. Instancie un ServiceContainer
     * @param \PDO $bdd La connexion à la base de données si celle-ci a déjà été établie
     */
    public function __construct($bdd = null)
    {
        if (is_null($bdd)) {
            $connector = new BDDConnector();
            $bdd = $connector->connectBDD();
        }
        $this->bdd = $bdd;
        $this->services = new ServiceContainer($this->bdd);
    }

    /**
     * Effectue une requête à la base de données
     * @param string $query     La requête
     * @param array $options    Les paramètres de la requête
     * @return \PDOStatement    L'objet résultat
     */
    protected function bddRequest(string $query, array $options = [])
    {
        $req = $this->bdd->prepare($query);
        $req->execute($options);

        if($req->errorCode() != 0) {
            echo '<h1>Erreur MySQL</h1>';
            Dev::dump(['requête' => $req, 'erreur' => $req->errorInfo()]);
        }

        return $req;
    }

    /**
     * Redirige l'utilisateur vers la page précédente
     */
    protected function redirectToReferer()
    {
        $redirectPage = $_SESSION['UriStack'][0]; // Default behaviour => refreshes the page, should not be used

        if (isset($_SESSION['UriStack'][1])) {
            $redirectPage = $_SESSION['UriStack'][1];
        }

        $this->redirectToURI($redirectPage);
    }

    /**
     * Redirige l'utilisateur vers l'URL indiquée
     *
     * Tente une redirection PHP fiable. Si celle-ci n'est pas possible, tente une redirection JavaScript
     * @param $uri L'adresse cible
     */
    protected function redirectToURI($uri)
    {
        if (!headers_sent()) { // PHP redirect => safer
            header('Location: ' . $uri);
        } else { // try JS redirection (NOT safe)
            echo '
                    <script type="text/javascript">
                        window.location.replace("' . Functions::escapeQuoteAndNL($uri) . '");
                    </script>
            ';
        }

        die();
    }
}