<?php
error_reporting(E_ERROR | E_PARSE);
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

require_once('lib/VimeAPI-Include.php');

class VimeAPI extends VimeAPI_Include {  
    public $returnJson = true; // false - возвращает json текст, true - возвращает json

    public function __construct($phpToken = null, $devToken = null) {
        $this->phpToken = $phpToken;
        $this->devToken = $devToken;
        $this->cookies = 'PHPSESSID='.$this->phpToken.';';

        $this->result = $this->getPage($this->mainUrl);

        if (!$phpToken || !$devToken) {
            $this->classError = $this->returnJson(array(
                'status' => 'error',
                'type' => 'Bad Config'
            ));
        }

        if (!$this->isDevTokenValid()) {
            $this->classError = $this->returnJson(array(
                'status' => 'error',
                'type' => 'invalid devToken'
            ));
        }

        if (!$this->isPhpTokenValid()) {
            $this->classError = $this->returnJson(array(
                'status' => 'error',
                'type' => 'invalid phpToken'
            ));
        }

        $this->nickName = $this->getNickName();
    }

    public function getCsrfToken($url) {
        $page = $this->getPage($url);

        $doc = new DOMDocument();
        $doc->loadHTML($page->content);
        $doc = $doc->getElementsByTagName('input');

        foreach ($doc as $d) {
            if ($d->getAttribute('name') == 'csrf_token') {
                return $d->getAttribute('value'); 
            }
        } 

        return false;
    }

    public function getNickName() {
        $doc = new DOMDocument();
        $doc->loadHTML($this->result->content);
        $doc = $doc->getElementsByTagName('span');

        foreach ($doc as $d) {
            if ($d->getAttribute('class') == 'pull-left') {
                if (strripos($d->nodeValue, 'Вы зашли как ') !== false) {                  
                    return explode('Вы зашли как ', $d->nodeValue)[1];
                }
            }
        }   

        return false;
    }

    private function isPhpTokenValid() {
        $doc = new DOMDocument();
        $doc->loadHTML($this->result->content);
        $doc = $doc->getElementsByTagName('h2');

        foreach ($doc as $d) {
            if (strpos($d->nodeValue, 'Личный Кабинет') !== false) {
                return true;
            }
        }

        return false;
    }

    private function isDevTokenValid() {
        $devTokenInfo = $this->request('GET', 'https://api.vimeworld.ru/misc/token/'.$this->devToken, array())->content;

        return json_decode($devTokenInfo)->valid;
    }
}
?>