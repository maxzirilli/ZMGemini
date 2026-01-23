<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS");
      
      class TAdvQueryModificaRateFatturaXNotaDiCredito extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri     = JSON_decode($_POST['Params']);
          
          $ListaChiaviFatture = $Parametri->ListaChiaviFatture;
          $ChiaveNota = $Parametri->ChiaveNota;

          for($i = 0; $i < count($ListaChiaviFatture); $i++)
          {
            $ChiaveFattura = $ListaChiaviFatture[$i];
            $Importo = 0;

            $this->FControlloRateNoteCorrelate($PDODBase, $ChiaveFattura, $ChiaveNota, $Importo);

            $ArrTotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase, intval($ChiaveFattura));

            $SQLBody = "SELECT * 
                          FROM rate_fattura 
                        WHERE rate_fattura.ID_FATTURA = " . $ChiaveFattura;

            $TotaleRate = 0; 
            $RatePagate = 0;
            $ListaChiaviRateNonPagate = '';

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                if(isset($Row['DATA_PAGAMENTO']) || isset($Row['ID_MOVIMENTO']))
                  $RatePagate += $Row['IMPORTO'] / 100;
                else $ListaChiaviRateNonPagate .= $Row['CHIAVE'] . ',';
              }
            }

            if($TotaleRate != 0 && $TotaleRate == $RatePagate)
            {
              return;
            }
            else
            {
              if($ListaChiaviRateNonPagate != '')
              {
                $ListaChiaviRateNonPagate = rtrim($ListaChiaviRateNonPagate, ",");
                
                $SQLBody = "DELETE FROM rate_fattura 
                                  WHERE rate_fattura.CHIAVE IN ($ListaChiaviRateNonPagate)";
                
                if(!$PDODBase->query($SQLBody))
                  throw new Exception('Impossibile cancellare le rate non pagate');
              }

              if($ArrTotaliFattura[0]->NettoAPagare - $Importo - $RatePagate > 0.01)
              {
                $ImportoRata = ($ArrTotaliFattura[0]->NettoAPagare - $Importo - $RatePagate) * 100;
                $SQLBody = "INSERT INTO rate_fattura (CHIAVE, ID_FATTURA, DATA, IMPORTO, NOTE)
                            VALUES (" . $this->FGetNewKey($PDODBase,GEN_CHIAVI) . ", " . $ChiaveFattura . ",  NOW(), $ImportoRata, 'Rata generata dalla creazione della nota di credito')";
                
                if(!$PDODBase->query($SQLBody))
                  throw new Exception('Impossibile inserire la rata mancante');
              }

            }

            $Importo = 0;
          }
        }

        private function FControlloRateNoteCorrelate($PDODBase, $ChiaveFattura, $ChiaveNota, &$Importo)
        {
          if($ChiaveFattura != null && $ChiaveNota != null)
          {
            $SQLBody = "SELECT rate_note.IMPORTO / 100 AS IMPORTO

                          FROM rate_note

                          WHERE rate_note.ID_NOTA = " . $ChiaveNota . " AND rate_note.ID_FATTURA = " . $ChiaveFattura;
            
            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                $Importo += $Row['IMPORTO'];
          }
          else
            $Importo = 0;
          
        }
      }

      $Connection = new TAdvQueryModificaRateFatturaXNotaDiCredito();
      $Connection->ServerSideScript();