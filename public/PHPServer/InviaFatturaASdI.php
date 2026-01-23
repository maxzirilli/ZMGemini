<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once "ClassXGenerateFatture.php";
      include_once 'ClassStampaFattura.php';
      include_once 'ClassStampaNotaDiCredito.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
      include_once PATH_LIBRERIE . 'ZStringFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 

      class TParametri
      {
        public $Chiave       = null;
        public $Oggetto      = null;
        public $Destinatario = null;
        public $Cc           = null;
        public $Ccn          = null;
        public $CorpoEmail   = null;

        public function __construct($Chiave, $Oggetto, $Destinatario, $Cc, $Ccn, $CorpoEmail) 
        {
          $this->Chiave       = $Chiave;
          $this->Oggetto      = $Oggetto;
          $this->Destinatario = $Destinatario;
          $this->Cc           = $Cc;
          $this->Ccn          = $Ccn;
          $this->CorpoEmail   = $CorpoEmail;
        }
      }

      class TParametriEmail
      {
        public $CorpoEmail   = 'Corpo email';
        public $OggettoEmail = 'Oggetto email';
      }
      
      class TAdvQuerySendToSdI extends TAdvQueryGeneraXMLInterface
      {
          protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
          { 
            $Parametri = JSON_decode($_POST['Params']); 

            $ParametriEmail = $this->FGetParametriEmailConfigurazioni($PDODBase);

            for($i = 0; $i < count($Parametri); ++$i)
            {
              if($Parametri[$i]->IS_NOTA_DI_CREDITO == 'T')
                $this->FInvioDocumento($this->FPrepareParameterValue($Parametri[$i]->ChiaveNota,':'), TIPO_DOCUMENTO_NOTA, $PDODBase, $ParametriEmail, $JSONAnswer);
              else 
              {
                if($Parametri[$i]->IS_AUTOFATTURA == 'T')
                  $this->FInvioDocumento($this->FPrepareParameterValue($Parametri[$i]->ChiaveAutofattura,':'), TIPO_DOCUMENTO_AUTOFATTURA, $PDODBase, $ParametriEmail, $JSONAnswer); 
                else
                 $this->FInvioDocumento($this->FPrepareParameterValue($Parametri[$i]->ChiaveFattura,':'), TIPO_DOCUMENTO_FATTURA, $PDODBase, $ParametriEmail, $JSONAnswer); 
              }
            }
          }

          protected function FInvioDocumento($Chiave, $TipoDocumento, $PDODBase, $ParametriEmail, &$JSONAnswer)
          {
            $FatturaIsInviata = false;

            if(!DEVELOPER_MODE)
            {
                $Fattura = $this->FCreateFatturaElettronica($Chiave, $TipoDocumento, $PDODBase); 
                
                $ChiaveInvioFatturaElettronica = $this->FGetNewKey($PDODBase,GEN_CHIAVI);

                $ChiaveInvioFatturaElettronicaBase32 = CharLeading(base_convert($ChiaveInvioFatturaElettronica,10,32),'0',5);

                $DatiAzienda = TSystemInformation::GetDatiAzienda($PDODBase);
                $NomeDelDocumento = $DatiAzienda['SIGLA_PAESE_TRASMITTENTE'] . $DatiAzienda['PIVA_TRASMITTENTE'] . '_' . $ChiaveInvioFatturaElettronicaBase32 . '.xml';

                $ErrorInvioDocumento = '';

                $MySond = TSystemInformation::CreazioneMySond($PDODBase);

                if(!isset($MySond))
                  throw new Exception('Impostazioni di amministrazione non presenti');
                else
                {
                  $BodyFattura = $Fattura->XMLBody->saveXML();

                  if($TipoDocumento == TIPO_DOCUMENTO_NOTA)
                    $this->FGestioneErroreFatturaInvioNotaPresenteSuDatabase($PDODBase, $Chiave, $BodyFattura);
                  
                  $FatturaIsInviata = $MySond->InviaFileXML($BodyFattura, false, MYSOND_INVIA_IMMEDIATAMENTE, $ErrorInvioDocumento,$NomeDelDocumento);

                  $BodyFattura = $this->FPrepareParameterValue($BodyFattura, '#'); 

                  $NomeFile = $this->FPrepareParameterValue($NomeDelDocumento, '#'); 

                  $PDODBase->beginTransaction();
                  try
                  {
                    $ChiaveInfoFatturaElettronica = $this->FPrepareParameterValue($ChiaveInvioFatturaElettronica, ':');

                    switch ($TipoDocumento) 
                    {
                      case TIPO_DOCUMENTO_FATTURA     : $this->FPrepareDocumentoFileXML($PDODBase, $FatturaIsInviata, $ErrorInvioDocumento, $ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, $NomeFile, 'fatture', 'ID_FATTURA' );
                                                        break;
                      case TIPO_DOCUMENTO_NOTA        : $this->FPrepareDocumentoFileXML($PDODBase, $FatturaIsInviata, $ErrorInvioDocumento, $ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, $NomeFile, 'note_di_credito', 'ID_NOTA_DI_CREDITO' );
                                                        break;
                      case TIPO_DOCUMENTO_AUTOFATTURA : $this->FPrepareDocumentoFileXML($PDODBase, $FatturaIsInviata, $ErrorInvioDocumento, $ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, $NomeFile, 'autofatture', 'ID_AUTOFATTURA');
                                                        break;
                    }
                    $PDODBase->commit();
                  }
                  catch(Exception $e)
                  {
                    $PDODBase->rollBack();
                    throw new Exception($e->getMessage());         
                  }

                  if(!$FatturaIsInviata)
                    throw new Exception($ErrorInvioDocumento);
                }
              }
              else $FatturaIsInviata = true;

              if($FatturaIsInviata)
              {
                switch ($TipoDocumento) 
                {
                  case TIPO_DOCUMENTO_FATTURA     : $this->FInvioEmailConFatturaDiCortesia($Chiave, $PDODBase, $ParametriEmail, $JSONAnswer);
                                                    break;
                  case TIPO_DOCUMENTO_NOTA        : $this->FInvioEmailConNotaDiCortesia($Chiave, $PDODBase, $ParametriEmail, $JSONAnswer);
                                                    break;
                }
              }
          }

          private function FGestioneErroreFatturaInvioNotaPresenteSuDatabase ($PDODBase, $Chiave, &$BodyFattura)
          {
            $SQLBody = "SELECT XML_DA_INVIARE_A_SDI 
                          FROM note_di_credito
                         WHERE CHIAVE = $Chiave";
            
            if($Query = $PDODBase->query($SQLBody))
              if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if(isset($Row['XML_DA_INVIARE_A_SDI']))
                  $BodyFattura = $Row['XML_DA_INVIARE_A_SDI'];

          }

          private function FInvioEmailConNotaDiCortesia($Chiave, $PDODBase, $ParametriEmail, &$JSONAnswer)
          {
            $OpzioneInvioEmail = true;
                  
            $Oggetto      = $ParametriEmail->OggettoEmail;
            $Destinatario = '';
            $Cc           = '';
            $Ccn          = '';
            $CorpoEmail   = $ParametriEmail->CorpoEmail;

            $SQLBody = "SELECT IF(clienti.ID_AMMINISTRATORE IS NOT NULL, amministratori_telefono.EMAIL, clienti_telefono.EMAIL) AS EMAIL
                          FROM note_di_credito
                                           JOIN clienti ON note_di_credito.ID_CLIENTE = clienti.CHIAVE
                                LEFT OUTER JOIN clienti_telefono ON (clienti_telefono.ID_CLIENTE = clienti.CHIAVE AND clienti_telefono.PRINCIPALE = 'T')
                                LEFT OUTER JOIN amministratori ON amministratori.CHIAVE = clienti.ID_AMMINISTRATORE 
                                LEFT OUTER JOIN amministratori_telefono ON (amministratori_telefono.ID_AMMINISTRATORE = amministratori.CHIAVE AND amministratori_telefono.PRINCIPALE = 'T')
                         WHERE note_di_credito.CHIAVE = $Chiave
                         ORDER BY EMAIL";

            $InseritaEmail = false;
            $LastEmail     = -1;
            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if(isset($Row['EMAIL']))
                  {
                    if($Row['EMAIL'] != '')
                    {
                      if($LastEmail != $Row['EMAIL'])
                      {
                        $Ccn .= $Row['EMAIL'] . ';';
                        $InseritaEmail = true;
                        $LastEmail = $Row['EMAIL'];
                      }
                    }
                  }
                }
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            if(DEVELOPER_MODE)
            {
              $InseritaEmail = true;
              $Ccn = MAIL_DEVELOPER . ';';
              error_log('MAIL DI TEST PERCHE\' SIAMO IN DEVELOPER MODE');
            }


            if($InseritaEmail)
            {
              $Ccn = substr($Ccn, 0, -1);
              $Parametri   = new TParametri($Chiave, $Oggetto, $Destinatario, $Cc, $Ccn, $CorpoEmail);

              $AConnection = new TExtraStampaNota($Parametri, true, true, true);
              $MailAnswer  = $AConnection->ServerSideScript(false);
              foreach ($MailAnswer as $key => $value)
                  $JSONAnswer->$key = $value;
            }
          }

          private function FInvioEmailConFatturaDiCortesia($Chiave, $PDODBase, $ParametriEmail, &$JSONAnswer)
          {
            $OpzioneInvioEmail = true;
                  
            $Oggetto      = $ParametriEmail->OggettoEmail;
            $Destinatario = '';
            $Cc           = '';
            $Ccn          = '';
            $CorpoEmail   = $ParametriEmail->CorpoEmail;

            $SQLBody = "SELECT IF(clienti.ID_AMMINISTRATORE IS NOT NULL, amministratori_telefono.EMAIL, clienti_telefono.EMAIL) AS EMAIL
                          FROM fatture
                                           JOIN clienti ON fatture.ID_CLIENTE = clienti.CHIAVE
                                LEFT OUTER JOIN clienti_telefono ON (clienti_telefono.ID_CLIENTE = clienti.CHIAVE AND clienti_telefono.PRINCIPALE = 'T')
                                LEFT OUTER JOIN amministratori ON amministratori.CHIAVE = clienti.ID_AMMINISTRATORE 
                                LEFT OUTER JOIN amministratori_telefono ON (amministratori_telefono.ID_AMMINISTRATORE = amministratori.CHIAVE AND amministratori_telefono.PRINCIPALE = 'T')
                        WHERE fatture.CHIAVE = $Chiave
                        ORDER BY EMAIL";

            $InseritaEmail = false;
            $LastEmail     = -1;
            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if(isset($Row['EMAIL']))
                  {
                    if($Row['EMAIL'] != '')
                    {
                      if($LastEmail != $Row['EMAIL'])
                      {
                        $Ccn .= $Row['EMAIL'] . ';';
                        $InseritaEmail = true;
                        $LastEmail = $Row['EMAIL'];
                      }
                    }
                  }
                }
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            if(DEVELOPER_MODE)
            {
              $InseritaEmail = true;
              $Ccn = MAIL_DEVELOPER . ';';
              error_log('MAIL DI TEST PERCHE\' SIAMO IN DEVELOPER MODE');
            }

            if($InseritaEmail)
            {
              $Ccn = substr($Ccn, 0, -1);
              $Parametri   = new TParametri($Chiave, $Oggetto, $Destinatario, $Cc, $Ccn, $CorpoEmail);

              $AConnection = new TExtraStampaFattura($Parametri, true, true, true);
              $MailAnswer  = $AConnection->ServerSideScript(false);
              foreach ($MailAnswer as $key => $value)
                  $JSONAnswer->$key = $value;
            }
          }

          private function FPrepareDocumentoFileXML($PDODBase, $FatturaIsInviata, $ErrorInvioDocumento, $ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, $NomeFile, $NomeTabella, $IdCampo )
          {
              if($FatturaIsInviata)
              {
                $SQLBody = "UPDATE $NomeTabella
                               SET INVIATA_ALLO_SDI = 'T'
                             WHERE CHIAVE = $Chiave";

                try
                {           
                  if(!$PDODBase->query($SQLBody))
                  {
                      error_log($SQLBody);
                      throw new Exception('Impossibile impostare come inviato il documento');
                  }
                }
                catch(Exception $e)
                {
                  error_log($SQLBody);
                  throw new Exception($e->getMessage());         
                }
                
                $SQLBody = "INSERT INTO xml_" . $NomeTabella . " (CHIAVE, $IdCampo, XML_BODY, DATA_INVIO,  NOME_FILE)
                                                          VALUES ($ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, NOW(), $NomeFile)";
                try
                {
                  if(!$PDODBase->query($SQLBody))
                  {
                      error_log($SQLBody);
                      throw new Exception('Impossibile impostare come inviata il documento');
                  }
                }
                catch(Exception $e)
                {
                  error_log($SQLBody);
                  throw new Exception($e->getMessage());         
                }
              }
              else
              {
                $ErrorInvioDocumento = $this->FPrepareParameterValue($ErrorInvioDocumento, '#'); 
                $SQLBody = "INSERT INTO xml_" . $NomeTabella . " (CHIAVE, $IdCampo, XML_BODY, DATA_INVIO, RISULTATO, NOME_FILE)
                                                          VALUES ($ChiaveInvioFatturaElettronica, $Chiave, $BodyFattura, NOW(), $ErrorInvioDocumento, $NomeFile)";
                
                try
                {
                  if(!$PDODBase->query($SQLBody))
                  {
                      error_log($SQLBody);
                      throw new Exception('Impossibile impostare l\'insuccesso del documento');
                  }
                }
                catch(Exception $e)
                {
                  error_log($SQLBody);
                  throw new Exception($e->getMessage());         
                }
              }
          }

          private function FGetParametriEmailConfigurazioni($PDODBase)
          {
            $SQLBody = "SELECT impostazioni.CORPO_EMAIL_DOPO_SDI,
                               impostazioni.OGGETTO_EMAIL_DOPO_SDI
                          FROM impostazioni";

            $Result = new TParametriEmail();
            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if(isset($Row['OGGETTO_EMAIL_DOPO_SDI']) && $Row['OGGETTO_EMAIL_DOPO_SDI'] != '')
                    $Result->OggettoEmail = $Row['OGGETTO_EMAIL_DOPO_SDI'];
                  if(isset($Row['CORPO_EMAIL_DOPO_SDI']) && $Row['CORPO_EMAIL_DOPO_SDI'] != '')
                    $Result->CorpoEmail   = $Row['CORPO_EMAIL_DOPO_SDI'];
                }
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            return $Result;
          }
      }

      $Connection = new TAdvQuerySendToSdI();
      $Connection->ServerSideScript();