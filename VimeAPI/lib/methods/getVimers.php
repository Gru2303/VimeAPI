<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait getVimers {
    public function getVimers() {
        if ($this->classError) {
            return $this->classError;
        }

        $page = $this->getPage('https://cp.vimeworld.ru/');

        if ($page->http_code[0] == 200) {
            $doc = new DOMDocument();
            $doc->loadHTML($page->content);
            $doc = $doc->getElementById('vimers');

            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'get vimers',
                'data' => $doc->nodeValue
            ));
        }

        return null;
    }
}
?>