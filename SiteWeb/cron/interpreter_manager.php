<?php
chdir(__DIR__);

date_default_timezone_set('Europe/Paris');

require_once '../vendor/autoload.php';

(new \Affluatif\Services\InterpretersManager())->restartInactives();