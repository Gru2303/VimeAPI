<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait enableTwoFA {
    public function enableTwoFA($pass = null) {
        if ($this->classError) {
            return $this->classError;
        }
        if (!$pass) {
            return $this->returnInvalidParams();
        }

        $csrf = $this->getCsrfToken($this->securityUrl);
        $sudoEnter = $this->twoFASudoEnter($pass, $csrf);

        if ($sudoEnter->code != 1) {
            return $sudoEnter;
        }

        $totpSetup = $this->twoFATotpSetup($csrf);

        if ($totpSetup->code != 1) {
            return $totpSetup; 
        }

        if (!$this->twoFAWriteFile($totpSetup->data)) {
            return $this->returnJson(array(
                'status' => 'error',
                'code' => 0,
                'type' => 'failed create file',
                'data' => null
            ));  
        }

        return $this->twoFABuildReturn($totpSetup->data, $this->twoFATotpSetupConfirm($totpSetup->data->secret, $csrf));
    }

    private function twoFASudoEnter($pass, $csrf) {
        $res = $this->twoFASudoEnterRequest($pass, $csrf);

        switch (json_decode($res->content)->msg) {
            case null:
                if (json_decode($res->content)->state == 'success') {
                    return $this->returnJson(array(
                        'status' => 'success',
                        'code' => 1,
                        'type' => 'okey',
                        'data' => null
                    ), true);
                }
            case 'Двухэтапная аутентификация еще не подключена':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 2,
                    'type' => 'twoFA is disable',
                    'data' => null
                ));
            case 'Вы ввели неправильный пароль':
                return $this->returnJson(array(
                    'status' => 'error',
                    'code' => 3,
                    'type' => 'entered the wrong password',
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

     private function twoFASudoEnterRequest($pass, $csrf) {
        return $this->fakeAjaxRequest('POST', $this->securityAjaxUrl, 'https://cp.vimeworld.ru/security', array(
            'action' => 'sudo-enter',
            'password' => $pass,
            'csrf_token' => $csrf
        ), $this->cookies);
     }
 
     private function twoFATotpSetup($csrf) {
        $res = $this->twoFATotpSetupRequest($csrf);    
        $json = json_decode($res->content);

        $data = $this->returnJson(array(
            'username' => $json->username,
            'secret' => $json->secret,
            'recovery' => $json->recovery
        ), true);
 
         if ($json->state == 'success') {
            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'okey',
                'data' => $data
            ), true);
        } else if ($json->msg == 'Двухэтапная аутентификация уже активна') {
            return $this->returnJson(array(
                'status' => 'error',
                'code' => 2,
                'type' => 'twoFA is already active',
                'data' => null
            ));
        } else {
            return $this->returnJson(array(
                'status' => 'error',
                'code' => 0,
                'type' => '',
                'data' => $res->content
            ));  
        }
     }

     private function twoFATotpSetupRequest($csrf) {
         return $this->fakeAjaxRequest('POST', $this->securityAjaxUrl, 'https://cp.vimeworld.ru/security', array(
            'action' => 'totp-setup',
            'csrf_token' => $csrf
        ), $this->cookies);
     }
 
     private function twoFAWriteFile($json) {
         $dir = 'VimeSave';
 
         try {
             if (is_dir($dir) === false){
                mkdir($dir);
             }
 
             $file = fopen($dir. '/' . $json->username .'.txt', 'wb');
             $txt .= 'Secret: '. $json->secret ."\n\n\n";
             $txt .= "Recovery Codes:\n";
 
             foreach ($json->recovery as $code) {
                $txt .= $code."\n";
             }
 
             fwrite($file, $txt);
             fclose($file);
         } catch (Throwable $th) {
             return false;
         }
 
         return true;
     }

    private function twoFATotpSetupConfirm($secret, $csrf) {
        $code = $this->getOTPCode($secret);

        $res = $this->twoFATotpSetupConfirmRequest($secret, $csrf);
        $json = json_decode($res->content);

        return $json->state == 'success' ? true : false;
    }

    private function twoFATotpSetupConfirmRequest($secret, $csrf) {
        return $this->fakeAjaxRequest('POST', $this->securityAjaxUrl, 'https://cp.vimeworld.ru/security', array(
            'action' => 'totp-setup-confirm',
            'totp' => $code,
            'csrf_token' => $csrf
        ), $this->cookies);
    }

    private function twoFABuildReturn($json, $bool) {
        if ($bool) {
            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'twoFA enabled',
                'data' => $json
            ));
        } else {
            return $this->returnJson(array(
                'status' => 'error',
                'code' => 0,
                'type' => 'unknown error',
                'data' => null
            )); 
        }
    }  
}
?>