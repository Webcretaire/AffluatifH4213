<?php

namespace Affluatif\Services;

use Affluatif\BaseClass;
use Affluatif\Dev;
use GuzzleHttp\Client;

/**
 * Class InterpretersManager
 *
 * @package Affluatif\Services
 */
class InterpretersManager extends BaseClass
{
    /**
     * Remet les flux vidéos qui ne sont plus traités dans la queue
     */
    public function restartInactives()
    {
        echo 'Execution time : ' . date('j M Y H:i') . PHP_EOL . PHP_EOL;
        $inactives = $this->getInactives();
        foreach ($inactives as $source) {
            try {
                $code = (new Client(['timeout' => 5]))->head($source['url'])->getStatusCode();
                if ($code == 200) {
                    echo 'Restarted video ' . $source['id'] . PHP_EOL;
                    $this->services->getRabbitMQ()->newVideoSource($source);
                } else {
                    echo 'Video ' . $source['id'] . ' is dead (' . $code . ')' . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo 'Video ' . $source['id'] . ' is dead (' . $e->getMessage() . ')' . PHP_EOL;
            }
        }
    }

    /**
     * Détecte les flux vidéos dont le traitement a été arrêté
     * @return array
     */
    private function getInactives()
    {
        return $this->bddRequest(
            'SELECT DISTINCT f.id, f.url
            FROM flux_video f LEFT JOIN affluence_flux a on f.id = a.flux_id 
            WHERE f.actif AND NOT f.waiting_interpret = 1 AND (
              a.date IS NULL
              OR a.flux_id NOT IN (SELECT flux_id FROM affluence_flux WHERE date > (DATE_SUB(NOW(), INTERVAL 5 MINUTE)))
            )'
        )->fetchAll();
    }
}