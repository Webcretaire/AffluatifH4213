<?php

namespace Affluatif\Processing;

use Affluatif\BaseClass;
use Affluatif\Services\Securite;

/**
 * Class AuthProcessing
 *
 * @package Affluatif\Processing
 */
class AuthProcessing extends BaseClass
{
    /**
     * Tente de connecter l'utilisateur automatiquement à l'aide de ses cookies
     */
    public function connexionAuto()
    {
        if (isset($_COOKIE['connexion_Affluatif']) && isset($_COOKIE['password_Affluatif']) && !isset($_SESSION['prenom'])) {
            try {
                $this->connexion($_COOKIE['connexion_Affluatif'], $_COOKIE['password_Affluatif'], true);
            } catch (\Exception $e) {
                $this->deconnexion();
            }
        }
    }

    /**
     * Connecte l'utilisateur si celui-ci vient du site web classique
     */
    public function connexionWeb()
    {
        if (isset($_POST['mail']) && !empty($_POST['mail']) && isset($_POST['password']) && !empty($_POST['password'])) {
            $connexion_auto = false;
            if (isset($_POST['connexion_auto'])) {
                $connexion_auto = $_POST['connexion_auto'];
            }

            try {
                $this->connexion($_POST['mail'], $_POST['password'], $connexion_auto);
                $this->services->getNotify()->setNotif('Bonjour ' . $_SESSION['prenom'], 'success', 5000);
            } catch (\Exception $e) {
                $this->services->getNotify()->setNotif('Adresse mail ou mot de passe invalide', 'danger', 5000);
                $this->redirectToReferer();
            }
        } else {
            $this->services->getNotify()->setNotif('Veuillez remplir votre adresse mail et mot de passe', 'danger', 5000);
            $this->redirectToReferer();
        }

        if (isset($_SESSION['redirectAfterLogin'])) {
            $this->redirectToURI($_SESSION['redirectAfterLogin']);
        } else {
            $this->redirectToURI('/');
        }
    }

    /**
     * Fonction générale pour connecter un utilisateur
     * @param int|string $identifier     L'identifiant de l'utilisateur (id ou mail selon le cas)
     * @param string     $password       Le mot de passe (déjà encrypté ou pas selon le cas)
     * @param bool       $connexion_auto Définis si l'utilisateur veut être connecté automatiquement la prochaine fois
     * @return int        Code de retour (0 = succès)
     * @throws \Exception Mauvaise identification
     */
    public function connexion($identifier, $password, $connexion_auto = false)
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $searchOn = 'mail';  // Connexion standard par email/password_clair
        } elseif (filter_var($identifier, FILTER_VALIDATE_INT)) {
            $searchOn = 'id';  // Connexion par id/password_encrypté
            $password = $this->services->getSecurite()->aesDecrypt($password, $identifier);
        } else {
            throw new \Exception("Mauvais format d'entrée", 1002);
        }
        $plainPassword = $password;
        $password      = hash('sha256', $this->services->getConfig()->getPasswordSalt() . $password);
        $resultat      = $this->bddRequest(
            "SELECT * FROM utilisateurs WHERE $searchOn = :identifier AND password = :password",
            ['identifier' => strip_tags($identifier), 'password' => $password]
        )->fetch();
        if (!$resultat) {
            throw new \Exception("Mauvaise adressse ou mot de passe", 1001);
        } else {
            unset($_SESSION['erreur_connexion']);
            $_SESSION['id']           = $resultat['id'];
            $_SESSION['prenom']       = $resultat['prenom'];
            $_SESSION['nom']          = $resultat['nom'];
            $_SESSION['mail']         = $resultat['mail'];
            $_SESSION['statut']       = $resultat['statut'];
            $_SESSION['flux_allowed'] = array_column($this->bddRequest(
                'SELECT flux_id FROM flux_utilisateur WHERE utilisateur_id = :user',
                ['user' => $_SESSION['id']]
            )->fetchAll(), 'flux_id');

            if ($connexion_auto) {
                setcookie(
                    'connexion_Affluatif',
                    $resultat['id'],
                    time() + 365 * 24 * 3600,
                    '/',
                    null,
                    false,
                    false
                );
                setcookie(
                    'password_Affluatif',
                    $this->services->getSecurite()->aesEncrypt($plainPassword, $resultat['id']),
                    time() + 365 * 24 * 3600,
                    '/',
                    null,
                    false,
                    false
                );
            }

            return 0;
        }
    }

    /**
     * Déconnecte l'utilisateur
     *
     * Suppression de la session en cours et des cookies
     */
    public function deconnexion()
    {
        // Suppression des variables de session et de la session
        session_unset();
        session_destroy();

        if (isset($_COOKIE['connexion_Affluatif'])) {
            setcookie('connexion_Affluatif', null, -1, '/');
            setcookie('password_Affluatif', null, -1, '/');
        }

        $this->redirectToURI('/');
    }

    public function inscription()
    {
        $this->services->getSecurite()->verificationAdmin();

        if ($_POST['password'] != $_POST['password_conf']) {
            $this->services->getNotify()->setNotif('Les mots de passe ne correspondent pas', 'danger', 5000);
            $this->redirectToReferer();
        }

        if (strlen($_POST['password']) < 7) {
            $this->services->getNotify()->setNotif('Mot de passe trop court (minimum 8 caractères)', 'danger', 5000);
            $this->redirectToReferer();
        }

        $this->bddRequest(
            'INSERT INTO utilisateurs (prenom, nom, mail, password, statut) 
            VALUES (:prenom, :nom, :mail, :password, :statut)',
            [
                'prenom'   => strip_tags($_POST['prenom']),
                'nom'      => strip_tags($_POST['nom']),
                'mail'     => strip_tags($_POST['mail']),
                'password' => hash('sha256', $this->services->getConfig()->getPasswordSalt() . $_POST['password']),
                'statut'   => strip_tags($_POST['statut']),
            ]
        );

        $this->services->getNotify()->setNotif('Utilisateur ajouté', 'success', 5000);

        $this->redirectToReferer();
    }

    public function editUser()
    {
        $this->services->getSecurite()->verificationAdmin();

        if ($_POST['password'] != $_POST['password_conf']) {
            $this->services->getNotify()->setNotif('Les mots de passe ne correspondent pas', 'danger', 5000);
            $this->redirectToReferer();
        }

        if (strlen($_POST['password']) < 7) {
            $this->services->getNotify()->setNotif('Mot de passe trop court (minimum 8 caractères)', 'danger', 5000);
            $this->redirectToReferer();
        }

        $this->bddRequest(
            'UPDATE utilisateurs 
            SET prenom = :prenom, nom = :nom, mail = :mail, password = :password, statut = :statut 
            WHERE id = :id',
            [
                'id'       => Securite::validateInt($_GET['u']),
                'prenom'   => strip_tags($_POST['prenom']),
                'nom'      => strip_tags($_POST['nom']),
                'mail'     => strip_tags($_POST['mail']),
                'password' => hash('sha256', $this->services->getConfig()->getPasswordSalt() . $_POST['password']),
                'statut'   => strip_tags($_POST['statut']),
            ]
        );

        $this->services->getNotify()->setNotif('Utilisateur modifié', 'success', 5000);

        $this->redirectToReferer();
    }

    public function deleteUser()
    {
        $this->services->getSecurite()->verificationAdmin();

        $this->bddRequest('DELETE FROM utilisateurs WHERE id = :id', ['id' => Securite::validateInt($_GET['u'])]);

        $this->services->getNotify()->setNotif('Utilisateur supprimé', 'success', 5000);

        $this->redirectToReferer();
    }
}