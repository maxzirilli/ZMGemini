<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TOggetto
      {
        public $ChiaveCliente   = null;
        public $REGIONE         = null;
        public $COMUNE          = null;
        public $INDIRIZZO       = null;
        public $NR_CIVICO       = null;
        public $PROVINCIA       = null;
        public $RAGIONE_SOCIALE = null;
        public $CAP             = null;
        public $CATEGORIA       = null;

        function __construct($ChiaveCliente, $Regione, $Comune, $Provincia, $Cap, $RagioneSociale, $Indirizzo, $NrCivico, $Categoria)
        {
          $this->ChiaveCliente   = $ChiaveCliente;
          $this->REGIONE         = $Regione;
          $this->COMUNE          = $Comune;
          $this->INDIRIZZO       = $Indirizzo;
          $this->NR_CIVICO       = $NrCivico;
          $this->PROVINCIA       = $Provincia;
          $this->RAGIONE_SOCIALE = $RagioneSociale;
          $this->CAP             = $Cap;
          $this->CATEGORIA       = $Categoria;
        }
      }

      class TAdvQueryEsportaFileExcel extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $ListaClienti       = [];
          $ListaChiaviClienti = '';
          $Parametri          = JSON_decode($_POST['Params']); 

          $this->FGetListaClienti($PDODBase, $ListaClienti, $ListaChiaviClienti);

          $JSONAnswer->ListaClienti = $ListaClienti;
        }

        private function FGetListaClienti($PDODBase, &$ListaClienti, &$ListaChiaviClienti)
        {
          $Parametri        = JSON_decode($_POST['Params']); 

          $QueryPart = explode('FROM anagrafiche',
                                $this->FGetQueryCompiled($PDODBase,
                                                        'Clienti', 
                                                        'SelectClientiXFiltro',
                                                        'SelectClientiXFiltro', 
                                                        get_object_vars($Parametri)));

          $QueryPart = explode('ORDER BY UPPER(RAGIONE_SOCIALE', $QueryPart[count($QueryPart) - 1]);

          $SQLBody = "SELECT  anagrafiche.CHIAVE,
                              anagrafiche.RAGIONE_SOCIALE,
                              anagrafiche.INDIRIZZO_FATTURAZIONE AS INDIRIZZO,
                              anagrafiche.NR_CIVICO_FATTURAZIONE AS NR_CIVICO,
                              anagrafiche.COMUNE_FATTURAZIONE AS COMUNE,
                              anagrafiche.CAP_FATTURAZIONE AS CAP,
                              province.NOME AS PROVINCIA,
                              regioni.DESCRIZIONE AS REGIONE,
                              cat_clienti.DESCRIZIONE AS CATEGORIA
                         FROM anagrafiche 
                        " . $QueryPart[0] . " 
                        AND anagrafiche.PROVINCIA_FATTURAZIONE IS NOT NULL
                        ORDER BY REGIONE, PROVINCIA, COMUNE, CAP";

          try 
          {
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                $OggettoCliente = new TOggetto($Row['CHIAVE'], 
                                              $Row['REGIONE'], 
                                              $Row['COMUNE'], 
                                              $Row['PROVINCIA'], 
                                              $Row['CAP'], 
                                              $Row['RAGIONE_SOCIALE'],
                                              $Row['INDIRIZZO'],
                                              $Row['NR_CIVICO'],
                                              $Row['CATEGORIA']);

                $ListaChiaviClienti .= $Row['CHIAVE'] . ',';
                array_push($ListaClienti, $OggettoCliente);
              }
            }
          }
          catch (Exception $e) 
          {
            error_log($SQLBody);
            error_log($e->getMessage());
          }

          if($ListaChiaviClienti != '')
            $ListaChiaviClienti = substr($ListaChiaviClienti, 0, -1);
        }  

      }

      $Connection = new TAdvQueryEsportaFileExcel();
      $Connection->ServerSideScript();
?>