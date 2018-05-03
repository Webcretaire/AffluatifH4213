<?php

namespace Affluatif\View\Modal;

use Affluatif\Services\Securite;
use Affluatif\Traits\Autocomplete;

/**
 * Class AjoutUserModale
 *
 * @package Affluatif\View\Modal
 */
class AjoutUserModale extends BaseModal
{
    protected $id = 'modale__newUser';

    use Autocomplete;

    public function autocomplete()
    {
        $this->autocomplete = $this->bddRequest(
            'SELECT * FROM utilisateurs WHERE id = :id',
            ['id' => Securite::validateInt($_GET['u'])]
        )->fetch();
        $this->id           = 'modale__editUser';
        $this->render();
    }

    protected function blockModalHeader()
    {
        if (!is_null($this->autocomplete)) {
            ?>
            <h2>Édition d'un utilisateur</h2>
            <?php
        } else {
            ?>
            <h2>Nouvel utilisateur</h2>
            <?php
        }
    }

    protected function blockModalBody()
    {
        ?>
        <form action="<?php
        if (!is_null($this->autocomplete)) {
            echo '/p/edition-utilisateur?u=' . $_GET['u'];
        } else {
            echo '/p/inscription';
        }
        ?>" method="post">
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                <input type="email" name="mail"
                       placeholder="Adresse mail"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Adresse mail'"
                       required class="single-input"
                    <?php $this->complete('mail'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-user-o" aria-hidden="true"></i></div>
                <input type="text" name="prenom"
                       placeholder="Prénom"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Prénom'"
                       required class="single-input"
                    <?php $this->complete('prenom'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-user-o" aria-hidden="true"></i></div>
                <input type="text" name="nom"
                       placeholder="Nom"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Nom'"
                       required class="single-input"
                    <?php $this->complete('nom'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                <input type="password" name="password"
                       placeholder="Mot de passe"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Mot de passe'"
                       required class="single-input">
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                <input type="password" name="password_conf"
                       placeholder="Confirmation"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Confirmation'"
                       required class="single-input">
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-star-o" aria-hidden="true"></i></div>
                <div class="form-select" id="default-select">
                    <select name="statut">
                        <?php
                        foreach (Securite::$statuts as $statut => $code) {
                            echo '<option value="' . $code . '" ' .
                                ($code == $this->autocomplete['statut'] ? 'selected' : '') .
                                ' >' . $statut . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="mt-10" style="text-align: center">
                <button class="genric-btn success circle" type="submit">
                    Valider
                </button>
            </div>
        </form>
        <?php
    }
}