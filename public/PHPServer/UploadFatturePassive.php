<?php
    include_once 'AcquisisciFattura.php';
    include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
        
    header("Content-Type: application/json;charset=UTF-8");
    header(ACCESS_CONTROLL_SHARED);
    header("Access-Control-Allow-Methods:POST,GET, OPTIONS"); 

    // echo('ciao');
    class AdvQueryUploadFatture extends TAdvQueryAcquisisciFatura
    {
        protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
            {   
                $LsFatturePassive = []; 
                $JSONAnswer->LsFatturePassive = [];
                $Error = '';
                $Fattura = json_decode($_POST['Params']);
                $this->FAcquisisciFatura($PDODBase, $JSONAnswer, $Fattura->Body, $Fattura->File);
            }
    }
    $Connection = new AdvQueryUploadFatture();
    $Connection->ServerSideScript();