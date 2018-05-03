<?php

namespace Affluatif\View;

use Affluatif\Services\Functions;

/**
 * Class Alertes
 *
 * @package Affluatif\View
 */
class Alertes extends BaseTemplate
{
    private $alertes;

    public function __construct($bdd = null)
    {
        parent::__construct($bdd);

        $this->services->getSecurite()->verificationUser();

        $this->alertes = $this->bddRequest(
            'SELECT a.flux_id, f.description, a.heure_debut, a.heure_fin, a.derniere_alerte
            FROM alertes a, flux_video f
            WHERE a.flux_id IN (
              SELECT flux_id 
              FROM flux_utilisateur 
              WHERE utilisateur_id = :user
            ) AND f.id = a.flux_id 
              AND f.actif
            ORDER BY flux_id',
            ['user' => $_SESSION['id']]
        )->fetchAll();
    }

    protected function blockBanner()
    {
        ?>
        <h1 class="text-white black-glow">Mes alertes</h1>
        <?php
    }

    protected function blockSections()
    {
        ?>
        <div class="whole-wrap">
            <div class="container">

                <!-- VIDÉOS -->

                <div class="section-top-border">
                    <div class="progress-table-wrap">
                        <div class="progress-table">
                            <div class="table-head">
                                <div class="table_text table_text_30 pl-30">Flux</div>
                                <div class="table_text table_text_25">Début</div>
                                <div class="table_text table_text_25">Fin</div>
                                <div class="table_text table_text_20">Dernière infraction</div>
                            </div>
                            <?php
                            foreach ($this->alertes as $alerte) {
                                ?>
                                <div class="table-row">
                                    <div class="table_text table_text_30 pl-30">
                                        <a href="/video-<?php echo $alerte['flux_id']; ?>">
                                            <?php echo $alerte['description']; ?>
                                        </a>
                                    </div>
                                    <div class="table_text table_text_25"><?php echo $alerte['heure_debut']; ?></div>
                                    <div class="table_text table_text_25"><?php echo $alerte['heure_fin']; ?></div>
                                    <div class="table_text table_text_20">
                                        <?php
                                        if (!$alerte['derniere_alerte'])
                                            echo 'Aucune';
                                        else
                                            echo ucfirst(Functions::dateToFrench($alerte['derniere_alerte'], false));
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}