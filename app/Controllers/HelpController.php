<?php

namespace App\Controllers;

use App\Views\View;

class HelpController
{
    public function show()
    {
        return View::helper();
    }
}