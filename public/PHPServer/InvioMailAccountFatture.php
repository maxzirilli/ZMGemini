<?php
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once 'ClassStampaDocumentoGenerico.php';
  require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
  require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
  require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
  include_once 'ClassForMail.php';
  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST,GET, OPTIONS");

  class TExtraInvioMail extends TAdvQueryClassForMail
  {
    protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
    {
      $Parametri    = json_decode($_POST['Params']) ;
      $Oggetto      = $Parametri->Oggetto;
      $Destinatario = $Parametri->Destinatario;
      $Cc           = $Parametri->Cc;
      $Ccn          = $Parametri->Ccn;
      $CorpoEmail   = $Parametri->CorpoEmail;

      if(isset($Parametri->IdCliente))
        $this->IdCliente = $Parametri->IdCliente;
      
      $LsAllegati   = array();
      $NomeCartella = '';

      if(isset($Parametri->Allegato) && is_array($Parametri->Allegato) && count(array_filter($Parametri->Allegato)) > 0)
      {
         if(isset($Parametri->InoltroEmail))
          $this->FGestioneInoltroEmail($Parametri, $NomeCartella);
         else 
          $this->FGestioneInvioAllegatiSolleciti($Parametri, $NomeCartella);
      }

      $Errore = '';
      $JSONAnswer->Risposta = 'MAIL_INVIATA';
              
      if(INVIO_MAIL_ABILITATO)
      {
        $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_FATTURE);

        if(!$this->FSendMail($Destinatario, $Cc, $Ccn , $Oggetto, $this->FGetTestoMail($CorpoEmail), $this->FSmtpFromName, $this->FSmtpFromMail, $Errore, $NomeCartella, true, $PDODBase))
        {
          $JSONAnswer->ErroreMail    = $Errore;
          $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
        }
      }

      DeleteFullDir($this->FNomeCartellaAttacMail . '/' . $NomeCartella, '/' );
    }

    private function FGestioneInoltroEmail($Parametri, &$NomeCartella)
    {
      $LsAllegati    = $Parametri->Allegato;
      $InfoPosizione = array();
      
      foreach ($LsAllegati as $Allegato)
      {
        $Posizione = explode(';', $Allegato->POSIZIONE);
        
        if(isset($Posizione[1]))
        {
          $Oggetto              = new stdClass();
          $Oggetto->POSIZIONE   = $Posizione[1];
          $Oggetto->DESCRIZIONE = $Allegato->DESCRIZIONE;

          array_push($InfoPosizione, $Oggetto);
        }
      }

      $ConteggioAllegati = [];

      // $NomeCartella = 'AttacInoltri' . gmdate('d_m_Y_H_i_s', time());

      $Microtime = microtime(true);
      $Milliseconds = sprintf('%03d', ($Microtime - floor($Microtime)) * 1000);
      $NomeCartella = 'AttacInoltri_' . gmdate('d_m_Y_H_i_s', (int)$Microtime) . "_$Milliseconds";


      for ($i = 0; $i < count($InfoPosizione); $i++)
      {
        if (is_string($InfoPosizione[$i]->POSIZIONE))
        {
           $FilePath     = FOLDER_BLOB . $InfoPosizione[$i]->POSIZIONE;

           if (file_exists($FilePath))
           {  
               $FileContent      = file_get_contents($FilePath);

               $NomeAllegato     = $InfoPosizione[$i]->DESCRIZIONE;

               ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);

               $PercorsoCompleto = $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeAllegato;

               if(!file_exists($PercorsoCompleto))
                 file_put_contents($PercorsoCompleto, $FileContent);
           } 
           else
           {
              error_log("File non trovato o fattura non valida: " . $FilePath);
           }
        }
      }

      if(isset($Parametri->AllegaListaEventiSolleciti) && $Parametri->AllegaListaEventiSolleciti)
      {
        $ParametriEventiSolleciti              = new stdClass();
        $ParametriEventiSolleciti->Descrizione = $Parametri->DescrizioneListaEventi;
        $ParametriEventiSolleciti->Titolo      = $Parametri->TitoloListaEventi;
        $AConnection                           = new TExtraStampaDocumentoGenerico($ParametriEventiSolleciti, true);
        $Base64StampaDocumento                 = $AConnection->ServerSideScript(false);

        $FileContent           = $Base64StampaDocumento->PDF;
        $PDFdata               = base64_decode($FileContent);

        $NomeFile              = 'ListaEventi.pdf';

        $PercorsoCompleto = $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeFile;

        if(!file_exists($PercorsoCompleto))
          file_put_contents($PercorsoCompleto, $PDFdata);
      }
    }

    private function FGestioneInvioAllegatiSolleciti($Parametri, &$NomeCartella)
    {
       $LsAllegati    = $Parametri->Allegato;
       $InfoPosizione = array();
       
       foreach ($LsAllegati as $Allegato)
       {
         $Posizione = explode(';', $Allegato);
         if(isset($Posizione[1]))
         {
           array_push($InfoPosizione, $Posizione[1]);
         }
       }
       
       $ConteggioAllegati = [];
       $ListaFatture = isset($Parametri->NumeroFattura) ? (is_array($Parametri->NumeroFattura) ? $Parametri->NumeroFattura : [$Parametri->NumeroFattura]) : [];
        
      //  $NomeCartella = 'AttacFattPregresse_' . gmdate('d_m_Y_H_i_s', time());

       $Microtime = microtime(true);
       $Milliseconds = sprintf('%03d', ($Microtime - floor($Microtime)) * 1000);
       $NomeCartella = 'AttacFattPregresse_' . gmdate('d_m_Y_H_i_s', (int)$Microtime) . "_$Milliseconds";

       
       for ($i = 0; $i < count($InfoPosizione); $i++)
       {
         if (is_string($InfoPosizione[$i]))
         {
           $FilePath = FOLDER_BLOB . $InfoPosizione[$i];
           $NumeroFattura = isset($ListaFatture[$i]) ? $ListaFatture[$i] : null;
           
           if ($NumeroFattura !== null && file_exists($FilePath))
           {  
               $FileContent = file_get_contents($FilePath);
               $EstraiEstensione = explode(".", $InfoPosizione[$i]);
               $Estensione = end($EstraiEstensione);

               if (!isset($ConteggioAllegati[$NumeroFattura]))
               {
                 $ConteggioAllegati[$NumeroFattura] = 0;
                 $NomeAllegato = 'AllegatoFatturaPregressa_' . $NumeroFattura . '.' . $Estensione;
               } 
               else
               {
                 $ConteggioAllegati[$NumeroFattura]++;
                 $NomeAllegato = 'AllegatoFatturaPregressa_' . $NumeroFattura . '_' . $ConteggioAllegati[$NumeroFattura] . '.' . $Estensione;
               }

               ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);
               $PercorsoCompleto = $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeAllegato;

               if (!file_exists($PercorsoCompleto))
               {
                 file_put_contents($PercorsoCompleto, $FileContent);
               }
           } 
           else
           {
               error_log("File non trovato o fattura non valida: " . $FilePath);
           }
         }
       }
    }

    private function FGetTestoMail($Testo)
    {
      $TestoMail = "";
      $TestoMail = file_get_contents('../SendMailDocumento.html');
      $TestoMail = str_replace('_TOKEN_CORPO_EMAIL_', $Testo, $TestoMail);

      return $TestoMail;
    }
  }

  $Connection = new TExtraInvioMail();
  $Connection->ServerSideScript();