<?php

namespace Affluatif\View\Modal;

/**
 * Class ConnexionModale
 *
 * @package Affluatif\View\Modal
 */
class ConnexionModale extends BaseModal
{
    protected $id = 'modale__connexion';

    protected function blockModalHeader()
    {
        ?>
        <h2>Connexion</h2>
        <?php
    }

    protected function blockModalBody()
    {
        ?>
        <form name="formulaire" method="post" action="/p/connexion" class="form-horizontal">
            <div class="mt-10">
                <input type="email" name="mail"
                       placeholder="Adresse mail"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Adresse mail'"
                       required class="single-input">
            </div>
            <div class="mt-10">
                <input type="password" name="password"
                       placeholder="Mot de passe"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Mot de passe'"
                       required class="single-input">
            </div>
            <div class="mt-10">
                <div class="switch-wrap d-flex justify-content-center">
                    <div class="primary-switch" style="margin-right: 20px;">
                        <input type="checkbox" name="connexion_auto" id="connexion_auto">
                        <label for="connexion_auto"></label>
                    </div>
                    <p>Se souvenir de moi</p>
                </div>
            </div>

            <div class="mt-10">
                <button class="genric-btn success circle" type="submit">
                    Valider
                </button>
            </div>
        </form>
        <?php
    }
}