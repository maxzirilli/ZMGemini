<?php

include_once PATH_LIBRERIE . 'ZDateFunct.php';
include_once PATH_LIBRERIE . 'ZMySond.php';
include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';

const PERCENTUALE_IVA = 22;

const LUGLIO_2024 = 202407;

const NOME_CARTELLA_FILE_TEMP = PATH_ACCESSORIE . 'TempImage';
const NOME_CARTELLA_MODELLI_TEMP = PATH_ACCESSORIE . 'ModelliStampe';

const TIPOLOGIA_MODIFICA_CLIENTE        = 'C';
const TIPOLOGIA_MODIFICA_AMMINISTRATORE = 'A';
const TIPOLOGIA_MODIFICA_FILIALE        = 'F';

const TIPO_AUTOFATTURA_INTRO            = 'I';
const TIPO_AUTOFATTURA_EXTRA            = 'E';
const TIPO_AUTOFATTURA_SAN_MARINO       = 'S';
const TIPO_AUTOFATTURA_ACQ_BENI_EX17    = 'A';
const TIPO_AUTOFATTURA_REG_INTEGR_FATT  = 'F';
const TIPO_AUTOFATTURA_SPLAFONAMENTO    = 'P';

const STATO_PREVENTIVO_CONFERMATO = 'C';
const STATO_PREVENTIVO_SOSPESO    = 'S';
const STATO_PREVENTIVO_RIFIUTATO  = 'R';

const MODALITA_INCASSO_CONTANTE      = 'C';
const MODALITA_INCASSO_ASSEGNI       = 'A';
const MODALITA_INCASSO_POS           = 'P';
const MODALITA_INCASSO_NON_INCASSATO = 'N';

const ESIGIBILITA_SPLIT_PAYMENT                = 'S';

const TIPO_FATTURAZIONE_SEMESTRALE = 'S';
const TIPO_FATTURAZIONE_ANNUALE    = 'A';

const FATTURAZIONE_EFFETTUATA_PRIMO_SEMESTRE   = 'P';
const FATTURAZIONE_EFFETTUATA_SECONDO_SEMESTRE = 'S';
const FATTURAZIONE_EFFETTUATA_ANNUALE          = 'A';

const NUMERO_MESI_SEMESTRE       = 6;
const NUMERO_MESI_ANNUALE        = 12;

const BOLLO_PAGATO_DAL_CLIENTE   = 'C';

const GRANDEZZA_CONTI_PRIMA_NOTA   = 32;
const GRANDEZZA_MINIMA_DESCRIZIONE = 40;

const TABLE_LOG_EMAIL          = 'log_email';
const TABLE_ALLEGATI_LOG_EMAIL = 'allegati_log_email';

const LOG_GENERATORE_ANNUALE          = 'A';
const LOG_GENERATORE_PRIMO_SEMESTRE   = 'P';
const LOG_GENERATORE_SECONDO_SEMESTRE = 'S';

const TIPO_DOCUMENTO_FATTURA          = 'F';
const TIPO_DOCUMENTO_INTERNO          = 'I';
const TIPO_DOCUMENTO_NOTA             = 'N';
const TIPO_DOCUMENTO_PREVENTIVO       = 'P';
const TIPO_DOCUMENTO_PREVENTIVO_MULTI = 'M';
const TIPO_DOCUMENTO_AUTOFATTURA      = 'A';

const SCELTA_CHECKLIST_CONFERMATO      = 'C';
const SCELTA_CHECKLIST_NON_CONFERMATO  = 'N';
const SCELTA_CHECKLIST_NON_APPLICABILE = 'A';
                                
const STATO_INVIO_MANUALE_FATTURA_IN_COMPILAZIONE = 'C';
const STATO_INVIO_MANUALE_FATTURA_DA_ZIPPARE      = 'D';
const STATO_INVIO_MANUALE_FATTURA_INVIATO         = 'I';
const STATO_INVIO_MANUALE_FATTURA_CONFERMATO      = 'Z';

class TTotaliNota
{
   public $TotaleImponibile = null;
   public $TotaleIva = null;
   public $TotaleIvato = null;
   public $TotaleRitenuta = null;
   public $Totale = null;
   public $SommaImponibile = null;
   public $SommaIva = null;
   public $Ritenuta = null;
   public $IdNota = null;
   public $EsigibilitaIVA = 'I';
   public $LsIva = null;
   public $NettoAPagare = null;
   public $Rate         = null;
}

class TTotaliFattura
{
   public $TotaleImponibile      = null;
   public $TotaleIva             = null;
   public $TotaleIvato           = null;
   public $TotaleRitenuta        = null;
   public $Totale                = null;
   public $SommaImponibile       = null;
   public $SommaIva              = null;
   public $Ritenuta              = null;
   public $CostoBollo            = null;
   public $LsIva                 = array();
   public $IdFattura             = null;
   public $IdCliente             = null;
   public $Anno                  =  null;
   public $Numero                = null;
   public $IdentificativoFiscale = null;
   public $DataFattura           = null;
   public $TotalePagato          = null;
   public $TotalePagatoXAnno     = array();
   public $TotaliDaIncassare     = null;
   public $NettoAPagare          = null;
   public $SommaRitenuta         = 0;
   public $SommaRitenutaXAnno    = null;
   public $EsigibilitaIVA        = 'I';
   public $Rate                  = array();
   public $NumeroAnticipo        = null;
   public $NettoAPagareSenzaAnticipo = null;
   public $IsRitenutaDiAcconto   = false;
   public $IsRitenutaCertificata = false;
   public $TotaleRitenutaDaBanca = array();
   public $DataRitenutaCertificata = null;
   public $ContieneAlmenoUnaRataConRdA = false;
}

class TTotaliFatturaPassiva
{
   public $TotaleImponibile = null;
   public $TotaleIva = null;
   public $TotaleIvato = null;
   public $TotaleRitenuta = null;
   public $Totale = null;
   public $SommaImponibile = null;
   public $SommaIva = null;
   public $Ritenuta = null;
   public $LsIva = array();
   public $IdFattura = null;
   public $IdFornitore = null;
   public $Anno =  null;
   public $Numero = null;
   public $IdentificativoFiscale = null;
   public $DataFattura = null;
   public $TotalePagato = null;
   public $TotalePagatoXAnno = array();
   public $TotaliDaIncassare = null;
   public $NettoAPagare = null;
   public $SommaRitenutaXAnno = null;
   public $EsigibilitaIVA = 'I';
}

class TObjectAnno
{
   public $Anno = null;
   public $IdentificativoFiscale = null;
   public $SommaRitenuta = null;
   public $SommaRitenutaDiFattureNonPagateMaCertificate = null;
   public $SommaRitenutaDiFattureRDA = null;
   public $RitenutaCertificata = null;
}

class TObjectCliente
{
   public $IdCliente      = null;
   public $RagioneSociale = null;
   public $Ritenute       = null;
   public $CodiceCliente  = null;
}

class TTotaliPreventivo
{
   public $TotaleImponibile = 0;
   public $TotaleIva = 0;
   public $TotaleIvato = 0;
   public $TotaleRitenuta = 0;
   public $Totale = 0;
   public $SommaImponibile = 0;
   public $SommaIva = 0;
   public $Ritenuta = 0;
   public $IdPreventivo = null;
   public $LsIva  = array();
   public $NettoAPagare = null;
   public $EsigibilitaIVA = 'I';
}

class TTotaliPreventivoMultiparametrico
{
   public $TotaleImponibile = 0;
   public $TotaleIva = 0;
   public $TotaleIvato = 0;
   public $TotaleRitenuta = 0;
   public $Totale = 0;
   public $SommaImponibile = 0;
   public $SommaIva = 0;
   public $Ritenuta = 0;
   public $IdPreventivo = null;
   public $LsIva  = array();
   public $NettoAPagare = null;
   public $EsigibilitaIVA = 'I';
}


class TTotaliAutofattura
{
   public $TotaleImponibile = 0;
   public $TotaleIva        = 0;
   public $TotaleIvato      = 0;
   public $TotaleRitenuta   = 0;
   public $Totale           = 0;
   public $SommaImponibile  = 0;
   public $SommaIva         = 0;
   public $Ritenuta         = 0;
   public $IdAutofattura    = null;
   public $LsIva            = array();
   public $NettoAPagare     = null;
   public $EsigibilitaIVA   = 'I';
}

class TObjectIva
{
   public $IVA             = null;
   public $SommaImponibile = null; 
   public $NaturaPagamento = null; 
}

class TSystemInformation 
{
   public $ListaNaturaPagamento = array();

   public static function GetDescrizioneNaturaPagamento($ChiaveNatura)
   {
      $ListaNaturaPagamento = GetLsNaturaPagamento();

      for($i = 0; $i < count($ListaNaturaPagamento); $i++)
         if($ListaNaturaPagamento[$i]->Id == $ChiaveNatura)
            return 'escl.[' . $ListaNaturaPagamento[$i]->Descrizione . ']';
   }

   public static function CreazioneMySond($PDODBase)
   {
      $MySond = null;

      $SQLBody = "SELECT *
                    FROM impostazioni_amministrazione";

      if($Query = $PDODBase->query($SQLBody))
         if($Row = $Query->fetch(PDO::FETCH_ASSOC))
            $MySond = new TMySond($Row['USERNAME_MY_SOND'], $Row['PASSWORD_MY_SOND'], $Row['COD_AZIENDA_MY_SOND'],dirname(PATH_LOG));
      return $MySond;
   }

   public static function GetIvaSuggerita($PDODBase)
   {
      $SQLBody = "SELECT IVA_SUGGERITA
                    FROM impostazioni";

      if($Query = $PDODBase->query($SQLBody))
         if($Row = $Query->fetch(PDO::FETCH_ASSOC))
            return $Row['IVA_SUGGERITA'] / 10;
      return 0;
   }

   public static function GetDescrizioneTipoVisite($Periodicita)
   {
      switch($Periodicita)
      {
         case PERIODICITA_VISITE_MENSILE     : return 'Mensile';    
                                               break;
         case PERIODICITA_VISITE_BIMESTRALE  : return 'Bimestrale';
                                               break; 
         case PERIODICITA_VISITE_TRIMESTRALE : return 'Trimestrale';
                                               break;
         case PERIODICITA_VISITE_SEMESTRALE  : return 'Semestrale';
                                               break;
         default                             : return '';
                                               break;
      }
   }

   public static function GetDescrizioneMesiPrimaVisita($PrimaVisita)
   {
      $StringaMesi = '';

      if(isset($PrimaVisita))
      {
        $VisitaSuccessiva = $PrimaVisita + NUMERO_MESI_SEMESTRE;
        if($VisitaSuccessiva > NUMERO_MESI_ANNUALE)
          $VisitaSuccessiva = $VisitaSuccessiva - NUMERO_MESI_ANNUALE;

        if($VisitaSuccessiva > $PrimaVisita)
          $StringaMesi = LISTA_MESI_ESTESA[$PrimaVisita - 1 ] . ' - ' .LISTA_MESI_ESTESA[$VisitaSuccessiva - 1];
        else $StringaMesi = LISTA_MESI_ESTESA[$VisitaSuccessiva - 1] . ' - ' .LISTA_MESI_ESTESA[$PrimaVisita - 1];
      }
      
      return $StringaMesi;
   }

   public static function GetTotaliNota($PDODBase, $IdNoteDiCredito, $CalcoloNaturaPagamento = false)
   {
      $IdLastNota = -1;
      $TotaliNota = null;
      $Result = array();
      $SQLBody = "SELECT voci_note_di_credito.*, 
                         note_di_credito.RITENUTA,
                         note_di_credito.ESIGIBILITA_IVA
                    FROM voci_note_di_credito
                         LEFT OUTER JOIN note_di_credito ON  note_di_credito.CHIAVE = voci_note_di_credito.ID_NOTA_DI_CREDITO" .
                 " WHERE voci_note_di_credito.ID_NOTA_DI_CREDITO " . (is_int($IdNoteDiCredito) ? " = $IdNoteDiCredito" : " IN ($IdNoteDiCredito)") .
              " ORDER BY voci_note_di_credito.ID_NOTA_DI_CREDITO DESC";

      if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            if($Row['ID_NOTA_DI_CREDITO'] != $IdLastNota)
            {
               if($IdLastNota != -1)
                  self::FCompilaTotale($TotaliNota);
               $TotaliNota = new TTotaliNota();
               $TotaliNota->TotaleImponibile = 0;
               $TotaliNota->TotaleIva = 0;
               $TotaliNota->TotaleIvato = 0;
               $TotaliNota->TotaleRitenuta = 0;
               $TotaliNota->Totale = 0;
               $TotaliNota->Rate = array();
               $TotaliNota->SommaImponibile = 0;
               $TotaliNota->SommaIva = 0;
               $TotaliNota->Ritenuta = 0;
               $TotaliNota->IdNota = intval($Row['ID_NOTA_DI_CREDITO']);
               $TotaliNota->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
               $TotaliNota->LsIva = array();
               array_push($Result,$TotaliNota);
               $IdLastNota = $Row['ID_NOTA_DI_CREDITO'];
            }
            if($Row['IMPORTO_IVATO'] == 'T')
            {
               $Importo = ($Row['IMPORTO']/100) * 100 / (($Row['IVA']/100) + 100);
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA'] / 100, $TotaliNota->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Importo - ($Importo * ($Row['SCONTO']/100) / 100)));
               if($Row['RITENUTA'])
                  $TotaliNota->Ritenuta = $Row['RITENUTA'] / 10;
            }
            else
            {
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA'] / 100, $TotaliNota->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Row['IMPORTO']/100 - ($Row['IMPORTO']/100 * $Row['SCONTO']/100 / 100)));
               if($Row['RITENUTA'])
                  $TotaliNota->Ritenuta = $Row['RITENUTA']/10;
            }
         }
      }

      if($IdLastNota != -1)
         self::FCompilaTotale($TotaliNota);
      
      return $Result;
   }

   private static function GetIva($IVA, &$LsIva, $CalcoloNaturaPagamento = false, $NaturaPagamento = null)
   {
      for($i = 0; $i < count($LsIva); $i++)
      {
         if($LsIva[$i]->IVA == $IVA)
         {
            if($CalcoloNaturaPagamento && $IVA == 0)
            {
               if($LsIva[$i]->NaturaPagamento == $NaturaPagamento)
                  return $LsIva[$i];
            }
            else return $LsIva[$i];
         }
      }

      $Result                  = new TObjectIva();
      $Result->IVA             = $IVA;
      $Result->SommaImponibile = 0;
      $Result->NaturaPagamento = $CalcoloNaturaPagamento? $NaturaPagamento : null;
      array_push($LsIva, $Result);
      return $Result;
   }

   public static function GetTotaliAutofattura($PDODBase, $IdPreventivi, $CalcoloNaturaPagamento = false)
   {
      $IdLastAutofattura = -1;
      $TotaliAutofattura = null;
      $Result = array();
      $SQLBody = "SELECT voci_autofatture.*, 
                         autofatture.RITENUTA, 
                         autofatture.ESIGIBILITA_IVA
                    FROM voci_autofatture
                         LEFT OUTER JOIN autofatture ON autofatture.CHIAVE = voci_autofatture.ID_AUTOFATTURA" .
                 " WHERE voci_autofatture.ID_AUTOFATTURA " . (is_int($IdPreventivi) ? " = $IdPreventivi" : " IN ($IdPreventivi)") .
                 " ORDER BY voci_autofatture.ID_AUTOFATTURA DESC";

     if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            if($Row['ID_AUTOFATTURA'] != $IdLastAutofattura)
            {
               if($IdLastAutofattura != -1)
                  self::FCompilaTotale($TotaliAutofattura);
               $TotaliAutofattura = new TTotaliAutofattura();
               $TotaliAutofattura->TotaleImponibile = 0;
               $TotaliAutofattura->TotaleIva = 0;
               $TotaliAutofattura->TotaleIvato = 0;
               $TotaliAutofattura->TotaleRitenuta = 0;
               $TotaliAutofattura->Totale = 0;
               $TotaliAutofattura->SommaImponibile = 0;
               $TotaliAutofattura->SommaIva = 0;
               $TotaliAutofattura->Ritenuta = 0;
               $TotaliAutofattura->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
               $TotaliAutofattura->IdAutofattura = intval($Row['ID_AUTOFATTURA']);
               $TotaliAutofattura->LsIva = array();
	        			   array_push($Result,$TotaliAutofattura);
               $IdLastAutofattura = $Row['ID_AUTOFATTURA'];
            }
            if($Row['IMPORTO_IVATO'] == 'T')
            {
               $Importo = ($Row['IMPORTO']/100) * 100 / (($Row['IVA']/100) + 100);
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliAutofattura->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Importo - ($Importo * ($Row['SCONTO']/100) / 100)));
               if($Row['RITENUTA'])
                  $TotaliAutofattura->Ritenuta = $Row['RITENUTA'] / 10;
            }
            else
            {
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliAutofattura->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Row['IMPORTO']/100 - ($Row['IMPORTO']/100 * $Row['SCONTO']/100 / 100)));
               if($Row['RITENUTA'])
                  $TotaliAutofattura->Ritenuta = $Row['RITENUTA']/10;
            }
         }
      }
      if($IdLastAutofattura != -1)
         self::FCompilaTotale($TotaliAutofattura);

      return $Result;
   } 

   public static function GetTotaliPreventivo($PDODBase, $IdPreventivi, $CalcoloNaturaPagamento = false)
   {
      $IdLastPreventivo = -1;
      $TotaliPreventivo = null;
      $Result = array();
      $SQLBody = "SELECT voci_preventivi.*, 
                         preventivi.RITENUTA, 
                         preventivi.ESIGIBILITA_IVA
                    FROM voci_preventivi
                    LEFT OUTER JOIN preventivi ON  preventivi.CHIAVE = voci_preventivi.ID_PREVENTIVO" .
                 " WHERE voci_preventivi.ID_PREVENTIVO " . (is_int($IdPreventivi) ? " = $IdPreventivi" : " IN ($IdPreventivi)") .
                 " ORDER BY voci_preventivi.ID_PREVENTIVO DESC";
     if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            if($Row['ID_PREVENTIVO'] != $IdLastPreventivo)
            {
               if($IdLastPreventivo != -1)
                  self::FCompilaTotale($TotaliPreventivo);
               $TotaliPreventivo = new TTotaliPreventivo();
               $TotaliPreventivo->TotaleImponibile = 0;
               $TotaliPreventivo->TotaleIva = 0;
               $TotaliPreventivo->TotaleIvato = 0;
               $TotaliPreventivo->TotaleRitenuta = 0;
               $TotaliPreventivo->Totale = 0;
               $TotaliPreventivo->SommaImponibile = 0;
               $TotaliPreventivo->SommaIva = 0;
               $TotaliPreventivo->Ritenuta = 0;
               $TotaliPreventivo->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
               $TotaliPreventivo->IdPreventivo = intval($Row['ID_PREVENTIVO']);
               $TotaliPreventivo->LsIva = array();
	        			   array_push($Result,$TotaliPreventivo);
               $IdLastPreventivo = $Row['ID_PREVENTIVO'];
            }
            if($Row['IMPORTO_IVATO'] == 'T')
            {
               $Importo = ($Row['IMPORTO']/100) * 100 / (($Row['IVA']/100) + 100);
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliPreventivo->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Importo - ($Importo * ($Row['SCONTO']/100) / 100)));
               if($Row['RITENUTA'])
                  $TotaliPreventivo->Ritenuta = $Row['RITENUTA'] / 10;
            }
            else
            {
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliPreventivo->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Row['IMPORTO']/100 - ($Row['IMPORTO']/100 * $Row['SCONTO']/100 / 100)));
               if($Row['RITENUTA'])
                  $TotaliPreventivo->Ritenuta = $Row['RITENUTA']/10;
            }
         }
      }
      if($IdLastPreventivo != -1)
         self::FCompilaTotale($TotaliPreventivo);
      return $Result;
   }
   
   public static function GetTotaliPreventivoMultiParametrico($PDODBase, $IdPreventiviMultiParametrici, $CalcoloNaturaPagamento = false)
   {
      $IdLastPreventivoMultiParametrico                 = -1;
      $TotaliPreventivoMultiParametrico                 = null;
      $Result  = array();
      $SQLBody = " SELECT preventivi_multi_voci_soluzione.*,
                          preventivi_multiparametrici.RITENUTA,
                          preventivi_multiparametrici.ESIGIBILITA_IVA
                    FROM  preventivi_multi_voci_soluzione
                    LEFT OUTER JOIN preventivi_multiparametrici ON preventivi_multiparametrici.CHIAVE = preventivi_multi_voci_soluzione.ID_PREVENTIVO_MULTI
                    WHERE preventivi_multi_voci_soluzione.ID_PREVENTIVO_MULTI = $IdPreventiviMultiParametrici
                    ORDER BY preventivi_multi_voci_soluzione.ID_PREVENTIVO_MULTI DESC";

      if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            if($Row['ID_PREVENTIVO_MULTI'] != $IdLastPreventivoMultiParametrico)
            {
               if($IdLastPreventivoMultiParametrico != -1)
                  self::FCompilaTotale($TotaliPreventivoMultiparametrico);
               $TotaliPreventivoMultiparametrico = new TTotaliPreventivoMultiparametrico();
               $TotaliPreventivoMultiparametrico->TotaleImponibile = 0;
               $TotaliPreventivoMultiparametrico->TotaleIva = 0;
               $TotaliPreventivoMultiparametrico->TotaleIvato = 0;
               $TotaliPreventivoMultiparametrico->TotaleRitenuta = 0;
               $TotaliPreventivoMultiparametrico->Totale = 0;
               $TotaliPreventivoMultiparametrico->SommaImponibile = 0;
               $TotaliPreventivoMultiparametrico->SommaIva = 0;
               $TotaliPreventivoMultiparametrico->Ritenuta = 0;
               $TotaliPreventivoMultiparametrico->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
               $TotaliPreventivoMultiparametrico->IdPreventivo = intval($Row['ID_PREVENTIVO_MULTI']);
               $TotaliPreventivoMultiparametrico->LsIva = array();
               array_push($Result,$TotaliPreventivoMultiparametrico);
               $IdLastPreventivoMultiParametrico = $Row['ID_PREVENTIVO_MULTI'];
             }
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliPreventivoMultiparametrico->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Row['IMPORTO']/100 - ($Row['IMPORTO']/100 * $Row['SCONTO']/100 / 100)));
               if($Row['RITENUTA'])
                  $TotaliPreventivoMultiparametrico->Ritenuta = $Row['RITENUTA']/10;
          }
          

        if($IdLastPreventivoMultiParametrico != -1)
          self::FCompilaTotale($TotaliPreventivoMultiparametrico);
      }
       return $Result;
   }
   private static function FCompilaTotale($Totali)
   {
      foreach($Totali->LsIva as &$value)
      {
        if($value->SommaImponibile < 0.0001)
          $value->SommaImponibile = 0;

        $Totali->SommaImponibile = $Totali->SommaImponibile + $value->SommaImponibile;
      }

      $Totali->SommaImponibile = round($Totali->SommaImponibile, 2);
      $Totali->SommaIva        = round($Totali->SommaIva, 2);
      
      foreach($Totali->LsIva as &$value)
      {
        if($value->SommaImponibile < 0.0001)
          $value->SommaImponibile = 0;
        
        if($Totali->SommaIva < 0.0001)
          $Totali->SommaIva = 0;
        
        $Totali->SommaIva = $Totali->SommaIva + ($value->SommaImponibile * $value->IVA / 100);
      }

      $Totali->SommaImponibile = round($Totali->SommaImponibile, 2);
      $Totali->SommaIva        = round($Totali->SommaIva, 2);
      
      if($Totali->Ritenuta != 0)
      {
        $Totali->TotaleRitenuta = $Totali->SommaImponibile * $Totali->Ritenuta / 100;

        $Totali->TotaleRitenuta = round($Totali->TotaleRitenuta, 2);
        if($Totali->TotaleRitenuta < 0.0001)
          $Totali->TotaleRitenuta = 0;        
      }

      $Totali->TotaleIvato = $Totali->SommaImponibile + $Totali->SommaIva;
      $Totali->TotaleIvato = round($Totali->TotaleIvato, 2);

      $Totali->Totale = $Totali->TotaleIvato;
      if(isset($Totali->CostoBollo) && $Totali->CostoBollo != 0)
      {
         $Totali->Totale += $Totali->CostoBollo;
         $Totali->Totale  = round($Totali->Totale, 2);
      }
      
      $Totali->NettoAPagare = $Totali->Totale;
      if($Totali->EsigibilitaIVA == ESIGIBILITA_SPLIT_PAYMENT)
         $Totali->NettoAPagare -= $Totali->SommaIva;

      $Totali->NettoAPagare    = round($Totali->NettoAPagare, 2);
     
      $Totali->NettoAPagare -= $Totali->TotaleRitenuta;

      if(isset($Totali->NumeroAnticipo) && $Totali->NumeroAnticipo != 0)
      {
         $Totali->NettoAPagareSenzaAnticipo  = $Totali->NettoAPagare - $Totali->NumeroAnticipo;
         $Totali->NettoAPagareSenzaAnticipo  = round($Totali->NettoAPagareSenzaAnticipo, 2);
      }

      $Totali->SommaImponibile = round($Totali->SommaImponibile, 2);
      $Totali->TotaleIvato     = round($Totali->TotaleIvato, 2);
      $Totali->NettoAPagare    = round($Totali->NettoAPagare, 2);
      $Totali->Totale          = round($Totali->Totale, 2);

      if($Totali->SommaImponibile < 0.0001)
        $Totali->SommaImponibile = 0;

      if($Totali->TotaleIvato < 0.0001)
        $Totali->TotaleIvato = 0;

      if($Totali->NettoAPagare < 0.0001)
        $Totali->NettoAPagare = 0;
      
      if($Totali->Totale < 0.0001)
        $Totali->Totale = 0;
   }

   public static function GetTotaliFattura($PDODBase, $IdFatture, $CalcolareRate = false, $CalcoloNaturaPagamento = false)
   {
      $IdLastFattura = -1;
      $TotaliFattura = null;
      $Result = array();
      $SQLBody = "SELECT voci_fatture.*, 
                         fatture.RITENUTA, 
                         fatture.ID_CLIENTE,
                         fatture.DATA, 
                         fatture.CODICE_FISCALE,
                         fatture.PARTITA_IVA,
                         fatture.ANNO AS ANNO_FATTURA,
                         fatture.NUMERO as NUMERO_FATTURA,
                         fatture.PAGAMENTO_BOLLO,
                         fatture.COSTO_BOLLO,
                         fatture.ESIGIBILITA_IVA,
                         fatture.DATA_RITENUTA_CERT,
                         fatture.NUMERO_ANTICIPO,
                         (SELECT COUNT(*) 
                                          FROM rate_fattura 
                                        WHERE rate_fattura.ID_FATTURA = fatture.CHIAVE 
                                          AND rate_fattura.IS_RITENUTA_ACCONTO = 'T') > 0 AS IS_RITENUTA_ACCONTO
                    FROM voci_fatture
                    JOIN fatture ON  fatture.CHIAVE = voci_fatture.ID_FATTURA
                   WHERE voci_fatture.ID_FATTURA " . (is_int($IdFatture) ? " = $IdFatture" : " IN ($IdFatture)") .
                 " ORDER BY voci_fatture.ID_FATTURA DESC";

      if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            if($Row['ID_FATTURA'] != $IdLastFattura)
            {
               if($IdLastFattura != -1)
                  self::FCompilaTotale($TotaliFattura);
               $TotaliFattura = new TTotaliFattura();
               $TotaliFattura->TotaleImponibile = 0;
               $TotaliFattura->TotaleIva = 0;
               $TotaliFattura->TotaleIvato = 0;
               $TotaliFattura->TotaleRitenuta = 0;
               $TotaliFattura->Totale = 0;
               $TotaliFattura->SommaImponibile = 0;
               $TotaliFattura->SommaIva = 0;
               $TotaliFattura->Ritenuta = 0;
               $TotaliFattura->CostoBollo = 0;
               $TotaliFattura->LsIva = array();
               $TotaliFattura->IdFattura = intval($Row['ID_FATTURA']);
               $TotaliFattura->IdCliente = intval($Row['ID_CLIENTE']);
               $TotaliFattura->EsigibilitaIVA = $Row['ESIGIBILITA_IVA'];
               $TotaliFattura->Anno = intval($Row['ANNO_FATTURA']);
               $TotaliFattura->Numero = $Row['NUMERO_FATTURA'];

               if($Row['IS_RITENUTA_ACCONTO'] == 1)
                  $TotaliFattura->IsRitenutaDiAcconto = true;
               if($Row['IS_RITENUTA_ACCONTO'] == 0)
                  $TotaliFattura->IsRitenutaDiAcconto = false;

               if(isset($Row['PARTITA_IVA']) && $Row['PARTITA_IVA'] != '')
                  $TotaliFattura->IdentificativoFiscale = 'P.IVA: ' . $Row['PARTITA_IVA'];
               if(isset($Row['CODICE_FISCALE']) && $Row['CODICE_FISCALE'] != '')
                  $TotaliFattura->IdentificativoFiscale = 'C.F: ' . $Row['CODICE_FISCALE'];
               $TotaliFattura->DataFattura = $Row['DATA'];
               $TotaliFattura->TotalePagato = 0;
               $TotaliFattura->TotalePagatoXAnno = array();
               $TotaliFattura->TotaliDaIncassare = 0;
               $TotaliFattura->NumeroAnticipo = isset($Row['NUMERO_ANTICIPO'])? $Row['NUMERO_ANTICIPO'] / 100 : 0;
               $TotaleLordo = $TotaliFattura->TotaleIvato + $TotaliFattura->CostoBollo;
               $Ritenuta = ($TotaliFattura->TotaleRitenuta > 0) ? $TotaliFattura->TotaleRitenuta : 0;
               $TotaliFattura->NettoAPagare = round($TotaleLordo - $Ritenuta, 2);
               array_push($Result,$TotaliFattura);
               $IdLastFattura = $Row['ID_FATTURA'];
            }
            if($Row['IMPORTO_IVATO'] == 'T')
            {
               $Importo = ($Row['IMPORTO']/100) * 100 / (($Row['IVA']/100) + 100);
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliFattura->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Importo - ($Importo * ($Row['SCONTO']/100) / 100)));
               if($Row['RITENUTA'])
                  $TotaliFattura->Ritenuta = $Row['RITENUTA'] / 10;
            }
            else
            {
               if($Row['QUANTITA'] != 0)
                  self::GetIva($Row['IVA']/100, $TotaliFattura->LsIva, $CalcoloNaturaPagamento = $CalcoloNaturaPagamento, $NaturaPagamento = $Row['NATURA_PAGAMENTO'])->SommaImponibile += ($Row['QUANTITA']/100 * ($Row['IMPORTO']/100 - ($Row['IMPORTO']/100 * ($Row['SCONTO']/100) / 100)));
               if($Row['RITENUTA'])
                  $TotaliFattura->Ritenuta = $Row['RITENUTA'] / 10;
            }
            if($Row['PAGAMENTO_BOLLO'] == BOLLO_PAGATO_DAL_CLIENTE)
               $TotaliFattura->CostoBollo = $Row['COSTO_BOLLO'] / 100;
         }
      }

      if($CalcolareRate)
      {
         $SQLBody = "SELECT SUM(rate_fattura.IMPORTO) AS TOTALE_IMPORTO,
                            (rate_fattura.DATA_PAGAMENTO IS NOT NULL OR rate_fattura.ID_MOVIMENTO IS NOT NULL) AS PAGATA,
                            EXTRACT(YEAR FROM COALESCE(rate_fattura.DATA_PAGAMENTO, movimenti.DATA)) AS ANNO_RATA,
                            rate_fattura.ID_FATTURA
                       FROM rate_fattura
                            LEFT OUTER JOIN movimenti ON movimenti.CHIAVE = rate_fattura.ID_MOVIMENTO
                      WHERE rate_fattura.ID_FATTURA" . (is_int($IdFatture) ? " = $IdFatture" : " IN ($IdFatture) ") .
                     " GROUP BY rate_fattura.ID_FATTURA, PAGATA, ANNO_RATA";

         if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
               for($i = 0; $i < count($Result); $i++)
                  if($Result[$i]->IdFattura == $Row['ID_FATTURA'])
                  {
                     if($Row['PAGATA'] == 1)
                     {
                        $Result[$i]->TotalePagato += $Row['TOTALE_IMPORTO'] / 100;
                        $Result[$i]->TotalePagatoXAnno[$Row['ANNO_RATA']] = $Row['TOTALE_IMPORTO'] / 100;
                     }
                     else $Result[$i]->TotaliDaIncassare += $Row['TOTALE_IMPORTO'] / 100;  
                     break;
                  }

         foreach ($Result as $Fattura)
         {
          if($Fattura->IsRitenutaDiAcconto)
          {
            $SQLBody = "SELECT SUM(rate_fattura.IMPORTO) AS TOTALE_IMPORTO,
                                (rate_fattura.DATA_PAGAMENTO IS NOT NULL OR rate_fattura.ID_MOVIMENTO IS NOT NULL) AS PAGATA,
                                EXTRACT(YEAR FROM COALESCE(rate_fattura.DATA_PAGAMENTO, movimenti.DATA)) AS ANNO_RATA,
                                rate_fattura.ID_FATTURA
                          FROM rate_fattura
                                LEFT OUTER JOIN movimenti ON movimenti.CHIAVE = rate_fattura.ID_MOVIMENTO
                          WHERE rate_fattura.ID_FATTURA = $Fattura->IdFattura 
                           AND rate_fattura.IS_RITENUTA_ACCONTO = 'T'
                        GROUP BY rate_fattura.ID_FATTURA, PAGATA, ANNO_RATA";

            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                for($i = 0; $i < count($Result); $i++)
                    if($Result[$i]->IdFattura == $Row['ID_FATTURA'])
                    {
                      $Result[$i]->TotaleRitenutaDaBanca[$Row['ANNO_RATA']] = $Row['TOTALE_IMPORTO'] / 100;
                      break;
                    }
          }
         }
      }

      
      if($IdLastFattura != -1)
         self::FCompilaTotale($TotaliFattura);
      return $Result;
   }
   
   public static function GetRitenutaNotaXRate($PDODBase, $IdNota)
   {
    $TotaliNote = self::GetTotaliNota($PDODBase, $IdNota, true);

    foreach ($TotaliNote as $Totale)
    {
      // if($Totale->$NettoAPagare == 0)
      //   continue;

      $SQLBody = "SELECT rate_note.CHIAVE,
			                   rate_note.IMPORTO,
			                   rate_note.DATA_PAGAMENTO,
			                   rate_note.ID_NOTA

                    FROM rate_note

                   WHERE rate_note.ID_NOTA = " . $Totale->IdNota . "
                
                ORDER BY rate_note.CHIAVE ";
      
      if($Query = $PDODBase->query($SQLBody))
        
        while($Row = $Query->fetch(PDO::FETCH_ASSOC)) 
        {
            $OggettoRate = new stdClass();
            $OggettoRate->IdRata   = $Row['CHIAVE'];
            $OggettoRate->Importo  = $Row['IMPORTO'] / 100;
            $OggettoRate->Ritenuta = 0; 
            $OggettoRate->Pagata   = isset($Row['DATA_PAGAMENTO']); 

            array_push($Totale->Rate, $OggettoRate);
        }
      
        $RitenutaAssegnata = 0.0;
        
        foreach ($Totale->Rate as $Rata) 
        {
          // Calcolo della ritenuta in proporzione alla rata
          $Quota = round(($Rata->Importo / $Totale->NettoAPagare) * $Totale->TotaleRitenuta, 2);
          $Rata->Ritenuta     = $Quota;
          $RitenutaAssegnata += $Quota;
        }

        $Differenza = round($Totale->TotaleRitenuta - $RitenutaAssegnata, 2);

        if (abs($Differenza) > 0) 
          if(isset($Totale->Rate[0]))
          {
              $Totale->Rate[0]->Ritenuta += $Differenza;
              $Totale->Rate[0]->Ritenuta = round($Totale->Rate[0]->Ritenuta, 2);
          }
    }
     
    return $TotaliNote;
     
   }

   public static function GetRitenutaFatturaXRate($PDODBase, $IdFattura)
   {
      $TotaliFatture = self::GetTotaliFattura($PDODBase, $IdFattura, true);

      foreach ($TotaliFatture as $Totale) 
      {
         if($Totale->NettoAPagare == 0)
           continue;

         $SQLBody = "SELECT rate_fattura.CHIAVE,
                            rate_fattura.IMPORTO,
                            rate_fattura.DATA_PAGAMENTO,
                            rate_fattura.ID_FATTURA,
                            rate_fattura.IS_RITENUTA_ACCONTO
                       FROM rate_fattura
                      WHERE rate_fattura.ID_FATTURA = " . $Totale->IdFattura . 
                    " ORDER BY rate_fattura.CHIAVE";
         
         if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC)) 
            {
               $OggettoRate = new stdClass();
               $OggettoRate->IdRata   = $Row['CHIAVE'];
               $OggettoRate->Importo  = $Row['IMPORTO'] / 100;
               $OggettoRate->Ritenuta = 0; 
               $OggettoRate->Pagata   = isset($Row['DATA_PAGAMENTO']); 

               array_push($Totale->Rate, $OggettoRate);

               if(isset($Row['IS_RITENUTA_ACCONTO']) && $Row['IS_RITENUTA_ACCONTO'] == 'T')
                  $Totale->ContieneAlmenoUnaRataConRdA = true;
            }

         $SQLBody = "SELECT rate_note.CHIAVE AS CHIAVE,
                            rate_note.IMPORTO AS IMPORTO
                       FROM rate_note
                      WHERE rate_note.ID_FATTURA = " . $Totale->IdFattura;
         
         if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC)) 
            {
               $OggettoRate = new stdClass();
               $OggettoRate->IdRata   = $Row['CHIAVE'];
               $TotaliNota = self::GetTotaliNota($PDODBase, $Row['CHIAVE']);
               $OggettoRate->Importo  = $Row['IMPORTO']/100;
               $OggettoRate->Ritenuta = 0; 
               $OggettoRate->Pagata   = true; 
               array_push($Totale->Rate, $OggettoRate);
            }

         $RitenutaAssegnata = 0.0;
         foreach ($Totale->Rate as $Rata) 
         {
            // Calcolo proporzionale
            $Quota = round(($Rata->Importo / $Totale->NettoAPagare) * $Totale->TotaleRitenuta, 2);
            $Rata->Ritenuta     = $Quota;
            $RitenutaAssegnata += $Quota;
         }

         $Differenza = round($Totale->TotaleRitenuta - $RitenutaAssegnata, 2);

         if (abs($Differenza) > 0) 
            if(isset($Totale->Rate[0]))
            {
               $Totale->Rate[0]->Ritenuta += $Differenza;
               $Totale->Rate[0]->Ritenuta = round($Totale->Rate[0]->Ritenuta, 2);
            }
      }

      return $TotaliFatture;
   }

   public static function GetRitenutaClienteXAnno($PDODBase, $IdCliente, $Anno = -1, $GetLista = false)
   {
      $Result = array();

      $SQLFatture = "SELECT fatture.CHIAVE 
                       FROM fatture 
                      WHERE NUMERO IS NOT NULL 
                       " . 
                     ($Anno != -1? " AND (
                                          (fatture.CHIAVE IN (SELECT rate_fattura.ID_FATTURA
                                                      FROM rate_fattura
                                                           LEFT OUTER JOIN movimenti ON movimenti.CHIAVE = rate_fattura.ID_MOVIMENTO
                                                     WHERE 
                                                           (
                                                               EXTRACT(YEAR FROM rate_fattura.DATA_PAGAMENTO) = $Anno 
                                                               AND
                                                               rate_fattura.DATA_PAGAMENTO IS NOT NULL
                                                            )
                                                            OR
                                                            (
                                                               EXTRACT(YEAR FROM movimenti.DATA) = $Anno 
                                                               AND
                                                               rate_fattura.ID_MOVIMENTO IS NOT NULL
                                                            )
                                                    )
                                           AND fatture.DATA_RITENUTA_CERT IS NULL
                                          )
                                          OR
                                          EXTRACT(YEAR FROM fatture.DATA_RITENUTA_CERT) = $Anno AND fatture.DATA_RITENUTA_CERT IS NOT NULL) " : "") .  

                      " AND ID_CLIENTE " . (is_int($IdCliente) ? " = $IdCliente" : " IN ($IdCliente)" );

      $TotaliFatture = self::GetTotaliFattura($PDODBase, $SQLFatture, true);

      usort($TotaliFatture, function($a, $b) 
      {
        if($a->IdCliente > $b->IdCliente)
           return 1;
        if($a->IdCliente < $b->IdCliente)
           return -1;
        if($a->Anno > $b->Anno)
           return 1;
        if($a->Anno < $b->Anno)
           return -1;
         return 0;
      });

      if(!$GetLista)
      {
         $ObjectCliente = null;
         $ObjectAnno    = null;
         $LastCliente   = -1;

         foreach ($TotaliFatture as $Fattura)
         {
            if(count($Fattura->TotalePagatoXAnno) != 0 && !isset($Fattura->DataRitenutaCertificata))
            {
               foreach($Fattura->TotalePagatoXAnno as $AnnoPagamento => $PagatoXAnno)
               {
                  if($LastCliente != $Fattura->IdCliente)
                  {
                     $ObjectCliente = new TObjectCliente();
                     $ObjectCliente->IdCliente = $Fattura->IdCliente;
                     $ObjectCliente->Ritenute = array();
                     $LastCliente = $Fattura->IdCliente;
                     array_push($Result, $ObjectCliente);
                  }

                  $ObjectAnno = null;
                  foreach($ObjectCliente->Ritenute as $RitenutaInserita)
                  {
                    if($RitenutaInserita->Anno == $AnnoPagamento && $RitenutaInserita->IdentificativoFiscale == $Fattura->IdentificativoFiscale)
                       $ObjectAnno = $RitenutaInserita;
                  }

                  if($ObjectAnno == null)
                  {
                    $ObjectAnno = new TObjectAnno();
                    $ObjectAnno->Anno = $AnnoPagamento;
                    if(isset($Fattura->IdentificativoFiscale))
                       $ObjectAnno->IdentificativoFiscale = $Fattura->IdentificativoFiscale;
                    $ObjectAnno->SommaRitenuta = 0;
                    $ObjectAnno->SommaRitenutaDiFattureNonPagateMaCertificate = 0;
                    $ObjectAnno->SommaRitenutaDiFattureRDA = 0;
                    $ObjectAnno->RitenutaCertificata = 0;
                    array_push($ObjectCliente->Ritenute, $ObjectAnno);
                  }

                  if($Fattura->TotaliDaIncassare != 0 || $Fattura->TotalePagato != 0)
                  {
                    if(!$Fattura->IsRitenutaDiAcconto)
                    {
                      $ObjectAnno->SommaRitenuta += ($PagatoXAnno / ($Fattura->TotalePagato + $Fattura->TotaliDaIncassare) * $Fattura->TotaleRitenuta);
                    }
                    else
                    {
                      if(isset($Fattura->TotaleRitenutaDaBanca[$AnnoPagamento]))
                        $ObjectAnno->SommaRitenutaDiFattureRDA += $Fattura->TotaleRitenutaDaBanca[$AnnoPagamento];
                    }
                    
                    foreach ($Fattura->TotalePagatoXAnno as $key => $value)
                    {
                       if(!isset($Fattura->SommaRitenutaXAnno))
                       {
                          $Fattura->SommaRitenutaXAnno = array();
                          $Fattura->SommaRitenutaXAnno[$key] = 0;
                       }
                       if(!isset($Fattura->SommaRitenutaXAnno[$key]))
                          $Fattura->SommaRitenutaXAnno[$key] = 0;
                       $Fattura->SommaRitenutaXAnno[$key] += ($value / ($Fattura->TotalePagato + $Fattura->TotaliDaIncassare) * $Fattura->TotaleRitenuta);  
                    }
                  }
               }
            }  
            else
            {
               // PER I CLIENTI CHE HANNO PAGATO LA RITENUTA MA NON HANNO PAGATO LA FATTURA
               if(isset($Fattura->DataRitenutaCertificata))
               {
                  $AnnoPagamento = (int)date('Y', strtotime($Fattura->DataRitenutaCertificata));

                  if($LastCliente != $Fattura->IdCliente)
                  {
                     $ObjectCliente = new TObjectCliente();
                     $ObjectCliente->IdCliente = $Fattura->IdCliente;
                     $ObjectCliente->Ritenute = array();
                     $LastCliente = $Fattura->IdCliente;
                     array_push($Result, $ObjectCliente);
                  }

                  $ObjectAnno = null;
                  foreach($ObjectCliente->Ritenute as $RitenutaInserita)
                  {
                     if($RitenutaInserita->Anno == $AnnoPagamento && $RitenutaInserita->IdentificativoFiscale == $Fattura->IdentificativoFiscale)
                        $ObjectAnno = $RitenutaInserita;
                  }

                  if($ObjectAnno == null)
                  {
                     $ObjectAnno = new TObjectAnno();
                     $ObjectAnno->Anno = $AnnoPagamento;
                     if(isset($Fattura->IdentificativoFiscale))
                        $ObjectAnno->IdentificativoFiscale = $Fattura->IdentificativoFiscale;
                     $ObjectAnno->SommaRitenuta = 0;
                     $ObjectAnno->SommaRitenutaDiFattureNonPagateMaCertificate = 0;
                     $ObjectAnno->SommaRitenutaDiFattureRDA = 0;
                     $ObjectAnno->RitenutaCertificata = 0;
                     array_push($ObjectCliente->Ritenute, $ObjectAnno);
                  }

                  if($Fattura->TotaleRitenuta != 0)
                  {
                    if(!$Fattura->IsRitenutaDiAcconto)
                    {
                      $ObjectAnno->SommaRitenuta += $Fattura->TotaleRitenuta;
                      $ObjectAnno->SommaRitenutaDiFattureNonPagateMaCertificate += $Fattura->TotaleRitenuta;
                      $ObjectAnno->RitenutaCertificata += $Fattura->TotaleRitenuta;
                    }
                    else 
                    {
                      if(isset($Fattura->TotaleRitenutaDaBanca[$AnnoPagamento]))
                        $ObjectAnno->SommaRitenutaDiFattureRDA += $Fattura->TotaleRitenutaDaBanca[$AnnoPagamento];
                    }
                    foreach ($Fattura->TotalePagatoXAnno as $key => $value)
                    {
                       if(!isset($Fattura->SommaRitenutaXAnno))
                       {
                          $Fattura->SommaRitenutaXAnno = array();
                          $Fattura->SommaRitenutaXAnno[$key] = 0;
                       }
                       if(!isset($Fattura->SommaRitenutaXAnno[$key]))
                          $Fattura->SommaRitenutaXAnno[$key] = 0;
                       $Fattura->SommaRitenutaXAnno[$key] += $Fattura->TotaleRitenuta;
                    }
                  }
               }
            }
         }
            
         return $Result;
      }
      else
      {
        foreach ($TotaliFatture as $Fattura)
        {
            $Denominatore = $Fattura->TotalePagato + $Fattura->TotaliDaIncassare;

            if ($Denominatore != 0 && !isset($Fattura->DataRitenutaCertificata))
            {
               $Fattura->SommaRitenuta = $Fattura->TotaleRitenuta;

               foreach ($Fattura->TotalePagatoXAnno as $key => $value)
                  $Fattura->SommaRitenutaXAnno[$key] = ($value / $Denominatore) * $Fattura->TotaleRitenuta;
            }
            else
            {
               if(isset($Fattura->DataRitenutaCertificata))
               {
                  $Fattura->SommaRitenuta = $Fattura->TotaleRitenuta;
                  $AnnoPagamento = (int)date('Y', strtotime($Fattura->DataRitenutaCertificata));

                  $Fattura->SommaRitenutaXAnno[$AnnoPagamento] = $Fattura->TotaleRitenuta;
                  $Fattura->IsRitenutaCertificata = true;
                  
               }
               else
               {
                  $Fattura->SommaRitenuta = 0; 

                  foreach ($Fattura->TotalePagatoXAnno as $key => $value)
                     $Fattura->SommaRitenutaXAnno[$key] = 0;
               }
            }
        }
        return $TotaliFatture;
      }
   }

   public static function GestioneArrotondamentoRateDerivatoDaErroreArrotondamento($ChiaveRata, $Totale)
   {
      $TotaleRate  = 0;
      $ImportoRata = 0;
      $PrimaRata   = false;
      $NumeroRate  = 0;
      foreach ($Totale->Rate as $Rata) 
      {
         $TotaleRate += $Rata->Importo;
         $NumeroRate++;

         if($ChiaveRata == $Rata->IdRata)
         {
            $ImportoRata = $Rata->Importo;
            if($NumeroRate == 1)
            $PrimaRata = true;
         }
      }

      $TotaleRate     = round($TotaleRate, 2);
      if($TotaleRate != $Totale->NettoAPagare && $PrimaRata)
      {
         if(abs(round(($Totale->NettoAPagare - $TotaleRate),2)) <= 0.011)
         {
            if($Totale->NettoAPagare < $TotaleRate)
            $ImportoRata -= 0.01;
            else 
            $ImportoRata += 0.01;
         }
      }

      return $ImportoRata;
   }

   public static function GestioneArrotondamentoSaldiAnnualiDerivatoDaErroreArrotondamento(&$Saldo, $ChiaveRata, $Totale)
   {
      $TotaleRate  = 0;
      $PrimaRata   = false;
      $NumeroRate  = 0;
      foreach ($Totale->Rate as $Rata) 
      {
        $TotaleRate += $Rata->Importo;
        $NumeroRate++;

        if($ChiaveRata == $Rata->IdRata)
          if($NumeroRate == 1)
            $PrimaRata = true;
      }

      $TotaleRate     = round($TotaleRate, 2);
      if($TotaleRate != $Totale->NettoAPagare && $PrimaRata)
      {
        if(abs(round(($Totale->NettoAPagare - $TotaleRate),2)) <= 0.011)
        {
          if($Totale->NettoAPagare < $TotaleRate)
            $Saldo       += 0.01;
          else 
            $Saldo       -= 0.01;
        }
      }
   }

   public static function GetDatiIntestazione($PDODBase, &$OggettoIntestazione)
   {
      $SQLBody       = "SELECT cfg_fattura_elettronica.*,
                       (SELECT NOME FROM province WHERE cfg_fattura_elettronica.ID_PROVINCIA_REA = province.CHIAVE) AS PROVINCIA_REA, 
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
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            $OggettoIntestazione->DENOMINAZIONE_SOCIETA =  $Row['DENOMINAZIONE_PRESTATORE'];
            $OggettoIntestazione->INTESTAZIONE_SOCIETA  = array( $Row['INDIRIZZO_SEDE_PRESTATORE'] . ' ' . $Row['NR_CIVICO_SEDE_PRESTATORE'] . ' - ' . $Row['CAP_SEDE_PRESTATORE'] . ' ' . $Row['COMUNE_SEDE_PRESTATORE'] . '(' . $Row['TARGA_PROVINCIA_SEDE_PRESTATORE'] . ')',
                                                                 'TEL. ' . $Row['TELEFONO_PRESTATORE'],
                                                                 'P.IVA ' . $Row['PARTITA_IVA_PRESTATORE'] . ' - REA ' . $Row['PROVINCIA_REA'] . ' ' . $Row['NUMERO_REA'],
                                                                 'CODICE FISCALE: ' . $Row['CODICE_FISCALE_PRESTATORE'],
                                                                 $Row['EMAIL_PRESTATORE'] );
         }
      }
   }

   public static function GetDatiAzienda($PDODBase)
   {
      $SQLBody       = "SELECT cfg_fattura_elettronica.*,
                       (SELECT SIGLA FROM nazioni WHERE cfg_fattura_elettronica.PAESE_TRASMITTENTE = nazioni.CHIAVE) AS SIGLA_PAESE_TRASMITTENTE
                          FROM cfg_fattura_elettronica";

      if($Query = $PDODBase->query($SQLBody))
      {
         while($Row = $Query->fetch(PDO::FETCH_ASSOC))
         { 
            return $Row;
         }
      }
   }

   public static function GestioneNomeAziendaBandHeader(&$ABand, $ASingleRecord)
   {
      if($ABand->Name == 'BAND_INTESTAZIONE')
      {
         for($i = 0; $i < count($ABand->Objects); $i++)
         {
            if($ABand->Objects[$i]->Name == 'DENOMINAZIONE_SOCIETA' && 
               isset($ASingleRecord->DENOMINAZIONE_SOCIETA) && 
               is_string($ASingleRecord->DENOMINAZIONE_SOCIETA) && 
               strlen(trim($ASingleRecord->DENOMINAZIONE_SOCIETA)) > 40
               )
            {
               $ABand->Objects[$i]->FontSize = 9;
            }
         }
      }
   }

   public static function GetDenominazioneAzienda($PDODBase)
   {
     $DenominazioneAzienda = '';
 
     $SQLBody = "SELECT DENOMINAZIONE_PRESTATORE
                   FROM cfg_fattura_elettronica";
 
     if($Query = $PDODBase->query($SQLBody))
     {
         if($Row = $Query->fetch(PDO::FETCH_ASSOC))
             $DenominazioneAzienda = $Row['DENOMINAZIONE_PRESTATORE'];
     }
     else
         error_log("Impossibile caricare la denominazione azienda da cfg_fattura_elettronica.");
 
     return $DenominazioneAzienda;
   }


}
?>