<?php

use App\Controllers as Controllers;
use App\Models as Models;

require_once __DIR__ . '/vendor/autoload.php';

switch($argv[0]) {
    case 'parser':
        $parseController = new Controllers\ParseController($argv[1]);
        $images = $parseController->getImages();
        break;
    case 'report':
        $reportContoreller = new Controllers\ReportController($argv[1]);
        $reportContoreller->show();
        break;
    case 'help':
        $helpContoroller = new Controllers\HelpController($argv[1]);
        $helpContoroller->show();
        break;
}