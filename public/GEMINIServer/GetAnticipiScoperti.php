<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TInfoFatturaAnticipo
      {
        public $Chiave   = null;
        public $Numero   = null;
        public $Data     = null;
        public $Importo  = 0;

        function __construct($Chiave, $Numero, $Data)
        {
          $this->Chiave = $Chiave;
          $this->Numero = $Numero;
          $this->Data   = $Data;
        }
      }

      class TAdvQueryAnticipiScoperti extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $ChiaveUtente           = json_decode($_POST['Params'])->ChiaveUtente;
          $JSONAnswer->LsAnticipi = array();

          // Query che torna tutti gli anticipi del cliente passato per parametro
          $SQLBody = "SELECT CHIAVE, NUMERO, DATA " . 
                     "FROM fatture " . 
                     "WHERE ANTICIPO = 'T' AND ID_CLIENTE = " . $ChiaveUtente;
          
          try
          {
            $Query = $PDODBase->query($SQLBody); 
            while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
              array_push($JSONAnswer->LsAnticipi, new TInfoFatturaAnticipo($Row['CHIAVE'], $Row['NUMERO'], $Row['DATA']));
            }
          }
          catch(Exception $e)
          {
            $JSONAnswer->Response = self::ERROR_SQL;
            $JSONAnswer->Error    = $e->getMessage();
            error_log($SQLBody);
          }

          foreach($JSONAnswer->LsAnticipi as $Anticipi)
          {
            $TotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase, $Anticipi->Chiave);
            $Anticipi->Importo = $TotaliFattura[0]->SommaImponibile;
          }

          /* PARTE NUOVA */
          $SQLBody = "SELECT SUM(voci_fatture.IMPORTO) AS IMPORTO,
                             voci_fatture.IVA,
                             voci_fatture.IMPORTO_IVATO,
                             voci_fatture.ID_FATTURA_ANTICIPO " .
                     "FROM voci_fatture " .
                     "WHERE voci_fatture.ID_FATTURA_ANTICIPO IN (SELECT CHIAVE FROM fatture WHERE ANTICIPO = 'T') " .
                     "GROUP BY voci_fatture.IMPORTO_IVATO,voci_fatture.ID_FATTURA_ANTICIPO,voci_fatture.IVA";
          
          try
          {
            $Query = $PDODBase->query($SQLBody);
            
            while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
              for($i = 0; $i < count($JSONAnswer->LsAnticipi); $i++)
                 if($JSONAnswer->LsAnticipi[$i]->Chiave == $Row["ID_FATTURA_ANTICIPO"])
                  {
                    $Importo = $Row['IMPORTO'] / 100;
                    if($Row['IMPORTO_IVATO'] == "T")
                       $Importo = GetImponibileFromIvato($Importo,$Row['IVA'] / 10);
                    $JSONAnswer->LsAnticipi[$i]->Importo += $Importo;
                    break;
                  }
            }
          }
          catch(Exception $e)
          {
            $JSONAnswer->Response = self::ERROR_SQL;
            $JSONAnswer->Error    = $e->getMessage();
            error_log($SQLBody);
          }
          $i = 0;
          while($i < count($JSONAnswer->LsAnticipi))
          {
            if(floor($JSONAnswer->LsAnticipi[$i]->Importo) <= 0)
               array_splice($JSONAnswer->LsAnticipi, $i, 1);
            else $i++;
          }
        }
      }

      $Connection = new TAdvQueryAnticipiScoperti();
      $Connection->ServerSideScript();
?>