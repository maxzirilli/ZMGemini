<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 

      class TPreventivoMultiParametrico
      {
        public $Chiave = null;
        public $InfoPreventivo = null;
        public $ListaVoci = null;

        public function __construct($Chiave, $InfoPreventivo) 
        {
          $this->Chiave         = $Chiave;
          $this->InfoPreventivo = $InfoPreventivo;

          $this->ListaVoci         = array();
        }
      }
      
      class TTrasformatorePreventiviInPreventiviMultiparametro extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {

          $ListaPreventiviDaTrasformare = array();
          
          $SQLBody = "SELECT preventivi.*
                        FROM preventivi 
                       WHERE preventivi.STATO <> '" . STATO_PREVENTIVO_CONFERMATO . "'";

          $ListaChiavi = '';

          if($Query = $PDODBase->query($SQLBody))
          {
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
                $OggettoPreventivoMulti = new TPreventivoMultiParametrico($Row['CHIAVE'],
                                                                          $Row);

                array_push($ListaPreventiviDaTrasformare, $OggettoPreventivoMulti);

                $ListaChiavi .= $Row['CHIAVE'] . ',';
            }
          }  

          if($ListaChiavi != '')
          {
            $ListaChiavi = substr($ListaChiavi, 0, -1);
          
            $SQLBody = "SELECT voci_preventivi.*
                          FROM voci_preventivi 
                         WHERE voci_preventivi.ID_PREVENTIVO IN ($ListaChiavi)";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                for($i = 0; $i < count($ListaPreventiviDaTrasformare); $i++)
                  if($ListaPreventiviDaTrasformare[$i]->Chiave == $Row['ID_PREVENTIVO'])
                    array_push($ListaPreventiviDaTrasformare[$i]->ListaVoci, $Row);
              }
            }

            // error_log(json_encode($ListaPreventiviDaTrasformare));

            $this->FTrasformaInMultiparametrico($ListaPreventiviDaTrasformare, $PDODBase);

            $this->FCancellazioneVecchiPreventivi($ListaChiavi, $PDODBase);

          }
        }

        private function FCancellazioneVecchiPreventivi($ListaChiavi, $PDODBase)
        {
          $SQLBody = "DELETE from voci_preventivi
                       WHERE ID_PREVENTIVO IN ($ListaChiavi)";

          try
          {
            $PDODBase->query($SQLBody);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw new Exception($e->getMessage());         
          }

          $SQLBody = "DELETE from allegati_preventivi
                       WHERE ID_PREVENTIVO IN ($ListaChiavi)";

          try
          {
            $PDODBase->query($SQLBody);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw new Exception($e->getMessage());         
          }

          $SQLBody = "DELETE from log_preventivi
                       WHERE ID_PREVENTIVO IN ($ListaChiavi)";

          try
          {
            $PDODBase->query($SQLBody);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw new Exception($e->getMessage());         
          }

          try
          {
            $PDODBase->query($SQLBody);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw new Exception($e->getMessage());         
          }

          $SQLBody = "DELETE from preventivi
                       WHERE CHIAVE IN ($ListaChiavi)";

          try
          {
            $PDODBase->query($SQLBody);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw new Exception($e->getMessage());         
          }
        }

        private function FTrasformaInMultiparametrico($ListaPreventiviDaTrasformare, $PDODBase)
        {
          $i = 1;
          foreach($ListaPreventiviDaTrasformare as $Preventivo)
          {
            $SQLBody = "INSERT INTO preventivi_multiparametrici (CHIAVE, NUMERO, ID_CLIENTE,ANNO,
                                                                RAGIONE_SOCIALE, INDIRIZZO_FATTURAZIONE, NR_CIVICO_FATTURAZIONE,
                                                                COMUNE_FATTURAZIONE, PROVINCIA_FATTURAZIONE, CAP_FATTURAZIONE,
                                                                DATA, PARTITA_IVA, CODICE_FISCALE,
                                                                COMUNE_DESTINAZIONE, PROVINCIA_DESTINAZIONE, CAP_DESTINAZIONE, NR_CIVICO_DESTINAZIONE,
                                                                DESTINAZIONE, INDIRIZZO_DESTINAZIONE,
                                                                COND_PAGAMENTO, NOTE, RITENUTA, 
                                                                ESIGIBILITA_IVA, IBAN, CAUSALE, IS_INVIATO, ID_CONTO_CORRENTE, BANCA, BIC, SWIFT,
                                                                COD_ENTE_SDI, PEC, ENTE_PUBBLICO, COD_UFFICIO_DEST, CIG, CUP, CONVENZIONE, VOCE_DI_RIFERIMENTO,
                                                                DOCUMENTO_NR, DATA_DOCUMENTO, DOCUMENTO_CORRELATO, NUMERO_REVISIONE, NAZIONE_DESTINAZIONE, 
                                                                NAZIONE_EM_PIVA, PROGRAMMA_PROSSIMA_VISITA, ID_FILIALE,
                                                                RIFIUTATO)
                                                      VALUES ( " . $Preventivo->InfoPreventivo['CHIAVE'] . ", 
                                                              " . $Preventivo->InfoPreventivo['NUMERO'] . ", 
                                                              " . $Preventivo->InfoPreventivo['ID_CLIENTE'] . ", 
                                                              EXTRACT(YEAR FROM '" . $Preventivo->InfoPreventivo['DATA'] . "'),
                                                              '" . str_replace("\\","\\\\",str_replace("'","''",$Preventivo->InfoPreventivo['RAGIONE_SOCIALE'])) . "',
                                                              '" . str_replace("\\","\\\\",str_replace("'","''",$Preventivo->InfoPreventivo['INDIRIZZO_FATTURAZIONE'])) . "',
                                                              '" . $Preventivo->InfoPreventivo['NR_CIVICO_FATTURAZIONE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['COMUNE_FATTURAZIONE'] . "',
                                                              " . (isset($Preventivo->InfoPreventivo['PROVINCIA_FATTURAZIONE'])? $Preventivo->InfoPreventivo['PROVINCIA_FATTURAZIONE'] : 'NULL') . ",
                                                              '" . $Preventivo->InfoPreventivo['CAP_FATTURAZIONE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['DATA'] . "',
                                                              '" . $Preventivo->InfoPreventivo['PARTITA_IVA'] . "',
                                                              '" . $Preventivo->InfoPreventivo['CODICE_FISCALE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['COMUNE_DESTINAZIONE'] . "',
                                                              " . (isset($Preventivo->InfoPreventivo['PROVINCIA_DESTINAZIONE'])? $Preventivo->InfoPreventivo['PROVINCIA_DESTINAZIONE'] : 'NULL') . ",
                                                              '" . $Preventivo->InfoPreventivo['CAP_DESTINAZIONE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['NR_CIVICO_DESTINAZIONE'] . "',
                                                              '" . str_replace("\\","\\\\",str_replace("'","''",$Preventivo->InfoPreventivo['DESTINAZIONE'])) . "',
                                                              '" . str_replace("\\","\\\\",str_replace("'","''",$Preventivo->InfoPreventivo['INDIRIZZO_DESTINAZIONE'])) . "',
                                                              " . $Preventivo->InfoPreventivo['COND_PAGAMENTO'] . ",
                                                              '" . $Preventivo->InfoPreventivo['NOTE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['RITENUTA'] . "',
                                                              '" . $Preventivo->InfoPreventivo['ESIGIBILITA_IVA'] . "',
                                                              '" . $Preventivo->InfoPreventivo['IBAN'] . "',
                                                              '" . $Preventivo->InfoPreventivo['CAUSALE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['IS_INVIATO'] . "',
                                                              " . (isset($Preventivo->InfoPreventivo['ID_CONTO_CORRENTE'])? $Preventivo->InfoPreventivo['ID_CONTO_CORRENTE'] : 'NULL') . ",
                                                              '" . $Preventivo->InfoPreventivo['BANCA'] . "',
                                                              '" . $Preventivo->InfoPreventivo['BIC'] . "',
                                                              '" . $Preventivo->InfoPreventivo['SWIFT'] . "',
                                                              '" . $Preventivo->InfoPreventivo['COD_ENTE_SDI'] . "',
                                                              '" . $Preventivo->InfoPreventivo['PEC'] . "',
                                                              '" . $Preventivo->InfoPreventivo['ENTE_PUBBLICO'] . "',
                                                              '" . $Preventivo->InfoPreventivo['COD_UFFICIO_DEST'] . "',
                                                              '" . $Preventivo->InfoPreventivo['CIG'] . "',
                                                              '" . $Preventivo->InfoPreventivo['CUP'] . "',
                                                              '" . $Preventivo->InfoPreventivo['CONVENZIONE'] . "',
                                                              '" . $Preventivo->InfoPreventivo['VOCE_DI_RIFERIMENTO'] . "',
                                                              '" . $Preventivo->InfoPreventivo['DOCUMENTO_NR'] . "',
                                                              '" . $Preventivo->InfoPreventivo['DATA_DOCUMENTO'] . "',
                                                              '" . $Preventivo->InfoPreventivo['DOCUMENTO_CORRELATO'] . "',
                                                              '" . $Preventivo->InfoPreventivo['NUMERO_REVISIONE'] . "',
                                                              " . (isset($Preventivo->InfoPreventivo['NAZIONE_DESTINAZIONE'])? $Preventivo->InfoPreventivo['NAZIONE_DESTINAZIONE'] : 'NULL') . ",
                                                              " . (isset($Preventivo->InfoPreventivo['NAZIONE_EM_PIVA'])? $Preventivo->InfoPreventivo['NAZIONE_EM_PIVA'] : 'NULL') . ",
                                                              '" . $Preventivo->InfoPreventivo['PROGRAMMA_PROSSIMA_VISITA'] . "',
                                                              " . (isset($Preventivo->InfoPreventivo['ID_FILIALE'])? $Preventivo->InfoPreventivo['ID_FILIALE'] : 'NULL') . ",
                                                              '" . ($Preventivo->InfoPreventivo['STATO'] == STATO_PREVENTIVO_RIFIUTATO? 'T' : 'F') . "')";
            // error_log($SQLBody);
            try
            {
              $PDODBase->query($SQLBody);
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            if(count($Preventivo->ListaVoci) != 0)
            {
              $NuovaChiaveSezione = $this->FGetNewKey($PDODBase,GEN_CHIAVI);

              $SQLBody = "INSERT INTO preventivi_multi_sezione (CHIAVE, ID_PREVENTIVO_MULTI)
                                                        VALUES ($NuovaChiaveSezione," . $Preventivo->Chiave . ")";

              try
              {
                $PDODBase->query($SQLBody);
              }
              catch(Exception $e)
              {
                error_log($SQLBody);
                throw new Exception($e->getMessage());         
              }

              $NuovaChiaveSoluzione = $this->FGetNewKey($PDODBase,GEN_CHIAVI);

              $SQLBody = "INSERT INTO preventivi_multi_soluzioni (CHIAVE, ID_SEZIONE, ID_PREVENTIVO_MULTI)
                                                          VALUES ($NuovaChiaveSoluzione, $NuovaChiaveSezione," . $Preventivo->Chiave . ")";

              try
              {
                $PDODBase->query($SQLBody);
              }
              catch(Exception $e)
              {
                error_log($SQLBody);
                throw new Exception($e->getMessage());         
              }

              foreach($Preventivo->ListaVoci as $Voce)
              {
                $SQLBody = "INSERT INTO preventivi_multi_voci_soluzione (CHIAVE, ID_SOLUZIONE, DESCRIZIONE, ORDINAMENTO, ID_PREVENTIVO_MULTI,
                                                                         UNITA_DI_MISURA, IMPORTO, QUANTITA, IVA, SCONTO, CODICE_PRODOTTO, NATURA_PAGAMENTO)
                                                                 VALUES (" . $this->FGetNewKey($PDODBase,GEN_CHIAVI) . ",
                                                                         $NuovaChiaveSoluzione,
                                                                         '" . str_replace("\\","\\\\",str_replace("'","''",$Voce['DESCRIZIONE'])) . "',
                                                                         " . $Voce['ORDINAMENTO'] . ",
                                                                         " . $Voce['ID_PREVENTIVO'] . ",
                                                                         " . $Voce['UNITA_DI_MISURA'] . ",
                                                                         " . $Voce['IMPORTO'] . ",
                                                                         " . $Voce['QUANTITA'] . ",
                                                                         " . $Voce['IVA'] . ",
                                                                         " . $Voce['SCONTO'] . ",
                                                                         '" . $Voce['CODICE_PRODOTTO'] . "',
                                                                         " . (isset($Voce['NATURA_PAGAMENTO'])? $Voce['NATURA_PAGAMENTO'] : 'NULL') . ")";

                try
                {
                  $PDODBase->query($SQLBody);
                }
                catch(Exception $e)
                {
                  error_log($SQLBody);
                  throw new Exception($e->getMessage());         
                }
              }
            }

            $i++;
          }
        }
      }

      $Connection = new TTrasformatorePreventiviInPreventiviMultiparametro();
      $Connection->ServerSideScript();