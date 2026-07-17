<?php
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZCreditBankIdentifier.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");

      define('RIBA_CODICE_SIA_DEFAULT', '00000');
      define('RIBA_NUMERO_CONTO_CORRENTE_DEFAULT', '000000000000');

      class TRecordFileBan
      {
        public $ChiaveFattura          = 0;
        public $NumeroFattura         = 0;
        public $DataFattura           = '';
        public $DataScadenza          = '';
        public $ImportoFattura        = 0;

        public $RagioneSocialeDebitore = '';
        public $IndirizzoDebitore      = '';
        public $CivicoDebitore         = '';
        public $ComuneDebitore         = '';
        public $ProvinciaDebitore      = '';
        public $TargaProvinciaDebitore = '';
        public $PartitaIvaDebitore     = '';
        public $CapDebitore            = '';

        public $RagioneSocialeCreditore = '';
        public $IndirizzoCreditore      = '';
        public $CivicoCreditore         = '';
        public $ComuneCreditore         = '';
        public $ProvinciaCreditore      = '';
        public $PartitaIvaCreditore     = '';
        public $CodiceSiaAzienda      = RIBA_CODICE_SIA_DEFAULT;
        public $CodiceAbiAzienda      = '';
        public $CodiceCabAzienda      = '';
        public $ContoAccreditoAzienda = '';

        public $BancaDebitore = '';
        public $IbanDebitore  = '';
        public $AbiDebitore   = '';
        public $CabDebitore   = '';
        public $NumeroContoCorrDebitore = '';
      }

      class TExtraEsportaFileBan extends TAdvQuery
      {
        const TIPO_RECORD_INTESTAZIONE_I = 'I';
        const TIPO_RECORD_INTESTAZIONE_R = 'R';

        public $ClientiSenzaIban = array();
        public $FattureNonEsportate = array();
        public $ListaFatture     = array();
        private $FCodiceSiaAzienda = RIBA_CODICE_SIA_DEFAULT;
        private $FCodiceAbiAzienda = '';
        private $FCodiceCabAzienda = '';
        private $FContoAccreditoAzienda = '';
        private $FProgressivoRicevutaCbi = 1;
        private $FNumeroRicevuteGenerate = 0;
        private $FTipoRecordIntestazioneCbi = self::TIPO_RECORD_INTESTAZIONE_I;

        function __construct()
        {
          parent::__construct();
          $this->ClientiSenzaIban = array();
          $this->FattureNonEsportate = array();
          $this->ListaFatture     = array();
          $this->FCodiceSiaAzienda = RIBA_CODICE_SIA_DEFAULT;
          $this->FCodiceAbiAzienda = '';
          $this->FCodiceCabAzienda = '';
          $this->FContoAccreditoAzienda = '';
          $this->FProgressivoRicevutaCbi = 1;
          $this->FNumeroRicevuteGenerate = 0;
          $this->FTipoRecordIntestazioneCbi = self::TIPO_RECORD_INTESTAZIONE_I;
        }

        private function FGetParametro($Parametri, $Nome, $Default = '')
        {
          if(isset($Parametri->$Nome))
            return $Parametri->$Nome;

          return $Default;
        }

        private function FGetDatiCreditore($PDODBase)
        {
          $DatiCreditore = null;
          $Query = $this->FGetQueryResult($PDODBase,
                                          'Impostazioni',
                                          'SelectDatiCreditoreFileBan',
                                          'SelectDatiCreditoreFileBan',
                                          array());

          while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          {
            $DatiCreditore = $Row;
            break;
          }

          return $DatiCreditore;
        }

        private function FGetValoreCampo($Row, $Nome)
        {
          if(isset($Row[$Nome]) && $Row[$Nome] != null)
            return trim($Row[$Nome]);

          return '';
        }

        private function FGetErroreCoordinateDebitore($Row)
        {
          $IbanDebitore = $this->FGetValoreCampo($Row, 'IBAN_DEBITORE');

          if($IbanDebitore != '')
          {
            $AbiCabDebitore = GetAbiCabFromIban(strtoupper(str_replace(' ', '', $IbanDebitore)));

            if($AbiCabDebitore->Valido)
              return '';

            return 'IBAN non valido: ' . $AbiCabDebitore->Errore;
          }

          $ListaMotivi = array();

          if($this->FGetValoreCampo($Row, 'ABI_DEBITORE') == '')
            array_push($ListaMotivi, 'ABI mancante');

          if($this->FGetValoreCampo($Row, 'CAB_DEBITORE') == '')
            array_push($ListaMotivi, 'CAB mancante');

          if(count($ListaMotivi) == 0)
            return '';

          return implode(', ', $ListaMotivi);
        }

        private function FHasCoordinateDebitore($Row)
        {
          if($this->FGetErroreCoordinateDebitore($Row) == '')
            return true;

          return false;
        }

        private function FAggiungiFatturaNonEsportata($Row, $Motivo)
        {
          $FatturaNonEsportata = new stdClass();
          $FatturaNonEsportata->Chiave = isset($Row['CHIAVE']) ? $Row['CHIAVE'] : 0;
          $FatturaNonEsportata->NumeroFattura = isset($Row['NUMERO_FATTURA']) ? $Row['NUMERO_FATTURA'] : '';
          $FatturaNonEsportata->DataFattura = isset($Row['DATA_FATTURA']) ? $Row['DATA_FATTURA'] : '';
          $FatturaNonEsportata->Debitore = isset($Row['DEBITORE']) ? $Row['DEBITORE'] : '';
          $FatturaNonEsportata->Motivo = $Motivo;

          array_push($this->FattureNonEsportate, $FatturaNonEsportata);
        }

        private function FBloccaECaricaProgressivoRicevutaCbi($PDODBase)
        {
          $Query = $this->FGetQueryResult($PDODBase,
                                          'Impostazioni',
                                          'LockProgressivoRicevutaCbi',
                                          'LockProgressivoRicevutaCbi',
                                          array());
          $Row = $Query->fetch(PDO::FETCH_ASSOC);

          if($Row == null)
            throw new Exception('Configurazione progressivo ricevuta CBI non trovata.');

          $this->FProgressivoRicevutaCbi = $Row['PROGRESSIVO_RICEVUTA_CBI'] != null ? intval($Row['PROGRESSIVO_RICEVUTA_CBI']) : 1;

          if($this->FProgressivoRicevutaCbi < 1)
            throw new Exception('Progressivo ricevuta CBI non valido in Configurazione | Generale | Dati fiscali.');
        }

        private function FGetDatiFatture($Parametri, $PDODBase)
        {
          $this->ClientiSenzaIban = array();
          $this->FattureNonEsportate = array();
          $this->ListaFatture     = array();

          $DatiCreditore = $this->FGetDatiCreditore($PDODBase);

          if($DatiCreditore == null)
            throw new Exception('Configurazione creditore non trovata in cfg_fattura_elettronica.');

          $this->FCodiceSiaAzienda = $DatiCreditore['CODICE_SIA'] != null && $DatiCreditore['CODICE_SIA'] != '' ? $DatiCreditore['CODICE_SIA'] : RIBA_CODICE_SIA_DEFAULT;
          $this->FCodiceAbiAzienda = $DatiCreditore['CODICE_ABI_AZIENDA'] != null ? trim($DatiCreditore['CODICE_ABI_AZIENDA']) : '';
          $this->FCodiceCabAzienda = $DatiCreditore['CODICE_CAB_AZIENDA'] != null ? trim($DatiCreditore['CODICE_CAB_AZIENDA']) : '';
          $this->FContoAccreditoAzienda = $DatiCreditore['CONTO_ACCREDITO_AZIENDA'] != null ? trim($DatiCreditore['CONTO_ACCREDITO_AZIENDA']) : '';
          $this->FTipoRecordIntestazioneCbi = $DatiCreditore['TIPO_RECORD_INTESTAZIONE_CBI'] != null && trim($DatiCreditore['TIPO_RECORD_INTESTAZIONE_CBI']) != '' ? trim($DatiCreditore['TIPO_RECORD_INTESTAZIONE_CBI']) : self::TIPO_RECORD_INTESTAZIONE_I;

          if($this->FCodiceAbiAzienda == '')
            throw new Exception('ABI CBI aziendale non configurato in Configurazione | Generale | Dati fiscali.');

          if($this->FCodiceCabAzienda == '')
            throw new Exception('CAB CBI aziendale non configurato in Configurazione | Generale | Dati fiscali.');

          if($this->FContoAccreditoAzienda == '')
            throw new Exception('Conto accredito CBI aziendale non configurato in Configurazione | Generale | Dati fiscali.');

          if($this->FTipoRecordIntestazioneCbi != self::TIPO_RECORD_INTESTAZIONE_I && 
             $this->FTipoRecordIntestazioneCbi != self::TIPO_RECORD_INTESTAZIONE_R)
              throw new Exception('Tipo record intestazione CBI non valido in Configurazione | Generale | Dati fiscali.');

          $Result = $this->FGetQueryResult($PDODBase,
                                           'Fatture',
                                           'SelectDatiFileBan',
                                           'SelectDatiFileBan',
                                           get_object_vars($Parametri));

          while($Row = $Result->fetch(PDO::FETCH_ASSOC))
          {
            if($this->FHasCoordinateDebitore($Row))
            {
              $Record = new TRecordFileBan();

              $Record->ChiaveFattura = $Row['CHIAVE'];
              $Record->NumeroFattura = $Row['NUMERO_FATTURA'];
              $Record->DataFattura   = $Row['DATA_FATTURA'];
              $Record->DataScadenza  = isset($Row['DATA_SCADENZA']) ? $Row['DATA_SCADENZA'] : $Row['DATA_FATTURA'];
              $Record->ImportoFattura = $Row['IMPORTO_FATTURA'];

              $Record->RagioneSocialeDebitore = $Row['DEBITORE'];
              $Record->IndirizzoDebitore = $Row['INDIRIZZO_FATTURAZIONE'];
              $Record->CivicoDebitore = $Row['CIVICO_FATTURAZIONE'];
              $Record->ComuneDebitore = $Row['COMUNE_FATTURAZIONE'];
              $Record->CapDebitore = $Row['CAP_FATTURAZIONE'];
              $Record->ProvinciaDebitore = $Row['PROVINCIA_FATTURAZIONE'];
              $Record->TargaProvinciaDebitore = $Row['PROVINCIA_TARGA_FATTURAZIONE'];
              $Record->PartitaIvaDebitore = $Row['PARTITA_IVA_DEBITORE'] != null ? $Row['PARTITA_IVA_DEBITORE'] :
                                            ($Row['CODICE_FISCALE_DEBITORE'] != null ? $Row['CODICE_FISCALE_DEBITORE'] : '');

              $Record->RagioneSocialeCreditore = $DatiCreditore['CREDITORE'];
              $Record->IndirizzoCreditore = $DatiCreditore['INDIRIZZO_CREDITORE'];
              $Record->CivicoCreditore = $DatiCreditore['CIVICO_CREDITORE'];
              $Record->ComuneCreditore = $DatiCreditore['COMUNE_CREDITORE'];
              $Record->ProvinciaCreditore = $DatiCreditore['PROVINCIA_CREDITORE'];
              $Record->PartitaIvaCreditore = $DatiCreditore['PARTITA_IVA_CREDITORE'];
              $Record->CodiceSiaAzienda = $this->FCodiceSiaAzienda;
              $Record->CodiceAbiAzienda = $this->FCodiceAbiAzienda;
              $Record->CodiceCabAzienda = $this->FCodiceCabAzienda;
              $Record->ContoAccreditoAzienda = $this->FContoAccreditoAzienda;

              $Record->BancaDebitore = $Row['BANCA_DEBITORE'];
              $Record->IbanDebitore = $this->FGetValoreCampo($Row, 'IBAN_DEBITORE');
              $Record->AbiDebitore = $this->FGetValoreCampo($Row, 'ABI_DEBITORE');
              $Record->CabDebitore = $this->FGetValoreCampo($Row, 'CAB_DEBITORE');
              $Record->NumeroContoCorrDebitore = $this->FGetValoreCampo($Row, 'NUMERO_CONTO_CORR_DEBITORE');

              if($Record->NumeroContoCorrDebitore == '')
                $Record->NumeroContoCorrDebitore = RIBA_NUMERO_CONTO_CORRENTE_DEFAULT;

              array_push($this->ListaFatture, $Record);
            }
            else
            {
              array_push($this->ClientiSenzaIban, $Row);
              $this->FAggiungiFatturaNonEsportata($Row, $this->FGetErroreCoordinateDebitore($Row));
            }
          }
        }

        private function FGetNumeroRicevuta()
        {
          if($this->FProgressivoRicevutaCbi > 999999)
            throw new Exception('Progressivo ricevuta CBI esaurito: il valore massimo e\' 999999.');

          $NumeroRicevuta = date('Y') . str_pad($this->FProgressivoRicevutaCbi, 6, '0', STR_PAD_LEFT);
          $this->FProgressivoRicevutaCbi++;
          $this->FNumeroRicevuteGenerate++;

          return $NumeroRicevuta;
        }

        private function FGetProgressivoRicevuta()
        {
          return $this->FProgressivoRicevutaCbi;
        }

        private function FAggiornaProgressivoRicevutaCbi($PDODBase)
        {
          if($this->FNumeroRicevuteGenerate == 0)
          {
            return;
          }

          $this->FGetQueryResult($PDODBase,
                                 'Impostazioni',
                                 'EditSQL',
                                 'AggiornaProgressivoRicevutaCbi',
                                 array('PROGRESSIVO_RICEVUTA_CBI' => $this->FProgressivoRicevutaCbi));
        }

        private function FGetTipoRecordIntestazione()
        {
          if($this->FTipoRecordIntestazioneCbi == self::TIPO_RECORD_INTESTAZIONE_R)
            return true;

          return false;
        }

        private function FCreaDisposizione($Fattura, $Parametri)
        {
          $IbanDebitore = strtoupper(str_replace(' ', '', $Fattura->IbanDebitore));
          $CodiceAbiDebitore = '';
          $CodiceCabDebitore = '';

          if($IbanDebitore != '')
          {
            $AbiCabDebitore = GetAbiCabFromIban($IbanDebitore);

            if(!$AbiCabDebitore->Valido)
              throw new Exception('IBAN debitore non valido per la fattura ' . $Fattura->NumeroFattura . ': ' . $AbiCabDebitore->Errore);

            $CodiceAbiDebitore = $AbiCabDebitore->ABI;
            $CodiceCabDebitore = $AbiCabDebitore->CAB;
          }
          else
          {
            $CodiceAbiDebitore = trim($Fattura->AbiDebitore);
            $CodiceCabDebitore = trim($Fattura->CabDebitore);

            if($CodiceAbiDebitore == '' || $CodiceCabDebitore == '')
              throw new Exception('Coordinate bancarie debitore non complete per la fattura ' . $Fattura->NumeroFattura . '.');
          }

          $NumeroFattura = str_pad($Fattura->NumeroFattura, 6, '0', STR_PAD_LEFT);
          $DataFattura = (new DateTime($Fattura->DataFattura))->format('d-m-y');
          $IndirizzoCreditore = trim($Fattura->IndirizzoCreditore . ', ' . $Fattura->CivicoCreditore, ' ,');
          $IndirizzoDebitore = trim($Fattura->IndirizzoDebitore . ', ' . $Fattura->CivicoDebitore, ' ,');
          $CodiceSiaAzienda = $Fattura->CodiceSiaAzienda;
          $CodiceAbiAzienda = $Fattura->CodiceAbiAzienda;
          $CodiceCabAzienda = $Fattura->CodiceCabAzienda;
          $ContoAccreditoAzienda = $Fattura->ContoAccreditoAzienda;

          $Disposizione = new TCreditBankIdentifierDisposizione();
          $Disposizione->Progressivo = $this->FGetProgressivoRicevuta();
          $NumeroRicevuta = $this->FGetNumeroRicevuta();
          $Disposizione->DataScadenza = $Fattura->DataScadenza;
          $Disposizione->ImportoCentesimi = $Fattura->ImportoFattura;
          $Disposizione->AbiBancaAssuntrice = $CodiceAbiAzienda;
          $Disposizione->CabBancaAssuntrice = $CodiceCabAzienda;
          $Disposizione->ContoAccredito = $ContoAccreditoAzienda;
          $Disposizione->AbiBancaDomiciliataria = $CodiceAbiDebitore;
          $Disposizione->CabBancaDomiciliataria = $CodiceCabDebitore;
          $Disposizione->CodiceSiaCreditore = $CodiceSiaAzienda;
          $Disposizione->CodiceClienteDebitore = $Fattura->ChiaveFattura;
          $Disposizione->DescrizioneCreditore = $Fattura->RagioneSocialeCreditore;
          $Disposizione->IndirizzoCreditore = $IndirizzoCreditore;
          $Disposizione->ComuneCreditore = trim($Fattura->ComuneCreditore . ' ' . $Fattura->ProvinciaCreditore);
          $Disposizione->DescrizioneDebitore = trim($Fattura->RagioneSocialeDebitore . ' @');
          $Disposizione->CodiceFiscaleDebitore = $Fattura->PartitaIvaDebitore;
          $Disposizione->IndirizzoDebitore = $IndirizzoDebitore;
          $Disposizione->CapDebitore = $Fattura->CapDebitore;
          $Disposizione->ComuneDebitore = $Fattura->ComuneDebitore;
          $Disposizione->ProvinciaDebitore = $Fattura->TargaProvinciaDebitore;
          $Disposizione->BancaDomiciliataria = $Fattura->BancaDebitore;
          $Disposizione->RiferimentoDebito = 'Per la fattura: ' . $NumeroFattura . ' del ' . $DataFattura;
          $Disposizione->CodiceFiscaleCreditore = $Fattura->PartitaIvaCreditore;
          $Disposizione->NumeroRicevuta = $NumeroRicevuta;
          $Disposizione->DenominazioneCreditore = $Fattura->RagioneSocialeCreditore;

          return $Disposizione;
        }

        private function FCreateFileBan($ListaFatture, $Parametri)
        {
          $CBI = new TCreditBankIdentifier();
          $CBI->Intestazione->CodiceSia = $this->FCodiceSiaAzienda;
          $CBI->Intestazione->CodiceAbi = $this->FCodiceAbiAzienda;
          $CBI->Intestazione->DataCreazione = new DateTime();
          $CBI->Intestazione->NomeSupporto = $this->FGetParametro($Parametri, 'NomeSupporto', '');
          $CBI->Intestazione->TipoRecordOldRB = $this->FGetTipoRecordIntestazione();

          for($i = 0; $i < count($ListaFatture); $i++)
          {
            $Disposizione = $this->FCreaDisposizione($ListaFatture[$i], $Parametri);
            $CBI->AggiungiDisposizione($Disposizione);
          }

          return $CBI;
        }

        private function FAggiornaFattureInviateTramiteCbi($PDODBase)
        {
          $ListaChiavi = array();

          for($i = 0; $i < count($this->ListaFatture); $i++)
          {
            if(!in_array($this->ListaFatture[$i]->ChiaveFattura, $ListaChiavi))
              array_push($ListaChiavi, $this->ListaFatture[$i]->ChiaveFattura);
          }

          $this->FGetQueryResult($PDODBase,
                                 'Fatture',
                                 'EditSQL',
                                 'AggiornaInviataTramiteCbi',
                                 array('ListaChiavi' => $ListaChiavi));
        }

        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = json_decode($_POST['Params']);

          try
          {
            $this->FGetDatiFatture($Parametri, $PDODBase);

            if(count($this->ListaFatture) == 0)
            {
              $JSONAnswer->Esito = false;
              $JSONAnswer->MessaggioUtente = 'Nessuna fattura Ri.Ba. inviata allo SDI e non ancora esportata tramite CBI.';
              $JSONAnswer->ListaFatture = $this->ListaFatture;
              $JSONAnswer->ClientiSenzaIban = $this->ClientiSenzaIban;
              $JSONAnswer->FattureNonEsportate = $this->FattureNonEsportate;
              return;
            }

            $PDODBase->beginTransaction();
            $this->FBloccaECaricaProgressivoRicevutaCbi($PDODBase);
            $this->FGetDatiFatture($Parametri, $PDODBase);

            if(count($this->ListaFatture) == 0)
            {
              if($PDODBase->inTransaction())
              {
                $PDODBase->rollBack();
              }

              $JSONAnswer->Esito = false;
              $JSONAnswer->MessaggioUtente = 'Nessuna fattura Ri.Ba. inviata allo SDI e non ancora esportata tramite CBI.';
              $JSONAnswer->ListaFatture = $this->ListaFatture;
              $JSONAnswer->ClientiSenzaIban = $this->ClientiSenzaIban;
              $JSONAnswer->FattureNonEsportate = $this->FattureNonEsportate;
              return;
            }

            $this->FNumeroRicevuteGenerate = 0;

            $CBI = $this->FCreateFileBan($this->ListaFatture, $Parametri);
            $JSONAnswer->Esito = true;
            $JSONAnswer->FileBan = $CBI->GeneraFile();
            $JSONAnswer->ListaFatture = $this->ListaFatture;
            $JSONAnswer->ClientiSenzaIban = $this->ClientiSenzaIban;
            $JSONAnswer->FattureNonEsportate = $this->FattureNonEsportate;

            $this->FAggiornaFattureInviateTramiteCbi($PDODBase);
            $this->FAggiornaProgressivoRicevutaCbi($PDODBase);
            $PDODBase->commit();
          }
          catch(Exception $e)
          {
            if($PDODBase->inTransaction())
            {
              $PDODBase->rollBack();
            }

            throw $e;
          }
        }
      }

      $AConnection = new TExtraEsportaFileBan();
      $AConnection->ServerSideScript();
