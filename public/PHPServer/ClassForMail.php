<?php
  include_once PATH_LIBRERIE . 'ZAdvQueryClient.php';
  include_once PATH_LIBRERIE . 'ZDBaseFunct.php';
  include_once 'AccessRights.php';
  include_once 'Configurations.php';
  include_once 'SystemInformation.php';

  class TAdvQueryClassForMail extends TAdvQuery
  {
    protected $FMailSystemAdministrator = '';
    protected $TipoDocumento;
    protected $IdCliente = null;

    function __construct($ScriptEsterno = false)
    {
      parent::__construct($ScriptEsterno);
      if(defined('MAIL_SYSTEM_ADMINISTRATOR'))
          $this->FMailSystemAdministrator = MAIL_SYSTEM_ADMINISTRATOR;
    }

    protected function AfterMailSent($PDODBase, $Destinatari, $Cc, $Ccn, $Oggetto, $CorpoMessaggio, $NomeMittente, $MailMittente, $VettoreAllegati, $BodyIsHtml, $Errore)
    {
      $Errore              = $this->FPrepareParameterValue($Errore,'#');
      $Destinatari         = $this->FPrepareParameterValue($Destinatari,'#');
      $MailMittente        = $this->FPrepareParameterValue($MailMittente,'#');
      $Cc                  = $this->FPrepareParameterValue($Cc,'#');
      $Ccn                 = $this->FPrepareParameterValue($Ccn,'#');
      $Oggetto             = $this->FPrepareParameterValue($Oggetto,'#');
      $TipoDocumento       = $this->FPrepareParameterValue($this->TipoDocumento,'#');
      $IdCliente           = isset($this->IdCliente)?        $this->FPrepareParameterValue($this->IdCliente,':') : null;
      $CorpoMessaggio      = $this->FPrepareParameterValue($CorpoMessaggio,'#');
      
      $NuovaChiaveLog      = $this->FGetNewKey($PDODBase, GEN_CHIAVI);
      
      $SQLBody = "INSERT INTO " . TABLE_LOG_EMAIL . " (CHIAVE, DESTINATARIO,MITTENTE, CC, CCN, OGGETTO, CORPO_EMAIL, TIPO_DOCUMENTO, DATA_INSERIMENTO, ERRORE_INVIO_EMAIL, ID_CLIENTE)
                  VALUES (" . $NuovaChiaveLog . ", " . 
                              $Destinatari . ", " . 
                              $MailMittente . ", " . 
                              $Cc . ", " . 
                              $Ccn . ", " . 
                              $Oggetto . ", " . 
                              $CorpoMessaggio . ", " . 
                              (isset($TipoDocumento) && $TipoDocumento != ''? $TipoDocumento : 'NULL') . 
                              ", CURRENT_TIMESTAMP(), " . 
                              $Errore . "," .
                              (isset($IdCliente) && $IdCliente != 0? $IdCliente : 'NULL') .")";

      try
      {
        $PDODBase->query($SQLBody);
      }
      catch(Exception $e)
      {
        error_log($SQLBody);
        throw new Exception($e->getMessage());         
      }

      // array_push($VettoreAllegati, "/ppp/ArtaxVarie/ppp.jpg");
      if(count($VettoreAllegati) != 0)
      {
        for($i = 0; $i < count($VettoreAllegati); $i++)
        {
          if(isset($VettoreAllegati[$i]) && $VettoreAllegati[$i] != '')
          {
            $VettoreInfoSplit = explode(".", $VettoreAllegati[$i]);
            $Estensione       = end($VettoreInfoSplit);
            $VettoreInfo      = explode("/", $VettoreAllegati[$i]);
            
            $NomeFile         = 'ALLEGATO_EMAIL';
            if(isset($VettoreInfo) && count($VettoreInfo) != 0)
              $NomeFile       = end($VettoreInfo);

            if(file_exists($VettoreAllegati[$i]))
              $this->InsertAllegatoEmail($NuovaChiaveLog, base64_encode(file_get_contents($VettoreAllegati[$i])), $NomeFile, $Estensione, $PDODBase);
          }
        }
      }
    }

    protected function InsertAllegatoEmail($ChiaveLog, $Allegato, $NomeFile, $Estensione, $PDODBase)
    {
        $Parametri = array();

        $OggettoBlob = new TAdvQueryOggettoBlob(OPERAZIONE_BLOB_INSERIMENTO, 
                                                PrepareForRecordString($Allegato),
                                                $this->GetStringFrameFromEstensione($Estensione),
                                                '',
                                                'ALLEGATO_EMAIL');

        $Parametri['CHIAVE']       = $this->FGetNewKey($PDODBase, GEN_CHIAVI);
        $Parametri['ID_LOG_EMAIL'] = $ChiaveLog;
        $Parametri['ALLEGATO']     = $OggettoBlob;
        $Parametri['DESCRIZIONE']  = PrepareForRecordString($NomeFile);

        $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                            'LogEmail', 
                                            'EditSQL',
                                            'InserisciAllegatoEmail', 
                                            $Parametri,
                                            [1]);
        
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

    private function GetStringFrameFromEstensione($Estensione)
    {
      $Result = 'data:';
      
      switch($Estensione)
      {
        case 'jpg' : $Result .= 'image/jpeg;';
                     break;
        case 'png' : $Result .= 'image/png;';
                     break;
        case 'bmp' : $Result .= 'image/bmp;';
                     break;
        case 'mp3' : $Result .= 'image/mpeg;';
                     break;
        case 'pdf' : $Result .= 'application/pdf;';
                     break;
        case 'zip' : $Result .= 'application/x-zip-compressed;';
                     break;
        case 'txt' : $Result .= 'text;';
                     break;
        default    : $Result .= 'unk;';
                     break;
      }

      return $Result;
    }

    protected function GetParameterEmail($PDODBase,$AccountMail)
    {
      global    $GlobalAccessRights;

      $SQLBody = "SELECT *
                    FROM impostazioni_amministrazione";

      try
      {
        if($Query = $PDODBase->query($SQLBody))
        {
          if($Row = $Query->fetch(PDO::FETCH_ASSOC))
          { 
            $this->FMailSystemAdministrator = $Row['MAIL_SYSTEM_ADMINISTRATOR'];
            switch($AccountMail)
            {
              case MAIL_ACCOUNT_FATTURE : $this->SetParameterEmail($Row['SMTP_HOST_FATTURE'], 
                                                                   $Row['SMTP_PORT_FATTURE'], 
                                                                   $Row['SMTP_SECURE_FATTURE'], 
                                                                   $Row['SMTP_AUTH_FATTURE'], 
                                                                   $Row['SMTP_USER_FATTURE'],
                                                                   $Row['SMTP_PASSWORD_FATTURE'], 
                                                                   $Row['SMTP_FROM_MAIL_FATTURE'], 
                                                                   $Row['SMTP_FROM_NAME_FATTURE']);
                                                                  //  if(!DEVELOPER_MODE? $Row['IMAP_FATTURE'] : '');
                                                                   break;
              case MAIL_ACCOUNT_AVVISI : $this->SetParameterEmail($Row['SMTP_HOST_AVVISI'], 
                                                                  $Row['SMTP_PORT_AVVISI'], 
                                                                  $Row['SMTP_SECURE_AVVISI'], 
                                                                  $Row['SMTP_AUTH_AVVISI'], 
                                                                  $Row['SMTP_USER_AVVISI'],
                                                                  $Row['SMTP_PASSWORD_AVVISI'], 
                                                                  $Row['SMTP_FROM_MAIL_AVVISI'], 
                                                                  $Row['SMTP_FROM_NAME_AVVISI']);
                                                                  // $GlobalAccessRights->FVisibilitaConfigurazioniImap() && !DEVELOPER_MODE? $Row['IMAP_AVVISI'] : '');
                                                                  break;
              case MAIL_ACCOUNT_SOLLECITI : $this->SetParameterEmail($Row['SMTP_HOST_SOLLECITI'], 
                                                                     $Row['SMTP_PORT_SOLLECITI'], 
                                                                     $Row['SMTP_SECURE_SOLLECITI'], 
                                                                     $Row['SMTP_AUTH_SOLLECITI'], 
                                                                     $Row['SMTP_USER_SOLLECITI'],
                                                                     $Row['SMTP_PASSWORD_SOLLECITI'], 
                                                                     $Row['SMTP_FROM_MAIL_SOLLECITI'], 
                                                                     $Row['SMTP_FROM_NAME_SOLLECITI']);
                                                                    //  $GlobalAccessRights->FVisibilitaConfigurazioniImap() && !DEVELOPER_MODE? $Row['IMAP_SOLLECITI'] : '');
                                                                     break;
            }
          }
        }
      }
      catch(Exception $e)
      {
        error_log($SQLBody);
        throw new Exception($e->getMessage());         
      }
    }
  }