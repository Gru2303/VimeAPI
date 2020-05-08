<?php
defined('Grusha-VimeAPI') or die('null');
include_once("lib/GoogleAuth/GoogleAuthenticator.php");

class VimeAPI_Utils {
    public $phpToken = '';
    public $devToken = '';
    public $cookies = '';
    public $nickName = '';

    public $mainUrl = 'https://cp.vimeworld.ru/';
    public $giveUrl = 'https://cp.vimeworld.ru/real?give';
    public $paylogUrl = 'https://cp.vimeworld.ru/real?paylog';
    public $securityUrl= 'https://cp.vimeworld.ru/security';
    public $securityAjaxUrl = 'https://cp.vimeworld.ru/ajax/security.php';

    public $result = '';
    public $classError = null;

    public function getBoardMessageWithHtml($html) {
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $doc = $doc->getElementsByTagName('div');

        foreach ($doc as $d) {
            $message = substr($d->nodeValue, 4, -1);

            if ($d->getAttribute('class') == 'alert alert-danger alert-dismissable') {
                return json_decode(json_encode(array(
                    'type' => 'error',
                    'message' => $message
                )));
            } else if ($d->getAttribute('class') == 'alert alert-success alert-dismissable') {
                return json_decode(json_encode(array(
                    'type' => 'success',
                    'message' => $message
                )));
            }
        } 

        return false;
    }

    public function getPage($url) {
        return $this->request('GET', $url, array(), $this->cookies);
    }

    public function getOTPCode($secret) {
        $g = new GoogleAuthenticator();

        return $g->getCode($secret);
    }

    public function returnInvalidParams() {
        return $this->returnJson(array(
            'status' => 'error',
            'type' => 'invalid params'
        ));
    }

    public function returnJson($array, $bool = null) {
        if ($bool) {
            return ($bool) ? json_decode(json_encode($array)) : json_encode($array);
        }

        if (is_array($array)) {
            return ($this->returnJson) ? json_decode(json_encode($array)) : json_encode($array);
        }

        return null;
    }

    public function request($type, $url, $data = array(), $cookies = '') {
        $data = http_build_query($data);

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36';
        $header = array (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36'
        );


		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_NOBODY, true);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($c, CURLOPT_HTTPHEADER, $header);
        curl_setopt($c, CURLOPT_COOKIE, $cookies);
		curl_setopt($c, CURLOPT_COOKIESESSION, true);
		curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, strtoupper($type));
        $return = curl_exec($c);
        curl_close($c);

		return $this->getContent($return);
    }

    public function fakeAjaxRequest($type, $url, $refererUrl, $data = array(), $cookies = '') {
        $data = http_build_query($data);

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0';
        $header = array (
            'X-Requested-With: XMLHttpRequest',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length: '.strlen($data),
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0'
        );


		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_NOBODY, true);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($c, CURLOPT_HTTPHEADER, $header);
        curl_setopt($c, CURLOPT_COOKIE, $cookies);
		curl_setopt($c, CURLOPT_COOKIESESSION, true);
		curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_REFERER, $refererUrl);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, strtoupper($type));
        $return = curl_exec($c);
        curl_close($c);

		return $this->getContent($return);
    }

    private function getContent($return) {
		$result_explode = explode("\r\n\r\n", $return);
		$headers = ((isset($result_explode[0])) ? $result_explode[0]."\r\n" : '').''.((isset($result_explode[1])) ? $result_explode[1] : '');
		$content = $result_explode[count($result_explode) - 1];
        preg_match_all('|set-cookie: (.*);|U', $headers, $parse_cookies);
        preg_match_all('/(?<=HTTP\/2 )\d+/', $headers, $code);

        $cookies = implode(';', $parse_cookies[1]);
        return $this->returnJson(array(
            'http_code' => $code[0],
            'cookies' => $cookies,
            'content' => $content
        ), true);
    }
}
?>