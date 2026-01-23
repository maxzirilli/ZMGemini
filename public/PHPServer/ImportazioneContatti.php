<?php
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';

  header("Content-Type: application/x-httpd-php;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS");

  // ATTENZIONE CONTROLLO CARATTERE \u00a0

  class TContattoPresenteSuDb
  {
    public $Chiave = '';
    public $Email  = '';

    public function __construct($Chiave, $Email) 
    {
      $this->Chiave = $Chiave;
      $this->Email  = $Email;
    }
  }

  class TContatto
  {
    public $RagioneSociale = '';
    public $P_Iva          = '';
    public $Indirizzo      = '';
    public $Comune         = '';
    public $Provincia      = -1;
    public $Cap            = '';
    public $Fatturato      =  0;
    public $ListaEmail     = [];
    public $Telefono       = '';
    public $Cellulare      = '';
    public $Linkedin       = '';
    public $SitoWeb        = '';
    public $Note           = '';
    public $EmailBenvenutoInviata           = false;
  }

  class TGetJSONClienti extends TAdvQuery
  {
    protected function FExtraScriptIgnoreLogin()
    {
      return true;
    }

    private function FControlloVettore($Stringa, $StringaSostitutiva, &$Vettore)
    {
      for($i = 0; $i < count($Vettore); $i++)
        while (str_contains($Vettore[$i], $Stringa))
          $Vettore[$i] = str_replace($Stringa, $StringaSostitutiva, $Vettore[$i]);
    }

    private function FControlloLista(&$ListaEmail)
    {
      $this->FControlloVettore(" @", "@", $ListaEmail);
      $this->FControlloVettore("\n", ";", $ListaEmail);
      $this->FControlloVettore("\r\n", ";", $ListaEmail);
      $this->FControlloVettore("\r", ";", $ListaEmail);
      $this->FControlloVettore(",", ";", $ListaEmail);
      $this->FControlloVettore(":", ";", $ListaEmail);
      $this->FControlloVettore(" ", ";", $ListaEmail);
      $this->FControlloVettore("|", ";", $ListaEmail);
      $this->FControlloVettore("\\", ";", $ListaEmail);
    }

    private function FGetStringaListaEmail($ListaEmail)
    {
      $Result = '';
      $Lista  = array();

      $this->FControlloLista($ListaEmail);

      foreach($ListaEmail as $Email)
      {
        $VettoreACapo = explode(";" , $Email);

        foreach($VettoreACapo as $Email1)
          if(str_contains($Email1, '@'))
            array_push($Lista, trim($Email1));
      }

      $Lista = array_values(array_unique($Lista));

      for($i = 0; $i < count($Lista); $i++)
        $Result .= $Lista[$i] . ';';

      return $Result;
    }

    protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
    {
      $ListaContatti = JSON_decode($_POST['Params']);

      $JSONAnswer->ContattiAggiunti            = array();
      $JSONAnswer->ContattiAggiornati          = array();
      $JSONAnswer->ContattiSenzaPartitaIva     = array();

      if(!isset($ListaContatti))
        throw new Exception('JSON errore sintassi');         
    
      for($i = 0; $i < count($ListaContatti); $i++)
      {
        $Contatto = $ListaContatti[$i];

        if(isset($Contatto->P_Iva) && $Contatto->P_Iva != '')
        {
          $ContattoRicerca              = new stdClass();
          $ContattoRicerca->PARTITA_IVA = $Contatto->P_Iva;

          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'Admin_GestioneContatti', 
                                              'EsisteContatto',
                                              'EsisteContatto', 
                                              (array) $ContattoRicerca);
          $ListaContattiGiaPresenti = array();

          $ContattoRicerca = null;

          try
          {
            if($Query = $PDODBase->query($SQLBody))
              if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if(isset($Row['CHIAVE']))
                  array_push($ListaContattiGiaPresenti, new TContattoPresenteSuDb($Row['CHIAVE'], $Row['EMAIL']));
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw $e;
          } 

          $this->FGestioneInsertUpdate($ListaContattiGiaPresenti, $Contatto, $PDODBase, $JSONAnswer);
        }
        else
        {
          array_push($JSONAnswer->ContattiSenzaPartitaIva, $Contatto->RagioneSociale);

          $ContattoRicercaRagioneSociale    = new stdClass();
          $ContattoRicercaRagioneSociale->RAGIONE_SOCIALE = $Contatto->RagioneSociale;

          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'Admin_GestioneContatti', 
                                              'EsisteContattoRagioneSociale',
                                              'EsisteContatto', 
                                              (array) $ContattoRicercaRagioneSociale);

          $ListaContattiGiaPresenti = array();

          $ContattoRicercaRagioneSociale = null;

          try
          {
            if($Query = $PDODBase->query($SQLBody))
              if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if(isset($Row['CHIAVE']))
                  array_push($ListaContattiGiaPresenti, new TContattoPresenteSuDb($Row['CHIAVE'], $Row['EMAIL']));
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw $e;
          } 

          $this->FGestioneInsertUpdate($ListaContattiGiaPresenti, $Contatto, $PDODBase, $JSONAnswer);
        }
      }

      error_log(json_encode($JSONAnswer));
    }

    private function FGestioneInsertUpdate($ListaContattiGiaPresenti, $Contatto, $PDODBase, &$JSONAnswer)
    {
      if(count($ListaContattiGiaPresenti) == 0)
      {
        $ParametriInserimento                  = new stdClass();
        $ParametriInserimento->RAGIONE_SOCIALE = $Contatto->RagioneSociale;
        $ParametriInserimento->P_IVA           = $Contatto->P_Iva;
        $ParametriInserimento->INDIRIZZO       = $Contatto->Indirizzo;
        $ParametriInserimento->PROVINCIA       = $Contatto->Provincia;
        $ParametriInserimento->COMUNE          = $Contatto->Comune;
        $ParametriInserimento->CAP             = $Contatto->Cap;
        $ParametriInserimento->FATTURATO       = $Contatto->Fatturato;
        $ParametriInserimento->COD_ATECO_1     = $Contatto->Ateco1;
        $ParametriInserimento->COD_ATECO_2     = $Contatto->Ateco2;
        $ParametriInserimento->COD_ATECO_3     = $Contatto->Ateco3;
        $ParametriInserimento->EMAIL           = $this->FGetStringaListaEmail($Contatto->ListaEmail);
        $ParametriInserimento->TELEFONO        = '';
        $ParametriInserimento->CELLULARE       = '';
        $ParametriInserimento->SITO_WEB        = $Contatto->SitoWeb;
        $ParametriInserimento->LINKEDIN        = $Contatto->Linkedin;
        $ParametriInserimento->NOTE            = $Contatto->Note;
        $ParametriInserimento->EMAIL_BENVENUTO_INVIATA    = 'F';

        $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                            'Admin_GestioneContatti', 
                                            'EditSQL',
                                            'Insert', 
                                            (array) $ParametriInserimento,
                                            [1]);

        try
        {
          $PDODBase->query($SQLBody);
          array_push($JSONAnswer->ContattiAggiunti, $Contatto->RagioneSociale);
        }
        catch(Exception $e)
        {
          error_log($SQLBody);
          throw $e;
        } 

        $ParametriInserimento = null;
      }
      else
      {
        for($j = 0; $j < count($ListaContattiGiaPresenti); $j++)
        {
          $EmailControllate = $this->FGetStringaListaEmail($Contatto->ListaEmail);

          $ParametriInserimento         = new stdClass();
          $ParametriInserimento->EMAIL  = $EmailControllate;
          $ParametriInserimento->CHIAVE = $ListaContattiGiaPresenti[$j]->Chiave;

          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'Admin_GestioneContatti', 
                                              'EditSQL',
                                              'UpdateEmail', 
                                              (array) $ParametriInserimento);

          try
          {
            $PDODBase->query($SQLBody);
            array_push($JSONAnswer->ContattiAggiornati, $Contatto->RagioneSociale);
          }
          catch(Exception $e)
          {
            error_log($SQLBody);
            throw $e;
          } 
          $ParametriInserimento = null;
        }
      }
    }

    // private function FControlloDoppioniInseriti($Contatto, $EmailGiaPresenti)
    // {
    //   $Result = '';

    //   $ArrayEmailContatto     = $this->FGetArrayListaEmail($Contatto->ListaEmail);

    //   $ArrayEmailGiaPresenti = explode(';', $EmailGiaPresenti);

    //   if(count($ArrayEmailGiaPresenti) != 0)
    //     if($ArrayEmailGiaPresenti[count($ArrayEmailGiaPresenti) - 1] == '')
    //       array_pop($ArrayEmailGiaPresenti);

    //   for($i = 0; $i < count($ArrayEmailContatto); $i++)
    //   {
    //     $Trovato = false;
    //     for($j = 0; $j < count($ArrayEmailGiaPresenti); $j++)
    //     {
    //       if(strtoupper($ArrayEmailGiaPresenti[$j]) == strtoupper($ArrayEmailContatto[$i]))
    //       {
    //         $Trovato = true;
    //         break; 
    //       }
    //     }  

    //     if(!$Trovato)      
    //       $Result .= $ArrayEmailContatto[$i] . ';';
    //   }

    //   return $Result;
    // }
  }

  $GetJSONClienti = new TGetJSONClienti();
  $GetJSONClienti->ServerSideScript(true);