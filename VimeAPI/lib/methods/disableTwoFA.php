<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait disableTwoFA {
    public function disableTwoFA($code = null) {
        if ($this->classError) {
            return $this->classError;
        }
        if (!$code || !is_numeric($code)) {
            return $this->returnInvalidParams();
        }

        $res = $this->disableTwoFARequest($code);

        switch (json_decode($res->content)->msg) {
            case 'Двухэтапная аутентификация отключена':
                return $this->returnJson(array(
                    'status' => 'success',
                    'code' => 1,
                    'type' => 'twoFA is disabled',
                    'data' => null
                ));
            case 'Двухэтапная аутентификация еще не подключена':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 2,
                    'type' => 'twoFA is disable',
                    'data' => null
                ));
            case 'Вы ввели неверный код':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 3,
                    'type' => 'entered the wrong code',
                    'data' => null
                ));
            case 'Некорректный запрос';
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 4,
                    'type' => 'invalid csrf_token',
                    'data' => null
                ));
            default:
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 0,
                    'type' => 'unknown error',
                    'data' => $res->content
                ));
        }

        return false;
    }

    private function disableTwoFARequest($code) {
        return $this->fakeAjaxRequest('POST', $this->securityAjaxUrl, 'https://cp.vimeworld.ru/security', array(
            'action' => 'totp-disable',
            'totp' => $code,
            'csrf_token' => $this->getCsrfToken($this->securityUrl)
        ), $this->cookies);
    }
}
?>