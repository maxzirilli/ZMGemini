<?php
     include_once 'Configurations.php';
     include_once 'Definitions.php';
     include_once PATH_LIBRERIE . 'ZAdvQuery.php';

     header("Content-Type: application/json;charset=UTF-8");
     header(ACCESS_CONTROLL_SHARED);
     header("Access-Control-Allow-Methods:POST, OPTIONS"); 

     const INTERVALLO_DI_TEMPO_PER_CHECK_IN_SECONDI = 60;

     class InvioManualeFatturaCheckTempoSenzaConferma extends TAdvQuery
     {
      protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
      {
       $Parametri = JSON_decode($_POST['Params']); 
       $DataLancioScript = date('Y-m-d H:i:s');

       $this->FCambioToStatoDaZipparePostCheck($PDODBase, $DataLancioScript, 'UpdateFattureResetDaZipparePostTimeCheck');
       $this->FCambioToStatoDaZipparePostCheck($PDODBase, $DataLancioScript, 'UpdateNoteDiCreditoResetDaZipparePostTimeCheck');
       $this->FCambioToStatoDaZipparePostCheck($PDODBase, $DataLancioScript, 'UpdateAutofattureResetDaZipparePostTimeCheck');
      }

      protected function FCambioToStatoDaZipparePostCheck($PDODBase, $DataLancioScript, $NomeQuery)
      {
       $Parametri = new stdClass();
       $Parametri->DataLancioScript = $DataLancioScript;
       $Parametri->IntervalloInSecondi = INTERVALLO_DI_TEMPO_PER_CHECK_IN_SECONDI;
       $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                           'EsportaFattureManualmente', 
                                           'EditSQL',
                                           $NomeQuery, 
                                           (array) $Parametri
                                          );

       try
       {           
         if(!$PDODBase->query($SQLBody))
         {
             error_log($SQLBody);
             throw new Exception('Impossibile resettare i documenti non confermati da troppo tempo');
         }
       }
       catch(Exception $e)
       {
         error_log($SQLBody);
         throw new Exception($e->getMessage());         
       }
      }
     }

     $Connection = new InvioManualeFatturaCheckTempoSenzaConferma();
     $Connection->ServerSideScript();