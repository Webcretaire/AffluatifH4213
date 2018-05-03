<?php

namespace Affluatif\View\Modal;

use Affluatif\Dev;
use Affluatif\Processing\VideoProcessing;
use Affluatif\Services\Securite;
use Affluatif\Traits\Autocomplete;

/**
 * Class AjoutSourceModale
 *
 * @package Affluatif\View\Modal
 */
class AjoutSourceModale extends BaseModal
{
    protected $id = 'modale__newSource';

    use Autocomplete;

    public function autocomplete()
    {
        $this->autocomplete = $this->bddRequest(
            'SELECT * FROM flux_video WHERE id = :id',
            ['id' => Securite::validateInt($_GET['s'])]
        )->fetch();

        $this->autocomplete['classes'] = [];

        $allClasses = $this->bddRequest(
            'SELECT classe FROM classe_flux WHERE flux_id = :flux', ['flux' => $_GET['s']]
        )->fetchAll();

        foreach ($allClasses as $class) {
            $this->autocomplete['classes'][] = $class['classe'];
        }

        $this->id = 'modale__editSource';
        $this->render();
    }

    protected function blockModalHeader()
    {
        if (!is_null($this->autocomplete)) {
            ?>
            <h2>Édition d'une source</h2>
            <?php
        } else {
            ?>
            <h2>Nouvelle source vidéo</h2>
            <?php
        }
    }

    protected function blockModalBody()
    {
        ?>
        <form action="<?php
        if (!is_null($this->autocomplete)) {
            echo '/p/edition-source?s=' . $_GET['s'];
        } else {
            echo '/p/ajout-source';
        }
        ?>" method="post">
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-link" aria-hidden="true"></i></div>
                <input type="url" name="url"
                       placeholder="URL"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'URL'"
                       required class="single-input"
                    <?php $this->complete('url'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-font" aria-hidden="true"></i></div>
                <input type="text" name="description"
                       placeholder="Description"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Description'"
                       required class="single-input"
                    <?php $this->complete('description'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-globe" aria-hidden="true"></i></div>
                <input type="text" name="loc_lat"
                       placeholder="Latitude"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Latitude'"
                       required class="single-input"
                    <?php $this->complete('loc_lat'); ?>>
            </div>
            <div class="input-group-icon mt-10">
                <div class="icon"><i class="fa fa-globe" aria-hidden="true"></i></div>
                <input type="text" name="loc_lon"
                       placeholder="Longitude"
                       onfocus="this.placeholder = ''"
                       onblur="this.placeholder = 'Longitude'"
                       required class="single-input"
                    <?php $this->complete('loc_lon'); ?>>
            </div>
            <h4 class="mt-10">Détecter :</h4>
            <div class="mt-10">
                <?php foreach (VideoProcessing::$classes as $text => $code) { ?>
                    <div class="switch-wrap d-flex justify-content-center">
                        <div class="primary-switch" style="margin-right: 20px;">
                            <input type="checkbox"
                                   name="classe_<?php echo $code; ?>"
                                   id="classe_<?php echo $code; ?>"
                                   value="1"
                                <?php
                                if (!is_null($this->autocomplete) && in_array($code, $this->autocomplete['classes'])) {
                                    echo 'checked';
                                }
                                ?>>
                            <label for="classe_<?php echo $code; ?>"></label>
                        </div>
                        <p><?php echo $text; ?></p>
                    </div>
                <?php } ?>
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