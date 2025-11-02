<?php
declare(strict_types=1);
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;
require_once 'vendor/autoload.php';

$configFile = 'db.config.ini';

session_start();

iutnc\deefy\repository\DeefyRepository::setConfig( 'db.config.ini' );

$action = (isset($_GET['action']))?$_GET['action']:'';

$app = new Dispatcher($action);
$app->run();
