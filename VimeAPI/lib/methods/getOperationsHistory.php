<?php
defined('Grusha-VimeAPI') or die('null');
define('Grusha-VimeAPI', true);

trait getOperationsHistory {
    public function getOperationsHistory($nick = null, $vimers = null) {
        if ($this->classError) {
            return $this->classError;
        }

        $j = 1;
        $last = array();
        $all = array();

        for ($i = 1; true ; $i++) {
            $data = $this->getOperationsHistoryTable($i);

            if (!$data) {
                break;
            }

            foreach($data as $d) 
            {
                if (!$d[1]) {
                    break;
                }

                if ($j <= 10) {
                    array_push($last, $this->returnJson(array(
                        'date' => $d[1],
                        'vimers' => $d[2],
                        'info' => $d[3]
                    ), false));
                }

                array_push($all, $this->returnJson(array(
                    'date' => $d[1],
                    'vimers' => $d[2],
                    'info' => $d[3]
                ), false));

                $j++;
            }
        }

        if ($last) {
            return $this->returnJson(array(
                'status' => 'success',
                'code' => 1,
                'type' => 'get operation history',
                'data' => $this->returnJson(array(
                        'last' => $last,
                        'all' => $all
                ))
            )); 
        } else {
            return $this->returnJson(array(
                'status' => 'error',
                'code' => 0,
                'type' => 'operation history is empty',
                'data' => null
            )); 
        }
    }

    private function getOperationsHistoryTable($page = 1) {
        $res = $this->getPage('https://cp.vimeworld.ru/real?paylog&page='.$page);

        $DOM = new DOMDocument();
        $DOM->loadHTML($res->content);
        
        $Header = $DOM->getElementsByTagName('th');
        $Detail = $DOM->getElementsByTagName('td');
    
        foreach($Header as $NodeHeader) 
        {
            $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
        }

        $i = 0;
        $j = 0;
        foreach($Detail as $sNodeDetail) 
        {
            $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
            $i = $i + 1;
            $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
        }
        
        return $aDataTableDetailHTML;
    }
}
?>