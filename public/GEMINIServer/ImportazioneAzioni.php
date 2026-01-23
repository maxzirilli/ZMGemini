<?php
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZDBaseFunct.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';

  header("Content-Type: application/x-httpd-php;charset=ISO-8859-15");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS");

  class TImportazioneAzioni extends TAdvQuery
  {
    private function FSalvaAzioniUtente($Azioni, $PDODBase)
    {
      $SQLBody = "INSERT INTO azioni_utenti_mobile (CHIAVE, ID_UTENTE, ID_DISPOSITIVO, NOME_AZIONE_COST, DATA_AZIONE, ORA_AZIONE, LATITUDINE, LONGITUDINE) VALUES ";

      $SQLValues = '';
      for ($i = 0; $i < count($Azioni); $i++) 
      { 
        if(isset($Azioni[$i]->NomeAzione) && strlen($Azioni[$i]->NomeAzione) == 1)
        {
          $SQLValues .= $this->FGetRowBodySQLAzioniUtente($Azioni[$i], $PDODBase);
          if ($i < count($Azioni) - 1) 
            $SQLValues .= ",";
        }
      }

      if($SQLValues != '')
      {
        $SQLBody = $SQLBody . $SQLValues;
        try
        {
          $PDODBase->query($SQLBody);
        }
        catch(Exception $e)
        {
          error_log($SQLBody);
          throw $e;
        }
      }
    }
    
    private function FGetRowBodySQLAzioniUtente($AzioneUtente, $PDODBase)
    {
      $IdUtente      = PrepareForRecordInteger($AzioneUtente->IdUtente);
      $IdDispositivo = PrepareForRecordInteger($AzioneUtente->IdDispositivo);
      $NomeAzione    = PrepareForRecordString($AzioneUtente->NomeAzione);
      $TimestampObj  = new DateTime($AzioneUtente->Timestamp);
      $DataAzione    = PrepareForRecordDate($TimestampObj->format('Y-m-d'));
      $OraAzione     = PrepareForRecordTime($TimestampObj->format('H:i:s'));
      $Latitudine    = PrepareForRecordInteger(round($AzioneUtente->coordinateGps->Latitude * 1000000));
      $Longitudine   = PrepareForRecordInteger(round($AzioneUtente->coordinateGps->Longitude * 1000000));

      $SQLBody = "(" . $this->FGetNewKey($PDODBase, GEN_CHIAVI) . "," . 
                            $IdUtente      . "," . 
                            $IdDispositivo . ",'" . 
                            $NomeAzione    . "','" . 
                            $DataAzione    . "','" . 
                            $OraAzione     . "'," . 
                            $Latitudine    . "," .
                            $Longitudine   . ")";

      return $SQLBody;
    }    

    protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
    {
      $Parametri = JSON_decode($_POST['Params']);

      $IdTecnico       = $Parametri->IdTecnico;
      $Azioni = $Parametri->Azioni;    
      $PDODBase->beginTransaction();
      
      try
      {
        $this->FSalvaAzioniUtente($Azioni, $PDODBase);
        $PDODBase->commit();
      }
      catch(Exception $Error)
      {        
        $this->FAssignJSONError($JSONAnswer, $Error->getMessage(), 'Errore nella procedura di invio azioni');
        $PDODBase->rollBack();
      }
      $JSONAnswer->Risposta = true;
    }
  }

  $GetJSONClienti = new TImportazioneAzioni();
  $GetJSONClienti->ServerSideScript();