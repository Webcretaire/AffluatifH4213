<?php

namespace Affluatif\View\Modal;

use Affluatif\View\BaseTemplate;

/**
 * Template des fenêtres modales
 *
 * @package Affluatif\View\Modal
 */
class BaseModal extends BaseTemplate
{
    /**
     * @var string id HTML de la modale
     */
    protected $id = 'modale__base';

    /**
     * @var bool Détermine si la modale a une en-tête
     */
    protected $hasHeader = true;

    /**
     * @var bool Détermine si l'utilisateur doit pouvoir fermer la modale
     */
    protected $canDismiss = true;

    /**
     * @var string Détermine la taille de la modale
     */
    protected $modalSize = '';

    /**
     * Affiche une modale de base
     *
     * La modale est construite à partir des paramètres dans les attributs et est vide par défaut
     */
    public function render()
    {
        ?>
        <!-- Modal -->
        <div class="modal fade" id="<?php echo $this->id; ?>" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true" <?php if (!$this->canDismiss) {
            ?>
            data-keyboard="false" data-backdrop="static"
            <?php
        }
        ?> style="text-align: center;">
            <div class="modal-dialog modal-dialog-centered <?php echo $this->modalSize; ?>">
                <div class="modal-content">
                    <?php if ($this->hasHeader) { ?>
                        <div class="modal-header">
                            <?php
                            $this->blockModalHeader();
                            if ($this->canDismiss)
                            {
                                ?>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;
                                </button>
                                <?php
                            } ?>
                        </div>
                        <?php
                    } ?>
                    <div class="modal-body modal_loading_body">
                        <?php $this->blockModalBody(); ?>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
    }

    /**
     * Corps de la modale
     */
    protected function blockModalBody()
    {
    }

    /**
     * En-tête, dans le cas ou l'attribut correspondant le permet
     * @see BaseModal::$hasHeader
     */
    protected function blockModalHeader()
    {
    }

    /**
     * Getter de l'id HTML de la modale
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Setter de l'attribut 'canDismiss'
     * @see BaseModal::$canDismiss
     * @param bool $dismissible
     */
    public function setDismissible(bool $dismissible)
    {
        $this->canDismiss = $dismissible;
    }
}