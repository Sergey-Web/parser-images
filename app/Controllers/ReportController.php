<?php

namespace App\Controllers;

use App\Models\Check;
use App\Models\Parser;
use App\Views\View;

class ReportController
{
    static protected $_url;

    public function __construct($url)
    {
        self::$_url = Check::url($url);
    }

    public function show()
    {
        $url = Parser::getDomain(self::$_url);
        $getLinks = Check::checkNameUrlFile($url);

        if($getLinks) {
            return View::report($getLinks);
        }
    }
}