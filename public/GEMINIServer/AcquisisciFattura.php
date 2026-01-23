<?php 
include_once 'Configurations.php';
include_once 'Definitions.php';
include_once 'SystemInformation.php';

include_once PATH_LIBRERIE . 'ZAdvQuery.php';
include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
include_once PATH_LIBRERIE . 'ZAdvQueryClient.php';
include_once PATH_LIBRERIE . 'ZGenericFunct.php';

const STRING_STYLE_NORMAL = '-';
const STRING_STYLE_UPPERCASE = 'U';
const STRING_STYLE_LOWERCASE = 'L'; 

class TMyFatturaPassiva
{
  public $Fornitore         = '';
  public $NumFattura        = '';
  public $IsNuovoFornitore  = false;

  function __construct($Fornitore, $NumFattura, $IsNuovoFornitore)
  {
    $this->Fornitore        = $Fornitore . '';      
    $this->NumFattura       = $NumFattura . '';
    $this->IsNuovoFornitore = $IsNuovoFornitore;  
  }       
}

class TAdvQueryAcquisisciFattura extends TAdvQuery
{

    public $FUltimaDataCaricata = '2024-01-01';
    public $FStileStinghe = STRING_STYLE_NORMAL;

    private function FIsAutoFattura($FatturaElettronica)
    {
      return ($FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docIntegrazioneAutoFatturaAcquistoServiziEstero ||
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docIntegrazioneAcquistoBeniIntracomunitari ||
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docAutofatturaAcquistoBeniSanMarino ||
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docIntegrazioneAutofatturaAcquistoBeniExArt17 ||
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docAutofatturaRegolarizzazioneIntegrazioneDellefatture || 
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docAutofatturaSplafonamento || 
              $FatturaElettronica->DatiGenerali->Tipo == ID_FATT_TIPO_docAutofatturaAcquistoBeniSanMarino);
    }

    private function FGetChiaveFornitore($PDODBase,$PartitaIVA,$CodiceFiscale)
    {
      $Result = -1;
      $Parametri = array();
      if(trim($PartitaIVA) != '') $Parametri['PARTITA_IVA'] = $PartitaIVA;
      if(trim($CodiceFiscale) != '') $Parametri['CODICE_FISCALE'] = $CodiceFiscale;
      $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                          'Fornitori', 
                                          'FindFornitore',
                                          'ChiaveFornitore', 
                                           $Parametri);
      //echo $SQLBody;
      $Query = $PDODBase->query($SQLBody); 
      if ($Row = $Query->fetch(PDO::FETCH_ASSOC))
         $Result = $Row["CHIAVE"];
      return $Result;
    }

    private function FGetChiaveFatturaPassiva($PDODBase,$ChiaveFornitore,$Numero)
    {
      $Result = -1;
      $Parametri = array();

      $SQLBody = "SELECT CHIAVE FROM fatture_passive 
                   WHERE NUMERO = " . $this->FPrepareParameterValue(trim($Numero),'#') .
                 "   AND ID_FORNITORE = " . $this->FPrepareParameterValue($ChiaveFornitore,':');

      $Query = $PDODBase->query($SQLBody); 
      if ($Row = $Query->fetch(PDO::FETCH_ASSOC))
         $Result = $Row["CHIAVE"];
      return $Result;
    }

    private function FRegistraRata($PDODBase,$ChiaveFatturaPassiva,$DataScadenzaPagamento,$ImportoPagamento)
    {
         $Parametri = array();
         $Parametri['CHIAVE_FATTURA_PASSIVA']     = $ChiaveFatturaPassiva;
         $Parametri['DATA_SCADENZA']              = $DataScadenzaPagamento;
         $Parametri['IMPORTO']                    = abs((int)($ImportoPagamento * 1000));
         $Parametri['NO_PRIMA_NOTA']              = 'F'; 

         $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                             'FatturePassive', 
                                             'EditSQL',
                                              'InserisciRateFatturaPassiva', 
                                              $Parametri,[2]);

         $PDODBase->query($SQLBody);
    }

    private function FIsFattura($TipoDoc)
    {
      return in_array($TipoDoc,array(
                                        ID_FATT_TIPO_docfattura,
                                        ID_FATT_TIPO_docAccontoSuFattura,
                                        ID_FATT_TIPO_docFatturaDifferitaArt21Comma4LetteraA,
                                        ID_FATT_TIPO_docFatturaDifferitaArt21Comma4LetteraB,
                                        ID_FATT_TIPO_docParcella,
                                        ID_FATT_TIPO_docIntegrazioneAcquistoBeniIntracomunitari,
                                        ID_FATT_TIPO_docIntegrazioneAutofatturaAcquistoBeniExArt17,
                                        ID_FATT_TIPO_docAutofatturaAcquistoBeniSanMarino,
                                        ID_FATT_TIPO_docAutofatturaRegolarizzazioneIntegrazioneDellefatture,
                                        ID_FATT_TIPO_docAutofatturaSplafonamento,
                                        ID_FATT_TIPO_docIntegrazioneAutoFatturaAcquistoServiziEstero
                                    ));
    }

    public function GetSommaIvaAliquota(&$Riepilogo, $Aliquota, $Imponibile, $Imposta, $Arrotondamento = 0) 
    {
        if(!isset($Riepilogo[$Aliquota])) 
        {
            $Riepilogo[$Aliquota] = 
            [
                'IMPONIBILE' => 0,
                'IMPOSTA' => 0,
                'ARROTONDAMENTO' => 0
            ];
        }
    
        $Riepilogo[$Aliquota]['IMPONIBILE'] += $Imponibile;
        $Riepilogo[$Aliquota]['IMPOSTA'] += $Imposta;
        $Riepilogo[$Aliquota]['ARROTONDAMENTO'] += $Arrotondamento;
    }
    

    public function FAcquisisciFattura($PDODBase, &$JSONAnswer, $XMLFattura, $NomeFile)
    {
        $FatturaElettronica = new TFatturaElettronica();
        $FatturaElettronica->AcquisisciFatturaElettronica($XMLFattura);

        $ChiaveFornitore = $this->FGetChiaveFornitore($PDODBase,
                                                    $FatturaElettronica->Configurazione->CedentePrestatore->PartitaIVA,
                                                    $FatturaElettronica->Configurazione->CedentePrestatore->CodiceFiscale);

                        
        $NuovoFornitore = $ChiaveFornitore == -1;
        if($NuovoFornitore)
        $ChiaveFornitore = $this->FGetNewKey($PDODBase,GEN_CHIAVI);

        $ChiaveFatturaPassiva = $this->FGetChiaveFatturaPassiva($PDODBase,
                                                                $ChiaveFornitore,
                                                                $FatturaElettronica->DatiGenerali->Numero);
                                                            
        $NuovaFatturaPassiva = $ChiaveFatturaPassiva == -1;
        if($NuovaFatturaPassiva)
        {
            $ChiaveFatturaPassiva = $this->FGetNewKey($PDODBase,GEN_CHIAVI);
            $JSONAnswer->ChiaveFattura = $ChiaveFatturaPassiva;
        }
    

        $ObjFP = new TMyFatturaPassiva( $FatturaElettronica->Configurazione->CedentePrestatore->Denominazione,
                                        $FatturaElettronica->DatiGenerali->Numero,
                                        $NuovoFornitore );
        
        array_push($JSONAnswer->LsFatturePassive, $ObjFP);

        $PDODBase->beginTransaction();
        try
        {
            // Registrazione fornitore
            $RagioneSocialeFornitore             = $FatturaElettronica->Configurazione->CedentePrestatore->PersonaFisica ? 
                                                    $FatturaElettronica->Configurazione->CedentePrestatore->Cognome . ' ' . $FatturaElettronica->Configurazione->CedentePrestatore->Denominazione :
                                                    $FatturaElettronica->Configurazione->CedentePrestatore->Denominazione;
            $Parametri = array();
            $Parametri['CHIAVE_FORNITORE']       = $ChiaveFornitore;
            $Parametri['RAGIONE_SOCIALE']        = $RagioneSocialeFornitore;
            $Parametri['PARTITA_IVA']            = $FatturaElettronica->Configurazione->CedentePrestatore->PartitaIVA;
            $Parametri['EMITTENTE_PIVA']         = $FatturaElettronica->Configurazione->CedentePrestatore->PaeseEmPIVA; 
            $Parametri['CODICE_FISCALE']         = $FatturaElettronica->Configurazione->CedentePrestatore->CodiceFiscale;
            $Parametri['INDIRIZZO_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Indirizzo;
            $Parametri['NR_CIVICO_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->NumeroCivico; 
            $Parametri['PROVINCIA_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Provincia;
            $Parametri['COMUNE_FATTURAZIONE']    = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Comune;
            $Parametri['CAP_FATTURAZIONE']       = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->CAP;

            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'Fornitori', 
                                                'EditSQL',
                                                $NuovoFornitore ? 'NuovoFornitoreImportazione' : 'ModificaFornitoreImportazione', 
                                                $Parametri);

            $PDODBase->query($SQLBody);

            // Registrazione fatture passive
            $Parametri = array();
            $Parametri['CHIAVE_FATTURA']         = $ChiaveFatturaPassiva;
            $Parametri['ID_FORNITORE']           = $ChiaveFornitore;

            if($this->FIsAutoFattura($FatturaElettronica) && count($FatturaElettronica->DatiFattureCollegate) != 0)
                $Parametri['NUMERO'] = $FatturaElettronica->DatiFattureCollegate[0]->IdDocumento;
            else $Parametri['NUMERO'] = $FatturaElettronica->DatiGenerali->Numero;

            $Parametri['IS_FATTURA']             = $this->FIsFattura($FatturaElettronica->DatiGenerali->Tipo) ? 'T' : 'F';
            $Parametri['DATA']                   = $FatturaElettronica->DatiGenerali->Data;
            $Parametri['RAGIONE_SOCIALE']        = $RagioneSocialeFornitore;
            $Parametri['INDIRIZZO_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Indirizzo;
            $Parametri['NR_CIVICO_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->NumeroCivico; 
            $Parametri['PROVINCIA_FATTURAZIONE'] = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Provincia;
            $Parametri['COMUNE_FATTURAZIONE']    = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->Comune;
            $Parametri['CAP_FATTURAZIONE']       = $FatturaElettronica->Configurazione->CedentePrestatore->Sede->CAP;
            $Parametri['PARTITA_IVA']            = $FatturaElettronica->Configurazione->CedentePrestatore->PartitaIVA;
            $Parametri['CODICE_FISCALE']         = $FatturaElettronica->Configurazione->CedentePrestatore->CodiceFiscale;
            
            $Parametri['TOTALE_FATTURA']         = abs((int)($FatturaElettronica->DatiGenerali->ImportoTotaleDocumento * 100));
            
            $Causale = '';
            foreach($FatturaElettronica->DatiGenerali->Causale as $RigaCausale)
            {
                if($Causale != '') $Causale .= ' - ';
                    $Causale .= $RigaCausale;
            }
            $Parametri['OGGETTO']                = $Causale;
            $Parametri['NAZIONE_EM_PIVA']        = $FatturaElettronica->Configurazione->CedentePrestatore->PaeseEmPIVA; 

            $TotalePerAutofattura = 0;
            
            if($this->FIsAutoFattura($FatturaElettronica))
            {
                foreach($FatturaElettronica->Configurazione->FLsVoci as $SingolaVoce)
                {
                    $TotalePerAutofattura += (int)($SingolaVoce->PrezzoTotale * 1000);
                }

                $Parametri['TOTALE_FATTURA'] = $TotalePerAutofattura / 10;
            }

            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'FatturePassive', 
                                                'EditSQL',
                                                $NuovaFatturaPassiva ? 'InserisciDaImportazione' : 'ModificaDaImportazione', 
                                                $Parametri);

            $PDODBase->query($SQLBody);

            if($this->FUltimaDataCaricata < trim($FatturaElettronica->DatiGenerali->Data))
            $this->FUltimaDataCaricata = trim($FatturaElettronica->DatiGenerali->Data);

            $PDODBase->query("DELETE FROM voci_fatture_passive WHERE ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva");

            // Registrazione voci fatture
            foreach($FatturaElettronica->Configurazione->FLsVoci as $SingolaVoce)
            {
            $Parametri = array();
            $SQLBody = "SELECT codici_fornitore.ID_PRODOTTO
                            FROM codici_fornitore
                        WHERE codici_fornitore.ID_FORNITORE    = $ChiaveFornitore
                            AND codici_fornitore.CODICE_PRODOTTO = " . "'$SingolaVoce->CodiceArticolo'";

            if($Query = $PDODBase->query($SQLBody))
            {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                $Parametri['ID_PRODOTTO'] = $Row['ID_PRODOTTO'];
                }
            }              
            $Parametri['CHIAVE_FATTURA_PASSIVA'] = $ChiaveFatturaPassiva;
            $Parametri['NUMERO_LINEA']           = $SingolaVoce->NumeroLinea;
            $Parametri['QUANTITA']               = $SingolaVoce->Quantita;
            $Parametri['BENE_SERVIZIO']          = $SingolaVoce->Descrizione;
            $Parametri['PREZZO']                 = (int)($SingolaVoce->PrezzoUnitario * 1000);
            $Parametri['CODICE_ARTICOLO']        = $SingolaVoce->CodiceArticolo;
            $Parametri['SCONTO1']                = 0;
            $Parametri['SCONTO2']                = 0;
            $Parametri['SCONTO3']                = 0;
            for($i = 0; $i < count($SingolaVoce->ScontoPerc); $i++)
                $Parametri['SCONTO' . ($i + 1)] = (int)($SingolaVoce->ScontoPerc[$i] * 100);

            $Parametri['TOTALE']                 = (int)($SingolaVoce->PrezzoTotale * 1000);

            if($this->FIsAutoFattura($FatturaElettronica))
                $Parametri['IVA'] = 0;
            else 
                $Parametri['IVA'] = (int)($SingolaVoce->IVA * 100);

            $Parametri['NOTE'] = '';

            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'FatturePassive', 
                                                'EditSQL',
                                                'InserisciVoceFatturaPassiva', 
                                                $Parametri,[2]);

            $PDODBase->query($SQLBody);
            }

            $PDODBase->query("DELETE FROM rate_fatture_passive WHERE ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva");

            foreach($FatturaElettronica->DatiPagamento->FlsDettagliPagamento as $SingolaRata)
                $this->FRegistraRata($PDODBase,
                                    $ChiaveFatturaPassiva,
                                    $SingolaRata->DataScadenzaPagamento == '' ? $FatturaElettronica->DatiGenerali->Data : $SingolaRata->DataScadenzaPagamento,
                                    $SingolaRata->ImportoPagamento);
            
            if(count($FatturaElettronica->DatiPagamento->FlsDettagliPagamento) == 0)
                $this->FRegistraRata($PDODBase,
                                    $ChiaveFatturaPassiva,
                                    $FatturaElettronica->DatiGenerali->Data,
                                    $this->FIsAutoFattura($FatturaElettronica)? 
                                        $TotalePerAutofattura / 1000 : 
                                        $FatturaElettronica->DatiGenerali->ImportoTotaleDocumento);

            $PDODBase->query("DELETE FROM ritenute_fatture_passive WHERE ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva");

            if(isset($FatturaElettronica->DatiGenerali->DatiRitenutaImportazione))
            {
                foreach($FatturaElettronica->DatiGenerali->DatiRitenutaImportazione as $SingoloElemento)
                {
                    $Parametri = array();
                    $Parametri['CHIAVE_FATTURA_PASSIVA']        = $ChiaveFatturaPassiva;
                    $Parametri['TIPO']                          = $SingoloElemento->Tipo;
                    $Parametri['ALIQUOTA']                      = (int)($SingoloElemento->Percentuale * 100);
                    $Parametri['IMPORTO']                       = abs((int)($SingoloElemento->Importo * 100));

                    $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                        'FatturePassive', 
                                                        'EditSQL',
                                                        'InserisciRitenute', 
                                                        $Parametri,[2]);
                    $PDODBase->query($SQLBody);
                }
            }
            
            if(isset($FatturaElettronica->DatiGenerali->DatiFiscali))
            {
                $RiepilogoAliquote = [];

                if($this->FIsAutoFattura($FatturaElettronica))
                {
                    foreach($FatturaElettronica->DatiGenerali->DatiFiscali as $SingoloElemento) 
                    {
                        $Aliquota       = 0;
                        $Imponibile     = (float)$SingoloElemento->ImponibileImporto;
                        $Imposta        = 0;
                        $Arrotondamento = 0;
                
                        $this->GetSommaIvaAliquota($RiepilogoAliquote, $Aliquota, $Imponibile, $Imposta, $Arrotondamento);
                    }
                }
                else
                {
                    foreach($FatturaElettronica->DatiGenerali->DatiFiscali as $SingoloElemento) 
                    {
                        $Aliquota       = $SingoloElemento->AliquotaIVA;
                        $Imponibile     = (float)$SingoloElemento->ImponibileImporto;
                        $Imposta        = (float)$SingoloElemento->Imposta;
                        $Arrotondamento = isset($SingoloElemento->Arrotondamento) ? (float)$SingoloElemento->Arrotondamento : 0;
                
                        $this->GetSommaIvaAliquota($RiepilogoAliquote, $Aliquota, $Imponibile, $Imposta, $Arrotondamento);
                    }
                }

                foreach($RiepilogoAliquote as $Aliquota => $Valori)
                {
                    $Parametri = array();
                    $Parametri['CHIAVE_FATTURA_PASSIVA']       = $ChiaveFatturaPassiva;
                    $Parametri['IVA']                          = $Aliquota;
                    $Parametri['ARROTONDAMENTO']               = $Valori['ARROTONDAMENTO'];
                    $Parametri['IMPONIBILE']                   = (int)($Valori['IMPONIBILE'] * 100);
                    $Parametri['IMPOSTA']                      = (int)($Valori['IMPOSTA'] * 100);

                    $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                        'FatturePassive', 
                                                        'EditSQL',
                                                        'InserisciRiepiloghiAliquote', 
                                                        $Parametri,[2]);

                    $PDODBase->query($SQLBody);
                }
            }

            if(isset($FatturaElettronica->DatiGenerali->DatiCassaPrevidenziale))
            {
                foreach($FatturaElettronica->DatiGenerali->DatiCassaPrevidenziale as $SingoloElemento)
                {
                    $Parametri = array();
                    $Parametri['CHIAVE_FATTURA_PASSIVA']       = $ChiaveFatturaPassiva;
                    $Parametri['ALIQUOTA']                     = $SingoloElemento->AliquotaIVA;
                    $Parametri['IMPOSTA']                      = $SingoloElemento->ImponibileCassa;
                    $Parametri['IMPORTO']                      = $SingoloElemento->ImportoContributoCassa;
                    $Parametri['TIPO']                         = $SingoloElemento->TipoCassa;
                    $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                        'FatturePassive', 
                                                        'EditSQL',
                                                        'InserisciCassePrevidenziali', 
                                                        $Parametri,[2]);

                    $PDODBase->query($SQLBody);
                }
            }
            
            if(isset($FatturaElettronica->DatiGenerali->AllegatiImportazione))
            {
                foreach($FatturaElettronica->DatiGenerali->AllegatiImportazione as $SingoloElemento)
                {
                    $Parametri = array();
                    $MIME = 'data:' . GetMimeType($SingoloElemento->FileName) . ';';

                    $OggettoBlob = new TAdvQueryOggettoBlob(OPERAZIONE_BLOB_INSERIMENTO, 
                                                            $SingoloElemento->Base64,
                                                            $MIME,
                                                            '',
                                                            'ALLEGATO_FATTURA_PASSIVA');
 
                    $Parametri['ID_FATTURA_PASSIVA']         = $ChiaveFatturaPassiva;
                    $Parametri['ALLEGATO']                   = $OggettoBlob;
                    $Parametri['DESCRIZIONE']                = $SingoloElemento->FileName;

                    $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                        'FatturePassive', 
                                                        'EditSQL',
                                                        'InserisciAllegato', 
                                                        $Parametri,[2]);
                    // file_put_contents('d:\ppp\query.sql',$SQLBody);

                    $PDODBase->query($SQLBody);
                };
            }

            $Parametri = array();
            $MIME = 'data:' . GetMimeType($NomeFile) . ';';

            $OggettoBlob = new TAdvQueryOggettoBlob(OPERAZIONE_BLOB_INSERIMENTO, 
                                                     base64_encode($XMLFattura),
                                                     $MIME,
                                                     $NomeFile,
                                                    'ALLEGATO_XML');

            $Parametri['NOME_FILE']                  = $NomeFile;
            $Parametri['ID_FATTURA_PASSIVA']         = $ChiaveFatturaPassiva;
            $Parametri['ALLEGATO_XML']               = $OggettoBlob;


            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'FatturePassive', 
                                                'EditSQL',
                                                'InserisciXmlFatturaPassiva', 
                                                $Parametri,[2]);
            // file_put_contents('d:\ppp\query.sql',$SQLBody);

            if(!($Query = $PDODBase->query($SQLBody)))
            throw new Exception('Impossibile raccogliere dal database i nomi file delle fatture già importate');

            $PDODBase->commit();
        }
        catch(Exception $e)
        {
            $PDODBase->rollBack();
            $this->FUltimaDataCaricata = null;
            throw $e;
        }


    }
}