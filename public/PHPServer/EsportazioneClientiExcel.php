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

          $ArrayChiavi = [];

          $ResultChiavi = $this->FGetQueryResult($PDODBase,
                                                'Clienti', 
                                                'SelectClientiXFiltro',
                                                'SelectClientiXFiltro', 
                                                get_object_vars($Parametri));
                
          if($ResultChiavi)
            while($Row = $ResultChiavi->fetch(PDO::FETCH_ASSOC))
              array_push($ArrayChiavi, $Row['CHIAVE']);
          
          $StringaChiavi = implode(',', $ArrayChiavi);

          $Parametri->ListaChiavi = $ArrayChiavi;


          $ResultClienti = $this->FGetQueryResult($PDODBase,
                                                'Clienti', 
                                                'EsportaXLSXClienti',
                                                'EsportaXLSXClienti', 
                                                get_object_vars($Parametri));

          try 
          {
            if($ResultClienti)
            {
              while($Row =  $ResultClienti->fetch(PDO::FETCH_ASSOC))
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
