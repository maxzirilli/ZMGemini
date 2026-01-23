<?php
      include_once 'SystemInformation.php';
      class TOggettoMovimento
      {
        public $Descrizione          = null;
        public $Data                 = null;
        public $DataDocumento        = null;
        public $Codice               = null;
        public $Importo              = null;
        public $ContoCorrente        = null;
        public $IdTipologiaMovimento = null;
        public $DescrizioneTipologia = null;
        public $Note                 = null;
      }

      class TOggettoContoCorrente
      {
        public $Chiave          = null;
        public $Descrizione     = null;
        public $DescrizioneAbbr = null;
        public $NrConto         = null;
        public $IsContoCorrente = null;
        public $Saldo           = null; 
        public $TotaleTmp       = null;
      }
        
      class TAdvQueryCaricaInformazioniPrimaNota extends TAdvQuery
      {

            public $DallaData          = '';
            public $AllaData           = '';
            public $OrdineDecrescente  = false;
            public $IdContoCasse       = null;

            private function FGetObjContoCorrente($ListaContiCorrenti,$Chiave)
            {
               for($i = 0; $i < count($ListaContiCorrenti); $i++)
                   if($ListaContiCorrenti[$i]->Chiave == $Chiave)
                      return $ListaContiCorrenti[$i];
               return null;
            }

            private function FGetSQLListaMovimenti($DallaData,$AllaData)
            {
              return "SELECT movimenti.DESCRIZIONE AS DESCRIZIONE,
                              movimenti.DATA AS DATA, 
                              movimenti.CHIAVE, 
                              movimenti.IMPORTO * -1 AS IMPORTO, 
                              movimenti.CONTO_CORRENTE_PRELIEVO AS CONTO_CORRENTE,
                              movimenti.ID_CATEGORIA_MOVIMENTO,
                              cat_movimenti.DESCRIZIONE AS CATEGORIA_MOVIMENTO,
                              '' AS DATA_DOCUMENTO,
                              '' AS NOTE,
                              COALESCE(clienti.CODICE_CLIENTE, fornitori.CODICE_FORNITORE, '') AS CODICE,
                              COALESCE(clienti.RAGIONE_SOCIALE, fornitori.RAGIONE_SOCIALE) AS RAGIONE_SOCIALE_MOVIM_INFORMALE,
                              NULL as IS_NON_SCARICATA 
                         FROM movimenti
                              LEFT OUTER JOIN cat_movimenti ON cat_movimenti.CHIAVE   = movimenti.ID_CATEGORIA_MOVIMENTO
                              LEFT OUTER JOIN clienti 		  ON movimenti.ID_CLIENTE 	= clienti.CHIAVE
                              LEFT OUTER JOIN fornitori		  ON movimenti.ID_FORNITORE = fornitori.CHIAVE
                        WHERE movimenti.DATA >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                          AND movimenti.DATA <= " . $this->FPrepareParameterValue($AllaData,'#') . "        
                          AND movimenti.CONTO_CORRENTE_PRELIEVO IS NOT NULL
                          AND movimenti.NO_PRIMA_NOTA = 'F' 
                        UNION   
                       SELECT movimenti.DESCRIZIONE AS DESCRIZIONE,
                              movimenti.DATA AS DATA, 
                              movimenti.CHIAVE, 
                              movimenti.IMPORTO AS IMPORTO, 
                              movimenti.CONTO_CORRENTE_VERSAMENTO AS CONTO_CORRENTE,
                              movimenti.ID_CATEGORIA_MOVIMENTO,
                              cat_movimenti.DESCRIZIONE AS CATEGORIA_MOVIMENTO,
                              '' AS DATA_DOCUMENTO,
                              '' AS NOTE,
                              COALESCE(clienti.CODICE_CLIENTE, fornitori.CODICE_FORNITORE, '') AS CODICE,
                              COALESCE(clienti.RAGIONE_SOCIALE, fornitori.RAGIONE_SOCIALE) AS RAGIONE_SOCIALE_MOVIM_INFORMALE,
                              NULL as IS_NON_SCARICATA
                         FROM movimenti
                              LEFT OUTER JOIN cat_movimenti ON  cat_movimenti.CHIAVE  = movimenti.ID_CATEGORIA_MOVIMENTO
                              LEFT OUTER JOIN clienti 		  ON movimenti.ID_CLIENTE 	= clienti.CHIAVE
                              LEFT OUTER JOIN fornitori		  ON movimenti.ID_FORNITORE = fornitori.CHIAVE
                        WHERE movimenti.DATA >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                          AND movimenti.DATA <= " . $this->FPrepareParameterValue($AllaData,'#') . "        
                          AND movimenti.CONTO_CORRENTE_VERSAMENTO IS NOT NULL
                          AND movimenti.NO_PRIMA_NOTA = 'F'
                        UNION
                        SELECT CONCAT((IF(fatture.NUMERO IS NOT NULL,
                                        CONCAT('Fatt. nr. ',fatture.NUMERO,' - ',fatture.ANNO, ' - '),
                                        CONCAT('Avviso di fattura nr. ',fatture.CHIAVE, ' - '))),
                                      fatture.RAGIONE_SOCIALE) AS DESCRIZIONE,
                                rate_fattura.DATA_PAGAMENTO AS DATA, 
                                rate_fattura.CHIAVE,
                                rate_fattura.IMPORTO AS IMPORTO,
                                rate_fattura.ID_CONTO_CASSE AS CONTO_CORRENTE,
                                '' AS ID_CATEGORIA_MOVIMENTO,
                                '' AS CATEGORIA_MOVIMENTO,
                                fatture.DATA AS DATA_DOCUMENTO,
                                rate_fattura.NOTE,
                                clienti.CODICE_CLIENTE AS CODICE,
                                NULL as RAGIONE_SOCIALE_MOVIM_INFORMALE,
                                rate_fattura.IS_NON_SCARICATA
                           FROM rate_fattura,fatture,clienti
                          WHERE fatture.CHIAVE = rate_fattura.ID_FATTURA 
                            AND fatture.NUMERO IS NOT NULL
                            AND clienti.CHIAVE = fatture.ID_CLIENTE
                            AND rate_fattura.DATA_PAGAMENTO IS NOT NULL
                            AND rate_fattura.DATA_PAGAMENTO >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                            AND rate_fattura.DATA_PAGAMENTO <= " . $this->FPrepareParameterValue($AllaData,'#') . "        
                            AND rate_fattura.ID_CONTO_CASSE IS NOT NULL
                            AND rate_fattura.NO_PRIMA_NOTA = 'F'
                        UNION
                         SELECT CONCAT ( IF(fatture_passive.IS_FATTURA = 'F', 'Nota Cred. ', ''),
                                  fatture_passive.NUMERO,' - ', fatture_passive.RAGIONE_SOCIALE
                                ) AS DESCRIZIONE,
                                rate_fatture_passive.DATA_PAGAMENTO AS DATA,
                                rate_fatture_passive.CHIAVE,
                                rate_fatture_passive.IMPORTO / 10 * IF(fatture_passive.IS_FATTURA = 'T',-1,1) AS IMPORTO,
                                rate_fatture_passive.ID_CONTO_CASSA AS CONTO_CORRENTE,
                                '' AS ID_CATEGORIA_MOVIMENTO,
                                '' AS CATEGORIA_MOVIMENTO,
                                fatture_passive.DATA AS DATA_DOCUMENTO,
                                rate_fatture_passive.NOTE,
                                fornitori.CODICE_FORNITORE AS CODICE,
                                NULL AS RAGIONE_SOCIALE_MOVIM_INFORMALE,
                                NULL as IS_NON_SCARICATA
                           FROM rate_fatture_passive,fatture_passive,fornitori
                          WHERE rate_fatture_passive.ID_FATTURA_PASSIVA = fatture_passive.CHIAVE
                            AND fornitori.CHIAVE = fatture_passive.ID_FORNITORE
                            AND rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL
                            AND rate_fatture_passive.NO_PRIMA_NOTA = 'F'
                            AND rate_fatture_passive.DATA_PAGAMENTO >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                            AND rate_fatture_passive.DATA_PAGAMENTO <= " . $this->FPrepareParameterValue($AllaData,'#') . "        
                            AND rate_fatture_passive.ID_CONTO_CASSA IS NOT NULL
                        UNION
                         SELECT CONCAT('Fatt. pregressa ', fatture_insolute_pregresse.NUMERO, ' - ', clienti.RAGIONE_SOCIALE) AS DESCRIZIONE,
                                fatture_insolute_pregresse.DATA_PAGAMENTO AS DATA,
                                fatture_insolute_pregresse.CHIAVE,
                                fatture_insolute_pregresse.IMPORTO,
                                fatture_insolute_pregresse.ID_CONTO_PAGAMENTO AS CONTO_CORRENTE,
                                '' AS ID_CATEGORIA_MOVIMENTO,
                                '' AS CATEGORIA_MOVIMENTO,
                                fatture_insolute_pregresse.DATA AS DATA_DOCUMENTO,
                                fatture_insolute_pregresse.NOTE,
                                clienti.CODICE_CLIENTE AS CODICE,
                                NULL AS RAGIONE_SOCIALE_MOVIM_INFORMALE,
                                NULL as IS_NON_SCARICATA
                           FROM fatture_insolute_pregresse, clienti
                          WHERE clienti.CHIAVE = fatture_insolute_pregresse.ID_CLIENTE
                            AND fatture_insolute_pregresse.DATA_PAGAMENTO IS NOT NULL
                            AND fatture_insolute_pregresse.NO_PRIMA_NOTA = 'F'
                            AND fatture_insolute_pregresse.DATA_PAGAMENTO >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                            AND fatture_insolute_pregresse.DATA_PAGAMENTO <= " . $this->FPrepareParameterValue($AllaData,'#') . "
                        UNION
                         SELECT CONCAT('Fatt. pregr. fornitore ', fatture_insolute_pregresse_fornitori.NUMERO, ' - ', fornitori.RAGIONE_SOCIALE) AS DESCRIZIONE,
                                fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO AS DATA,
                                fatture_insolute_pregresse_fornitori.CHIAVE,
                                fatture_insolute_pregresse_fornitori.IMPORTO * -1 AS IMPORTO,
                                fatture_insolute_pregresse_fornitori.ID_CONTO_PAGAMENTO AS CONTO_CORRENTE,
                                '' AS ID_CATEGORIA_MOVIMENTO,
                                '' AS CATEGORIA_MOVIMENTO,
                                fatture_insolute_pregresse_fornitori.DATA AS DATA_DOCUMENTO,
                                fatture_insolute_pregresse_fornitori.NOTE,
                                fornitori.CODICE_FORNITORE AS CODICE,
                                NULL AS RAGIONE_SOCIALE_MOVIM_INFORMALE,
                                NULL as IS_NON_SCARICATA
                           FROM fatture_insolute_pregresse_fornitori, fornitori
                          WHERE fornitori.CHIAVE = fatture_insolute_pregresse_fornitori.ID_FORNITORE
                            AND fatture_insolute_pregresse_fornitori.NO_PRIMA_NOTA = 'F'
                            AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO IS NOT NULL
                            AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= " . $this->FPrepareParameterValue($DallaData,'#') . "        
                            AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= " . $this->FPrepareParameterValue($AllaData,'#');
            }

            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri               = JSON_decode($_POST['Params']); 
              $this->DallaData         = $Parametri->Dal;
              $this->AllaData          = $Parametri->Al;
              $this->OrdineDecrescente = $Parametri->OrdineDecrescente;
              $this->IdContoCasse      = $Parametri->IdContoCasse;
              $ListaContiCorrenti      = array();

              $SQLBody = "SELECT CHIAVE, 
                                 SALDO_AD_INIZIO_SESSIONE AS SALDO, 
                                 DESCRIZIONE,
                                 DESCRIZIONE_ABBR,
                                 CONTO_CORRENTE, 
                                 NR_CONTO
                            FROM conti_correnti_casse "
                          . (isset($this->IdContoCasse)? "WHERE CHIAVE = " . $this->IdContoCasse : '') . " 
                           ORDER BY CONTO_CORRENTE DESC";

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                    $ObjContoCorrente                  = new TOggettoContoCorrente();
                    $ObjContoCorrente->Chiave          = $Row['CHIAVE'];
                    $ObjContoCorrente->Descrizione     =  $Row['DESCRIZIONE'];
                    $ObjContoCorrente->DescrizioneAbbr = $Row['DESCRIZIONE_ABBR'] != '' ? $Row['DESCRIZIONE_ABBR'] : $Row['DESCRIZIONE'];
                    $ObjContoCorrente->NrConto         = $Row['NR_CONTO'];
                    $ObjContoCorrente->IsContoCorrente = $Row['CONTO_CORRENTE'];
                    $ObjContoCorrente->Saldo           = $Row['SALDO']; 
                    $ObjContoCorrente->TotaleTmp       = 0;
                    array_push($ListaContiCorrenti,$ObjContoCorrente);
                }
              }       
              else throw new Exception('Impossibile trovare i conti correnti/casse');

              //MOVIMENTI PRIMA DELLA DATA
              $SQLBody = "SELECT SUM(movimenti.IMPORTO) AS MOVIMENTO, 
                                 movimenti.CONTO_CORRENTE_PRELIEVO, 
                                 movimenti.CONTO_CORRENTE_VERSAMENTO
                            FROM movimenti
                           WHERE movimenti.`DATA` < " . $this->FPrepareParameterValue($this->DallaData,'#') . "
                             AND movimenti.NO_PRIMA_NOTA = 'F'
                        GROUP BY movimenti.CONTO_CORRENTE_PRELIEVO, movimenti.CONTO_CORRENTE_VERSAMENTO";

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  if($Row["CONTO_CORRENTE_PRELIEVO"] != null)
                  {
                    $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti, $Row['CONTO_CORRENTE_PRELIEVO']);
                    if($ObjContoCorrente != null)
                      $ObjContoCorrente->Saldo -= $Row["MOVIMENTO"];
                  }
                  if($Row["CONTO_CORRENTE_VERSAMENTO"] != null)
                  {
                    $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti, $Row['CONTO_CORRENTE_VERSAMENTO']);
                      if($ObjContoCorrente != null)
                        $ObjContoCorrente->Saldo += $Row["MOVIMENTO"];
                  }
                }
              }

              //FATTURE PRIMA DELLA DATA
              $SQLBody = "SELECT rate_fattura.ID_FATTURA, 
                                 rate_fattura.CHIAVE, 
                                 rate_fattura.DATA_PAGAMENTO, 
                                 rate_fattura.IMPORTO, 
                                 rate_fattura.ID_CONTO_CASSE,
                                 rate_fattura.IS_NON_SCARICATA
                            FROM rate_fattura
                                 JOIN fatture ON fatture.CHIAVE = rate_fattura.ID_FATTURA
                           WHERE rate_fattura.DATA_PAGAMENTO IS NOT NULL
                             AND fatture.NUMERO IS NOT NULL
                             AND rate_fattura.DATA_PAGAMENTO IS NOT NULL
                             AND rate_fattura.ID_CONTO_CASSE IS NOT NULL
                             AND rate_fattura.NO_PRIMA_NOTA = 'F'
                             AND rate_fattura.DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti, $Row['ID_CONTO_CASSE']);
                  if($ObjContoCorrente != null)
                  {
                    if(isset($Row['IS_NON_SCARICATA']) && $Row['IS_NON_SCARICATA'] == 'T')
                      $ObjContoCorrente->Saldo -= $Row["IMPORTO"];
                    else $ObjContoCorrente->Saldo += $Row["IMPORTO"];
                  }
                    
                }
              }

              //NOTE PRIMA DELLA DATA

              $SQLBody = "SELECT rate_note.ID_CONTO_CASSE,
			                           rate_note.IMPORTO AS IMPORTO,
                                 rate_note.ID_NOTA 
                            FROM rate_note
                           WHERE rate_note.DATA_PAGAMENTO IS NOT NULL
	                           AND rate_note.SCALATA_IN_FATTURA = 'F'
	                           AND rate_note.DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');
              
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  //$TotaliNota = TSystemInformation::GetTotaliNota($PDODBase, intval($Row['CHIAVE']));
                  $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti, $Row['ID_CONTO_CASSE']);
                  if($ObjContoCorrente != null)
                    $ObjContoCorrente->Saldo -= $Row['IMPORTO'];
                }
              }
              

              // $SQLBody = "SELECT CHIAVE
              //                    ID_CONTO_PAGAMENTO
              //               FROM note_di_credito
              //              WHERE DATA_PAGAMENTO IS NOT NULL
              //                AND note_di_credito.NO_PRIMA_NOTA   = 'F'
              //                AND DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');

              // if($Query = $PDODBase->query($SQLBody))
              // {
              //   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              //   {
              //     $TotaliNota = TSystemInformation::GetTotaliNota($PDODBase, intval($Row['CHIAVE']));

              //     $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti, $Row['ID_CONTO_PAGAMENTO']);
              //     if($ObjContoCorrente != null)
              //       $ObjContoCorrente->Saldo -= ($TotaliNota[0]->Totale - $TotaliNota[0]->TotaleRitenuta) * 100;
              //   }
              // }

              //FATTURE PREGRESSE PRIMA DELLA DATA
              $SQLBody = "SELECT IMPORTO, 
                                 ID_CONTO_PAGAMENTO
                            FROM fatture_insolute_pregresse
                           WHERE DATA_PAGAMENTO IS NOT NULL
                             AND fatture_insolute_pregresse.NO_PRIMA_NOTA = 'F'
                             AND DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti,$Row['ID_CONTO_PAGAMENTO']);
                  if($ObjContoCorrente != null)
                    $ObjContoCorrente->Saldo += $Row["IMPORTO"];
                }
              }

              //FATTURE PREGRESSE FORNITORI PRIMA DELLA DATA
              $SQLBody = "SELECT IMPORTO, 
                                 ID_CONTO_PAGAMENTO
                            FROM fatture_insolute_pregresse_fornitori
                           WHERE DATA_PAGAMENTO IS NOT NULL
                             AND fatture_insolute_pregresse_fornitori.NO_PRIMA_NOTA = 'F'
                             AND DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti,$Row['ID_CONTO_PAGAMENTO']);
                  if($ObjContoCorrente != null)
                    $ObjContoCorrente->Saldo -= $Row["IMPORTO"];
                }
              }

              //FATTURE PASSIVE PRIMA DELLA DATA
              $SQLBody = "SELECT rate_fatture_passive.DATA_PAGAMENTO, 
                                 rate_fatture_passive.ID_CONTO_CASSA, 
                                 rate_fatture_passive.IMPORTO / 10 AS IMPORTO, 
                                 fatture_passive.IS_FATTURA, 
                                 rate_fatture_passive.CHIAVE
                            FROM rate_fatture_passive, fatture_passive
                           WHERE DATA_PAGAMENTO IS NOT NULL
                             AND rate_fatture_passive.NO_PRIMA_NOTA = 'F'
                             AND rate_fatture_passive.ID_FATTURA_PASSIVA = fatture_passive.CHIAVE
                             AND DATA_PAGAMENTO < " . $this->FPrepareParameterValue($this->DallaData,'#');
              
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  if($Row["IS_FATTURA"] == 'T')
                  {
                    $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti,$Row['ID_CONTO_CASSA']);
                    if($ObjContoCorrente != null)
                      $ObjContoCorrente->Saldo -= $Row["IMPORTO"];
                  }
                  else
                  {
                    $ObjContoCorrente = $this->FGetObjContoCorrente($ListaContiCorrenti,$Row['ID_CONTO_CASSA']);
                      if($ObjContoCorrente != null)
                        $ObjContoCorrente->Saldo += $Row["IMPORTO"];
                  }
                }
              }

              // lista movimenti dalla data alla data
              $SQLBody = $this->FGetSQLListaMovimenti($this->DallaData, $this->AllaData);

              $ListaMovimenti = array();

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  $ObjMovimento = new TOggettoMovimento();
                  $ObjMovimento->Descrizione = $Row['DESCRIZIONE']/* . ($Row['NOTE'] != ''? " \n" . $Row['NOTE'] : '')*/;

                  if(isset($Row['RAGIONE_SOCIALE_MOVIM_INFORMALE']))
                    $ObjMovimento->Descrizione = $Row['RAGIONE_SOCIALE_MOVIM_INFORMALE'] . ' \n' . $ObjMovimento->Descrizione;

                  $ObjMovimento->Note                 = $Row['NOTE'];
                  $ObjMovimento->Data                 = $Row['DATA'];
                  $ObjMovimento->DataDocumento        = $Row['DATA_DOCUMENTO'];
                  $ObjMovimento->Codice               = $Row['CODICE'];
                  if(isset($Row['IS_NON_SCARICATA']) && $Row['IS_NON_SCARICATA'] == 'T')
                    $ObjMovimento->Importo            = $Row['IMPORTO'] * -1;
                  else $ObjMovimento->Importo         = $Row['IMPORTO'];
                  $ObjMovimento->ContoCorrente        = $Row['CONTO_CORRENTE'];
                  $ObjMovimento->DescrizioneTipologia = $Row['CATEGORIA_MOVIMENTO'];
                  $ObjMovimento->IdTipologiaMovimento = $Row['ID_CATEGORIA_MOVIMENTO'];

                  array_push($ListaMovimenti,$ObjMovimento);
                }
              }

              $this->FInserisciNoteDiCreditoNellaNota($this->DallaData, $this->AllaData, $PDODBase, $ListaMovimenti);

              for($j = 0; $j < count($ListaContiCorrenti); $j++)
              {
                $ListaContiCorrenti[$j]->TotaleTmp = intval($ListaContiCorrenti[$j]->Saldo);
                for($i = 0; $i < count($ListaMovimenti); $i++)
                  if( $ListaMovimenti[$i]->ContoCorrente == $ListaContiCorrenti[$j]->Chiave)
                    $ListaContiCorrenti[$j]->TotaleTmp += intval($ListaMovimenti[$i]->Importo);
              }

              $JSONAnswer->LsContiCorrenti = $ListaContiCorrenti;

              if(isset($this->IdContoCasse))
                $this->FGestisciMovimentiFiltroContoCasse($this->IdContoCasse, $ListaMovimenti);

              $JSONAnswer->LsMovimenti = $ListaMovimenti;
            }

            private function FGestisciMovimentiFiltroContoCasse($IdContoCassa, &$ListaMovimenti)
            {
              for($i = 0; $i < count($ListaMovimenti); $i++)
              {
                if($ListaMovimenti[$i]->ContoCorrente != $IdContoCassa)
                {
                  array_splice($ListaMovimenti, $i, 1);
                  $i--;
                }
              }
            }

            private function FInserisciNoteDiCreditoNellaNota($DallaData, $AllaData, $PDODBase, &$ListaMovimenti)
            {

              $SQLBody = "SELECT CONCAT((IF(note_di_credito.NUMERO IS NOT NULL,
                                 CONCAT('Nota di credito. nr. ',note_di_credito.NUMERO,' - ',note_di_credito.ANNO, ' - '),
                                 CONCAT('Avviso nota nr. ', note_di_credito.CHIAVE, ' - '))),
                                 clienti.RAGIONE_SOCIALE) AS DESCRIZIONE,
                                 rate_note.DATA_PAGAMENTO AS DATA_PAGAMENTO_RATA,
                                 rate_note.DATA AS DATA_RATA_NOTA,
                                 rate_note.IMPORTO / 100 AS IMPORTO,
                                 rate_note.ID_CONTO_CASSE AS CONTO_CORRENTE,
                                 '' AS ID_CATEGORIA_MOVIMENTO,
                                 '' AS CATEGORIA_MOVIMENTO,
                                 note_di_credito.DATA AS DATA_DOCUMENTO,
                                 rate_note.NOTE AS NOTE,
                                 clienti.CODICE_CLIENTE AS CODICE,
                                 note_di_credito.CHIAVE AS CHIAVE_NOTA
                            FROM note_di_credito
                            JOIN clienti ON (clienti.CHIAVE = note_di_credito.ID_CLIENTE)
                            JOIN rate_note ON (rate_note.ID_NOTA = note_di_credito.CHIAVE)
                           WHERE note_di_credito.NUMERO IS NOT NULL
                             AND rate_note.SCALATA_IN_FATTURA  <> 'T' 
                             AND (rate_note.DATA_PAGAMENTO IS NOT NULL OR rate_note.ID_FATTURA IS NOT NULL)
                             AND note_di_credito.NUMERO IS NOT NULL
                             AND rate_note.DATA_PAGAMENTO IS NOT NULL 
                             AND rate_note.DATA_PAGAMENTO >= " . $this->FPrepareParameterValue($this->DallaData,'#') . 
                           " AND rate_note.DATA_PAGAMENTO <= " . $this->FPrepareParameterValue($this->AllaData,'#');
              
                
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {

                  $ObjMovimento = new TOggettoMovimento();
                  $ObjMovimento->Descrizione = $Row['DESCRIZIONE']/* . ($Row['NOTE'] != ''? " \n" . $Row['NOTE'] : '')*/;
                  $ObjMovimento->Data = $Row['DATA_PAGAMENTO_RATA'] ? $Row['DATA_PAGAMENTO_RATA'] : $Row['DATA_RATA_NOTA'];
                  $ObjMovimento->DataDocumento = $Row['DATA_DOCUMENTO'];
                  $ObjMovimento->Codice = $Row['CODICE'];
                  $ObjMovimento->Importo = $Row['IMPORTO'] * -1 * 100;
                  $ObjMovimento->ContoCorrente = $Row['CONTO_CORRENTE'];
                  $ObjMovimento->IdTipologiaMovimento = $Row['ID_CATEGORIA_MOVIMENTO'];
                  $ObjMovimento->DescrizioneTipologia = $Row['CATEGORIA_MOVIMENTO'];
                  array_push($ListaMovimenti,$ObjMovimento);
                }
              }
            }
      }