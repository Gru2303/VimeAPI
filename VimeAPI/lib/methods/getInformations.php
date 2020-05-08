<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait getInformations {
    public function getInformations() {
        if ($this->classError) {
            return $this->classError;
        }

        if (!$this->changeServer()) {
            return false;
        }

        $res = $this->getInformationsRequest();

        if ($res->http_code[0] == 200) {
            $json = json_decode($res->content);
            $playtime = str_replace(array('<b>', '</b>'), '', $json->playtime);

            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'informations',
                'data' => $this->returnJson(array(
                    'donat' => $json->status,
                    'coins' => $json->coins,
                    'playtime' => $playtime,
                    'regtime' => $this->getRegTime()->data
                ))
            ));
        }


    
        return false;
    }

    private function changeServer() {
        $res = $this->fakeAjaxRequest('POST', 'https://cp.vimeworld.ru/ajax/change_server.php', 'https://cp.vimeworld.ru/', array(
            'newserver' => 'lobby'
        ), $this->cookies);

        sleep(2);

        return $res->http_code[0] == 200 ? true : false;
    }

    private function getInformationsRequest() {
        return $this->fakeAjaxRequest('POST', 'https://cp.vimeworld.ru/servers/lobby/ajax/update_main.php', 'https://cp.vimeworld.ru/', array(), $this->cookies);
    }

    private function getRegTime() {
        $page = $this->getPage('https://cp.vimeworld.ru/');

        if ($page->http_code[0] == 200) {
            preg_match_all('/.html\(([0-9]+) == \w/', $page->content, $regtime);

            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'get reg time',
                'data' => $regtime[1][0]
            ), true);
        }

        return null;
    }
}
?>