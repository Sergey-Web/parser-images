<?php

namespace App\Controllers;

use App\Models\Check;
use App\Models\Parser;
use App\Views\View;

class ParseController
{
    static protected $_url;

    public function __construct($url)
    {
        self::$_url = Check::url($url);
    }

    public function getImages()
    {
        $parser = new Parser(self::$_url);

        $res = $parser->getLink('images')->saveFile();

        if(!$res) {
            return "ERROR";
        }

        return View::pathFile();
    }
}