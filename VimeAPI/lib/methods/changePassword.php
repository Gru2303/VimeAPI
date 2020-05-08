<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait changePassword {
    public function changePassword($oldPass  = null, $newPass = null) {
        if ($this->classError) {
            return $this->classError;
        }
        if (!$oldPass || !$newPass) {
            return $this->returnInvalidParams();
        }

        $res = $this->changePassRequest($oldPass, $newPass);
        $board = $this->getBoardMessageWithHtml($res->content);

        switch ($board->message) {
            case 'Пароль успешно изменен':
                return $this->returnJson(array(
                    'status' => 'success',
                    'code' => 1,
                    'type' => 'password changed',
                    'data' => null
                )); 
            case 'Текущий пароль введен не верно':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 2,
                    'type' => 'invalid current password',
                    'data' => null
                ));
            case 'Введенные пароли не совпадают':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 3,
                    'type' => 'entered passwords do not occur',
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

    private function changePassRequest($oldPass, $newPass) {
        return $this->request('POST', $this->securityUrl, array(
            'csrf_token' => $this->getCsrfToken($this->securityUrl),
            'change_pass' => '',
            'password' => $oldPass,
            'new_password' => $newPass,
            'new_password_confirm' => $newPass
        ), $this->cookies);
    }
}
?>