<?php

namespace Affluatif\Processing;

use Affluatif\BaseClass;
use Affluatif\BDDConnector;
use Affluatif\Services\Functions;
use Affluatif\Services\Securite;
use DateInterval;
use GuzzleHttp\Client;

/**
 * Class VideoProcessing
 *
 * @package Affluatif\Processing
 */
class VideoProcessing extends BaseClass
{
    /**
     * @var array Classes d'objet reconnaissables
     */
    static $classes = [
        'Personnes' => 1,
        'Vélos'     => 2,
        'Voitures'  => 3,
    ];

    public function addSource()
    {
        $this->services->getSecurite()->verificationAdmin();

        $post = Functions::cleanInput($_POST);

        if (
            isset($post['loc_lat']) && filter_var($post['loc_lat'], FILTER_VALIDATE_FLOAT) &&
            isset($post['loc_lon']) && filter_var($post['loc_lon'], FILTER_VALIDATE_FLOAT) &&
            isset($post['url']) && filter_var($post['url'], FILTER_VALIDATE_URL) &&
            isset($post['description'])
        ) {
            $this->bddRequest(
                'INSERT INTO flux_video (url, description, loc_lat, loc_lon) 
                VALUES (:url, :description, :loc_lat, :loc_lon)',
                [
                    'url'         => $post['url'],
                    'description' => $post['description'],
                    'loc_lat'     => $post['loc_lat'],
                    'loc_lon'     => $post['loc_lon'],
                ]
            );

            $fluxId = BDDConnector::lastInsertedId($this->bdd, 'flux_video');

            foreach (VideoProcessing::$classes as $code) {
                if (isset($post['classe_' . $code]) && $post['classe_' . $code]) {
                    $this->bddRequest(
                        'INSERT INTO classe_flux (flux_id, classe) VALUES (:flux_id, :classe)',
                        ['flux_id' => $fluxId, 'classe' => $code]
                    );

                    $this->services->getRabbitMQ()->newVideoSource(
                        [
                            'id'     => $fluxId,
                            'url'    => $post['url'],
                            'classe' => $code,
                        ]
                    );
                }
            }

            $this->services->getNotify()->setNotif('Source ajoutée avec succès', 'success', 5000);
        } else {
            $this->services->getNotify()->setNotif('Données entrée incorrectes', 'danger', 5000);
        }

        $this->redirectToReferer();
    }

    public function editSource()
    {
        $this->services->getSecurite()->verificationAdmin();

        $post = Functions::cleanInput($_POST);

        if (
            isset($post['loc_lat']) && filter_var($post['loc_lat'], FILTER_VALIDATE_FLOAT) &&
            isset($post['loc_lon']) && filter_var($post['loc_lon'], FILTER_VALIDATE_FLOAT) &&
            isset($post['url']) && filter_var($post['url'], FILTER_VALIDATE_URL) &&
            isset($post['description'])
        ) {
            $this->bddRequest(
                'UPDATE flux_video 
                SET url = :url, description = :description, loc_lat = :loc_lat, loc_lon = :loc_lon
                WHERE id = :id',
                [
                    'url'         => $post['url'],
                    'description' => $post['description'],
                    'loc_lat'     => $post['loc_lat'],
                    'loc_lon'     => $post['loc_lon'],
                    'id'          => Securite::validateInt($_GET['s']),
                ]
            );

            foreach (VideoProcessing::$classes as $code) {
                if (isset($post['classe_' . $code]) && $post['classe_' . $code]) {
                    if (!$this->bddRequest(
                        'SELECT id FROM classe_flux WHERE classe = :classe AND flux_id = :flux_id',
                        ['flux_id' => $_GET['s'], 'classe' => $code]
                    )->fetch()) { // Pas déjà existant
                        $this->bddRequest(
                            'INSERT INTO classe_flux (flux_id, classe) VALUES (:flux_id, :classe)',
                            ['flux_id' => $_GET['s'], 'classe' => $code]
                        );
                    }
                } else {
                    $this->bddRequest(
                        'DELETE FROM classe_flux WHERE classe = :classe AND flux_id = :flux_id',
                        ['flux_id' => $_GET['s'], 'classe' => $code]
                    );
                }
            }

            $this->services->getNotify()->setNotif('Source modifiée avec succès', 'success', 5000);
        } else {
            $this->services->getNotify()->setNotif('Données entrée incorrectes', 'danger', 5000);
        }

        $this->redirectToReferer();
    }

    public function deleteVideo()
    {
        $this->services->getSecurite()->verificationAdmin();

        $this->bddRequest('DELETE FROM flux_video WHERE id = :id', ['id' => Securite::validateInt($_GET['u'])]);

        $this->services->getNotify()->setNotif('Flux supprimé', 'success', 5000);

        $this->redirectToReferer();
    }

    public function setInactive()
    {
        $this->services->getSecurite()->verificationAdmin();

        $this->bddRequest('UPDATE flux_video SET actif = 0 WHERE id = :id', ['id' => Securite::validateInt($_GET['s'])]);

        $this->services->getNotify()->setNotif('Flux désactivé', 'success', 5000);

        $this->redirectToReferer();
    }

    public function setActive()
    {
        $this->services->getSecurite()->verificationAdmin();

        $this->bddRequest('UPDATE flux_video SET actif = 1 WHERE id = :id', ['id' => Securite::validateInt($_GET['s'])]);

        $this->services->getNotify()->setNotif('Flux activé', 'success', 5000);

        $this->redirectToReferer();
    }

    public function allowFlux()
    {
        $this->services->getSecurite()->verificationAdmin();

        if (!$this->bddRequest(
            'SELECT id FROM flux_utilisateur WHERE flux_id = :flux AND utilisateur_id = :user',
            ['flux' => Securite::validateInt($_POST['flux']), 'user' => Securite::validateInt($_GET['u'])]
        )->fetch()) {
            $this->bddRequest(
                'INSERT INTO flux_utilisateur (flux_id, utilisateur_id) VALUES (:flux, :user)',
                ['flux' => $_POST['flux'], 'user' => $_GET['u']]
            );
        }

        $this->services->getNotify()->setNotif('Autorisation accordée', 'success', 5000);

        $this->redirectToReferer();
    }

    public function disallowFlux()
    {
        $this->services->getSecurite()->verificationAdmin();

        $this->bddRequest(
            'DELETE FROM flux_utilisateur WHERE flux_id = :flux AND utilisateur_id = :user',
            ['flux' => Securite::validateInt($_GET['f']), 'user' => Securite::validateInt($_GET['u'])]
        );

        $this->services->getNotify()->setNotif('Autorisation supprimée', 'success', 5000);

        $this->redirectToReferer();
    }

    public function getPrediction()
    {
        $this->services->getSecurite()->verificationFlux(Securite::validateInt($_GET['f']));

        echo substr((new Client())->get(
            $this->services->getConfig()->getAnalizerEndpoint() . '?' . http_build_query(
                [
                    'fid'       => $_GET['f'],
                    'time_unit' => Securite::validateInt($_GET['tu']),
                    'delta'     => Securite::validateInt($_GET['d']),
                ]
            ),
            ['auth' => [
                $this->services->getConfig()->getAnalizerLogin(),
                $this->services->getConfig()->getAnalizerPassword(),
            ]]
        )->getBody(), 1, -2);
    }

    public function chartistData()
    {
        $this->services->getSecurite()->verificationFlux(Securite::validateInt($_GET['f']));

        $labels = [];
        $series = [[]];

        header('Content-Type: application/json');

        $classes = $this->bddRequest(
            'SELECT DISTINCT classe FROM classe_flux WHERE flux_id = :flux ORDER BY classe',
            ['flux' => $_GET['f']]
        );

        $i = 0;
        foreach ($classes as $classe) {
            switch (Functions::cleanInput($_GET['d'])) {
                case 'heure':
                    $labels     = Functions::last(12, 'minutes', 'H:i');
                    $series[$i] = [];
                    foreach (Functions::last(12, 'minutes', 'Y-m-d H:i:s') as $date) {
                        $date_max     = Functions::datify($date)->add(new DateInterval('PT5M'))->format('Y-m-d H:i:s');
                        $series[$i][] = (int)$this->bddRequest(
                            "SELECT MAX(nombre) 
                            FROM affluence_flux 
                            WHERE flux_id = :id 
                              AND date > '$date' 
                              AND date < '$date_max' 
                              AND type = :type",
                            ['id' => $_GET['f'], 'type' => $classe['classe']]
                        )->fetchColumn();
                    }
                    break;
                case 'jour':
                    $labels     = Functions::last(24, 'hours', 'H:i');
                    $series[$i] = [];
                    foreach (Functions::last(24, 'hours', 'Y-m-d H:i:s') as $date) {
                        $date_max     = Functions::datify($date)->add(new DateInterval('PT1H'))->format('Y-m-d H:i:s');
                        $series[$i][] = (int)$this->bddRequest(
                            "SELECT MAX(nombre) 
                            FROM affluence_flux 
                            WHERE flux_id = :id 
                              AND date > '$date' 
                              AND date < '$date_max'
                              AND type = :type",
                            ['id' => $_GET['f'], 'type' => $classe['classe']]
                        )->fetchColumn();
                    }
                    break;
                case 'semaine':
                    $labels     = Functions::last(7, 'days', 'd/m');
                    $series[$i] = [];
                    foreach (Functions::last(7, 'days', 'Y-m-d') as $date) {
                        $series[$i][] = (int)$this->bddRequest(
                            "SELECT MAX(nombre) 
                            FROM affluence_flux 
                            WHERE flux_id = :id 
                              AND date LIKE '$date%'
                              AND type = :type",
                            ['id' => $_GET['f'], 'type' => $classe['classe']]
                        )->fetchColumn();
                    }
                    break;
            }
            $i++;
        }

        echo json_encode(['labels' => $labels, 'series' => $series]);
    }
}