<?php

namespace Affluatif\View\Modal;

/**
 * Class ContactModal
 *
 * @package Affluatif\View\Modal
 */
class ContactModale extends BaseModal
{
    protected $id = 'modale__contact';

    protected function blockModalHeader()
    {
        ?>
        <h2>Nous contacter</h2>
        <?php
    }

    protected function blockModalBody()
    {
        ?>
        <p>Laissez-nous vos informations pour que nous puissions vous envoyer plus de détails sur nos offres</p>
        <form name="formulaire" method="post" class="form-horizontal">
            <div class="mt-10">
                <input type="email" name="mail"
                       placeholder="Adresse mail"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Adresse mail'"
                       required class="single-input">
            </div>
            <div class="mt-10">
                <textarea class="single-textarea" placeholder="Message" onfocus="this.placeholder = ''"
                          onblur="this.placeholder = 'Message'" required></textarea>
            </div>

            <div class="mt-10">
                <button class="genric-btn success circle" type="button" onclick="$.notify({
                        message: 'Merci, nous vous recontacterons dans les plus brefs délais'
                    },{
                        type: 'success'
                    }); $('#modale__contact').modal('hide');">
                    <span class="fa fa-plane"></span> Envoyer
                </button>
            </div>
        </form>
        <?php
    }
}