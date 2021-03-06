<?php

namespace Affluatif;

/**
 * Ensemble de fonctions utilisées uniquement dans un environnement de développement
 *
 * @package Affluatif
 */
class Dev
{
    /**
     * Affichage complet d'une variable, peu importe son type
     *
     * @param mixed $var        La variable à afficher
     * @param bool $usePrintR   Le type d'affichage à utiliser
     */
    public static function dump($var, $usePrintR = null)
    {
        self::dumpVar($var, $usePrintR);

        die();
    }

    /**
     * Affichage complet d'une variable
     *
     * @param mixed $var        La variable à afficher
     * @param bool $usePrintR   Le type d'affichage à utiliser
     */
    public static function dumpVar($var, $usePrintR = null)
    {
        ob_start();

        if (is_null($usePrintR)) {
            if (is_array($var)) {
                print_r($var);
            } else {
                var_dump($var);
            }
        } else {
            if ($usePrintR) {
                print_r($var);
            } else {
                var_dump($var);
            }
        }

        $dump = ob_get_contents();
        ob_end_clean();

        echo '<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?skin=sunburst"></script>';
        echo '<pre class="prettyprint">';

        if (is_array($var) || $usePrintR === true) {
            echo "<span class='nocode' style='color:white;'>";
        }
        echo $dump;
        if (is_array($var) || $usePrintR === true) {
            echo "</span>";
        }
        echo '</pre>';
    }
}