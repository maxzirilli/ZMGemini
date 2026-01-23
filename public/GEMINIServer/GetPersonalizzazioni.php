<?php
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';

  header("Content-Type: application/x-httpd-php;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS");

  class TGetPersonalizzazione extends TAdvQuery
  {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
           $JSONAnswer->NoteInstallazione =  NOTE_INSTALLAZIONE;
           $NoteInstallazione = json_decode(NOTE_INSTALLAZIONE);
           if($NoteInstallazione->Cliente == "FROM_DBASE")
           {
              try
              {
                   $SQLBody = "SELECT * FROM delta";
                   if($Query = $PDODBase->query($SQLBody))
                   {
                     if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                        $JSONAnswer->NoteInstallazione =  Decifra($Row['TOKEN']);  
                   }
              }
              catch(Exception $e)
              {
                error_log($SQLBody);
                error_log($e->getMessage());
                throw new Exception($e->getMessage());         
              }
           }
        }
  }

    $GetPersonalizzazione = new TGetPersonalizzazione();
    $GetPersonalizzazione->ServerSideScript(true);
?>