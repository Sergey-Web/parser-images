<?php

namespace App\Models;

class Check
{
    static protected $_data;

    static public function getData()
    {
        $row = 0;
        if (($handle = fopen(__DIR__ . "../../../file.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, "\n")) !== FALSE) {
                $num = count($data);
                $splitData = explode(';', $data[0]);
                $getData[$row] = $splitData;
                $row++;
            }
            fclose($handle);
        }
        self::$_data = $getData;
        return $getData;
    }

    static public function checkNameUrlFile($domain)
    {
        if (($handle = fopen("file.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, "\n")) !== FALSE) {
                $splitData = explode(';', $data[0]);
                if($splitData[0] === $domain) {
                    return $data[0];
                }
            }
            fclose($handle);
        }
    }

    static public function url($url)
    {
        $strImgs = '';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resUrl = curl_exec($ch);

        preg_match('/HTTP.+(301)/i', $resUrl, $matches);
        if(!empty($matches[1])) {
            preg_match('/Location:(.*?)\n/', $resUrl, $matches);
            $getUrl = $matches[1];
        } else {
            preg_match('/HTTP.+(200)/i', $resUrl, $matches);
            if(!empty($matches[1])) {
                $getUrl = $url;
            }
        }
        curl_close($ch);

        if(!$resUrl) {
           return FALSE;
        }

        return trim($getUrl);
    }
}