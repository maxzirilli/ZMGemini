<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS");
      
      class InserimentoNuoveRateNoteDiCredito extends TAdvQuery
      {
        protected function FExtraScriptIgnoreLogin()
        {
          return true;
        }
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $SQLBody = "SELECT CHIAVE,
                             DATA,
                             DATA_PAGAMENTO,
                             NOTE_PAGAMENTO,
                             ID_CONTO_PAGAMENTO,
                             ID_FATTURA,
                             NO_PRIMA_NOTA,
                             SCALATA_IN_FATTURA
                        FROM note_di_credito";

          $ListaNote = array();
          try
          {
            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                array_push($ListaNote, $Row);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw $e;
          }


          foreach ($ListaNote as $Nota) 
          {
            $TotaliNota = new stdClass();
            $TotaliNota = TSystemInformation::GetTotaliNota($PDODBase, $Nota['CHIAVE']);

            $SQLBody = "INSERT INTO rate_note (
                                                CHIAVE,
                                                ID_NOTA,
                                                DATA,
                                                DATA_PAGAMENTO,
                                                IMPORTO,
                                                ID_CONTO_CASSE,
                                                NOTE,
                                                SCALATA_IN_FATTURA,
                                                ID_FATTURA
                                              ) 
                                       VALUES ( GET_NEW_KEY(), " . 
                                                $Nota['CHIAVE'] . ", '" .
                                                $Nota['DATA'] . "', '" .
                                                (isset($Nota['DATA_PAGAMENTO']) && $Nota['NO_PRIMA_NOTA'] <> 'T'? $Nota['DATA_PAGAMENTO'] : "NULL") . "', " .
                                                $TotaliNota[0]->NettoAPagare * 100 . ", " .
                                                (isset($Nota['ID_CONTO_PAGAMENTO'])? $Nota['ID_CONTO_PAGAMENTO'] : "NULL") . ", '" .
                                                $Nota['NOTE_PAGAMENTO'] . "', " .
                                                ($Nota['NO_PRIMA_NOTA'] == 'T' || $Nota['SCALATA_IN_FATTURA'] == 'T'? "'T'" : "'F'") . ", " .
                                                (isset($Nota['ID_FATTURA'])? $Nota['ID_FATTURA'] : "NULL") . ")";

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
      }

      $Connection = new InserimentoNuoveRateNoteDiCredito();
      $Connection->ServerSideScript();