<?php

namespace Affluatif\View\Modal;

/**
 * Class ConfirmModale
 *
 * @package Affluatif\View\Modal
 */
class ConfirmModale extends BaseModal
{
    protected $id = 'modale__confirm';

    protected $hasHeader = false;

    protected function blockModalBody()
    {
        ?>
        <p id="confirm_message" class="mb-20">Êtes-vous sûr de vouloir effectuer cette action ?</p>
        <button class="genric-btn danger circle"
                type="button"
                onclick="$('#modale__confirm').modal('hide');">
            Annuler
        </button>
        <button id="confirm_success"
                class="genric-btn success circle"
                type="button">
            Oui
        </button>
        <?php
    }
}