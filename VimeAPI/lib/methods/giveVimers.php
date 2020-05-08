<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait giveVimers {
    public function giveVimers($nick = null, $vimers = null) {
        if ($this->classError) {
            return $this->classError;
        }
        if (!$nick || !is_numeric($vimers)) {
            return $this->returnInvalidParams();
        }

        $res = $this->giveVimersRequest($nick, $vimers);
        $board = $this->getBoardMessageWithHtml($res->content);

        switch ($board->message) {
            case 'Вимеры успешно переданы':
                return $this->returnJson(array(
                    'status' => 'success',
                    'code' => 1,
                    'type' => 'transferred',
                    'data' => null
                )); 
            case 'У вас недостаточно вимеров на счету':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 2,
                    'type' => 'not enough on account',
                    'data' => null
                ));
            case 'Неверное количество вимеров':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 3,
                    'type' => 'invalid amount',
                    'data' => null
                ));
            case 'Вы не можете передать себе':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 5,
                    'type' => 'you cannot pass on to yourself',
                    'data' => null
                ));   
            case 'Такого пользователя не существует':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 6,
                    'type' => 'this user does not exist',
                    'data' => null
                ));
            case 'Произошла ошибка при выполнении запроса. Обновите страницу и попробуйте еще раз.':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 7,
                    'type' => 'invalid csrf_token',
                    'data' => null
                ));
            default:
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 0,
                    'type' => 'unknown error',
                    'data' => $board->message
                ));
        }
    }

    private function giveVimersRequest($nick, $vimers) {
        return $this->request('POST', $this->giveUrl, array(
            'csrf_token' => $this->getCsrfToken($this->giveUrl),
            'usrnm' => $nick,
            'amount' => $vimers,
            'process' => ''
        ), $this->cookies);
    }
}
?>