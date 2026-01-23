<?php
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZStringFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 

class GetZipDatiPersonali extends TAdvQuery
{
  protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
  { 
    $NomeCartellaDati = 'EsportazioneDati';
    $Parametri = json_decode($_POST['Params']);
    $JSONAnswer->FileZip = base64_encode(file_get_contents(PATH_SALVATAGGIO_DATI. $NomeCartellaDati. '/'. $Parametri->NomeFIle. '.zip'));
  }
}

$Result = new GetZipDatiPersonali(true);
$Result->ServerSideScript();