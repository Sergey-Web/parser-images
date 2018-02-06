<?php

namespace App\Models;

use App\Models\Check;

class Parser
{
    static protected $_url;
    static protected $_pattLink = '#<a href=["\']([\w\/_\-:\.]{3,})\/?["\']#i';
    static protected $_pattImg = '#<img.+src=["\'](([\w\/_\-\.\:]+)\.(jpg|jpeg|svg|gif|png))["\']#i';
    static protected $_pattDomain = '#(https:\/\/|http:\/\/(www\.)?|www\.|https:\/\/www\.)([\w\.]+)#i';
    static protected $_getLinks;
    static protected $_links;
    static protected $_check;
    static protected $_domain;

    public function __construct($url)
    {
        self::$_url = $url;
    }

    static protected function _addProtocol($links, $url) 
    {
        foreach($links as $keyLink => $valLink) {
            $checkHttp = substr($valLink, 0, 4);
            if($checkHttp === 'http' || $checkHttp === 'www.') {
                $links[$keyLink] = $valLink;
                continue;
            }
            $links[$keyLink] = $url . $valLink;
        }

        return array_values(array_unique($links));
    }

    static public function findUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $exec = curl_exec($ch);
        curl_close($ch);

        return $exec;
    }

    static public function _getImgLinks($links, $pattern, $check)
    {
        $domain = self::$_domain;
        $url = self::$_url;

        foreach($links as $keyLink => $link) {
            $exec = self::findUrl($link);
            preg_match_all($pattern, $exec, $matches);

            if(strpos($link, $domain) !== FALSE) {
                $getLinks[] = $matches[1];
            }
        }

        $arrayMerge = [];
        foreach($getLinks as $getLink) {
            $checkLink = self::_addProtocol($getLink, $url);
            $arrayMerge = array_merge($arrayMerge, $checkLink);
        }

        self::$_links[$check] = array_merge($arrayMerge);
    }

    public function getLink($check = 'links') 
    {
        $url = self::$_url;
        $exec = self::findUrl($url);
        self::$_domain = self::getDomain($url);

        switch($check) {
            case "images": 
                $pattern = self::$_pattImg;

                if(self::$_getLinks) {
                    $getLinks = self::_getImgLinks(self::$_getLinks, $pattern, $check);
                } else {
                    preg_match_all($pattern, $exec, $matches);
                    $getLinks = $matches[1];
                    self::$_links[$check] = self::_addProtocol($getLinks, $url);
                }

                break;
            case "links":
                $pattern = self::$_pattLink;
                 preg_match_all($pattern, $exec, $matches);
                 self::$_getLinks = $matches[1];
                break;
            default:
                return FALSE;
                break;
        }

        self::$_check = $check;
;
        return $this;
    }

    public function saveFile()
    {
        $links = self::$_links;
        $url = self::$_url;
        $domain = self::$_domain;

        if(!count($links) > 1) {
            return FALSE;
        }
        array_unshift($links[self::$_check], $domain . ';');

        if(Check::checkNameUrlFile($domain)) {
            self::_delOvelapFile($domain);
        }

        $fp = fopen('file.csv', 'a+');
        foreach($links as $key => $link) {
            fputcsv($fp, $link);
        }
        fclose($fp);

        return TRUE;
    }

    static protected function _delOvelapFile($domain)
    {
        if (($handle = fopen("file.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, "\n")) !== FALSE) {
                $splitData = explode(';', $data[0]);
                $splitLinks = explode(',', $splitData[1]);

                if($splitData[0] !== $domain) {
                    $row = 0;
                    foreach($splitLinks as $splitLink) {
                        if($splitLink) {
                            if($row == 0) {
                                $newData[$splitData[0]][] = $splitData[0] . ';';
                                $row++;
                            }
                            $newData[$splitData[0]][] = $splitLink;
                        }
                    }
                }
            }

            if($newData) {
                $fp = fopen('file.csv', 'w+');
                foreach($newData as $link) {
                    fputcsv($fp, $link);
                }
            }

            fclose($handle);
        }
    }

    static public function getDomain($url)
    {
        preg_match(self::$_pattDomain, $url, $matches);
        return $matches[3];
    }
}