<?php

namespace Affluatif\View\Modal;

use Affluatif\Services\Functions;
use Affluatif\Services\Securite;

/**
 * Class AlertesModale
 *
 * @package Affluatif\View\Modal
 */
class AlertesModale extends BaseModal
{
    protected $id = 'modale__alertes';

    protected $alertes;

    public function __construct(\PDO $bdd = null)
    {
        parent::__construct($bdd);

        $this->services->getSecurite()->verificationFlux(Securite::validateInt($_GET['f']));

        $this->alertes = $this->bddRequest(
            'SELECT id, heure_debut, heure_fin
            FROM alertes
            WHERE flux_id = :flux',
            ['flux' => $_GET['f']]
        )->fetchAll();
    }

    protected function blockModalHeader()
    {
        ?>
        <h2>Alertes pour ce flux</h2>
        <?php
    }

    protected function blockModalBody()
    {
        ?>
        <div class="text-left">
            <h4 class="mb-10">Alertes actuelles</h4>
            <?php
            if (empty($this->alertes)) {
                ?>
                <p>Aucune</p>
                <?php
            }
            foreach ($this->alertes as $alerte) {
                echo 'De ' . $alerte['heure_debut'] . ' à ' . $alerte['heure_fin'];
                ?>
                <a href="/p/suppression-alerte?a=<?php echo $alerte['id']; ?>"
                   class="text-danger ml-5">
                    <span class="fa fa-times"></span>
                </a>
                <br/>
                <?php
            }
            ?>
            <h4 class="mt-10">Ajouter une alerte</h4>
            <form action="/p/ajout-alerte?f=<?php echo $_GET['f']; ?>" method="post">
                <div class="input-group-icon mt-10">
                    <div class="input-group date" id="datetimepicker1">
                        <input name="heure_debut" id="heure_debut" type="text"
                               class="form-control" placeholder="Heure de début">
                        <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                        </span>
                    </div>
                </div>
                <div class="input-group-icon mt-10">
                    <div class="input-group date" id="datetimepicker2">
                        <input name="heure_fin" id="heure_fin" type="text"
                               class="form-control" placeholder="Heure de fin">
                        <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                        </span>
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