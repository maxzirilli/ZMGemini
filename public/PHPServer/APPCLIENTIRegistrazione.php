<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods: POST, OPTIONS");

      const ACCESSO_IMMEDIATO = 'I';
      const ACCESSO_SOSPESO   = 'S';
      const ACCESSO_NEGATO    = 'N';

      const RESPONSE_UTENTE_REGISTRATO     = 0;
      const RESPONSE_UTENTE_GIA_REGISTRATO = 1;
      const RESPONSE_IN_ATTESA_DI_CONFERMA = 2;
      const RESPONSE_CLIENTE_NON_TROVATO   = 3;
      const RESPONSE_MAIL_GIA_PRESENTE     = 4;
      
      class TAdvQueryAppClientiRegistrazione extends TAdvQuery
      {
        protected function FExtraScriptIgnoreLogin()
        {
          return true;
        }
 
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        { 
          $Input = JSON_decode($_POST['Params']);
          $Parametri = array();

          $Parametri['PARTITA_IVA']    = $Input->PartitaIVA ?? NULL;
          $Parametri['CODICE_FISCALE'] = $Input->CodiceFiscale ?? NULL;
          $Parametri['EMAIL']          = $Input->Email; 

          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'AppClientiLogin',
                                              'ExtraScriptLoginQueries',
                                              'ControlloAccesso',
                                              $Parametri,[]);
          $Query = $PDODBase->query($SQLBody);

          if($Row = $Query->fetch(PDO::FETCH_ASSOC))
          {
            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'AppClientiLogin',
                                                'ExtraScriptLoginQueries',
                                                'ControlloEmailDoppia',
                                                $Parametri,[]);
            $Query = $PDODBase->query($SQLBody);

            $NumeroMail = $Query->fetch(PDO::FETCH_ASSOC)['NUMERO_MAIL'];

            if(($Row['EMAIL_CLIENTI_TROVATA'] != 1 || $Row['EMAIL_AMMINISTRATORI_TROVATA'] != 1) && $NumeroMail > 0)
              $Parametri['ACCESSO'] = ACCESSO_NEGATO;
            else
              $Parametri['ACCESSO'] = ($Row['EMAIL_CLIENTI_TROVATA'] == 1 || $Row['EMAIL_AMMINISTRATORI_TROVATA'] == 1) ? ACCESSO_IMMEDIATO : ACCESSO_SOSPESO;

            $Parametri['RAGIONE_SOCIALE']   = $Row['RAGIONE_SOCIALE'];
            $Parametri['ID_CLIENTE']        = $Row['CHIAVE'];
            $Parametri['ID_AMMINISTRATORE'] = $Row['ID_AMMINISTRATORE'];

            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                  'AppClientiLogin',
                                                  'ExtraScriptLoginQueries',
                                                  'AppClientiLog',
                                                  $Parametri,[]);
            $PDODBase->exec($SQLBody);
            $JSONAnswer->StoricoLogin = "Log eseguito";

            if($Parametri['ACCESSO'] == ACCESSO_IMMEDIATO && $NumeroMail == 0)
            {       
              $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                  'AppClientiLogin',
                                                  'ExtraScriptLoginQueries',
                                                  'UtenteInserito',
                                                  $Parametri,[]);
              $PDODBase->exec($SQLBody);

              $JSONAnswer->Utente = "Utente registrato";
              $JSONAnswer->IdResponseRegistrazione = RESPONSE_UTENTE_REGISTRATO;
            }
            else if ($Parametri['ACCESSO'] == ACCESSO_IMMEDIATO && $NumeroMail > 0)
            {
              $JSONAnswer->Utente = "Utente già registrato";
              $JSONAnswer->IdResponseRegistrazione = RESPONSE_UTENTE_GIA_REGISTRATO;
            }

            else if (($Parametri['ACCESSO'] == ACCESSO_SOSPESO || $Parametri['ACCESSO'] == ACCESSO_NEGATO) && $NumeroMail > 0)
            {
              $JSONAnswer->Utente = "Email già utilizzata";
              $JSONAnswer->IdResponseRegistrazione = RESPONSE_MAIL_GIA_PRESENTE;
            }

            else if ($Parametri['ACCESSO'] == ACCESSO_SOSPESO)
            {
              $JSONAnswer->Utente = "Email non presente nel database";
              $JSONAnswer->IdResponseRegistrazione = RESPONSE_IN_ATTESA_DI_CONFERMA;
            }
          }
          else
          {
            $JSONAnswer->StoricoLogin = "Log non eseguito: partita iva o codice fiscale non trovati";
            $JSONAnswer->Utente = '';
            $JSONAnswer->IdResponseRegistrazione = RESPONSE_CLIENTE_NON_TROVATO;
          }
        }
      }

      $Connection = new TAdvQueryAppClientiRegistrazione();
      $Connection->ServerSideScript();