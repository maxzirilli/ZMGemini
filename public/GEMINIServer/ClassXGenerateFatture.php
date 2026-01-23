<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
      include_once PATH_LIBRERIE . 'ZStringFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
    
      define('MY_CONST_ARRAY', ['value1', 'value2', 'value3']);

      class TAdvQueryGeneraXMLInterface extends TAdvQuery
      {
            private $FIvaSuggerita = 0;

            protected function FCreateFatturaElettronica($Chiave, $TipoDocumento, $PDODBase)
            {
              $this->FIvaSuggerita = TSystemInformation::GetIvaSuggerita($PDODBase);
              $Fattura = new TFatturaElettronica();
              $SQLBody = "SELECT cfg_fattura_elettronica.*,
                                 (SELECT TARGA FROM province WHERE cfg_fattura_elettronica.ID_PROVINCIA_REA = province.CHIAVE) AS PROVINCIA_REA, 
                                 (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.PAESE_TRASMITTENTE = nazioni.CHIAVE) AS SIGLA_PAESE_TRASMITTENTE,
                                 (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.NAZIONE_EM_PIVA_PRESTATORE = nazioni.CHIAVE) AS SIGLA_NAZIONE_EM_PIVA_PRESTATORE,
                                 (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.NAZIONE_SEDE_PRESTATORE = nazioni.CHIAVE) AS SIGLA_NAZIONE_SEDE_PRESTATORE,
                                 (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.NAZIONE_SUCCURSALE_PRESTATORE = nazioni.CHIAVE) AS SIGLA_NAZIONE_SUCCURSALE_PRESTATORE,
                                 (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.NAZIONE_EM_PIVA_RAPPR_FISCALE = nazioni.CHIAVE) AS SIGLA_NAZIONE_EM_PIVA_RAPPR_FISCALE,
                                 (SELECT TARGA FROM province WHERE cfg_fattura_elettronica.PROVINCIA_SEDE_PRESTATORE = province.CHIAVE) AS TARGA_PROVINCIA_SEDE_PRESTATORE,
                                 (SELECT TARGA FROM province WHERE cfg_fattura_elettronica.PROVINCIA_SUCCURSALE_PRESTATORE = province.CHIAVE) AS TARGA_PROVINCIA_SUCCURSALE_PRESTATORE		 	 		 		 		        
                            FROM cfg_fattura_elettronica";

              if($Query = $PDODBase->query($SQLBody))
              {
                if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  if($TipoDocumento == TIPO_DOCUMENTO_AUTOFATTURA)
                  {
                    $this->FGetDocumentoDaFornitore($Fattura, $Row);
                  }
                  else
                  {
                    $this->FGetDocumentoPerCliente($Fattura, $Row);
                  }
                }
                else throw new Exception('Configurazione fattura elettronica assente');
              }
              else throw new Exception('Impossibile ottenere la configurazione della fattura elettronica');

              switch($TipoDocumento)
              {
                 case TIPO_DOCUMENTO_FATTURA        : $this->FPrepareFattura($Chiave, $Fattura, $PDODBase);
                                                      break;
                 case TIPO_DOCUMENTO_AUTOFATTURA    : $this->FPrepareAutofattura($Chiave, $Fattura, $PDODBase);
                                                      break;
                 case TIPO_DOCUMENTO_NOTA           : $this->FPrepareNota($Chiave, $Fattura, $PDODBase);
                                                      break; 
              }

              $Fattura->CompilaFatturaElettronica();
              return $Fattura;
            }

            private function FGetDocumentoDaFornitore(&$Fattura, $Row)
            {
              $Fattura->Configurazione->DatiTrasmissione->PaeseTrasmittente = $Row['SIGLA_PAESE_TRASMITTENTE'];
              $Fattura->Configurazione->DatiTrasmissione->PIVATrasmittente  = $Row['PIVA_TRASMITTENTE'];
              
              if(isset($Row['CODICE_SDI_PRESTATORE']) && $Row['CODICE_SDI_PRESTATORE'] != '')
                $Fattura->CodiceDestinatario = $Row['CODICE_SDI_PRESTATORE']; 
              else $Fattura->CodiceDestinatario = '0000000';
              
              $Fattura->Committente->CodiceFiscale      = $Row['CODICE_FISCALE_PRESTATORE'];
              $Fattura->Committente->Denominazione      = $Row['DENOMINAZIONE_PRESTATORE'];
              $Fattura->Committente->Sede->Indirizzo    = $Row['INDIRIZZO_SEDE_PRESTATORE'];
              $Fattura->Committente->Sede->NumeroCivico = $Row['NR_CIVICO_SEDE_PRESTATORE'];
              $Fattura->Committente->Sede->CAP          = $Row['CAP_SEDE_PRESTATORE'];
              $Fattura->Committente->Sede->Comune       = $Row['COMUNE_SEDE_PRESTATORE'];
              $Fattura->Committente->Sede->Provincia    = $Row['TARGA_PROVINCIA_SEDE_PRESTATORE'];
              $Fattura->Committente->Sede->Nazione      = $Row['SIGLA_NAZIONE_SEDE_PRESTATORE'];
              $Fattura->Committente->PartitaIVA         = $Row['PARTITA_IVA_PRESTATORE'];
              $Fattura->Committente->PaeseEmPIVA        = $Row['SIGLA_NAZIONE_EM_PIVA_PRESTATORE'];
              $Fattura->Committente->PEC                = isset($Row['PEC_PRESTATORE'])? $Row['PEC_PRESTATORE'] : '';
            }

            private function FGetDocumentoPerCliente(&$Fattura, $Row)
            {
              $Fattura->Configurazione->DatiTrasmissione->PaeseTrasmittente   = $Row['SIGLA_PAESE_TRASMITTENTE'];
              $Fattura->Configurazione->DatiTrasmissione->PIVATrasmittente    = $Row['PIVA_TRASMITTENTE'];

              $Fattura->Configurazione->CedentePrestatore->PaeseEmPIVA        = $Row['SIGLA_NAZIONE_EM_PIVA_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->PartitaIVA         = $Row['PARTITA_IVA_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->CodiceFiscale      = $Row['CODICE_FISCALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Denominazione      = $Row['DENOMINAZIONE_PRESTATORE'];
              //$Fattura->Configurazione->CedentePrestatore->Cognome          = $Row['COGNOME_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->RegimeFiscale      = $Row['REGIME_FISCALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->PersonaFisica      = $Row['PRESTATORE_PERSONA_FISICA'] == 'T';

              $Fattura->Configurazione->CedentePrestatore->Sede->Indirizzo    = $Row['INDIRIZZO_SEDE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Sede->NumeroCivico = $Row['NR_CIVICO_SEDE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Sede->CAP          = $Row['CAP_SEDE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Sede->Comune       = $Row['COMUNE_SEDE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Sede->Provincia    = $Row['TARGA_PROVINCIA_SEDE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Sede->Nazione      = $Row['SIGLA_NAZIONE_SEDE_PRESTATORE'];

              $Fattura->Configurazione->CedentePrestatore->Contatti->Telefono = $Row['TELEFONO_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->Contatti->EMail    = $Row['EMAIL_PRESTATORE'];                

              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->Indirizzo    = $Row['INDIRIZZO_SUCCURSALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->CAP          = $Row['CAP_SUCCURSALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->NumeroCivico = $Row['NR_CIVICO_SUCCURSALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->Comune       = $Row['COMUNE_SUCCURSALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->Provincia    = $Row['TARGA_PROVINCIA_SUCCURSALE_PRESTATORE'];
              $Fattura->Configurazione->CedentePrestatore->StabileOrganizzativo->Nazione      = $Row['SIGLA_NAZIONE_SUCCURSALE_PRESTATORE'];

              $Fattura->Configurazione->CedentePrestatore->UfficioREA->Iscritto        = $Row['ISCRITTO_REA'] == 'T';
              $Fattura->Configurazione->CedentePrestatore->UfficioREA->ProvUfficio     = $Row['PROVINCIA_REA'];
              $Fattura->Configurazione->CedentePrestatore->UfficioREA->NumeroRea       = $Row['NUMERO_REA'];
              $Fattura->Configurazione->CedentePrestatore->UfficioREA->CapitaleSociale = $Row['CAPITALE_SOCIALE_REA'];
              $Fattura->Configurazione->CedentePrestatore->UfficioREA->SocioUnico      = $Row['SOCIO_UNICO_REA'] == 'T';
              $Fattura->Configurazione->CedentePrestatore->UfficioREA->InLiquidazione  = $Row['IN_LIQUIDAZIONE_REA'] == 'T';

              $Fattura->Configurazione->RappresentanteFisc->Presente      = $Row['RAPPRESENTANTE_FISCALE'] == 'T';
              $Fattura->Configurazione->RappresentanteFisc->PaeseEmPIVA   = $Row['SIGLA_NAZIONE_EM_PIVA_RAPPR_FISCALE'];
              $Fattura->Configurazione->RappresentanteFisc->PartitaIVA    = $Row['PARTITA_IVA_RAPPR_FISCALE'];
              $Fattura->Configurazione->RappresentanteFisc->PersonaFisica = $Row['RAPPR_FISCALE_PERSONA_FISICA'];
              $Fattura->Configurazione->RappresentanteFisc->Denominazione = $Row['DENOMINAZIONE_RAPPR_FISCALE'];

              $Fattura->Ritenuta->Tipo                                    = $Row['TIPO_RITENUTA'];
            }

            private function FGetCodeNaturaPagamento($IdNaturaPagamento)
            {
               switch($IdNaturaPagamento)
               {
                   case 1         : return(ID_FATT_NAT_PAGAMENTI_natEscluseExArt15);    
                   case 4         : return(ID_FATT_NAT_PAGAMENTI_natEsenti);    
                   case 5         : return(ID_FATT_NAT_PAGAMENTI_natRegimeDelMargine);    
                   case 7         : return(ID_FATT_NAT_PAGAMENTI_natIVAEstera);    
                   case 8         : return(ID_FATT_NAT_PAGAMENTI_natNonSoggettArt7);    
                   case 9         : return(ID_FATT_NAT_PAGAMENTI_natNonSoggetteAltriCasi);    
                   case 10        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliEsportazioni);    
                   case 11        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliCessioniIntracomunitarie);    
                   case 12        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliCessioniSanMarimo);    
                   case 13        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliAssimilateCessioneEsportazione);    
                   case 14        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliDichiarazioniDIntento);    
                   case 15        : return(ID_FATT_NAT_PAGAMENTI_natNonImponibiliAltreOperazioni);    
                   case 16        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileRottami);    
                   case 17        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileOroArgento);    
                   case 18        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileSubappaltoEdile);    
                   case 19        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileCessioneFabbricati);    
                   case 20        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileCessioneTelefoniCellulari);    
                   case 21        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileCessioneProdottiElettronici);    
                   case 22        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileCompartoEdileEdAffini);    
                   case 23        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileOperazioniSettoreEnergetico);    
                   case 24        : return(ID_FATT_NAT_PAGAMENTI_natInversioneContabileAltriCasi);    
                   default        : return(ID_FATT_NAT_PAGAMENTI_natNonDefinito);
                 
               }
            }

            protected function FPrepareFattura($Chiave, &$Fattura, $PDODBase)
            {
                $ModPagamento = 1;
                $IBANContoCorrente = '';
                $SQLBody = "SELECT fatture.*, 
                                   province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                                   nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                                   cond_pagamento.DESCRIZIONE AS DESCRIZIONE_COND_PAGAMENTO,
                                   cond_pagamento.COND_PAG_FATT_ELE,
                                   cond_pagamento.MOD_PAG_FATT_ELE,
                                   conti_correnti_casse.IBAN AS IBAN_CONTO_CORRENTE,
                                   causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE
                              FROM fatture
                                   LEFT OUTER JOIN province ON province.CHIAVE = fatture.PROVINCIA_FATTURAZIONE
                                   LEFT OUTER JOIN nazioni ON nazioni.CHIAVE = fatture.NAZIONE_EM_PIVA
                                   LEFT OUTER JOIN cond_pagamento ON cond_pagamento.CHIAVE = fatture.COND_PAGAMENTO
                                   LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = fatture.ID_CONTO_CORRENTE
                                   LEFT OUTER JOIN causali              ON causali.CHIAVE = fatture.CAUSALE
                             WHERE fatture.CHIAVE = $Chiave";

                if($Query = $PDODBase->query($SQLBody))
                {
                  if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {
                    $Fattura->Committente->EntePubblico = $Row['ENTE_PUBBLICO'] == 'T';

                    if(isset($Row['COD_UFFICIO_DEST']) && $Row['COD_UFFICIO_DEST'] != '')
                      $Fattura->CodiceDestinatario = $Row['COD_UFFICIO_DEST'];
                    else if(isset($Row['COD_ENTE_SDI']))
                      $Fattura->CodiceDestinatario =$Row['COD_ENTE_SDI'];
                    else $Fattura->CodiceDestinatario = '0000000';

                    if(isset($Row['PEC']))
                      $Fattura->Committente->PEC = $Row['PEC'];
                    else $Fattura->Committente->PEC = '';

                    $Fattura->Committente->CodiceFiscale = $Row['CODICE_FISCALE'];
                    $Fattura->Committente->Denominazione = $Row['RAGIONE_SOCIALE'];
                    $Fattura->Committente->Sede->Indirizzo = $Row['INDIRIZZO_FATTURAZIONE'];
                    $Fattura->Committente->Sede->NumeroCivico = $Row['NR_CIVICO_FATTURAZIONE'];
                    $Fattura->Committente->Sede->CAP = $Row['CAP_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Comune = $Row['COMUNE_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Provincia = $Row['TARGA_PROVINCIA_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Nazione = $Row['SIGLIA_NAZIONE_EM_PIVA'];
                    $Fattura->Committente->PartitaIVA = $Row['PARTITA_IVA'];
                    $Fattura->Committente->PaeseEmPIVA = $Row['SIGLIA_NAZIONE_EM_PIVA'];
                    
                    $Fattura->DatiGenerali->Data = $Row['DATA'];
                    $Fattura->DatiGenerali->Numero = ($Row['DA_BANCO'] == 'T' ? 'B' : 'F') . $Row['NUMERO'];
                    //$Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docfattura;
                    $Fattura->DatiGenerali->Tipo = ($Row['ANTICIPO'] == 'T' ? ID_FATT_TIPO_docAccontoSuFattura : ID_FATT_TIPO_docfattura);

                    $Fattura->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
                    
                    $NuovoDocumento = null;
                    if($Row['DOCUMENTO_CORRELATO'] == 'O')
                      $NuovoDocumento = $Fattura->AddOrdineAcquisto();
                    if($NuovoDocumento != null)
                    {
                      $NuovoDocumento->Data = $Row['DATA_DOCUMENTO'];
                      $NuovoDocumento->CodiceCIG = $Row['CIG'];
                      $NuovoDocumento->CodiceCUP = $Row['CUP'];
                      $NuovoDocumento->IdDocumento = $Row['DOCUMENTO_NR'];
                      $NuovoDocumento->CodiceCommessaConvenz = $Row['CONVENZIONE'];
                    }

                    if(isset($Row['PAGAMENTO_BOLLO']) && $Row['PAGAMENTO_BOLLO'] != 'N')
                    {
                      $Fattura->ImportoBollo = $Row['COSTO_BOLLO'] / 100;
                    }
                    $Fattura->ProgressivoTrasmissione = strtoupper(dechex($Row['CHIAVE']));

                    $Fattura->Ritenuta->Percentuale = $Row['RITENUTA'] / 10;
                    array_push($Fattura->DatiGenerali->Causale, $Row['DESCRIZIONE_CAUSALE']);
                    $Fattura->DatiPagamento->CondPagamento = $Row['COND_PAG_FATT_ELE'];
                    $ModPagamento = $Row['MOD_PAG_FATT_ELE'];
                    if(!is_null($Row['IBAN_CONTO_CORRENTE']))
                       $IBANContoCorrente = $Row['IBAN_CONTO_CORRENTE'];
                  }
                }
                else throw new Exception('Impossibile caricare i dati delle fatture');

                $TotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase, $Chiave, false, true);
                $Fattura->TotaleIvato           = $TotaliFattura[0]->TotaleIvato;
                $Fattura->Ritenuta->Importo     = $TotaliFattura[0]->TotaleRitenuta;

                for($i = 0; $i < count($TotaliFattura[0]->LsIva); $i++)
                   $Fattura->AddIVA($TotaliFattura[0]->LsIva[$i]->IVA, $TotaliFattura[0]->LsIva[$i]->SommaImponibile, $this->FGetCodeNaturaPagamento($TotaliFattura[0]->LsIva[$i]->NaturaPagamento));
                
                $TotaliFattura = null;

                $SQLBody = "SELECT rate_fattura.*
                              FROM rate_fattura
                             WHERE rate_fattura.ID_FATTURA = $Chiave
                             ORDER BY rate_fattura.DATA";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                        $Fattura->DatiPagamento->AddDettagliPagamento($Row['DATA'],
                                                                      $Row['IMPORTO'] / 100,
                                                                      '',
                                                                      $ModPagamento, 
                                                                      $IBANContoCorrente);
                } 
                else throw new Exception('Impossibile trovare le rate della fattura');

                $SQLBody = "SELECT voci_fatture.*, 
                                   unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                              FROM voci_fatture
                                   LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_fatture.UNITA_DI_MISURA
                             WHERE voci_fatture.ID_FATTURA = $Chiave
                             ORDER BY voci_fatture.ORDINAMENTO";

                $SomeIvaSuggerita = false;

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {   
                    $NuovaVoce = $Fattura->Configurazione->AddVoce();
                    $NuovaVoce->Descrizione = ($Row['DESCRIZIONE']);
                    $NuovaVoce->Quantita = ($Row['QUANTITA']) / 100;
                    if($Row['IMPORTO_IVATO'] == 'T')
                      $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/(100 + ($Row['IVA'])/100);
                    else $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/ 100;
                    $NuovaVoce->IVA = $Row['QUANTITA'] == 0 ? $this->FIvaSuggerita : $Row['IVA'] / 100;
                    if($Row['QUANTITA'] == 0)
                      $SomeIvaSuggerita = true;
                    $NuovaVoce->Udm = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                    if($Row['SCONTO'] != 0)
                      $NuovaVoce->AddScontoPerc(($Row['SCONTO']) / 100);  

                    $NuovaVoce->NatPagamVoce = $this->FGetCodeNaturaPagamento($Row['NATURA_PAGAMENTO']);
                            
                  }
                } 
                else throw new Exception('Impossibile trovare le voci fattura');

                if($SomeIvaSuggerita)
                  $this->FAddEmptyIVA($Fattura);
            }

            private function FAddEmptyIva(&$Fattura)
            {
              $Trovato = false;
              for($i = 0; $i < count($Fattura->IVA); $i++)
                if($Fattura->IVA[$i]->IVA == $this->FIvaSuggerita)
                {
                  $Trovato = true;
                  break;
                }
                
              if(!$Trovato)
                $Fattura->AddIVA($this->FIvaSuggerita, 0, ID_FATT_NAT_PAGAMENTI_natInversioneContabileSubappaltoEdile);
            }


            protected function FPrepareNota($Chiave, &$Fattura, $PDODBase)
            {

                $SQLBody = "SELECT note_di_credito.*, 
                                   province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                                   nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                                   cond_pagamento.DESCRIZIONE AS DESCRIZIONE_COND_PAGAMENTO,
                                   causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE
                              FROM note_di_credito
                                   LEFT OUTER JOIN province ON province.CHIAVE = note_di_credito.PROVINCIA_FATTURAZIONE
                                   LEFT OUTER JOIN nazioni ON nazioni.CHIAVE = note_di_credito.NAZIONE_EM_PIVA
                                   LEFT OUTER JOIN cond_pagamento ON cond_pagamento.CHIAVE = note_di_credito.COND_PAGAMENTO
                                   LEFT OUTER JOIN causali        ON causali.CHIAVE = note_di_credito.ID_CAUSALE
                            WHERE note_di_credito.CHIAVE = $Chiave";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {                      
                    $Fattura->Committente->EntePubblico = $Row['ENTE_PUBBLICO'] == 'T';
                    if(isset($Row['COD_UFFICIO_DEST']) && $Row['COD_UFFICIO_DEST'] != '')
                      $Fattura->CodiceDestinatario = $Row['COD_UFFICIO_DEST'];
                    else if(isset($Row['COD_ENTE_SDI']))
                      $Fattura->CodiceDestinatario = $Row['COD_ENTE_SDI'];
                    else $Fattura->CodiceDestinatario = '0000000';
                    $Fattura->Committente->CodiceFiscale = $Row['CODICE_FISCALE'];
                    $Fattura->Committente->Denominazione = $Row['RAGIONE_SOCIALE'];
                    $Fattura->Committente->Sede->Indirizzo = $Row['INDIRIZZO_FATTURAZIONE'];
                    $Fattura->Committente->Sede->NumeroCivico = $Row['NR_CIVICO_FATTURAZIONE'];
                    $Fattura->Committente->Sede->CAP = $Row['CAP_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Comune = $Row['COMUNE_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Provincia = $Row['TARGA_PROVINCIA_FATTURAZIONE'];
                    $Fattura->Committente->Sede->Nazione = $Row['SIGLIA_NAZIONE_EM_PIVA'];
                    $Fattura->Committente->PartitaIVA = $Row['PARTITA_IVA'];
                    $Fattura->Committente->PaeseEmPIVA = $Row['SIGLIA_NAZIONE_EM_PIVA'];;
                    $Fattura->DatiGenerali->Data = $Row['DATA'];
                    $Fattura->DatiGenerali->Numero = 'N' . $Row['NUMERO'];
                    $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docNotaCredito;
                    $Fattura->ProgressivoTrasmissione = strtoupper(dechex($Row['CHIAVE']));

                    $NuovoDocumento = null;
                    if($Row['DOCUMENTO_CORRELATO'] == 'O')
                      $NuovoDocumento = $Fattura->AddOrdineAcquisto();
                    if($NuovoDocumento != null)
                    {
                      $NuovoDocumento->Data = $Row['DATA_DOCUMENTO'];
                      $NuovoDocumento->CodiceCIG = $Row['CIG'];
                      $NuovoDocumento->CodiceCUP = $Row['CUP'];
                      $NuovoDocumento->IdDocumento = $Row['DOCUMENTO_NR'];
                      $NuovoDocumento->CodiceCommessaConvenz = $Row['CONVENZIONE'];
                    }
                    
                    $Fattura->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];

                    $Fattura->Ritenuta->Percentuale = $Row['RITENUTA'] / 10;
                    array_push($Fattura->DatiGenerali->Causale, $Row['DESCRIZIONE_CAUSALE']);
                  }
                }

                $TotaliNota = TSystemInformation::GetTotaliNota($PDODBase, $Chiave, true);

                $Fattura->TotaleIvato           = $TotaliNota[0]->TotaleIvato;
                $Fattura->Ritenuta->Importo     = $TotaliNota[0]->TotaleRitenuta;

                for($i = 0; $i < count($TotaliNota[0]->LsIva); $i++)
                   $Fattura->AddIVA($TotaliNota[0]->LsIva[$i]->IVA,$TotaliNota[0]->LsIva[$i]->SommaImponibile, $this->FGetCodeNaturaPagamento($TotaliNota[0]->LsIva[$i]->NaturaPagamento));

                $TotaliNota = null;

                $SQLBody = "SELECT voci_note_di_credito.*, 
                                   unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                              FROM voci_note_di_credito
                                   LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_note_di_credito.UNITA_DI_MISURA
                             WHERE voci_note_di_credito.ID_NOTA_DI_CREDITO = $Chiave
                             ORDER BY voci_note_di_credito.ORDINAMENTO";

                $SomeIvaSuggerita = false;

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {   
                    $NuovaVoce = $Fattura->Configurazione->AddVoce();
                    $NuovaVoce->Descrizione = ($Row['DESCRIZIONE']);
                    $NuovaVoce->Quantita = ($Row['QUANTITA'])/ 100;
                    if($Row['IMPORTO_IVATO'] == 'T')
                      $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/(100 + ($Row['IVA'])/100);
                    else $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/ 100;
                    $NuovaVoce->IVA = $Row['QUANTITA'] == 0 ? $this->FIvaSuggerita : $Row['IVA'] / 100;
                    $NuovaVoce->Udm = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                    if($Row['SCONTO'] != 0)
                       $NuovaVoce->AddScontoPerc(($Row['SCONTO']) / 100);          

                    if($Row['QUANTITA'] == 0)
                       $SomeIvaSuggerita = true;

                    $NuovaVoce->NatPagamVoce = $this->FGetCodeNaturaPagamento($Row['NATURA_PAGAMENTO']);
                  }
                } 
                else throw new Exception('Impossibile trovare le voci della nota');

                if($SomeIvaSuggerita)
                  $this->FAddEmptyIVA($Fattura);
            }


            protected function FPrepareAutofattura($Chiave, &$Fattura, $PDODBase)
            {
                  $SQLBody = "SELECT autofatture.*, 
                                     province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                                     nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                                     causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE
                                FROM autofatture
                                     LEFT OUTER JOIN province ON province.CHIAVE = autofatture.PROVINCIA_FATTURAZIONE
                                     LEFT OUTER JOIN nazioni ON nazioni.CHIAVE = autofatture.NAZIONE_EM_PIVA
                                     LEFT OUTER JOIN causali ON causali.CHIAVE = autofatture.CAUSALE
                               WHERE autofatture.CHIAVE = $Chiave";

                  if($Query = $PDODBase->query($SQLBody))
                  {
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {           
                      $Fattura->Configurazione->CedentePrestatore->Denominazione      = $Row['RAGIONE_SOCIALE'];
                      $Fattura->Configurazione->CedentePrestatore->PaeseEmPIVA        = $Row['SIGLIA_NAZIONE_EM_PIVA'];
                      $Fattura->Configurazione->CedentePrestatore->PartitaIVA         = $Row['PARTITA_IVA'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->Indirizzo    = $Row['INDIRIZZO_FATTURAZIONE'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->NumeroCivico = $Row['NR_CIVICO_FATTURAZIONE'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->CAP          = $Row['CAP_FATTURAZIONE'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->Comune       = $Row['COMUNE_FATTURAZIONE'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->Provincia    = $Row['TARGA_PROVINCIA_FATTURAZIONE'];
                      $Fattura->Configurazione->CedentePrestatore->Sede->Nazione      = $Row['SIGLIA_NAZIONE_EM_PIVA'];
                      $Fattura->Configurazione->CedentePrestatore->Contatti->EMail    = isset($Row['PEC'])? $Row['PEC'] : '';                
                      $Fattura->Configurazione->CedentePrestatore->RegimeFiscale      = $Row['REGIME_FISCALE'];

                      $Fattura->DatiGenerali->Data              = $Row['DATA'];
                      $Fattura->DatiGenerali->Numero            = 'A' . $Row['NUMERO'];

                      // SE AGGIUNGI UN TIPO RICORDARSI DI INSERIRE NELLA ZFATTURAELETTRONICA
                      // LE CONDIZIONI PER INSERIRE IL SOGGETTO EMITTENTE NEL XML
                      // AGGIUNGERE ANCHE AD "AcquisisciFattura" I TIPI CHE SONO NOTA DI CREDITO O FATTURA
                      switch($Row['TIPO_AUTOFATTURA'])
                      {
                        case TIPO_AUTOFATTURA_EXTRA             : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docIntegrazioneAutoFatturaAcquistoServiziEstero;
                                                                  break;
                        case TIPO_AUTOFATTURA_INTRO             : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docIntegrazioneAcquistoBeniIntracomunitari;
                                                                  break;
                        case TIPO_AUTOFATTURA_SAN_MARINO        : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docAutofatturaAcquistoBeniSanMarino;
                                                                  break;
                        case TIPO_AUTOFATTURA_ACQ_BENI_EX17     : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docIntegrazioneAutofatturaAcquistoBeniExArt17;
                                                                  break;
                        case TIPO_AUTOFATTURA_REG_INTEGR_FATT   : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docAutofatturaRegolarizzazioneIntegrazioneDellefatture;
                                                                  break;
                        case TIPO_AUTOFATTURA_SPLAFONAMENTO     : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docAutofatturaSplafonamento;
                                                                  break;
                        default                                 : $Fattura->DatiGenerali->Tipo = ID_FATT_TIPO_docIntegrazioneAutoFatturaAcquistoServiziEstero;
                                                                  break;
                      }

                      $Fattura->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];

                      $Fattura->ProgressivoTrasmissione = strtoupper(dechex($Row['CHIAVE']));

                      $Fattura->Ritenuta->Percentuale = $Row['RITENUTA'] / 10;
                      $Fattura->Ritenuta->Tipo        = $Row['TIPO_RITENUTA'];
                      array_push($Fattura->DatiGenerali->Causale, $Row['DESCRIZIONE_CAUSALE']);

                      $FattureCollegate = $Fattura->AddFattureCollegate();
                      $FattureCollegate->IdDocumento = $Row['NUMERO_DEL_FORNITORE'];
                      $FattureCollegate->Data        = $Row['DATA'];
                    }
                  }
                  else 
                  {
                    error_log($SQLBody);
                    throw new Exception('Impossibile caricare i dati delle Autofatture');
                  }

                  $TotaliAutofattura = TSystemInformation::GetTotaliAutofattura($PDODBase, $Chiave, true);

                  $Fattura->TotaleIvato           = $TotaliAutofattura[0]->TotaleIvato;
                  $Fattura->Ritenuta->Importo     = $TotaliAutofattura[0]->TotaleRitenuta;

                  for($i = 0; $i < count($TotaliAutofattura[0]->LsIva); $i++)
                    $Fattura->AddIVA($TotaliAutofattura[0]->LsIva[$i]->IVA, $TotaliAutofattura[0]->LsIva[$i]->SommaImponibile, $this->FGetCodeNaturaPagamento($TotaliAutofattura[0]->LsIva[$i]->NaturaPagamento));

                  $TotaliAutofattura = null;

                  $SQLBody = "SELECT voci_autofatture.*, 
                                     unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                                FROM voci_autofatture
                                     LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_autofatture.UNITA_DI_MISURA
                               WHERE voci_autofatture.ID_AUTOFATTURA = $Chiave
                               ORDER BY voci_autofatture.ORDINAMENTO";

                  $SomeIvaSuggerita = false;

                  if($Query = $PDODBase->query($SQLBody))
                  {
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {   
                      $NuovaVoce = $Fattura->Configurazione->AddVoce();
                      $NuovaVoce->Descrizione = ($Row['DESCRIZIONE']);
                      $NuovaVoce->Quantita = ($Row['QUANTITA']) / 100;
                      if($Row['IMPORTO_IVATO'] == 'T')
                        $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/(100 + ($Row['IVA'])/100);
                      else $NuovaVoce->PrezzoUnitario = ($Row['IMPORTO'])/ 100;
                      $NuovaVoce->IVA = $Row['QUANTITA'] == 0 ? $this->FIvaSuggerita : $Row['IVA'] / 100;
                      $NuovaVoce->Udm = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                      $NuovaVoce->AddScontoPerc(($Row['SCONTO']) / 100);   
                      if($Row['QUANTITA'] == 0)
                        $SomeIvaSuggerita = true;
                      if($Row['SCONTO'] != 0)
                        $NuovaVoce->AddScontoPerc(($Row['SCONTO']) / 100);   
                      
                      $NuovaVoce->NatPagamVoce = $this->FGetCodeNaturaPagamento($Row['NATURA_PAGAMENTO']);
                    }
                  } 
                  else 
                  {
                    error_log($SQLBody);
                    throw new Exception('Impossibile trovare le voci della nota');
                  } 

                  if($SomeIvaSuggerita)
                    $this->FAddEmptyIVA($Fattura);
            }
               
      }