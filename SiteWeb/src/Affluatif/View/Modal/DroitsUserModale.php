<?php

namespace Affluatif\View\Modal;

use Affluatif\Services\Securite;

/**
 * Class DroitsUserModale
 *
 * @package Affluatif\View\Modal
 */
class DroitsUserModale extends BaseModal
{
    protected $id = 'modale__rightsUser';

    private $flux;

    public function __construct(\PDO $bdd = null)
    {
        parent::__construct($bdd);

        $this->services->getSecurite()->verificationAdmin();

        $this->flux = $this->bddRequest(
            'SELECT v.id, v.description
                FROM flux_video v, flux_utilisateur u 
                WHERE v.id = u.flux_id 
                  AND u.utilisateur_id = :user',
            ['user' => Securite::validateInt($_GET['u'])]
        )->fetchAll();
    }

    protected function blockModalHeader()
    {
        ?>
        <h2>Flux de l'utilisateur</h2>
        <?php
    }

    protected function blockModalBody()
    {
        ?>
        <div class="text-left">
            <h4 class="mb-10">Autorisations actuelles</h4>
            <?php
            if(empty($this->flux)) {
                ?>
                <p>Aucune</p>
                <?php
            }
            foreach ($this->flux as $flux) {
                echo $flux['description'];
                ?>
                <a href="/p/suppression-droit?u=<?php echo $_GET['u']; ?>&f=<?php echo $flux['id']; ?>"
                   class="text-danger ml-5">
                    <span class="fa fa-times"></span>
                </a>
                <br/>
                <?php
            }
            ?>
            <h4 class="mt-10">Ajouter un flux</h4>
            <form action="/p/ajout-droit?u=<?php echo $_GET['u']; ?>" method="post">
                <div class="input-group-icon mt-10">
                    <div class="icon"><i class="fa fa-video-camera" aria-hidden="true"></i></div>
                    <div class="form-select" id="default-select">
                        <select name="flux">
                            <?php
                            $allFlux = $this->bddRequest('SELECT id, description FROM flux_video')->fetchAll();
                            foreach ($allFlux as $flux) {
                                if (!in_array($flux['id'], array_column($this->flux, 'id'))) {
                                    echo '<option value="' . $flux['id'] . '">' . $flux['description'] . '</option>';
                                }
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
        </div>
        <?php
    }
}