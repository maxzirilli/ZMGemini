<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once 'ClassPrimaNota.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TMisure
      {
        public $Width = null;
        public $Left  = null;
      }

      class TStampaPrimaNota
      {
        public $BAND_INTESTAZIONE = null;
        public $BAND_GROUP_FOOTER = null;
        public $BAND_MOVIMENTI    = null;
        public $BAND_PAGE_FOOTER  = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null; 
      }

      class TRigaMovimento
      {
        public $LB_DATA               = null;
        public $LB_DATA_DOC           = null;
        public $LB_COD_CLIENTE        = null;
        public $MEMO_DESCRIZIONE      = null;
        public $NomeEtichettaEntrante = null;
        public $NomeEtichettaUscente  = null;
        public $LB_ENTRANTE0          = null;
        public $LB_USCENTE0           = null;
        public $LB_ENTRANTE1          = null;
        public $LB_USCENTE1           = null;
        public $LB_ENTRANTE2          = null;
        public $LB_USCENTE2           = null;
        public $LB_ENTRANTE3          = null;
        public $LB_USCENTE3           = null;
        public $LB_ENTRANTE4          = null;
        public $LB_USCENTE4           = null;
        public $LB_ENTRANTE5          = null;
        public $LB_USCENTE5           = null;
        public $LB_ENTRANTE6          = null;
        public $LB_USCENTE6           = null;
        public $LB_ENTRANTE7          = null;
        public $LB_USCENTE7           = null;
        public $LB_ENTRANTE8          = null;
        public $LB_USCENTE8           = null;
        public $LB_ENTRANTE9          = null;
        public $LB_USCENTE9           = null;
      }

      class TRigaTitoliFinali
      {
        public $TotaleEtichetta         = null;
        public $LB_TITOLO_TOTALE        = null;
        public $LB_TITOLO_TOTALE0       = null;
        public $LB_TITOLO_TOTALE1       = null;
        public $LB_TITOLO_TOTALE2       = null;
        public $LB_TITOLO_TOTALE3       = null;
        public $LB_TITOLO_TOTALE4       = null;
        public $LB_TITOLO_TOTALE5       = null;
        public $LB_TITOLO_TOTALE6       = null;
        public $LB_TITOLO_TOTALE7       = null;
        public $LB_TITOLO_TOTALE8       = null;
        public $LB_TITOLO_TOTALE9       = null;
        public $LB_TOTALE_FINE_PERIODO0 = null;
        public $LB_TOTALE_FINE_PERIODO1 = null;
        public $LB_TOTALE_FINE_PERIODO2 = null;
        public $LB_TOTALE_FINE_PERIODO3 = null;
        public $LB_TOTALE_FINE_PERIODO4 = null;
        public $LB_TOTALE_FINE_PERIODO5 = null;
        public $LB_TOTALE_FINE_PERIODO6 = null;
        public $LB_TOTALE_FINE_PERIODO7 = null;
        public $LB_TOTALE_FINE_PERIODO8 = null;
        public $LB_TOTALE_FINE_PERIODO9 = null;
        public $LB_TOTALE_ENTRANTE0     = null;
        public $LB_TOTALE_USCENTE0      = null;
        public $LB_TOTALE_ENTRANTE1     = null;
        public $LB_TOTALE_USCENTE1      = null;
        public $LB_TOTALE_ENTRANTE2     = null;
        public $LB_TOTALE_USCENTE2      = null;
        public $LB_TOTALE_ENTRANTE3     = null;
        public $LB_TOTALE_USCENTE3      = null;
        public $LB_TOTALE_ENTRANTE4     = null;
        public $LB_TOTALE_USCENTE4      = null;
        public $LB_TOTALE_ENTRANTE5     = null;
        public $LB_TOTALE_USCENTE5      = null;
        public $LB_TOTALE_ENTRANTE6     = null;
        public $LB_TOTALE_USCENTE6      = null;
        public $LB_TOTALE_ENTRANTE7     = null;
        public $LB_TOTALE_USCENTE7      = null;
        public $LB_TOTALE_ENTRANTE8     = null;
        public $LB_TOTALE_USCENTE8      = null;
        public $LB_TOTALE_ENTRANTE9     = null;
        public $LB_TOTALE_USCENTE9      = null;
      }
      
      class TRigaTitoli
      {
        public $LB_TITOLO = null;
        public $NomeEtichetta = null;
        public $TotaleEtichetta = null;
        public $QR_LOGO = null;
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null;
        public $LB_TITOLO_CONTO0 = null;
        public $LB_TITOLO_CONTO1 = null;
        public $LB_TITOLO_CONTO2 = null;
        public $LB_TITOLO_CONTO3 = null;
        public $LB_TITOLO_CONTO4 = null;
        public $LB_TITOLO_CONTO5 = null;
        public $LB_TITOLO_CONTO6 = null;
        public $LB_TITOLO_CONTO7 = null;
        public $LB_TITOLO_CONTO8 = null;
        public $LB_TITOLO_CONTO9 = null;
      }

      class TReportPrimaNota extends TZReport
      {
         private $FReportEnded  = true;
         private $FContatore    = 0;
         private $FCambioColore = false;

         public function BeforeBandRepetitionPrint($ABand, $ASingleRecord, &$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($this->FContatore == 1)
           {
            if($ABand->Name == 'BAND_INTESTAZIONE')
            {
                $PrintBand = $this->FReportEnded;
                $this->FReportEnded = false;
            }
           }
           else $this->FContatore = 1;

           if($ABand->Name == 'BAND_MOVIMENTI')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }


      class TEtichetteConto 
      {
        public $EtichettaIntestazioneNomeConto              = null;
        public $EtichettaIntestazioneEntrateConto           = null;
        public $EtichettaIntestazioneUsciteConto            = null;
        public $EtichettaIntestazioneTotaleConto            = null;
        public $EtichettaTotaleEntrate                      = null;
        public $EtichettaTotaleUscite                       = null;
        public $EtichettaIntestazioneTotaleContoFinePeriodo = null;
        public $EtichettaEntrataConto                       = null;
        public $EtichettaUscitaConto                        = null;
        public $EtichettaDescrizione                        = null;
        public $LineeVerticaliTabella                       = null;
      }

      class TExtraStampaPrimaNota extends TAdvQueryCaricaInformazioniPrimaNota
      {  
        public $Etichette               = array();
        public $SizeDescrizione         = 0;
        public $SizeSaldoInizio         = 0;
        public $SizeSaldoFine           = 0;
        public $SizeTotaleEntrateUscite = 0;
        public $SizeLbDescrizione       = 0;
        public $SpazioCalcolatoXConti   = 0;
        public $NumeroConti             = 0;
        public $PageSizeWidth           = 0;
        public $PageMarginRight         = 0;

        private function FLoadEtichette($JSONAnswer,&$Model, $Report)
        {

          for($i = 0; $i < count($JSONAnswer->LsContiCorrenti); $i++)
          {
            $Etichetta = new TEtichetteConto();
            array_push($this->Etichette, $Etichetta);
          }
          
          $MisureXConto = GRANDEZZA_CONTI_PRIMA_NOTA;
          $MisuraMinimaDescrizione = GRANDEZZA_MINIMA_DESCRIZIONE;

          for($j = 0; $j < count($Model->Bands); $j++)
            if(isset($Model->Bands[$j]->ChildBand))
              for($i = 0; $i < count($Model->Bands[$j]->ChildBand->Objects); $i++)
                if($Model->Bands[$j]->ChildBand->Objects[$i]->Name == 'LB_TITOLO_DESCRIZIONE')
                {
                  $this->SizeDescrizione = $Report->GetObjectSizes($Model->Bands[$j]->ChildBand->Objects[$i]);
                  $this->SizeDescrizione->Width = $this->PageSizeWidth - $this->SizeDescrizione->Left - ($this->PageMarginRight * 2) - ($MisureXConto * $this->NumeroConti);
                  if($this->SizeDescrizione->Width < $MisuraMinimaDescrizione)
                  {
                    $this->SizeDescrizione->Width = $MisuraMinimaDescrizione;
                  }
                  $Report->SetObjectSizes($Model->Bands[$j]->ChildBand->Objects[$i], $this->SizeDescrizione);
                }
            
          for($j = 0; $j < count($Model->Bands); $j++)
          {
            if(isset($Model->Bands[$j]->Objects) && $Model->Bands[$j]->Name == 'BAND_MOVIMENTI')
              for($i = 0; $i < count($Model->Bands[$j]->Objects); $i++)
              {
                if($Model->Bands[$j]->Objects[$i]->Name == 'MEMO_DESCRIZIONE')
                {
                  $this->SizeLbDescrizione = $Report->GetObjectSizes($Model->Bands[$j]->Objects[$i]);
                  $this->SizeLbDescrizione->Width = $this->PageSizeWidth - $this->SizeDescrizione->Left - ($this->PageMarginRight * 2) - ($MisureXConto * $this->NumeroConti);
                  if($this->SizeLbDescrizione->Width < $MisuraMinimaDescrizione)
                  {
                    $this->SizeLbDescrizione->Width = $MisuraMinimaDescrizione;
                    $MisureXConto = ($this->PageSizeWidth - $this->SizeDescrizione->Left - ($this->PageMarginRight * 2) - $this->SizeLbDescrizione->Width) / $this->NumeroConti;
                  }
                  $Report->SetObjectSizes($Model->Bands[$j]->Objects[$i], $this->SizeLbDescrizione);
                }
              }
          }
          // $this->SpazioCalcolatoXConti = $this->PageSizeWidth - $this->PageMarginRight - $this->SizeDescrizione->Left - $this->SizeDescrizione->Width - 9.8;

           
          for($j = 0; $j < count($Model->Bands); $j++)
            if(isset($Model->Bands[$j]->ChildBand) && $Model->Bands[$j]->Name == 'BAND_INTESTAZIONE')
             for($i = 0; $i < count($Model->Bands[$j]->ChildBand->Objects); $i++)
               for($k = 0; $k < 10; $k++ )
               {
                  $NomeEtichetta = 'LB_TITOLO_CONTO' . $k;
                  if($Model->Bands[$j]->ChildBand->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                        array_splice($Model->Bands[$j]->ChildBand->Objects,$i,1);
                        $i--;
                      }
                      else $this->Etichette[$k]->EtichettaIntestazioneNomeConto = $Model->Bands[$j]->ChildBand->Objects[$i];
                      break;
                  }
                  $NomeEtichetta = 'LB_TITOLO_ENTRANTI' . $k;
                  if($Model->Bands[$j]->ChildBand->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->ChildBand->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaIntestazioneEntrateConto = $Model->Bands[$j]->ChildBand->Objects[$i];
                      break;
                  }
                  $NomeEtichetta = 'LB_TITOLO_USCENTI' . $k;
                  if($Model->Bands[$j]->ChildBand->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->ChildBand->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaIntestazioneUsciteConto = $Model->Bands[$j]->ChildBand->Objects[$i];
                      break;
                  }
                 
                }

          for($j = 0; $j < count($Model->Bands); $j++)
            if(isset($Model->Bands[$j]->Objects) && $Model->Bands[$j]->Name == 'BAND_GROUP_FOOTER')
             for($i = 0; $i < count($Model->Bands[$j]->Objects); $i++)
             {
               if($Model->Bands[$j]->Objects[$i]->Name == 'LB_SALDO_FINE')
               {
                  $this->SizeSaldoFine = $Report->GetObjectSizes($Model->Bands[$j]->Objects[$i]);
                  $this->SizeSaldoFine->Width = $this->PageSizeWidth - ($this->PageMarginRight * 2) - ($MisureXConto * $this->NumeroConti);
                  $Report->SetObjectSizes($Model->Bands[$j]->Objects[$i], $this->SizeSaldoFine);
               }
                 
                if($Model->Bands[$j]->Objects[$i]->Name == 'LB_SALDO_INIZIO')
                {
                  $this->SizeSaldoInizio = $Report->GetObjectSizes($Model->Bands[$j]->Objects[$i]);
                  $this->SizeSaldoInizio->Width = $this->PageSizeWidth - ($this->PageMarginRight * 2) - ($MisureXConto * $this->NumeroConti);
                  $Report->SetObjectSizes($Model->Bands[$j]->Objects[$i], $this->SizeSaldoInizio);
                }
               
               if($Model->Bands[$j]->Objects[$i]->Name == 'LB_TOTALI_ENTRATE_USCITE')
               {
                 $this->SizeTotaleEntrateUscite = $Report->GetObjectSizes($Model->Bands[$j]->Objects[$i]);
                 $this->SizeTotaleEntrateUscite->Width = $this->PageSizeWidth - ($this->PageMarginRight *2 ) - ($MisureXConto * $this->NumeroConti);
                 $Report->SetObjectSizes($Model->Bands[$j]->Objects[$i], $this->SizeTotaleEntrateUscite);
               }
               
               for($k = 0; $k < 10; $k++ )
               {
                  $NomeEtichetta = 'LB_TOTALE_FINE_PERIODO' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaIntestazioneTotaleContoFinePeriodo = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                  
                   $NomeEtichetta = 'LB_TITOLO_TOTALE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaIntestazioneTotaleConto = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                  $NomeEtichetta = 'LB_TOTALE_ENTRANTE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                        array_splice($Model->Bands[$j]->Objects,$i,1);
                        $i--;
                      }
                      else $this->Etichette[$k]->EtichettaTotaleEntrate = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                  $NomeEtichetta = 'LB_TOTALE_USCENTE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaTotaleUscite = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                 
              }
            }
             

          for($j = 0; $j < count($Model->Bands); $j++)
            if(isset($Model->Bands[$j]->Objects) && $Model->Bands[$j]->Name == 'BAND_MOVIMENTI')
              for($i = 0; $i < count($Model->Bands[$j]->Objects); $i++)
              {
                for($k = 0; $k < 10; $k++ )
                {
                  $NomeEtichetta = 'LB_USCENTE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                        array_splice($Model->Bands[$j]->Objects,$i,1);
                        $i--;
                      }
                      else $this->Etichette[$k]->EtichettaUscitaConto = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                  $NomeEtichetta = 'LB_ENTRANTE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti))
                      {
                          array_splice($Model->Bands[$j]->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->EtichettaEntrataConto = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
                }
              }

          for($j = 0; $j < count($Model->Bands); $j++)
            if(isset($Model->Bands[$j]->Objects) && $Model->Bands[$j]->Name == 'BAND_MOVIMENTI')
             for($i = 0; $i < count($Model->Bands[$j]->Objects); $i++)
             {
               for($k = 0; $k < 10; $k++ )
               {
                  $NomeEtichetta = 'QR_SHAPE' . $k;
                  if($Model->Bands[$j]->Objects[$i]->Name == $NomeEtichetta)
                  {
                      if($k >= count($JSONAnswer->LsContiCorrenti) - 1)
                      {
                          array_splice($Model->Bands[$j]->Objects,$i,1);
                          $i--;
                      }
                      else $this->Etichette[$k]->LineeVerticaliTabella = $Model->Bands[$j]->Objects[$i];
                      break;
                  }
               }
               if($Model->Bands[$j]->Objects[$i]->Name == 'QR_LINEA_DESCRIZIONE')
               {
                  $Misure = new TMisure();
                  $Misure->Width = 1;
                  $Misure->Left  = $this->SizeDescrizione->Left + $this->SizeDescrizione->Width;
                  $Report->SetObjectSizes($Model->Bands[$j]->Objects[$i], $Misure);
               }
             }


          for($i = 0; $i < count($this->Etichette); $i++)
          {
            $Misure = new TMisure();
            $Misure->Width = $MisureXConto;
            $Misure->Left  = $this->SizeDescrizione->Left + $this->SizeDescrizione->Width + ($MisureXConto * $i);
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaIntestazioneNomeConto, $Misure);

            $Misure->Width = $MisureXConto;
            $Misure->Left  = $this->SizeDescrizione->Left + $this->SizeDescrizione->Width + ($MisureXConto * $i);
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaIntestazioneTotaleConto, $Misure);

            $Misure->Width = $MisureXConto / 2;
            $Misure->Left  = $Misure->Left;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaIntestazioneEntrateConto, $Misure);

            $Misure->Width = $MisureXConto / 2;
            $Misure->Left  = $Misure->Left;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaEntrataConto, $Misure);

            $Misure->Width = $MisureXConto / 2;
            $Misure->Left  = $Misure->Left + $MisureXConto / 2;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaIntestazioneUsciteConto, $Misure);

            $Misure->Width = $MisureXConto / 2;
            $Misure->Left  = $Misure->Left;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaUscitaConto, $Misure);
           
            $Misure->Width = $MisureXConto;
            $Misure->Left  = $this->SizeDescrizione->Left + $this->SizeDescrizione->Width + ($MisureXConto * $i);
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaIntestazioneTotaleContoFinePeriodo, $Misure);
            
            $Misure->Width = $MisureXConto;
            $Misure->Left  = $Misure->Left;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaTotaleEntrate, $Misure);

            $Misure->Width = $MisureXConto;
            $Misure->Left  = $Misure->Left;
            $Report->SetObjectSizes($this->Etichette[$i]->EtichettaTotaleUscite, $Misure);
            

            if($i < count($this->Etichette) - 1)
            {
              $Misure->Width = 1;
              $Misure->Left  = $this->SizeDescrizione->Left + $this->SizeDescrizione->Width + ($MisureXConto * ($i + 1));
              $Report->SetObjectSizes($this->Etichette[$i]->LineeVerticaliTabella, $Misure);
            }
          }
        }
        

        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
            parent::FExtraScriptServerSide($PDODBase,$JSONAnswer);

            $Report = new TReportPrimaNota();

            $Model = new stdClass();
            
           
            //modifico il modello per eliminare le label che non vogliamo mettere e cambiare posizione e dimensione delle altre
            $Model = file_get_contents('ModelliStampe/QrStampaPrimaNota.json');

            $Model = json_decode($Model);

            $this->NumeroConti = count($JSONAnswer->LsContiCorrenti);


            $PageValues  = $Report->GetDFMArray($Model->PageValues);

            $this->PageSizeWidth     = $PageValues[3] / 10;
            $this->PageMarginRight   = $PageValues[5] / 10;
            

            $this->FLoadEtichette($JSONAnswer, $Model, $Report);

            $NomeLogo = '';
            $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
                          FROM impostazioni";

            if($Query = $PDODBase->query($SQLBody))
               while($Row = $Query->fetch(PDO::FETCH_ASSOC))
               { 
                 if(isset($Row['LOGO_AZIENDA']))
                 {
                    $NomeLogo = date("Y-m-d_H_i_s_U");
                    ForceDirectory(NOME_CARTELLA_FILE_TEMP);
                    file_put_contents(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg',base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
                 }
               }

            $Report->AssignModel($Model);
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($JSONAnswer,$PDODBase, $NomeLogo))); 
            unset($JSONAnswer->LsContiCorrenti);
            unset($JSONAnswer->LsMovimenti);

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');
        }

        private function FGetDatiStampa($JSONAnswer, $PDODBase, $NomeLogo)
        {
          $Result = new TStampaPrimaNota();
          $Result->BAND_INTESTAZIONE = array();
          $DatiIntestazione = new TDatiIntestazione();

          $Result->BAND_GROUP_FOOTER = array();

          $RigaTitoli = new TRigaTitoli();
          
          TSystemInformation::GetDatiIntestazione($PDODBase, $RigaTitoli);

          $RigaTitoli->LB_TITOLO = 'PRIMA NOTA ' . date("d/m/Y", strtotime($this->DallaData)) . ' - ' . date("d/m/Y", strtotime($this->AllaData));
          for($i = 0; $i < count($JSONAnswer->LsContiCorrenti); $i++)
          {
            $NomeEtichetta = 'LB_TITOLO_CONTO' . $i;
            $RigaTitoli->$NomeEtichetta = $JSONAnswer->LsContiCorrenti[$i]->DescrizioneAbbr;
                  
          }  
          array_push($Result->BAND_INTESTAZIONE,$RigaTitoli);

        
          $RigaTitoliFinali = new TRigaTitoliFinali();
          for($i = 0; $i < count($JSONAnswer->LsContiCorrenti); $i++)
          {
            $RigaTitoliFinali->LB_TITOLO_TOTALE = '';
            $TotaleEtichettaEntrante = 'LB_TOTALE_ENTRANTE' . $i;
            $TotaleEtichettaUscente  = 'LB_TOTALE_USCENTE'. $i;

            $TotaleEntrante          = 0;
            $TotaleUscente           = 0;
            
            $TotaleEtichetta = 'LB_TOTALE_FINE_PERIODO' . $i;
            $RigaTitoliFinali->$TotaleEtichetta = number_format($JSONAnswer->LsContiCorrenti[$i]->TotaleTmp /100, 2, ',', '.') . '€'; 
            $TotaleEtichettaInizio = 'LB_TITOLO_TOTALE' . $i;
            $RigaTitoliFinali->$TotaleEtichettaInizio =  number_format($JSONAnswer->LsContiCorrenti[$i]->Saldo/100, 2, ',', '.') . '€';             
            foreach ($JSONAnswer->LsMovimenti as $Movimento)    
            {
               if($JSONAnswer->LsContiCorrenti[$i]->Chiave == $Movimento->ContoCorrente )
               {
                 if($Movimento->Importo >= 0)
                    $TotaleEntrante += $Movimento->Importo;
                 if($Movimento->Importo < 0)
                    $TotaleUscente += $Movimento->Importo;
               }    
            }
             $RigaTitoliFinali->$TotaleEtichettaEntrante = number_format($TotaleEntrante/100, 2, ',', '.') . '€';
             $RigaTitoliFinali->$TotaleEtichettaUscente  = number_format($TotaleUscente/100, 2, ',', '.') . '€';
          
          }  
          array_push($Result->BAND_GROUP_FOOTER,$RigaTitoliFinali);

          if(!$this->OrdineDecrescente)
            usort($JSONAnswer->LsMovimenti, function($a, $b) {return strcmp($a->Data, $b->Data);});
          else usort($JSONAnswer->LsMovimenti, function($a, $b) {return strcmp($b->Data, $a->Data);});

         

          $Result->BAND_MOVIMENTI = array();
          foreach ($JSONAnswer->LsMovimenti as $Movimento)    
          {
            $RigaMovimento = new TRigaMovimento();

            $RigaMovimento->LB_DATA = date("d/m/Y", strtotime($Movimento->Data));
            $RigaMovimento->LB_DATA_DOC = $Movimento->DataDocumento != ''? date("d/m/Y", strtotime($Movimento->DataDocumento)) : '';
            $RigaMovimento->LB_COD_CLIENTE = $Movimento->Codice != ''? $Movimento->Codice : '';

            $RigaMovimento->MEMO_DESCRIZIONE = $Movimento->Descrizione; 
            if(isset($Movimento->DescrizioneTipologia))
            {
              $RigaMovimento->MEMO_DESCRIZIONE = $Movimento->Descrizione . ' ' . $Movimento->DescrizioneTipologia;
            }
            
            if ($Movimento->Note != '')
              $RigaMovimento->MEMO_DESCRIZIONE .= "\nNote: " . $Movimento->Note;
/*            if($Movimento->DataDocumento != '')
              array_push($RigaMovimento->MEMO_DESCRIZIONE,'Data documento: ' . date("d/m/Y", strtotime($Movimento->DataDocumento)));*/
            
            for($i = 0; $i < count($JSONAnswer->LsContiCorrenti); $i++)
            {
              $NomeEtichettaEntrante = 'LB_ENTRANTE' . $i;
              $NomeEtichettaUscente  = 'LB_USCENTE' . $i;
              if($Movimento->Importo >= 0)
              {
                if($JSONAnswer->LsContiCorrenti[$i]->Chiave == $Movimento->ContoCorrente)
                  $RigaMovimento->$NomeEtichettaEntrante = number_format($Movimento->Importo / 100, 2, ',', '.') . '€';
                else $RigaMovimento->$NomeEtichettaEntrante = '';
                $RigaMovimento->$NomeEtichettaUscente = '';
              }
              else
              {
                if($JSONAnswer->LsContiCorrenti[$i]->Chiave == $Movimento->ContoCorrente)
                  $RigaMovimento->$NomeEtichettaUscente = number_format($Movimento->Importo / 100, 2, ',', '.') . '€';
                else $RigaMovimento->$NomeEtichettaUscente = '';
                $RigaMovimento->$NomeEtichettaEntrante = '';
              }
              
            }
            array_push($Result->BAND_MOVIMENTI,$RigaMovimento);

          } 

          $Result->BAND_PAGE_FOOTER = array();
          $RigaFooter = new stdClass();  
          array_push($Result->BAND_PAGE_FOOTER,$RigaFooter);
          
          if($NomeLogo != '')
            $RigaTitoli->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

          return $Result;
        }
      }

      $Connection = new TExtraStampaPrimaNota();
      $Connection->ServerSideScript();