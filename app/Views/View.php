<?php

namespace App\Views;

class View
{
    static public function report($data)
    {
        $links = explode(',', $data);

        foreach($links as $keyLink => $link) {
            if($links) {
                if($keyLink === 0) {
                    echo 'Domain: ' . $link . "\n";
                    continue;
                }
            }

            echo $keyLink . ". - $link \n";
        }
        return;
    }

    static public function helper()
    {
        echo "--> PARSER <-- \n";
        echo "Comands: \n";
        echo "  'parser url' - getting and saving links to pictures from this url,\n";
        echo "  'report url' - outputs to the console the analysis results for the domain, takes the required domain parameter (both with the protocol and without),\n";
        echo "  'help'       - displays a list of commands with explanations.\n";
        return;
    }

    static public function pathFile()
    {
        echo __DIR__ . DIRECTORY_SEPARATOR . "file.csv";
    }
}