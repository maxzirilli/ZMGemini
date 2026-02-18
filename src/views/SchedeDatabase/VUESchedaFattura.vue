<template ref="refTemplate">
  <VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>
  
 <VUEConfirm :Popup="PopupSchedaFattura" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Vuoi numerare la fattura?'" @onClickConfermaPopup="ConfermaNumerazioneFattura" @onClickChiudiPopup="AnnullaPopup">
 </VUEConfirm>

 <VUEConfirm :Popup="SchedaFattura.PopupAnticipoCliente" 
             :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma"
             :Richiesta="'Il cliente ha un anticipo da scalare. Vuoi aggiungere ' + SchedaFattura.AnticipoCliente + ' € di anticipo alla fattura?'" 
             @onClickConfermaPopup="ConfermaAnticipoInFattura" 
             @onClickChiudiPopup="OnClickRifiutaAnticipo">
 </VUEConfirm>

 <VUEConfirm :Popup="SchedaFattura.PopupNotaDiCreditoApertaCliente" 
             :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
             :Richiesta="'Il cliente ha una nota di credito da scalare. Vuoi andare alla nota?'" 
             @onClickConfermaPopup="OnClickConfermaVaiAllaNotaDiCreditoAperta" 
             @onClickChiudiPopup="OnClickRifiutaVaiAllaNotaDiCreditoAperta">
 </VUEConfirm>

 <VUEConfirm :Popup="PopupCorreggiFattura"  :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Richiesta="'Vuoi correggere la fattura?'" @onClickConfermaPopup="ConfermaCorrezione" @onClickChiudiPopup="AnnullaPopup">
 </VUEConfirm>
 <VUEModal v-if="PopupScegliFileXML" :Titolo="'Lista file .xml'" :Altezza="'20%'" :Larghezza="'50%'"
          @onClickChiudiModal="OnClickChiudiPopupScegliFile">
    <template v-slot:Body>
          <div class="row wrapper" style="height:100%">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:4%"></th>
                      <th style="width:20%">Data file</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="File in ListaFileXML" :key="File.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="File.Selezionato">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        Data invio del file: {{ File.DATA_INVIO }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
            
          </div>
    </template>
    <template v-slot:Footer>
      <button class="btn btn-danger" @Click="OnClickChiudiPopupScegliFile" style="float:right;margin-right:20px;width:20%">Annulla</button>
      <button v-if="ControlloFileXMLSelezionati" class="btn btn-success" @click="OnClickDownloadFilesXML" style="float:right;margin-right:20px;width:20%">Conferma</button>
    </template>
 </VUEModal>

 <VUEModal v-if="PopupNotadiCredito" :Titolo=" 'Nota di credito' " :Altezza="'550px'" :Larghezza="'1250px'"
              @onClickChiudiModal="PopupNotadiCredito = false">
      <template v-slot:Body>
        <div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:1px;padding-bottom:1px"> 
          <label style="font-weight:bold;font-size:18px">Totale fattura: {{ SchedaFattura.TOTALE_FATTURA }} €</label>
          <VUESchedaNotaDiCredito v-if="PopupNotadiCredito" :SchedaNotaDiCredito="SchedaSelezionataPopup"/>
        </div>
      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:15%" @click="PopupNotadiCredito = false" data-dismiss="modal">Annulla</button>
        <a v-if="SchedaSelezionataPopup.CanRecord()" class="btn btn-s-md btn-success" style="margin-right:10px" @click="OnClickConfermaNCPopup">Conferma</a>
      </template>
  </VUEModal>

 <div class="ZMCorpoSchedeDati" ref="refScrollingDivZMCorpoSchedeDati">
  <header class="panel-heading bg-light" >
    <ul class="nav nav-tabs nav-justified">
      <li v-for="ATab in TabsVisibili" :Key="ATab.Id"
          :class="{ active : ATab.Id == Tabs.ActiveTab }">
          <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
          <img v-if="SchedaFattura.Dati.DA_BANCO && ATab.Id == 'Fattura'" src="@/assets/images/IconeAlbero/FatturaDaBanco2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
          <img v-else-if="ATab.Id == 'Fattura'" src="@/assets/images/IconeAlbero/Fattura2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
          </a>
      </li>
    </ul>
   </header>
   <div style="height:5px;"></div>

   <div v-if="Tabs.ActiveTab == 'Fattura'">
    <div v-if="VisualizzaAnticipiFattura" style="background-color: #ABF7B1; border: 2px solid green; border-radius: 2px; padding: 15px; margin: 5px; font-weight: bold; display: flex; align-items: center; justify-content: space-between; width: 100%">
      <div style="margin: auto 0">Hai delle fatture da scalare</div>
      <VUEModal v-if="PopupFattureAnticipo" :Titolo="'Fatture da scalare'" :Altezza="'500px'" :Larghezza="'1200px'"
      @onClickChiudiModal="OnClickAnnullaFattureDaScalare">
        <template v-slot:Body>
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:10%;position: sticky; top: 0;"></th>
                      <th style="width:30%;position: sticky; top: 0;">Numero</th>
                      <th style="width:30%;position: sticky; top: 0;">Data</th>
                      <th style="width:30%;position: sticky; top: 0;">Importo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Anticipo in this.SchedaFattura.Anticipi" :key="Anticipo.Chiave">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" v-model="Anticipo.Selezionato"
                                               style="height: 20px;"
                                               class="form-control">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Anticipo.Numero == null ? "Avviso di fattura" : Anticipo.Numero }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ FormattaData(Anticipo.Data) }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ FormattaImporto(Anticipo.Importo) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </template>
        <template v-slot:Footer>
          <button class="btn btn-danger" @click="OnClickAnnullaFattureDaScalare"  style="float:right;margin-right:20px; width:20%">Annulla</button>
          <button v-if="this.SchedaFattura.Anticipi.length != 0" class="btn btn-success" @click="AggiungiFattureAnticipoSelezionate()" style="float:right;margin-right:20px; width:20%">Conferma</button>
        </template>
      </VUEModal>
      <div>
        <button class="btn btn-sm btn-info" @click="OnClickScegliFattureDaScalare">Scegli</button>
      </div>
    </div>

        <div class="col-md-9">

          <!-- <div style="float:right;margin-left:10px">
            <img src="@/assets/images/Euro.png" style="cursor:pointer" @click="OnClickRate" />
          </div>           -->

          <div style="float:right;margin-left:10px">
              <text style="font-weight: bold;">Data</text>
              <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="date" id="input-data" class="form-control" v-model="SchedaFattura.Dati.DATA"/>
              <label v-if="SchedaFattura.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
          </div>

          <div v-if="SchedaFattura.Dati.NUMERO != 0" style="float:right;width:13%">
            <div v-if="SchedaFattura.Dati.DA_BANCO">
              <text style="float:left;font-weight: bold;padding-right: 10%">Fattura N.</text>
              <label class="ZMLabelInput">B{{ SchedaFattura.Dati.NUMERO }}/{{ SchedaFattura.Dati.ANNO }}</label>
            </div>
            <div v-else-if="SchedaFattura.Dati.NOTA_DI_DEBITO">
              <text style="float:left;font-weight: bold;padding-right: 10%">Fattura N.</text>
              <label class="ZMLabelInput">ND{{ SchedaFattura.Dati.NUMERO }}/{{ SchedaFattura.Dati.ANNO }}</label>
            </div>
            <div v-else>
              <text style="float:left;font-weight: bold;padding-right: 10%">Fattura N.</text>
              <label class="ZMLabelInput">{{ SchedaFattura.Dati.NUMERO }}/{{ SchedaFattura.Dati.ANNO }}</label>
            </div>
          </div>
          <div v-else-if="!SchedaFattura.DatiModificati()" style="float:right;margin-right:50px">
            <button style="margin-top:20px;width:130%" class="btn btn-sm btn-info" :disabled="SchedaFattura.DisposizioneInCorso" @click="OnClickNumera">Numera fattura</button>
          </div>
          <div v-if="SchedaFattura.Chiave != -1" style="float:right;margin-right:10px;">
            <text style="float:left;font-weight: bold;padding-right: 1%">Avviso fattura N.</text>
            <label style="float:left;font-size:15px;width:151px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaFattura.Chiave }}</label>
          </div>
          <div style="float:right;" v-if="!SchedaFattura.DatiModificati() && !SchedaFattura.IsNuovo() && SchedaFattura.Dati.RATE_NON_PAGATE == 0">
            <text style="font-weight: bold;padding-right:1%;margin-right:10px">&nbsp;</text>
            <div class="btn-group open" >
              <button type="button" 
                      class="btn btn-default" 
                      :class="{'data-toggle' : this.MenuNuovo.length != 0}" 
                      aria-expanded="true" 
                      style="width:120px;z-index: 9999;"
                      @click="OnClickNuovo()">
                  Nuovo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <i class="fa" 
                    :class="{ 'fa-sort-up' : MenuNuovo.length != 0, 'fa-sort-down' : MenuNuovo.length == 0 }" 
                    style="box-sizing: border-box;"></i>
              </button>
              <ul v-if="MenuNuovo.length != 0" class="dropdown-menu" style="z-index: 9999;">
                <li v-for="(AMenu,index) in MenuNuovo" :Key="index">
                  <a v-if="AMenu.Caption != ''" @click="OnNextMenuNuovo(AMenu)">
                      {{AMenu.Caption}}
                      <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                  </a>
                  <div v-else class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <div class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <a @click="MenuNuovo = []">Chiudi</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      <div class="ZMNuovaRigaScheda" style="padding-top:3px">
        <div class="col-md-12" style="align-items: center">
          <text v-if="!SchedaFattura.Dati.ENTE_PUBBLICO" style="font-weight: bold;float:left;margin-top:5px" >Reverse Charge&nbsp;</text>
          <input v-if="!SchedaFattura.Dati.ENTE_PUBBLICO" class="form-control" style="width: 18px; height: auto; margin-right: 35px;float:left;margin-top:8px" type="checkbox" v-model="SchedaFattura.Dati.REVERSE_CHARGE" @change="CambiaStatoReverseCharge(SchedaFattura.Dati.REVERSE_CHARGE)"/>
          
          <text style=" font-weight: bold;float:left;margin-top:5px">Anticipo&nbsp;</text>
          <input class="form-control" style="width: 18px; height: auto; margin-right: 35px;float:left;margin-top:8px" type="checkbox" v-model="SchedaFattura.Dati.ANTICIPO" @change="CambiaStatoAnticipo(SchedaFattura.Dati.ANTICIPO)"/>
      
          <text style=" font-weight: bold;float:left;margin-top:5px">Inviata tramite email&nbsp;</text>
          <input class="form-control" style="width: 18px; height: auto; margin-right: 35px;float:left;margin-top:8px" type="checkbox" v-model="SchedaFattura.Dati.INVIATA_TRAMITE_EMAIL"/>

          <text style=" font-weight: bold;float:left;margin-top:5px">Accompagnatoria&nbsp;</text>
          <input class="form-control" style="width: 18px; height: auto; margin-right: 35px;float:left;margin-top:8px" type="checkbox" v-model="SchedaFattura.Dati.ACCOMPAGNATORIA"/>

          <div v-if="!SchedaFattura.Dati.DA_BANCO && VisibilitaNotaDiDebito">
            <text style=" font-weight: bold;float:left;margin-top:5px">Nota di debito&nbsp;</text>
            <input class="form-control" style="width: 18px; height: auto; margin-right: 35px;float:left;margin-top:8px" type="checkbox" v-model="SchedaFattura.Dati.NOTA_DI_DEBITO" 
                  :disabled="!SchedaFattura.IsNuovo() && SchedaFattura.Dati.NUMERO && SchedaFattura.Dati.NUMERO != 0"/>
          </div>

        </div>
      </div>
      <div v-if="SchedaFattura.Dati.FATTURA_SCALATA && !SchedaFattura.Dati.ANTICIPO">
          <label class="ZMFormLabelError">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ATTENZIONE: La fattura è già stata scalata</label> 
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
      <div class="ZMSeparatoreScheda">    
        <text style="font-weight: bold;">Intestatario</text>
        <hr style="margin-top:1px">
      </div>
      <div class="col-md-4" v-if="SchedaFattura.Dati.INVIATA_ALLO_SDI" style="border:1px solid #b3dbff">
            <img src="@/assets/images/IconeAlbero/Inviata2.png" style="margin-top:6px;float:left;width:20%">
            <text style="float:left;margin-top:2%;font-weight: bold;font-size:15px;width:80%">FATTURA TRASMESSA ALLO SDI</text>
      </div>
      <div class="col-md-4" v-if="SchedaFattura.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickScaricaFileFattura">Scarica file xml inviato a SdI</button>
      </div>
      <div class="col-md-4" v-if="SchedaFattura.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickCorreggiFattura">Correggi fattura</button>
      </div>
      <div class="ZMNuovaRigaScheda" style="margin-top:-18px">
        <div style="float:left;width:50%">
          <text style="font-weight: bold; float:left; width:100%">Cliente
            <div style="float:right" v-if="!SchedaFattura.Dati.REVERSE_CHARGE">
              <label>Ente pubblico&nbsp;</label>
            </div>
            <div style="float:right;margin-right:4px" v-if="!SchedaFattura.Dati.REVERSE_CHARGE">
              <input :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="checkbox" v-model="SchedaFattura.Dati.ENTE_PUBBLICO" @change="OnChangeEntePubblico"/>
            </div>
          </text>
          <VUEInputClienti v-model="SchedaFattura.Dati.ID_CLIENTE" 
                           :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI"
                           @onUpdate="newValue => SchedaFattura.Dati.ID_CLIENTE = newValue"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:49%">
          <text style="font-weight: bold; float:left; width:100%">Ragione Sociale </text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.RAGIONE_SOCIALE" type="text" class="form-control" placeholder="Ragione Sociale"/>
          <label v-if="SchedaFattura.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>
      <div class="ZMNuovaRigaScheda">
       <div style="float:left;width:30%">
        <text style="font-weight: bold">Indirizzo</text>
        <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.INDIRIZZO_FATTURAZIONE" type="text" class="form-control" placeholder="Indirizzo">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" maxlength="7" class="form-control" v-model="SchedaFattura.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" placeholder="CAP" v-model="SchedaFattura.Dati.CAP_FATTURAZIONE"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:26%">
        <text style="font-weight: bold">Comune</text>
        <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:20%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true" style="cursor:default"/>
       </div> 
      </div>

      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:11%">
          <text style="font-weight: bold">Partita IVA</text>
          <VUEInputPartitaIVA :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.PARTITA_IVA" placeholder="Partita IVA"></VUEInputPartitaIVA>
          <label v-if="(SchedaFattura.Dati.PARTITA_IVA.trim() == '' && SchedaFattura.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaFattura.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:16%">
          <label style="font-weight: bold;">Codice Fiscale</label>
          <VUEInputCodiceFiscale :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.CODICE_FISCALE" style="float:left" placeholder="Codice Fiscale"></VUEInputCodiceFiscale>
          <label v-if="(SchedaFattura.Dati.PARTITA_IVA.trim() == '' && SchedaFattura.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
            class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaFattura.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:8%">
            <label style="font-weight: bold;">Codice SDI</label>
            <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaFattura.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
            <label v-if="SchedaFattura.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
              {{(SchedaFattura.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
            </label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:9%">
            <label style="font-weight: bold;">Cod. uff. dest.</label>
            <input type="text" class="form-control" v-model="SchedaFattura.Dati.COD_UFFICIO_DEST"
                    placeholder="Codice ufficio destinazione"
                    :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI"
                    maxlength="6"/>
            <label v-if="SchedaFattura.Dati.COD_UFFICIO_DEST.length != 6 && SchedaFattura.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
            </label>
        </div> 

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:14%">
            <label style="font-weight: bold;">Nazione emittente</label>
            <VUEInputNazioni :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.NAZIONE_EM_PIVA" emptyElement="false" style="cursor:default"/>
            <label v-if="SchedaFattura.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:26%">
          <label style="font-weight: bold;">PEC</label>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="email" class="form-control" v-model="SchedaFattura.Dati.PEC" placeholder="PEC"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>

      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold">Indirizzo di Destinazione</text>
        <hr style="margin-top:1px">
      </div>

      <VUEModalButtonRecapitiFiliali :styleForButton="'float:right; margin-right: 0px'"
                                     :IdCliente="SchedaFattura.Dati.ID_CLIENTE"
                                     @filiale-selected="OnFilialeSelected"   />

      <div class="ZMNuovaRigaScheda" style="margin-top:-18px">
        <div style="float:left;width:50%">
          <text style="font-weight: bold">Destinazione</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.DESTINAZIONE" type="text" class="form-control" placeholder="Destinazione">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <text style="font-weight: bold">Indirizzo</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.INDIRIZZO_DESTINAZIONE" type="text" class="form-control" placeholder="Indirizzo">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:9%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" maxlength="7" class="form-control" v-model="SchedaFattura.Dati.NR_CIVICO_DESTINAZIONE" placeholder="Nr. civico"/>
        </div> 
      </div>
       
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" placeholder="CAP" v-model="SchedaFattura.Dati.CAP_DESTINAZIONE"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <text style="font-weight: bold">Comune</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.COMUNE_DESTINAZIONE" type="text" class="form-control" placeholder="Comune">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.PROVINCIA_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <label style="font-weight: bold;">Nazione</label>
          <VUEInputNazioni :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.NAZIONE_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>
   </div>

   <div v-if="Tabs.ActiveTab == 'VociFattura'" style="width:100%;">
    <div v-if="VisualizzaAnticipiFattura" style="background-color: #ABF7B1; border: 2px solid green; border-radius: 2px; padding: 15px; margin: 5px; font-weight: bold; display: flex; align-items: center; justify-content: space-between; width: 100%">
      <div style="margin: auto 0">Hai delle fatture da scalare</div>
      <VUEModal v-if="PopupFattureAnticipo" :Titolo="'Fatture da scalare'" :Altezza="'500px'" :Larghezza="'1200px'"
      @onClickChiudiModal="OnClickAnnullaFattureDaScalare">
        <template v-slot:Body>
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:10%;position: sticky; top: 0;"></th>
                      <th style="width:30%;position: sticky; top: 0;">Numero</th>
                      <th style="width:30%;position: sticky; top: 0;">Data</th>
                      <th style="width:30%;position: sticky; top: 0;">Importo (&#8364;)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Anticipo in this.SchedaFattura.Anticipi" :key="Anticipo.Chiave">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" v-model="Anticipo.Selezionato"
                                               style="height: 20px;"
                                               class="form-control">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Anticipo.Numero == null ? "Avviso di fattura" : Anticipo.Numero }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ FormattaData(Anticipo.Data) }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ FormattaImporto(Anticipo.Importo) }}
                      </td>  
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </template>
        <template v-slot:Footer>
          <button class="btn btn-danger" @click="OnClickAnnullaFattureDaScalare"  style="float:right;margin-right:20px; width:20%">Annulla</button>
          <button v-if="this.SchedaFattura.Anticipi.length != 0" class="btn btn-success" @click="AggiungiFattureAnticipoSelezionate()" style="float:right;margin-right:20px; width:20%">Conferma</button>
        </template>
      </VUEModal>
      <div>
        <button class="btn btn-sm btn-info" @click="OnClickScegliFattureDaScalare">Scegli</button>
      </div>
    </div>  
      <!-- <div style="float:right;margin-left:10px">
        <img src="@/assets/images/Euro.png" style="cursor:pointer" @click="OnClickRate" />
      </div> -->
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Info contabili</text>
        <hr style="margin-top:1px">
      </div>
      <div class="ZMNuovaRigaScheda" style="margin-top:-20px">
        <div style="float:left;width:20%">
          <label style="font-weight: bold;">Cond. Pagamento</label>
          <VUEInputCondPagamenti :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.COND_PAGAMENTO" @change="RicalcolaRate" style="cursor:default"/>
          <label v-if="SchedaFattura.Dati.COND_PAGAMENTO == null || SchedaFattura.Dati.COND_PAGAMENTO == -1" class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="!SchedaFattura.ControlloCondPagamento()" class="ZMFormLabelError">Devi inserire il pagamento nelle rate</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Causale</text>
          <VUEInputCausali :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" 
                            v-model="SchedaFattura.Dati.CAUSALE" style="cursor:default"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Esigibilità IVA</text>
          <VUEInputEsigibilitaIVA :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI" @change="OnEsigibilitaChange" v-model="SchedaFattura.Dati.ESIGIBILITA_IVA" style="cursor:default"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Note in fattura</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaFattura.Dati.NOTE_IN_FATTURA">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Ordine</text>
          <select style="float:left;width:100%;margin-right:5px" class="form-control" @change="OnClickNessunDocumento()" v-model="SchedaFattura.Dati.DOCUMENTO_CORRELATO">
            <option v-for="(Selezionato) in LsDocumentoCorrelato" 
                          :Key="Selezionato.Id"
                          :value="Selezionato.Id"
                          :Id="Selezionato.Id">
            {{Selezionato.Descrizione}}
          </option>
          </select>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="ZMSeparatoreScheda" v-if="SchedaFattura.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">       
        <text style="font-weight: bold;margin-bottom:-20px">Ordine</text>
        <hr style="margin-top:1px; margin-bottom: -5px">
      </div>
      <div class="ZMNuovaRigaScheda" style="padding-top:3px" v-if="SchedaFattura.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Documento nr.</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.DOCUMENTO_NR" type="text" class="form-control" placeholder="Documento nr.">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">Data del documento</label>
            <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="date" class="form-control" v-model="SchedaFattura.Dati.DATA_DOCUMENTO"/>
        </div>
      </div>
      <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Voce di riferimento</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.VOCE_DI_RIFERIMENTO" type="text" class="form-control" placeholder="Voce di riferimento">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">CIG</label>
            <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaFattura.Dati.CIG" placeholder="CIG"/>
        </div>
      </div>
      <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Convenzione</text>
          <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaFattura.Dati.CONVENZIONE" type="text" class="form-control" placeholder="Convenzione">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">CUP</label>
            <input :readonly="SchedaFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaFattura.Dati.CUP" placeholder="CUP"/>
        </div>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Conto corrente / Riba</text>
        <hr style="margin-top:1px;margin-bottom:-0px">
      </div>
      <div class="ZMNuovaRigaScheda">
        <VUEInputContoRibaCorrente :InviataAlloSdi="SchedaFattura.Dati.INVIATA_ALLO_SDI" :ContoCorrente="SchedaFattura.Dati.ContoCorrente"/>
      </div>

      <div v-if="ListaMagazzini.length > 1">
        <div style="clear:both"></div>
        <div class="ZMSeparatoreScheda" style="margin-top:5px">    
          <text style="font-weight: bold;">Magazzini</text>
        </div>
        <div class="ZMNuovaRigaScheda">
          <text style="font-weight: bold">Magazzino</text>
          <select class="form-control" 
                  v-model="SchedaFattura.Dati.ID_MAGAZZINO"
                  :disabled="SchedaFattura.Dati.INVIATA_ALLO_SDI"
                  style="width: 25%;">
            <option v-for="Magazzino in ListaMagazzini"
                    :key="Magazzino.CHIAVE"
                    :value="Magazzino.CHIAVE">
              {{ Magazzino.DESCRIZIONE }}
            </option>
          </select>
        </div>
      </div>
      <div style="clear:both; padding-bottom:10px"></div>

      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Voci fattura</text>
        <hr style="margin-top:1px">
      </div>
      <div v-if="SchedaFattura.Dati.ID_CLIENTE != -1">
      <VUEVociDocumentiEconomici :SchedaVociDocumentiEconomici="SchedaFattura.SchedaVociFattura"
                                 :TastoCreaNotaVisibile="!SchedaFattura.DatiModificati()"
                                 :InviataAlloSdi="SchedaFattura.Dati.INVIATA_ALLO_SDI"
                                 @onChange="OnVociFatturaChange"
                                 :IsSchedaFattura="true"
                                 :IdCliente="SchedaFattura.Dati.ID_CLIENTE"
                                 @onClickGeneraNotaDiCredito="OnClickGeneraNotaDiCredito"
                                 @inviaChiaviDDTCaricate="RiceviChiaviDDTCaricate"
                                 :ReverseCharge="SchedaFattura.Dati.REVERSE_CHARGE"
                                 :RichiestaPopupNotaDiCredito="GetRichiestaNotaDiCreditoPopup()"
                                 @onChangeCondizioniDiPagamentoCaricandoConferme="OnChangeCondizioniDiPagamentoCaricandoConferme"/>
      </div>
      <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE NELLA FATTURA</div>
   </div>

   <div v-if="Tabs.ActiveTab == 'Rate'">
    <div class="ZMNuovaRigaScheda">
      <div class="ZMSeparatoreScheda">       
          <text style="font-weight: bold;">Totali</text>
          <hr style="margin-top:5px;margin-bottom:0px">
      </div>
      <div class="col-md-4" style="float:right; width:38%; padding-right:20px">
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Imponibile </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.SommaImponibile) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Totale IVA </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.SommaIva) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a  style="float:left">Totale Ivato </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;"> {{ FormattaImporto(SchedaFattura.SchedaVociFattura.TotaleIvato) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Ritenuta </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.TotaleRitenuta) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.SchedaVociFattura.Dati.NumeroAnticipo != 0">
          <a style="float:left">Anticipo </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.Dati.NumeroAnticipo) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.SchedaVociFattura.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDaNoi || SchedaFattura.SchedaVociFattura.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDalCliente">
          <a style="float:left;margin-top:5px">Costo bollo </a>
            <input type="number" style="width:70%; float:right; text-align: right;" class="form-control" 
                    v-model="SchedaFattura.SchedaVociFattura.Dati.CostoBollo"
                    :readonly="true"
                    step="0.01">
        </div>
        <div class="ZMNuovaRigaScheda">
          <a v-if="!SchedaFattura.SchedaVociFattura.SplitPayment" style="float:left">Netto a pagare </a>
          <a v-else style="float:left">Totale </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.TotaleFattura) }}</label>
          <label v-if="SchedaFattura.SchedaVociFattura.TotaleFattura < 0" class="ZMFormLabelError" style="float:right; text-align: right; font-size: 12px;">Il totale dev'essere positivo</label>
        </div>
        <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.SchedaVociFattura.SplitPayment">
          <a style="float:left">Netto a pagare </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaFattura.SchedaVociFattura.TotaleSplitPayment) }}</label>
        </div>
        
      </div>
      <div class="col-md-8" style="float:right; width:62%;">
        <div class="ZMNuovaRigaScheda">
          <input :disabled="SchedaFattura.SchedaVociFattura.Dati.RitenutaAcconto == 0" type="checkbox" style="height: 15px;float:left;width:4%;margin-top:7px" class="form-control" v-model="SchedaFattura.Dati.RitenutaCertificata"  @change="OnChangeRitenutaCertificata()">
          <a style="float:left;width:20%;font-weight:bold;margin-top:5px">&nbsp;&nbsp;Ritenuta certificata</a>
          <div v-if="SchedaFattura.Dati.RitenutaCertificata">
            <input type="date" style="width:20%" class="form-control" v-model="SchedaFattura.Dati.DATA_RITENUTA_CERT">
          </div>
        </div>
      </div>

    </div>

    <div class="ZMNuovaRigaScheda" ref="refDivRate">
        <VUERate :SchedaRate="SchedaFattura.SchedaRate"
                 :CondPagamento="SchedaFattura.Dati.COND_PAGAMENTO"
                 @onChange="OnRateFatturaChange"/>
    </div>

    <div class="ZMSeparatoreFiltri">&nbsp;</div>

    <div class="ZMNuovaRigaScheda" v-if="SchedaFattura.RateNoteCorrelate.length > 0">
      <div class="ZMSeparatoreScheda">       
          <text style="font-weight: bold;">Rate Note di Credito Correlate</text>
          <hr style="margin-top:5px;margin-bottom:0px">
      </div>
      <section class="panel panel-default" style="background-color:white">
        <div class="table-responsive" style="clear:both;max-height:350px;">
          <table class="table table-striped b-t b-light" style="">
            <thead>
              <tr>
                <th style="width:7%">Data</th>
                <th style="width:11%">Importo</th>
                <th style="width:7%">Data Pagamento</th>
                <th style="width:20%">Conto Corrente/Cassa</th>
                <th style="width:35%">Note</th>
                <th style="width:15%">Azioni</th>
              </tr>
            </thead>
            <tbody>
              <tr style="clear: both; width: 100%"
                v-for="Nota in SchedaFattura.RateNoteCorrelate" :key="Nota.CHIAVE">
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input :readonly="true" class="form-control" type="date" v-model="Nota.DATA">
                </td>
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input v-if="Nota.IMPORTO != undefined" :readonly="true" class="form-control" type="number" step="0.01" min="0" :value="(Nota.IMPORTO/100)"/>
                </td>
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input :readonly="true" class="form-control" type="date" v-model="Nota.DATA_PAGAMENTO"/>
                </td>
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <VUEInputContoCorrente :disabled="true" v-model="Nota.ID_CONTO_PAGAMENTO"/>
                </td>
                <td style=" border:1px solid #ddd;border-bottom:0; background-color:white">
                  <textarea :readonly="true" :style="{height : Nota.AltezzaTextArea? Nota.AltezzaTextArea : '34px'}" class="form-control" wrap="off" type="text" v-model="Nota.NOTE" style="resize:none; width:100%; scrollbar-width: thin;"/>
                </td>
                <td style=" border:1px solid #ddd;border-bottom:0; background-color:white">
                  <button @click="OnClickVisualizzaNotaDiCredito(Nota)" class="btn btn-sm btn-info" style="font-size:15px; font-weight:bold;">Visualizza nota di credito</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
   </div>

  <div v-if="Tabs.ActiveTab == 'LogFattura'" style="height:50%">
      <!-- <div style="float:right;margin-left:10px">
          <img src="@/assets/images/Euro.png" style="cursor:pointer" @click="OnClickRate" />
      </div> -->
      <VUELogDocumentiEconomici :SchedaLogDocumentiEconomici="SchedaFattura.SchedaLogFattura"/>
  </div>


  <div v-if="Tabs.ActiveTab == 'Allegati'" ref="last">
    <!-- <div style="float:right;margin-left:10px">
      <img src="@/assets/images/Euro.png" style="cursor:pointer" @click="OnClickRate" />
    </div> -->
    <VUEAllegati :SchedaAllegati="SchedaFattura.SchedaAllegati" 
                  NomeCampoModello="Fatture"
                  @onChange="OnAllegatiChange" />
  </div>

  <div v-if="Tabs.ActiveTab == 'PreventiviCollegati'">
    <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Preventivi collegati alla fattura</text>
        <hr style="margin-top:1px">
      </div>

    <section class="scrollable wrapper w-f">
      <section class="panel panel-default">
        <div class="table-responsive">
          <table class="table table-striped m-b-none" style="width:100%;height: 60%;">
            <thead>
              <tr>
                <th data-toggle="class" style="width:40%">Numero</th>
                <th data-toggle="class" style="width:40%">Data</th>
                <th data-toggle="class" style="width:20%"></th>
              </tr>
            </thead>
            <tbody v-if="this.SchedaFattura.SchedaVociFattura.PreventiviCorrelati === undefined">
              <td style="font-size:20px">Non sono presenti preventivi collegati</td>
            </tbody>
            <tbody v-else-if="this.SchedaFattura.SchedaVociFattura.PreventiviCorrelati.length != 0">
              <tr v-for="Preventivo in this.SchedaFattura.SchedaVociFattura.PreventiviCorrelati" :key="Preventivo.NUMERO">
                <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                  {{ Preventivo.NUMERO }}
                </td>
                <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                 {{ Preventivo.DATA ? FormattaData(Preventivo.DATA) : '-' }}
                </td>
                <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                  <button class="btn btn-danger" @click="this.ScollegaPreventivo(Preventivo)">Scollega</button>
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <td style="font-size:20px">Non sono presenti preventivi collegati</td>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </div>

</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation,
         DASHBOARD_FILTER_TYPES, 
         PAGAMENTO_BOLLO,
         FATT_ELE_ESIGIBILITA_IVA, 
         DOCUMENTO_CORRELATO,
         RUOLI,
         NOME_PROGRAMMA } from '@/SystemInformation.js'
import VUEModalButtonRecapitiFiliali from '@/components/VUEModalButtonRecapitiFiliali.vue'
import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
import VUEVociDocumentiEconomici, {TSchedaVociDocumentiEconomici, TSingoloVociDocumentiEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import VUERate, { TSchedaRate } from '@/views/SchedeDatabase/ComponentSchedaFattura/VUERate.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { ID_NODO_FATTURE } from '@/NodiVuoti'
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import VUELogDocumentiEconomici, { TSchedaLogDocumentiEconomici } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogDocumentiEconomici.vue';
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js'
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { saveAs } from 'file-saver';
import VUEModal from '@/components/Slots/VUEModal.vue';
import VUEInputContoCorrente from '@/components/InputComponents/VUEInputContoCorrente.vue'
import VUESchedaNotaDiCredito, { TSchedaNotaDiCredito } from '@/views/SchedeDatabase/VUESchedaNotaDiCredito.vue'
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';

export class TSchedaFattura extends TSchedaGenerica
{
  constructor(AdvQuery, AlberoMovimenti)
  {
    super(AdvQuery)
    
    if(AlberoMovimenti != undefined)
      this.AlberoMovimenti = AlberoMovimenti

    this.RicalcolaRateDisattivato = true
    this.DisposizioneInCorso      = false;
    this.SegnaTotaleFattura       = 0;
    this.TriggerRate              = 0;
    this.PulsanteAlberoPremuto    = false
    this.RateNoteCorrelate        = []
    this.PopupAnticipoCliente     = false
    this.PopupNotaDiCreditoApertaCliente  = false
    this.AnticipoCliente          = 0
    this.NoteDiCreditoAperteCliente = []
  }

  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)
     this.SchedaVociFattura = new TSchedaVociDocumentiEconomici()
     this.SchedaVociFattura.ClearVociDocumentiEconomici()
     this.SchedaRate        = new TSchedaRate()
     this.SchedaRate.ClearListaRate()
     this.SchedaLogFattura  = new TSchedaLogDocumentiEconomici()
     this.SchedaLogFattura.ClearLogDocumentiEconomici()
  }

  AssignDestinazioneFromFiliale(Filiale)
  {
    this.Dati.DESTINAZIONE             = Filiale.NOME;
    this.Dati.INDIRIZZO_DESTINAZIONE   = Filiale.INDIRIZZO;
    this.Dati.NR_CIVICO_DESTINAZIONE   = Filiale.NR_CIVICO;
    this.Dati.CAP_DESTINAZIONE         = Filiale.CAP;
    this.Dati.COMUNE_DESTINAZIONE      = Filiale.COMUNE;
    this.Dati.PROVINCIA_DESTINAZIONE   = Filiale.PROVINCIA;
    this.Dati.NAZIONE_DESTINAZIONE     = Filiale.NAZIONE;
  }

  CaricaAnticipi()
  {
    var Self = this;
    let Parametri = {ChiaveUtente : this.Dati.ID_CLIENTE};
    SystemInformation.AdvQuery.ExecuteExternalScript('GetAnticipiScoperti',Parametri,function(Answer) 
    { 
      if(Self.Anticipi != undefined)
      {
        Self.Anticipi = Answer.LsAnticipi;
        Self.Anticipi.forEach(function(AnAnticipo)
                                      {
                                        AnAnticipo.Selezionato = false;
                                      })
      }
    },
    function(ErrorMessage,DescrErrore)
    {
      alert(ErrorMessage + '[' + DescrErrore + ']');
    });
  }

  AssignSchedaCliente(Scheda)
  {
      this.Dati.ID_CLIENTE             = Scheda.Chiave
      this.Dati.RAGIONE_SOCIALE        = Scheda.Dati.RAGIONE_SOCIALE
      this.Dati.INDIRIZZO_FATTURAZIONE = Scheda.Dati.INDIRIZZO_FATTURAZIONE
      this.Dati.CAP_FATTURAZIONE       = Scheda.Dati.CAP_FATTURAZIONE
      this.Dati.COMUNE_FATTURAZIONE    = Scheda.Dati.COMUNE_FATTURAZIONE
      this.Dati.PROVINCIA_FATTURAZIONE = Scheda.Dati.PROVINCIA_FATTURAZIONE
      this.Dati.NR_CIVICO_FATTURAZIONE = Scheda.Dati.NR_CIVICO_FATTURAZIONE
      this.Dati.CAP_DESTINAZIONE       = Scheda.Dati.CAP_SPEDIZIONE
      this.Dati.DESTINAZIONE           = Scheda.Dati.PRESSO
      this.Dati.COMUNE_DESTINAZIONE    = Scheda.Dati.COMUNE_SPEDIZIONE
      this.Dati.PROVINCIA_DESTINAZIONE = Scheda.Dati.PROVINCIA_SPEDIZIONE
      this.Dati.INDIRIZZO_DESTINAZIONE = Scheda.Dati.INDIRIZZO_SPEDIZIONE
      this.Dati.NR_CIVICO_DESTINAZIONE = Scheda.Dati.NR_CIVICO_SPEDIZIONE
      this.Dati.PARTITA_IVA            = Scheda.Dati.PARTITA_IVA
      this.Dati.CODICE_FISCALE         = Scheda.Dati.CODICE_FISCALE
      this.Dati.COND_PAGAMENTO         = Scheda.Dati.COND_PAGAMENTO
      this.Dati.IVA_SUGGERITA          = Scheda.Dati.IVA_SUGGERITA_CLIENTE
      this.Dati.CAUSALE                = Scheda.Dati.CAUSALE
      this.Dati.NOTE_IN_FATTURA        = Scheda.Dati.NOTE_IN_FATTURA
      this.Dati.PEC                    = Scheda.Dati.PEC
      this.Dati.COD_ENTE_SDI           = Scheda.Dati.COD_ENTE_SDI
      this.Dati.ENTE_PUBBLICO          = Scheda.Dati.ENTE_PUBBLICO
      this.Dati.COD_UFFICIO_DEST       = Scheda.Dati.COD_UFFICIO_DEST
      this.Dati.NAZIONE_EM_PIVA        = Scheda.Dati.NAZIONE_EM_PIVA
      this.Dati.NAZIONE_DESTINAZIONE   = Scheda.Dati.NAZIONE_SPEDIZIONE
      this.Dati.ContoCorrente.IBAN                   = Scheda.Dati.ContoCorrente.IBAN
      this.Dati.ContoCorrente.ID_CONTO_CORRENTE      = Scheda.Dati.ContoCorrente.ID_CONTO_CORRENTE
      this.Dati.ContoCorrente.BANCA                  = Scheda.Dati.ContoCorrente.BANCA
      this.Dati.ContoCorrente.NR_CONTO               = Scheda.Dati.ContoCorrente.NUMERO_CONTO
      this.Dati.ContoCorrente.SWIFT                  = Scheda.Dati.ContoCorrente.SWIFT
      this.Dati.ContoCorrente.BIC                    = Scheda.Dati.ContoCorrente.BIC
      this.Dati.ContoCorrente.CONTO_RIBA             = Scheda.Dati.ContoCorrente.CONTO_RIBA
      this.Dati.ESIGIBILITA_IVA        = Scheda.Dati.ESIGIBILITA_IVA
      if(this.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
        this.Dati.NOTE_IN_FATTURA = "FATTURA CON SPLIT PAYMENT";

      this.SchedaVociFattura.SetDatiCliente(Scheda.Dati.IVA_SUGGERITA_CLIENTE,
                                            Scheda.Dati.RITENUTA,
                                            Scheda.Dati.BOLLO,
                                            Scheda.Dati.NATURA_PAGAMENTO)
      if(Scheda.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
        this.SchedaVociFattura.SetSplitPayment(true)   

      if(Scheda.Dati.SPESE_INCASSO_DESCRIZIONE != '' && Scheda.Dati.SPESE_INCASSO_SUGGERITE != 0)
        this.SchedaVociFattura.SetSpesaIncasso(Scheda.Dati.SPESE_INCASSO_DESCRIZIONE,
                                               Scheda.Dati.SPESE_INCASSO_SUGGERITE * 100,
                                               Scheda.Dati.ContoCorrente.CONTO_RIBA)

      this.SchedaVociFattura.CalcoloTotaliFattura()
  }

  AssignSchedaFromPreventivo(Scheda, FatturaDaBanco)
  {
    var Self = this
    this.Dati.ID_CLIENTE = Scheda.Dati.ID_CLIENTE
    this.CaricaDatiCliente(Scheda.Dati.ID_CLIENTE, function()
    {
      Self.Dati.DA_BANCO               = FatturaDaBanco != undefined? FatturaDaBanco : false

      Self.Dati.ID_CLIENTE             = Scheda.Dati.ID_CLIENTE
      Self.Dati.RAGIONE_SOCIALE        = Scheda.Dati.RAGIONE_SOCIALE
      Self.Dati.PROVINCIA_FATTURAZIONE = Scheda.Dati.PROVINCIA_FATTURAZIONE
      Self.Dati.CAP_DESTINAZIONE       = Scheda.Dati.CAP_DESTINAZIONE
      Self.Dati.DESTINAZIONE           = Scheda.Dati.DESTINAZIONE
      Self.Dati.COMUNE_DESTINAZIONE    = Scheda.Dati.COMUNE_DESTINAZIONE
      Self.Dati.PROVINCIA_DESTINAZIONE = Scheda.Dati.PROVINCIA_DESTINAZIONE
      Self.Dati.INDIRIZZO_DESTINAZIONE = Scheda.Dati.INDIRIZZO_DESTINAZIONE
      Self.Dati.NR_CIVICO_DESTINAZIONE = Scheda.Dati.NR_CIVICO_DESTINAZIONE
      Self.Dati.COND_PAGAMENTO         = Scheda.Dati.COND_PAGAMENTO
      Self.Dati.CAUSALE                = Scheda.Dati.CAUSALE
      Self.Dati.NOTE_IN_FATTURA        = Scheda.Dati.NOTE
      Self.Dati.COD_UFFICIO_DEST       = Scheda.Dati.COD_UFFICIO_DEST
      Self.Dati.CIG                    = Scheda.Dati.CIG
      Self.Dati.CUP                    = Scheda.Dati.CUP
      Self.Dati.CONVENZIONE            = Scheda.Dati.CONVENZIONE
      Self.Dati.DATA_DOCUMENTO         = Scheda.Dati.DATA_DOCUMENTO
      Self.Dati.DOCUMENTO_NR           = Scheda.Dati.DOCUMENTO_NR
      Self.Dati.VOCE_DI_RIFERIMENTO    = Scheda.Dati.VOCE_DI_RIFERIMENTO
      Self.Dati.DOCUMENTO_CORRELATO    = Scheda.Dati.DOCUMENTO_CORRELATO
      Self.Dati.NAZIONE_DESTINAZIONE   = Scheda.Dati.NAZIONE_DESTINAZIONE
      Self.Dati.ContoCorrente.IBAN                   = Scheda.Dati.ContoCorrente.IBAN
      Self.Dati.ContoCorrente.ID_CONTO_CORRENTE      = Scheda.Dati.ContoCorrente.ID_CONTO_CORRENTE
      Self.Dati.ContoCorrente.BANCA                  = Scheda.Dati.ContoCorrente.BANCA
      Self.Dati.ContoCorrente.NR_CONTO               = Scheda.Dati.ContoCorrente.NUMERO_CONTO
      Self.Dati.ContoCorrente.SWIFT                  = Scheda.Dati.ContoCorrente.SWIFT
      Self.Dati.ContoCorrente.BIC                    = Scheda.Dati.ContoCorrente.BIC
      Self.Dati.ContoCorrente.CONTO_RIBA             = Scheda.Dati.ContoCorrente.CONTO_RIBA
      Self.Dati.ESIGIBILITA_IVA        = Scheda.Dati.ESIGIBILITA_IVA
      if(Self.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
        Self.Dati.NOTE_IN_FATTURA = "FATTURA CON SPLIT PAYMENT";
      
      if(Self.Dati.DA_BANCO == true)
        Self.Dati.ACCOMPAGNATORIA = true;
    
      Self.SchedaVociFattura.CopyData(Scheda.SchedaVociPreventivo)
      Self.CollegaFatturaAlPreventivo(Scheda.Chiave)
      Self.RicalcolaRate()
    })
  }

  AssignSchedaFromDDT(Scheda)
  {
    var Self = this
    this.Dati.ID_CLIENTE             = Scheda.Dati.ID_CLIENTE
    this.CaricaDatiCliente(Scheda.Dati.ID_CLIENTE, function()
    {
      Self.Dati.PARTITA_IVA            = Scheda.Dati.PARTITA_IVA
      Self.Dati.CODICE_FISCALE         = Scheda.Dati.CODICE_FISCALE
      Self.Dati.CAUSALE                = Scheda.Dati.CAUSALE
      Self.Dati.NOTE_IN_FATTURA        = Scheda.Dati.NOTE
      
      Self.FromDDT = []
      Self.FromDDTEntrante = []
      if(Scheda.SchedaVociDocumentoDiTrasporto != undefined)
      {
        Self.SchedaVociFattura.CopyData(Scheda.SchedaVociDocumentoDiTrasporto, true)
        Self.FromDDT.push(Scheda.Chiave)
      }
      else 
      {
        if(Scheda.SchedaVociDocumentoDiTrasportoEntrante != undefined)
        {
          Self.SchedaVociFattura.CopyData(Scheda.SchedaVociDocumentoDiTrasportoEntrante, true)
          Self.FromDDTEntrante.push(Scheda.Chiave)
        }
      }
      Self.Dati.DESTINAZIONE                  = Scheda.Dati.DESTINAZIONE
      Self.Dati.INDIRIZZO_DESTINAZIONE        = Scheda.Dati.INDIRIZZO_DESTINAZIONE
      Self.Dati.NR_CIVICO_DESTINAZIONE        = Scheda.Dati.NR_CIVICO_DESTINAZIONE
      Self.Dati.CAP_DESTINAZIONE              = Scheda.Dati.CAP_DESTINAZIONE
      Self.Dati.COMUNE_DESTINAZIONE           = Scheda.Dati.COMUNE_DESTINAZIONE
      Self.Dati.PROVINCIA_DESTINAZIONE        = Scheda.Dati.PROVINCIA_DESTINAZIONE
      Self.Dati.NAZIONE_DESTINAZIONE          = Scheda.Dati.NAZIONE_DESTINAZIONE

      let InserimentoSeparatore = new TSingoloVociDocumentiEconomici ( 
                                                                       -1,
                                                                       'Destinazione: ' + Scheda.Dati.DESTINAZIONE + '\n' +
                                                                       Scheda.Dati.INDIRIZZO_DESTINAZIONE + ', ' +
                                                                       Scheda.Dati.NR_CIVICO_DESTINAZIONE + ', ' + 
                                                                       Scheda.Dati.COMUNE_DESTINAZIONE + ', ' + '(' + SystemInformation.GetTargaProvincia(Scheda.Dati.PROVINCIA_DESTINAZIONE) + ')',
                                                                       0.00,
                                                                       0.00,
                                                                       'F',
                                                                       0.00,
                                                                       0.00,
                                                                       -1,
                                                                      SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
                                                                        
      Self.SchedaVociFattura.SetAltezzaTextarea(InserimentoSeparatore)                                                
      Self.SchedaVociFattura.LsVociDocumentiEconomici.unshift(InserimentoSeparatore)

      InserimentoSeparatore = new TSingoloVociDocumentiEconomici(
                                                                  -1,
                                                                  'Riferimento DDT nr. ' + Scheda.Dati.NUMERO_DDT + ' del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy',new Date(Scheda.Dati.DATA_DDT)),
                                                                  0.00,
                                                                  0.00,
                                                                  'F',
                                                                  0.00,
                                                                  0.00,
                                                                  -1,
                                                                  SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
      
       Self.SchedaVociFattura.SetAltezzaTextarea(InserimentoSeparatore)                                             
       Self.SchedaVociFattura.LsVociDocumentiEconomici.unshift(InserimentoSeparatore)
       Self.RicalcolaRate()
       
    })
  }

  CollegaFatturaAlPreventivo(ChiavePreventivo)
  {
    let Preventivo    = {}
    Preventivo.CHIAVE = ChiavePreventivo
    this.SchedaVociFattura.PreventiviCorrelati.push(Preventivo);
  }

  ControlloCondPagamento()
  {
    if(this.Dati.COND_PAGAMENTO != null)
    {
      for(let i = 0; i < SystemInformation.Configurazioni.CondPagamenti.length; i++)
        if(SystemInformation.Configurazioni.CondPagamenti[i].CHIAVE == this.Dati.COND_PAGAMENTO)
        { 
          if(TSchedaGenerica.DisponiFromBoolean(SystemInformation.Configurazioni.CondPagamenti[i].PAGAMENTO_OBBLIGATORIO))
            return this.SchedaRate.ControlloRatePagate()
          break;
        }
      return true
    }
    return false
  }


  CanRecord()
  {
    return (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            this.Dati.DATA != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.ID_CLIENTE != -1 &&
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            (this.Dati.COD_UFFICIO_DEST.length == 6 || this.Dati.COD_UFFICIO_DEST == '') &&
            TZDateFunct.DateFromHTMLInput(this.Dati.DATA) > new Date(2020,1,1) &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            this.ControlloCondPagamento() &&
            (this.Dati.CODICE_FISCALE == '' || 
              (TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || 
               SystemInformation.DeveloperMode) || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              (TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || SystemInformation.DeveloperMode) || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaRate.AllDataOk() &&
            this.SchedaVociFattura.AllDataOk() &&
            this.SchedaVociFattura.VociPresenti() && 
            this.SchedaVociFattura.TotaleFattura >= 0
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
        return false;

    return this.Dati.NUMERO == 0;
  }

  Elimina(OnSuccess,OnError)
  {
    this.InEliminazione = true;
    var ObjQuery = { Operazioni : [ 
                                    {
                                      Query     : "EliminaVociTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaAllegatiTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaRateTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaLogTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "UpdatePreventivoTramiteEliminaFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "UpdateSaldoNoleggioTramiteEliminaFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "UpdateDepositoNoleggioTramiteEliminaFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "UpdateDDTRichiamataInFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "UpdateDDTEntranteRichiamataInFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaLogFattureGenerateTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaFattureDaMobileTramiteFattura",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaCorrelazioneANotaDiCredito",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaCorrelazioneARateNotaDiCredito",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "Elimina",
                                      Parametri : { CHIAVE_FATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaRecordSaldiChiusureAnnuali",
                                      Parametri : {
                                                     CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_CLIENTE),
                                                     ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                  }
                                    }
                                  ]};

    this.SchedaRate.GestioneSaldiChiusuraAnnuali(ObjQuery.Operazioni, this.Dati.ID_CLIENTE)

    this.AdvQuery.PostSQL('Fatture',ObjQuery,function()
    {
        OnSuccess();
    },
    function(HTTPError,SubHTTPError,Response)
    {
      OnError(HTTPError,SubHTTPError,Response);
    });
  }

  Registra(OnSuccess,OnError)
  {
      var Self = this
      if(this.CanRecord())
      {
        this.RicalcolaRate(function()
        {
          var ObjQuery = { Operazioni : [] };
          ObjQuery.Operazioni.push({
                                    Query     : Self.IsNuovo() ? "Inserisci" : "Modifica",
                                    Parametri : {
                                                  CHIAVE                    : Self.IsNuovo() ? undefined : Self.Chiave, 
                                                  ID_CLIENTE                : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_CLIENTE),
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.RAGIONE_SOCIALE),
                                                  CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(Self.Dati.CODICE_FISCALE),
                                                  DESTINAZIONE              : TSchedaGenerica.PrepareForRecordString(Self.Dati.DESTINAZIONE),
                                                  INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_FATTURAZIONE),
                                                  NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_FATTURAZIONE),
                                                  PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_FATTURAZIONE),
                                                  COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_FATTURAZIONE),
                                                  CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_FATTURAZIONE),
                                                  DATA                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.DATA),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(Self.Dati.PARTITA_IVA),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_EM_PIVA),
                                                  NAZIONE_DESTINAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_DESTINAZIONE),
                                                  INDIRIZZO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_DESTINAZIONE),
                                                  NR_CIVICO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_DESTINAZIONE),
                                                  PROVINCIA_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_DESTINAZIONE),
                                                  COMUNE_DESTINAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_DESTINAZIONE),
                                                  CAP_DESTINAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_DESTINAZIONE),
                                                  COND_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.COND_PAGAMENTO),
                                                  IVA_SUGGERITA             : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaVociFattura.RitornaIvaSuggerita()),
                                                  PAGAMENTO_BOLLO           : TSchedaGenerica.PrepareForRecordString(Self.SchedaVociFattura.RitornaPagamentoBollo()),
                                                  COSTO_BOLLO               : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaVociFattura.RitornaCostoBollo()),
                                                  NOTE_IN_FATTURA           : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE_IN_FATTURA),
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaVociFattura.RitornaRitenutaAcconto()),
                                                  NUMERO_ANTICIPO           : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaVociFattura.RitornaNumeroAnticipo()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(Self.Dati.ESIGIBILITA_IVA),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.CAUSALE),
                                                  IBAN                      : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.IBAN)  : null,
                                                  BANCA                     : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BANCA) : null,
                                                  BIC                       : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BIC)   : null,
                                                  SWIFT                     : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.SWIFT) : null,
                                                  ID_CONTO_CORRENTE         : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                  INVIATA_ALLO_SDI          : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.INVIATA_ALLO_SDI),
                                                  ENTE_PUBBLICO             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.ENTE_PUBBLICO),
                                                  COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_ENTE_SDI),
                                                  COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_UFFICIO_DEST),
                                                  PEC                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.PEC),
                                                  CUP                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CUP),
                                                  CIG                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CIG),
                                                  DOCUMENTO_CORRELATO       : TSchedaGenerica.PrepareForRecordString(Self.Dati.DOCUMENTO_CORRELATO),
                                                  CONVENZIONE               : TSchedaGenerica.PrepareForRecordString(Self.Dati.CONVENZIONE),
                                                  VOCE_DI_RIFERIMENTO       : TSchedaGenerica.PrepareForRecordString(Self.Dati.VOCE_DI_RIFERIMENTO),
                                                  DOCUMENTO_NR              : TSchedaGenerica.PrepareForRecordString(Self.Dati.DOCUMENTO_NR),
                                                  DATA_DOCUMENTO            : TSchedaGenerica.PrepareForRecordString(Self.Dati.DATA_DOCUMENTO),                                                  
                                                  DA_BANCO                  : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.DA_BANCO),                                                  
                                                  MESE_GENERAZIONE          : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.MESE_GENERAZIONE),
                                                  REVERSE_CHARGE            : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.REVERSE_CHARGE),
                                                  ANTICIPO                  : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.ANTICIPO),
                                                  INVIATA_TRAMITE_EMAIL     : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.INVIATA_TRAMITE_EMAIL),
                                                  ACCOMPAGNATORIA           : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.ACCOMPAGNATORIA),
                                                  NOTA_DI_DEBITO            : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.NOTA_DI_DEBITO),
                                                  DATA_RITENUTA_CERT        : TSchedaGenerica.PrepareForRecordDate(Self.Dati.DATA_RITENUTA_CERT),
                                                  ID_MAGAZZINO              : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_MAGAZZINO)
                                               }
                                 });
        Self.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_FATTURA')
        Self.SchedaVociFattura.PrepareQueryParameters(ObjQuery.Operazioni, 'ID_FATTURA')
        Self.SchedaRate.CheckBeforeRegistrazione(Self.SchedaVociFattura.SplitPayment? Self.SchedaVociFattura.TotaleSplitPayment : Self.SchedaVociFattura.TotaleFattura, 
                                                 parseFloat(Self.SchedaVociFattura.SommaIva),
                                                 Self.RateNoteCorrelate)
        Self.SchedaRate.PrepareQueryParameters(ObjQuery.Operazioni)
        Self.SchedaRate.GestioneSaldiChiusuraAnnuali(ObjQuery.Operazioni, Self.Dati.ID_CLIENTE)

        ObjQuery.Operazioni.push({
                                   Query     : "ScollegaPreventivo",
                                   Parametri : {
                                                 CHIAVE_FATTURA : Self.Chiave
                                               }
                                 });
                                 
        if(Self.SchedaVociFattura.PreventiviCorrelati.length > 0)
        {
          let LsPreventivi = []
          Self.SchedaVociFattura.PreventiviCorrelati.forEach(function(Preventivo)
          {
            LsPreventivi.push(Preventivo.CHIAVE)
          })
          var ParametriCollegamento = {
                                        Query     : "CollegaPreventivo",
                                        Parametri : {
                                                      ChiaviPreventivi : LsPreventivi
                                                    }
                                      }
          if(Self.Chiave != -1)
            ParametriCollegamento.Parametri.ID_FATTURA = Self.Chiave
          ObjQuery.Operazioni.push(ParametriCollegamento);
        }

        ObjQuery.Operazioni.push({
                                   Query     : "EliminaRecordSaldiChiusureAnnuali",
                                   Parametri : {
                                                 CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_CLIENTE),
                                                 ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Self.Dati.DATA)))
                                               }
                                 });
        Self.AdvQuery.PostSQL('Fatture',ObjQuery,function(Response)
        {
          if(Self.FromDDT != undefined && Self.FromDDT.length != 0)
          {
            Self.RegistraIdFatturaPerBloccoDDT(Response.NewKey1, Self.FromDDT)
          }
          if(Self.FromDDTEntrante != undefined && Self.FromDDTEntrante.length != 0)
          {
            Self.RegistraIdFatturaPerBloccoDDTEntrante(Response.NewKey1, Self.FromDDTEntrante)
          }
          if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
            Self.SchedaAllegati.DeleteFileDaEliminare()
          ObjQuery = {};
          if(Self.Chiave == -1)
             Self.Chiave = Response.NewKey1;
          Self.Dati.ModificaTabellaAllegati = 0;
          Self.Dati.ModificaTabellaVoci     = 0
          Self.CreateSnapshot();
          if(OnSuccess != undefined)
          {
              if(Self.PassaChiaveTramiteOnSuccess)
                OnSuccess(Response.NewKey1);
              else OnSuccess()
          }
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });
      })  
      }  
      else
      {
        if(OnError != undefined)
          OnError();
      }  
   }

   RegistraIdFatturaPerBloccoDDT(ChiaveFattura, ChiaviDDT)
   {
      var ObjQuery = { Operazioni : [] };
      for(let i = 0; i < ChiaviDDT.length; i++)
      {
        ObjQuery.Operazioni.push({
                                  Query     : "InserisciCollegamentoFattura",
                                  Parametri : {
                                                CHIAVE_FATTURA : TSchedaGenerica.PrepareForRecordInteger(ChiaveFattura),
                                                CHIAVE_DDT     : TSchedaGenerica.PrepareForRecordInteger(ChiaviDDT[i])
                                              }
                                })
      }
      this.AdvQuery.PostSQL('DocumentiDiTrasporto',ObjQuery,function()
      {
      },
      function(HTTPError,SubHTTPError,Response)
      {
        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
   }

   RegistraIdFatturaPerBloccoDDTEntrante(ChiaveFattura, ChiaviDDT)
   {
      var ObjQuery = { Operazioni : [] };
      for(let i = 0; i < ChiaviDDT.length; i++)
      {
        ObjQuery.Operazioni.push({
                                  Query     : "InserisciCollegamentoFattura",
                                  Parametri : {
                                                CHIAVE_FATTURA : TSchedaGenerica.PrepareForRecordInteger(ChiaveFattura),
                                                CHIAVE_DDT     : TSchedaGenerica.PrepareForRecordInteger(ChiaviDDT[i])
                                              }
                                })
      }
      this.AdvQuery.PostSQL('DocumentiDiTrasportoEntranti',ObjQuery,function()
      {
      },
      function(HTTPError,SubHTTPError,Response)
      {
        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
   }

   RegistraLogFattureGenerate(IdFattura, FatturazioneEffettuata, Anno, OnSuccess, OnError)
   {
      var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                                Query     : "InserisciLogFattureGenerate",
                                Parametri : {
                                              CHIAVE                  : undefined,
                                              ID_FATTURA              : IdFattura,
                                              FATTURAZIONE_EFFETTUATA : FatturazioneEffettuata,
                                              ANNO                    : Anno
                                            }
                               })
      this.AdvQuery.PostSQL('Fatture',ObjQuery,function()
      {
        OnSuccess(IdFattura)
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
   }

   RegistraIdFilialeFattureGenerate(ChiaveFattura, IdFiliale, OnSuccess, OnError)
   {
      var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                                Query     : "InserisciIdFilialeFattura",
                                Parametri : {
                                              CHIAVE                 : ChiaveFattura,
                                              ID_FILIALE             : TSchedaGenerica.PrepareForRecordInteger(IdFiliale),
                                            }
                               })
      this.AdvQuery.PostSQL('Fatture',ObjQuery,function()
      {
        OnSuccess()
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
   }

   CaricaRiassunto(Riassunto)
   {
      this.IsFattura                 = true
      this.Chiave                    = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociFattura.SetIdDocumento(this.Chiave)
      this.SchedaRate.SetIdDocumento(this.Chiave)
      this.Dati.ID_CLIENTE           = Riassunto.ID_CLIENTE;
      this.Dati.ANNO                 = Riassunto.ANNO
      this.Dati.DA_BANCO             = Riassunto.DA_BANCO == 'T'
      this.Dati.NOTA_DI_DEBITO       = TSchedaGenerica.DisponiFromBoolean(Riassunto.NOTA_DI_DEBITO)
      if(Riassunto.NUMERO != null && Riassunto.NUMERO != undefined)
          this.Dati.NUMERO = Riassunto.NUMERO;
      else this.Dati.NUMERO = 0;
      this.Dati.RAGIONE_SOCIALE      = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.Dati.INVIATA_ALLO_SDI     = Riassunto.INVIATA_ALLO_SDI == 'T'
      this.Dati.RATE_NON_PAGATE      = Riassunto.RATE_NON_PAGATE
      this.Dati.RATE_TOTALI          = Riassunto.RATE_TOTALI
      this.NonConformita             = Riassunto.NonConformita
      this.TOTALE_NOTA               = TSchedaGenerica.DisponiFromInteger(Riassunto.TOTALE_NOTA) / 100
      //per movimenti conti metto il totale delle rate, in seguito penseremo come risolvere 
      this.TOTALE_FATTURA            = Riassunto.TOTALE_FATTURA != 0? Riassunto.TOTALE_FATTURA : Riassunto.TOTALE_RATE 

      if(this.AlberoMovimenti)
        this.NomeCliente = TSchedaGenerica.DisponiFromString(Riassunto.NOME_CLIENTE)
      this.Dati.ModificaTabellaRate = 0
      this.CreateSnapshot();
   }

   GetDescrizione()
   {
      let Result = ''
      let StringaTotaleRate = this.TOTALE_FATTURA != undefined? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.TOTALE_FATTURA) : ''
      if(this.AlberoMovimenti)
      {
        if(this.Dati.ANNO == 0 || this.Dati.NUMERO == 0)
        {
          if(this.Dati.NOTA_DI_DEBITO)
            Result = (this.NomeCliente + ' -Nota di debito N. ' + this.Chiave + StringaTotaleRate);
          else Result = (this.NomeCliente + ' - Avviso fattura N. ' + this.Chiave + StringaTotaleRate);
        }
        else
        {
          if(this.Dati.DA_BANCO)
            Result = (this.NomeCliente + ' - Fat.ra da banco N. B' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate);
          else if(this.Dati.NOTA_DI_DEBITO)
            Result = (this.NomeCliente + ' - Fat.ra N. ND ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate);
          else Result = (this.NomeCliente + ' - Fat.ra N. ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate); 
        } 
      }
      else
      {
        if(this.Dati.ANNO == 0 || this.Dati.NUMERO == 0)
        {
          if(this.Dati.NOTA_DI_DEBITO)
            Result = ('Nota di debito N. ' + this.Chiave + StringaTotaleRate);
          else Result = ('Avviso fattura N. ' + this.Chiave + StringaTotaleRate);
        }
        else
        {
          if(this.Dati.DA_BANCO)
            Result = ('Fat.ra da banco N. B' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate);
          else if(this.Dati.NOTA_DI_DEBITO)
            Result = ('Fat.ra N. ND ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate);
          else Result = ('Fat.ra N. ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + StringaTotaleRate); 
        } 
      }

      return Result
   }
   
   Clear()
   {
      this.Anticipi = []
      this.RateNoteCorrelate = []
      this.SchedaVociFattura.PreventiviCorrelati = []
      this.SchedaVociFattura.SogliaNatPag = 0
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociFattura.SetIdDocumento(-1)
      this.SchedaLogFattura.AssignDati([])
      this.SchedaRate.SetIdDocumento(-1)
      this.SchedaAllegati.AssignDati([])
      this.SchedaVociFattura.AssignDati([], 
                                        SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA, 
                                        SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO,
                                        'ID_FATTURA',
                                        -1, 
                                        PAGAMENTO_BOLLO.NessunBollo ,
                                        SystemInformation.Configurazioni.Impostazioni.COSTO_BOLLO_SUGGERITO)
      this.SchedaRate.AssignDati([])
      this.Dati = {
                      NUMERO                        : 0,
                      ID_CLIENTE                    : 0,
                      DATA                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
                      RAGIONE_SOCIALE               : '',
                      INDIRIZZO_FATTURAZIONE        : '',
                      NR_CIVICO_FATTURAZIONE        : '',
                      COMUNE_FATTURAZIONE           : '',
                      PROVINCIA_FATTURAZIONE        : -1,
                      CAP_FATTURAZIONE              : '',
                      PARTITA_IVA                   : '',
                      CODICE_FISCALE                : '',
                      COMUNE_DESTINAZIONE           : '',
                      PROVINCIA_DESTINAZIONE        : -1,
                      CAP_DESTINAZIONE              : '',
                      DESTINAZIONE                  : '',
                      INDIRIZZO_DESTINAZIONE        : '',
                      NR_CIVICO_DESTINAZIONE        : '',
                      COND_PAGAMENTO                : null,
                      NOTE_IN_FATTURA               : '',
                      CAUSALE                       : -1,
                      INVIATA_ALLO_SDI              : false,
                      FATTURA_SCALATA               : false,
                      ENTE_PUBBLICO                 : false,
                      COD_ENTE_SDI                  : '0000000',
                      PEC                           : '',
                      COD_UFFICIO_DEST              : '',
                      CUP                           : '',
                      CIG                           : '',
                      DOCUMENTO_CORRELATO           : DOCUMENTO_CORRELATO.NessunDocumento,
                      CONVENZIONE                   : '',
                      VOCE_DI_RIFERIMENTO           : '',
                      DOCUMENTO_NR                  : '',
                      DATA_DOCUMENTO                : '',
                      DATA_RITENUTA_CERT            : '',
                      NAZIONE_DESTINAZIONE          : SystemInformation.Configurazioni.StatoItaliano,
                      NAZIONE_EM_PIVA               : SystemInformation.Configurazioni.StatoItaliano,
                      ContoCorrente                 : { 
                                                        IBAN              : '',
                                                        BANCA             : '',
                                                        ID_CONTO_CORRENTE : -1,
                                                        NR_CONTO          : '',
                                                        CONTO_RIBA        : false,
                                                        BIC               : '',
                                                        SWIFT             : ''
                                                      },
                      ESIGIBILITA_IVA               : '',
                      DA_BANCO                      : false,
                      MESE_GENERAZIONE              : null,
                      REVERSE_CHARGE                : false,
                      INVIATA_TRAMITE_EMAIL         : false,
                      ANTICIPO                      : false,
                      ACCOMPAGNATORIA               : false,
                      // Dati allegati
                      ModificaTabellaAllegati       : 0,
                      ModificaTabellaVoci           : 0,
                      ID_MAGAZZINO                  : SystemInformation.Configurazioni.Impostazioni.MAGAZZINO,

                  }
      this.PassaChiaveTramiteOnSuccess = false
      super.Clear();
    }

    Disponi(OnSuccess)
    {
      this.DisposizioneInCorso = true
      if(this.PulsanteAlberoPremuto)
        this.TriggerRate++
      var Self = this;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociFattura.SetIdDocumento(this.Chiave)
      this.SchedaRate.SetIdDocumento(this.Chiave)
      if(this.InEliminazione) return;


      let Parametri = {}
      Parametri.ChiaveCliente = this.Dati.ID_CLIENTE
      Parametri.CHIAVE        = this.Chiave
      Parametri.CHIAVE_CLIENTE = this.Dati.ID_CLIENTE
      Parametri.StatoConti    = true
      Parametri.DataDal       = "2000-01-01";
      let Anno = new Date().getFullYear()
      Parametri.DataAl        = Anno + "-12-31";
      
      this.AdvQuery.ExecuteExternalScript('SelectDatiFattura', Parametri,
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;

                                            if(Results != undefined)
                                            {
                                              let SituazioneSaldo = 0

                                              if(Results.DatiSituazioneContabile != undefined)
                                                if(Results.DatiSituazioneContabile.LsConti != undefined)
                                                  if(Results.DatiSituazioneContabile.LsConti.TotaleSaldoAttualePerSchedaCliente != undefined)
                                                    SituazioneSaldo = parseFloat(Results.DatiSituazioneContabile.LsConti.TotaleSaldoAttualePerSchedaClienteStringa.replace("€", "").replace(",", "."));

                                              if(SituazioneSaldo != undefined && SituazioneSaldo != 0 && SituazioneSaldo < 0)
                                                Self.AnticipoCliente      = Math.abs(SituazioneSaldo)

                                              let ArrayInfo          = Results.Dettaglio;
                                              let ArrayAllegati      = Results.DatiAllegati;
                                              let ArrayVociFattura   = Results.DatiVoci;
                                              let ArrayRateFattura   = Results.DatiRate;
                                              let ArrayLogFattura    = Results.DatiLog;
                                              Self.RateNoteCorrelate = Results.DatiRateNoteDiCreditoCorrelate;
                                              Self.NoteDiCreditoAperteCliente = Results.NoteDiCreditoAperteCliente

                                              Self.TOTALE_NOTA       = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TOTALE_NOTA) / 100

                                              Self.SchedaVociFattura.PreventiviCorrelati = Results.DatiPreventiviCollegati;
                                              Self.SchedaVociFattura.PreventiviCorrelati.forEach(function(ConfermaDOrdine)
                                              {
                                                ConfermaDOrdine.DATA = new Date(ConfermaDOrdine.DATA);
                                              })

                                              for(let i = 0; i < ArrayRateFattura.length; i++)
                                              {
                                                if(ArrayRateFattura[i].ID_MOVIMENTO != null)
                                                {
                                                    ArrayRateFattura[i].DATA_PAGAMENTO = ArrayRateFattura[i].DATA_MOVIMENTO
                                                    ArrayRateFattura[i].ID_CONTO_CASSE  = ArrayRateFattura[i].CONTO_CORRENTE  
                                                }
                                              }

                                              if(ArrayInfo != undefined && ArrayLogFattura != undefined && ArrayAllegati != undefined && ArrayVociFattura != undefined)
                                              {
                                                if(ArrayInfo.length != 0)
                                                {
                                                  if(Self.RateNoteCorrelate != undefined && Self.RateNoteCorrelate.length != 0)
                                                  {
                                                    Self.RateNoteCorrelate.forEach(function(Nota)
                                                    {
                                                      Nota.ID_FATTURA        = Self.Chiave 
                                                      Nota.NOTE              = Nota.NUMERO == undefined? 'Nota di credito non numerata' : 'Nota di credito n. ' + Nota.NUMERO + '/' + Nota.ANNO
                                                    })
                                                    Self.DisponiArrayInfo(ArrayInfo, ArrayLogFattura, ArrayAllegati, ArrayVociFattura, ArrayRateFattura,OnSuccess)
                                                  }
                                                  else Self.DisponiArrayInfo(ArrayInfo, ArrayLogFattura, ArrayAllegati, ArrayVociFattura, ArrayRateFattura, OnSuccess)
                                                }
                                                else SystemInformation.HandleError('Fattura eliminata')
                                              } 
                                              else SystemInformation.HandleError('Impossibile ottenere il dettaglio della fattura');      
                                            }  
                                            else SystemInformation.HandleError('Errore nei dati della fattura scaricati da databasee');      
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
      
    }

    DisponiArrayInfo(ArrayInfo, ArrayLogFattura, ArrayAllegati, ArrayVociFattura, ArrayRateFattura, OnSuccess)
    {
      let Self = this
      if(ArrayLogFattura.length != 0)
        Self.SchedaLogFattura.AssignDati(ArrayLogFattura,'ID_FATTURA')
      if(ArrayAllegati.length != 0)
        Self.SchedaAllegati.AssignDati(ArrayAllegati)
      if(ArrayVociFattura.length != 0)
        Self.SchedaVociFattura.AssignDati(ArrayVociFattura,
                                          TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_SUGGERITA_CLIENTE) / 10,
                                          TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA),
                                          'ID_FATTURA',
                                          TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NATURA_PAGAMENTO),
                                          TSchedaGenerica.DisponiFromString(ArrayInfo[0].PAGAMENTO_BOLLO),
                                          TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].COSTO_BOLLO),
                                          ArrayInfo[0].ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione? true : false,
                                          TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_ANTICIPO) / 100)
      if(ArrayRateFattura.length != 0)
        Self.SchedaRate.AssignDati(ArrayRateFattura, 
                                    TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA))
      
      if(ArrayInfo.length != 0)
      {
        Self.Dati = { 
                      NUMERO                        : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO),
                      ID_CLIENTE                    : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_CLIENTE),
                      RAGIONE_SOCIALE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                      INDIRIZZO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                      NR_CIVICO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                      COMUNE_FATTURAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                      PROVINCIA_FATTURAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                      CAP_FATTURAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                      DATA                          : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA),
                      PARTITA_IVA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                      CODICE_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                      NAZIONE_DESTINAZIONE          : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_DESTINAZIONE),
                      NAZIONE_EM_PIVA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                      COMUNE_DESTINAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_DESTINAZIONE),
                      PROVINCIA_DESTINAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_DESTINAZIONE),
                      CAP_DESTINAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_DESTINAZIONE),
                      DESTINAZIONE                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESTINAZIONE),
                      INDIRIZZO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_DESTINAZIONE),
                      NR_CIVICO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_DESTINAZIONE),
                      COND_PAGAMENTO                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].COND_PAGAMENTO),
                      NOTE_IN_FATTURA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE_IN_FATTURA),
                      CAUSALE                       : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CAUSALE),
                      ESIGIBILITA_IVA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                      MESE_GENERAZIONE              : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].MESE_GENERAZIONE),
                      REVERSE_CHARGE                : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].REVERSE_CHARGE),
                      INVIATA_TRAMITE_EMAIL         : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].INVIATA_TRAMITE_EMAIL),
                      ANTICIPO                      : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ANTICIPO),
                      FATTURA_SCALATA               : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].FATTURA_SCALATA) != 0,
                      INVIATA_ALLO_SDI              : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].INVIATA_ALLO_SDI),
                      COD_ENTE_SDI                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI),
                      PEC                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),
                      ENTE_PUBBLICO                 : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ENTE_PUBBLICO),       
                      CUP                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CUP),
                      CIG                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CIG),
                      DOCUMENTO_CORRELATO           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DOCUMENTO_CORRELATO),
                      CONVENZIONE                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CONVENZIONE),
                      VOCE_DI_RIFERIMENTO           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].VOCE_DI_RIFERIMENTO),
                      DOCUMENTO_NR                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DOCUMENTO_NR),
                      DATA_DOCUMENTO                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA_DOCUMENTO),
                      COD_UFFICIO_DEST              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_UFFICIO_DEST),
                      DA_BANCO                      : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].DA_BANCO),
                      ACCOMPAGNATORIA               : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ACCOMPAGNATORIA), 
                      DATA_RITENUTA_CERT            : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_RITENUTA_CERT),
                      NOTA_DI_DEBITO                : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].NOTA_DI_DEBITO),
                      
                      ID_MAGAZZINO                  : (ArrayInfo[0].ID_MAGAZZINO == null || ArrayInfo[0].ID_MAGAZZINO == undefined) 
                                                      ? SystemInformation.Configurazioni.Impostazioni.MAGAZZINO 
                                                      : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_MAGAZZINO),

                      ContoCorrente                 : {
                                                          IBAN              : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                          ID_CONTO_CORRENTE : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CONTO_CORRENTE),
                                                          BANCA             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA),
                                                          CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE != null? false : true,
                                                          NR_CONTO          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CONTO),
                                                          SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT),
                                                          BIC               : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC),
                                                      },
                      ModificaTabellaAllegati       : 0,
                      ModificaTabellaVoci           : 0,
                      ModificaTabellaRate           : 0,
                      RATE_NON_PAGATE               : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_NON_PAGATE),
                      RATE_TOTALI                   : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_TOTALI),
                      RitenutaCertificata           : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_RITENUTA_CERT) != '' ? true : false,
        }

        Self.SchedaVociFattura.SogliaEsenzioneIva = ArrayInfo[0].SOGLIA_PRESENTE == 'T'
        Self.SchedaVociFattura.Soglia             = (parseFloat(ArrayInfo[0].SOGLIA_X_NO_IVA / 100)).toFixed(2)
        Self.SchedaVociFattura.SogliaNatPag       = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].SOGLIA_NAT_PAG)
        if(Self.Dati.DATA != '')
          Self.Dati.ANNO = new Date(Self.Dati.DATA).getFullYear();
        else Self.Dati.ANNO = null;
        Self.CreateSnapshot();
        Self.CaricaAnticipi();

        if(OnSuccess != undefined)
          OnSuccess()
        Self.DisposizioneInCorso = false;
      }
      else SystemInformation.HandleError('Fattura eliminata')
    }

    GetClassName()
    {
      return 'TSchedaFattura';
    }

    GetImageIndex()
    {
      if(this.NonConformita)
      {
        if(this.Dati.RATE_NON_PAGATE == 0)
        {
          if(this.Dati.INVIATA_ALLO_SDI)
             return 'PagatoInviataNonConforme.png'; 
          return 'PagatoNonConforme.png';
        }
        if(this.TOTALE_NOTA != undefined && this.TOTALE_NOTA != 0 && this.TOTALE_NOTA < this.TOTALE_FATTURA)
        {
          if(this.Dati.INVIATA_ALLO_SDI) 
            return 'MezzoPagatoInviataNonConforme.png';
          return 'MezzoPagatoNonConforme.png';  
        }
        if(this.Dati.RATE_TOTALI != this.Dati.RATE_NON_PAGATE)
        {
          if(this.Dati.INVIATA_ALLO_SDI)
            return 'MezzoPagatoInviataNonConforme.png';  
          return 'MezzoPagatoNonConforme.png';  
        }
        if(this.Dati.INVIATA_ALLO_SDI)
          return 'NonPagatoInviataNonConforme.png';  
        return 'NonPagatoNonConforme.png'; 
      }
      else
      {
        if(this.Dati.RATE_NON_PAGATE == 0)
        {
          if(this.Dati.INVIATA_ALLO_SDI)
             return 'PagatoInviata.png'; 
          return 'Pagato.png';  
        }
        if(this.TOTALE_NOTA != undefined && this.TOTALE_NOTA != 0 && this.TOTALE_NOTA < this.TOTALE_FATTURA)
        {
          if(this.Dati.INVIATA_ALLO_SDI)
             return 'MezzoPagatoInviata.png';  
          return 'MezzoPagato.png'; 
        }
        if(this.Dati.RATE_TOTALI != this.Dati.RATE_NON_PAGATE)
        {
          if(this.Dati.INVIATA_ALLO_SDI)
             return 'MezzoPagatoInviata.png'; 
          return 'MezzoPagato.png'; 
        }
        if(this.Dati.INVIATA_ALLO_SDI)
             return 'NonPagatoInviata.png';
        return 'NonPagato.png';
      }
    }

    GetMeseGenerazione()
    {
      if(this.Dati.MESE_GENERAZIONE != null && this.Dati.MESE_GENERAZIONE != undefined && this.Dati.MESE_GENERAZIONE != 0)
      { 
        let MeseGenerazione = ''
        let Anno = Math.trunc(this.Dati.MESE_GENERAZIONE / 100)
        let Mese = this.Dati.MESE_GENERAZIONE % 100
        Mese = TZDateFunct.GetMesiInItaliano()[Mese - 1]
        MeseGenerazione = Mese + ' ' + Anno.toString()
        return MeseGenerazione
      }
      else return 'Manuale'
    }

   GetFiltriAbilitati(OnSuccess)
   {
      var Anno = new Date(this.Dati.DATA)
      OnSuccess([{
                   Name : DASHBOARD_FILTER_TYPES.Clienti,
                   Positions : [
                                  this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                  this.Dati.ID_CLIENTE,
                                  ID_NODO_FATTURE,
                                  Anno.getFullYear(),
                                  this.Chiave
                               ]
              }])
   }

    DatiStampabili()
    {
      return true
    }

    Stampa(OnSuccess)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaFattura', { Chiave : this.Chiave },
                                                      function(Result)
                                                      { 
                                                        if(Result.PDF != undefined)
                                                          OnSuccess('data:application/pdf;base64,' + Result.PDF);
                                                        else SystemInformation.HandleError('Documento non presente','','');
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      })   
    } 

    FatturaDaBanco()
    {
      this.Dati.DA_BANCO = true
      this.Dati.ACCOMPAGNATORIA = true
    }  

    NumeraFattura(ChiaveFattura, OnSuccess, OnError)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('NumeraFattura', { ChiaveFattura : ChiaveFattura },
                                                        function()
                                                        {
                                                          if(OnSuccess != undefined)
                                                            OnSuccess()
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          OnError(HTTPError,SubHTTPError,Response);
                                                        }) 
    }

    CaricaDatiCliente(ChiaveCliente, OnSuccess)
    {
      var Self = this;
      let Parametri = {}
      Parametri.ChiaveCliente = ChiaveCliente
      Parametri.CHIAVE        = ChiaveCliente
      Parametri.StatoConti    = true
      Parametri.DataDal       = "2000-01-01";
      let Anno = new Date().getFullYear()
      Parametri.DataAl        = Anno + "-12-31";
      
      SystemInformation.AdvQuery.ExecuteExternalScript('SelectDatiClienteAutocompleteFattura', Parametri,
                                        function(Results)
                                        {
                                          if(Results != undefined)
                                          {
                                            let ArrayInfo = Results.Dettaglio;

                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                              Self.Dati.RAGIONE_SOCIALE          = TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE)
                                              Self.Dati.INDIRIZZO_FATTURAZIONE   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE);
                                              Self.Dati.NR_CIVICO_FATTURAZIONE   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE);
                                              Self.Dati.PARTITA_IVA              = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA);
                                              Self.Dati.COMUNE_FATTURAZIONE      = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE);
                                              Self.Dati.CAP_FATTURAZIONE         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE);
                                              Self.Dati.CODICE_FISCALE           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE);
                                              Self.Dati.ESIGIBILITA_IVA          = ArrayInfo[0].ESIGIBILITA_IVA;
                                              Self.Dati.INDIRIZZO_DESTINAZIONE   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_SPEDIZIONE)
                                              Self.Dati.NR_CIVICO_DESTINAZIONE   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_SPEDIZIONE)
                                              Self.Dati.COMUNE_DESTINAZIONE      = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_SPEDIZIONE)
                                              Self.Dati.PROVINCIA_DESTINAZIONE   = ArrayInfo[0].PROVINCIA_SPEDIZIONE
                                              Self.Dati.DESTINAZIONE             = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PRESSO)

                                              Self.Dati.PEC                    = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC)
                                              Self.Dati.COD_ENTE_SDI           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI)
                                              Self.Dati.COD_UFFICIO_DEST       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_UFFICIO_DEST)
                                              
                                              Self.Dati.ENTE_PUBBLICO            = ArrayInfo[0].ENTE_PUBBLICO == 'T'? true : false
                                              Self.Dati.NAZIONE_DESTINAZIONE     = ArrayInfo[0].NAZIONE_SPEDIZIONE
                                              Self.Dati.NAZIONE_EM_PIVA          = ArrayInfo[0].NAZIONE_EM_PIVA
                                              Self.Dati.ContoCorrente = {
                                                                            IBAN              : ArrayInfo[0].IBAN,
                                                                            ID_CONTO_CORRENTE : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].ID_CONTO_CORRENTE : -1,
                                                                            BANCA             : ArrayInfo[0].BANCA_APPOGGIO,
                                                                            CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE? false : true,
                                                                            NR_CONTO          : ArrayInfo[0].NR_CONTO,
                                                                            BIC               : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].BIC_CONTO : ArrayInfo[0].BIC,
                                                                            SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].SWIFT_CONTO : ArrayInfo[0].SWIFT,
                                                                        }            
                                              Self.Dati.NOTE_IN_FATTURA          = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE_IN_FATTURA)
                                              Self.Dati.CAP_DESTINAZIONE         = ArrayInfo[0].CAP_SPEDIZIONE
                                              Self.Dati.COND_PAGAMENTO           = ArrayInfo[0].COND_PAGAMENTO
                                              if(ArrayInfo[0].PROVINCIA_FATTURAZIONE == null || ArrayInfo[0].PROVINCIA_FATTURAZIONE == undefined)
                                                  Self.Dati.PROVINCIA_FATTURAZIONE = -1
                                              else Self.Dati.PROVINCIA_FATTURAZIONE  = parseInt(ArrayInfo[0].PROVINCIA_FATTURAZIONE); 

                                              Self.Dati.IVA_SUGGERITA_CLIENTE = ArrayInfo[0].IVA_SUGGERITA_CLIENTE / 10

                                              Self.SchedaVociFattura.SetDatiCliente(Self.Dati.REVERSE_CHARGE? 0 : ArrayInfo[0].IVA_SUGGERITA_CLIENTE / 10,
                                                                                    ArrayInfo[0].RITENUTA / 10,
                                                                                    ArrayInfo[0].BOLLO, 
                                                                                    ArrayInfo[0].NATURA_PAGAMENTO)
                                                                                    
                                              if(ArrayInfo[0].SPESE_INCASSO_DESCRIZIONE != '' && ArrayInfo[0].SPESE_INCASSO_SUGGERITE != 0)
                                                Self.SchedaVociFattura.SetSpesaIncasso(ArrayInfo[0].SPESE_INCASSO_DESCRIZIONE,
                                                                                       ArrayInfo[0].SPESE_INCASSO_SUGGERITE,
                                                                                       Self.Dati.ContoCorrente.CONTO_RIBA)
                                              
                                              if(Self.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
                                                Self.SchedaVociFattura.SetSplitPayment(true)
                                              else Self.SchedaVociFattura.SetSplitPayment(false)

                                              // Self.Dati.REVERSE_CHARGE = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IMPIANTO_REVERSE_CHARGE)
                                              Self.CambiaStatoReverseCharge(Self.Dati.REVERSE_CHARGE)

                                              Self.SchedaVociFattura.CalcoloTotaliFattura()

                                              Self.SchedaVociFattura.SogliaEsenzioneIva = ArrayInfo[0].SOGLIA_PRESENTE == 'T'
                                              Self.SchedaVociFattura.Soglia             = (parseFloat(ArrayInfo[0].SOGLIA_X_NO_IVA / 100)).toFixed(2)
                                              Self.SchedaVociFattura.SogliaNatPag       = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].SOGLIA_NAT_PAG)
                                              if(OnSuccess != undefined)
                                                OnSuccess()
                                            }
                                          }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectCliente')
    } 

    OnEsigibilitaChange()
    {
      if(this.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
      { 
        this.SchedaVociFattura.SetSplitPayment(true);
        this.Dati.NOTE_IN_FATTURA = "FATTURA CON SPLIT PAYMENT";
      }
      else
      { 
        this.SchedaVociFattura.SetSplitPayment(false)
        this.Dati.NOTE_IN_FATTURA = "";
      }

      this.SchedaVociFattura.CalcoloTotaliFattura()
      this.RicalcolaRate()
    }

    RicalcolaRate(OnSuccess)
    {
      this.SchedaRate.EsigibilitaIva = this.Dati.ESIGIBILITA_IVA
      this.SchedaRate.Ricalcola(this.Dati.COND_PAGAMENTO, 
                                parseFloat(this.SchedaVociFattura.TotaleFattura),
                                parseFloat(this.SchedaVociFattura.SommaIva),
                                OnSuccess,
                                this.Dati.DATA != ''? this.Dati.DATA : undefined,
                                this.RateNoteCorrelate)
    }

    InserisciNuovaRigaVoceTramiteGenerazione(Descrizione,Quantita, Prezzo, Iva, Sconto, NaturaPagamento)
    {
      let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                    Descrizione,
                                                                    Prezzo,
                                                                    Quantita,
                                                                    'F',
                                                                    Iva, //  this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 0 : this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita,
                                                                    Sconto? Sconto : 0,
                                                                    -1,
                                                                    SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                                                                    '',
                                                                    null,
                                                                    false,
                                                                    NaturaPagamento)
      this.SchedaVociFattura.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
      this.SchedaVociFattura.SetAltezzaTextarea(this.SchedaVociFattura.LsVociDocumentiEconomici[this.SchedaVociFattura.LsVociDocumentiEconomici.length - 1])
      this.SchedaVociFattura.CalcoloTotaliFattura()
    }

    RecuperaEmailCliente(OnSuccess)
    {
      var Self = this;
      this.ListaEmailCliente = ''
      this.ListaEmailAmm     = ''
      
      SystemInformation.AdvQuery.GetSQL('Fatture', { CHIAVE : this.Chiave },
                                          function(Results)
                                          {
                                            let ArrayEmailCliente = SystemInformation.AdvQuery.FindResults(Results,"ListaEmailCliente");
                                            // var ArrayEmailPEC     = SystemInformation.AdvQuery.FindResults(Results,'EmailPEC')
                                            // var EmailPEC          = TSchedaGenerica.DisponiFromString(ArrayEmailPEC[0].PEC_CLIENTE)
                                            if(ArrayEmailCliente != undefined)
                                            {
                                              if(ArrayEmailCliente.length != 0)
                                                ArrayEmailCliente.forEach(function(Email)
                                                {
                                                  if(Email.EMAIL_CLIENTE != null)
                                                    Self.ListaEmailCliente += Email.EMAIL_CLIENTE + '; '
                                                })

                                                // if(EmailPEC != undefined && EmailPEC != '')
                                                // {
                                                //   Self.EmailPEC = Email.PEC + ';'
                                                // }
                                              Self.ListaEmailCliente = Self.ListaEmailCliente.substring(0, Self.ListaEmailCliente.length - 2)
                                              // Self.EmailPEC          = Self.EmailPEC.substring(0, Self.EmailPEC.length - 1)
                                              OnSuccess()
                                            }
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                          'ListaEmailFattura')
    }

    Invia(OggettoEmail, OnSuccess, FromSolleciti = false)
    {
      let Parametri        = OggettoEmail
      Parametri.Chiave     = this.Chiave
      Parametri.InvioEmail = true
      if(FromSolleciti)
        Parametri.FromSolleciti = -1
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaFattura', Parametri,
                                                      function(Result)
                                                      {  
                                                        if(Result.Risposta != undefined)
                                                          OnSuccess(Result);
                                                        else SystemInformation.HandleError('Problema nella risposta della procedura invio email','','');
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      })  
    }

    DatiInviabili()
    {
      return true
    }

    CambiaStatoReverseCharge(Value)
    {
      this.SchedaVociFattura.Dati.ReverseCharge = Value
      if(Value)
      {
        this.Dati.NOTE_IN_FATTURA  = 'FATTURA CON IVA REVERSE CHARGE'
        this.SchedaVociFattura.LsVociDocumentiEconomici.forEach((element) => 
                                                                {
                                                                  element.Dati.Iva = 0
                                                                  element.Dati.Ivato = TZEconomicFunct.GetIvatoFromImponibile(element.Dati.Imponibile, 0)
                                                                  element.CalcoloTotale()
                                                                })
        this.SchedaVociFattura.CalcoloTotaliFattura()        
        this.Dati.IVA_SUGGERITA    = 0
        this.SchedaVociFattura.Dati.IvaSuggerita    = 0
      } 
      else
      {
        this.Dati.NOTE_IN_FATTURA  = ''
      }
    }

    CambiaStatoAnticipo(Value)
    {
      this.SchedaVociFattura.Dati.ReverseCharge = Value;
    }

    FunzNodo1Abilitata()
    {
      return true
    }

    FunzNodo1Icona()
    {
      return "fa fa-eur text-info ZMIconButton" 
    }

    FunzNodo1()
    { 
      this.PulsanteAlberoPremuto = true;
      this.TriggerRate++
    }
}

export default 
{
   components : 
   {
      VUEModalButtonRecapitiFiliali,
      VUEInputProvince,
      VUEInputCAP,
      VUEInputCondPagamenti,
      VUEInputClienti,
      VUEInputCodiceFiscale,
      VUEInputPartitaIVA,
      VUEAllegati,
      VUEVociDocumentiEconomici,
      VUERate,
      VUEInputEsigibilitaIVA,
      VUEInputCausali,
      VUEInputContoRibaCorrente,
      VUELogDocumentiEconomici,
      VUEInputNazioni,
      VUEConfirm,
      VUEModal,
      VUEInputContoCorrente,
      VUESchedaNotaDiCredito,
      VUEModalCaricamentoDati,
   },
   data()
   {
     return {
              TotaleFattura                     : 0, 
              DeveloperMode                     : SystemInformation.DeveloperMode,
              DocumentoCorrelato                : DOCUMENTO_CORRELATO,
              LsDocumentoCorrelato              : SystemInformation.GetLsTipiDocumentoCorrelato(),
              CondPagamento                     : SystemInformation.Configurazioni.CondPagamenti,
              StatoItaliano                     : SystemInformation.Configurazioni.StatoItaliano,
              ListaMagazzini                    : SystemInformation.Configurazioni.Magazzini,
              PopupSchedaFattura                : false,
              PopupCorreggiFattura              : false,
              PopupScegliFileXML                : false,
              PopupFattureAnticipo              : false,
              PopupNotadiCredito                : false,
              ListaFileXML                      : [],
              MenuNuovo                         : [],
              SchedaSelezionataPopup            : new TSchedaGenerica(SystemInformation.AdvQuery),
              CostantePagamentoBollo            : PAGAMENTO_BOLLO,
              VisibilitaLogVariazioni           : SystemInformation.AccessRights.VisibilitaLogVariazioni(),
              VisibilitaNotaDiDebito            : SystemInformation.AccessRights.VisibilitaNotaDiDebito(),
              NomeProgramma                     : NOME_PROGRAMMA,
              PopupAttesaCalcolo                : false,
              NumerazioneAvviata                : false,
              Tabs                              : {
                                                      ActiveTab : 'Fattura',
                                                      Tabs      : [
                                                                    {
                                                                      Caption : 'Fattura',
                                                                      Id      : 'Fattura'
                                                                    },
                                                                    {
                                                                      Caption : 'Voci Fattura',
                                                                      Id      : 'VociFattura'
                                                                    },
                                                                    {
                                                                      Caption : 'Rate',
                                                                      Id      : 'Rate'
                                                                    },
                                                                    {
                                                                      Caption: 'Allegati',
                                                                      Id     : 'Allegati'
                                                                    },
                                                                    {
                                                                      Caption : 'Prev. coll.',
                                                                      Id : 'PreventiviCollegati',
                                                                    },
                                                                    {
                                                                      Caption : 'Variazioni',
                                                                      Id      : 'LogFattura' 
                                                                    }
                                                                  ]
                                                   },
                                 
            }
   },
   emits : ['onChangeNodiAlbero', 'OnClickGeneraNotaDiCredito'],
   props : ['SchedaFattura'],
   computed :
   {
     CurrentTriggerRateFattura :
     {
        get()
        {
          return this.SchedaFattura.TriggerRate;
        }
     },

     MeseGenerazione :
     {
        get()
        {
          var Mese = this.SchedaFattura.GetMeseGenerazione()
          return Mese
        }
     },

     CurrentCliente()
     {
       return this.SchedaFattura.Dati.ID_CLIENTE
     },

     CurrentTotaleFattura()
     {
       return this.SchedaFattura.SchedaVociFattura.TotaleFattura
     },

    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaFattura.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaFattura.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },
    ControlloFileXMLSelezionati :
    {
      get()
      {
        for(var i = 0; i < this.ListaFileXML.length; i++)
        if(this.ListaFileXML[i].Selezionato)
          return true
        return false
      }
    },
    VisualizzaAnticipiFattura :
    {
      get()
      {
        return (!this.SchedaFattura.Dati.INVIATA_ALLO_SDI) && (!this.SchedaFattura.Dati.ANTICIPO) && (this.SchedaFattura.Anticipi.length != 0) && (this.SchedaFattura.Anticipi.length == 1 ? (this.SchedaFattura.Dati.NUMERO != this.SchedaFattura.Anticipi[0].Numero) : true);
      }
    },

    TabsVisibili()
    {
      return this.Tabs.Tabs.filter(tab => tab.Id !== 'LogFattura' || this.VisibilitaLogVariazioni);
    },

   },
   watch:
   {
     CurrentTriggerRateFattura : 
     {
        handler(NewValue,OldValue)
        {
          if(NewValue != OldValue)
            if(OldValue != undefined || NewValue != 0)
              if(this.SchedaFattura.PulsanteAlberoPremuto)
              {
                this.OnClickRate()    
                this.SchedaFattura.PulsanteAlberoPremuto = false
              }
        },
        immediate : true
     },
      
     CurrentCliente:
     {
        handler(NewValue,OldValue)
        {
          if(this.SchedaFattura.DatiModificati()) 
            if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
              this.RicaricaDatiCliente(NewValue)

          if(NewValue != OldValue)
            this.SchedaFattura.CaricaAnticipi();
        }
     },

     CurrentTotaleFattura:
     {
        handler(NewValue, OldValue)
        {
          if(this.SchedaFattura.DatiModificati()) 
            if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
              if(this.SchedaFattura.RicalcolaRateAttivato)
                this.RicalcolaRate()
        }
     },
   },
   methods: 
   {

    OnFilialeSelected(FilialeSelezionata)
    {
      this.SchedaFattura.AssignDestinazioneFromFiliale(FilialeSelezionata)
    },

    OnClickRate()
    {
      this.Tabs.ActiveTab = 'Rate';     

      // setTimeout(this.ScrollToRate, 250);
    },

    // ScrollToRate()
    // {    
    //   const elementDivRate = this.$refs.refDivRate;
    //   var topPos = elementDivRate.offsetTop;

    //   const elementScrolligDiv = this.$refs.refScrollingDivZMCorpoSchedeDati.parentElement;
    //   elementScrolligDiv.scrollTop = topPos;
    // },

    CambiaStatoReverseCharge(NewValue)
    {
      this.SchedaFattura.CambiaStatoReverseCharge(NewValue)
    },

    CambiaStatoAnticipo(NewValue)
    {
      this.SchedaFattura.CambiaStatoAnticipo(NewValue);
    },

    OnClickGeneraNotaDiCredito()
    {
      this.$emit('OnClickGeneraNotaDiCredito', this.SchedaFattura)
    },  

    RiceviChiaviDDTCaricate(ListaChiaviDDT)
    {
      if(this.SchedaFattura.FromDDT == undefined)
      this.SchedaFattura.FromDDT = []
      for(let i = 0; i < ListaChiaviDDT.length; i++)
        this.SchedaFattura.FromDDT.push(ListaChiaviDDT[i])
    },

    OnClickChiudiPopupScegliFile()
    {
      this.ListaFileXML       = []
      this.PopupScegliFileXML = false
    },

    RicaricaDatiCliente(ChiaveCliente)
    {
      this.SchedaFattura.CaricaDatiCliente(ChiaveCliente)
    },

    ConfermaAnticipoInFattura()
    {
      this.SchedaFattura.SchedaVociFattura.SetNumeroAnticipo(this.SchedaFattura.AnticipoCliente)
      this.SchedaFattura.PopupAnticipoCliente = false
      this.SchedaFattura.SchedaVociFattura.CalcoloTotaliFattura()
      this.SchedaFattura.Dati.ModificaTabellaVoci++
    },

    OnClickConfermaVaiAllaNotaDiCreditoAperta()
    {
      this.SchedaFattura.PopupNotaDiCreditoApertaCliente = false
      if(this.SchedaFattura.NoteDiCreditoAperteCliente != null && this.SchedaFattura.NoteDiCreditoAperteCliente.length != 0 && this.SchedaFattura.NoteDiCreditoAperteCliente[0] != null)
      {
        this.SchedaSelezionataPopup = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
        this.SchedaSelezionataPopup.Nuovo();
        this.SchedaSelezionataPopup.Chiave = this.SchedaFattura.NoteDiCreditoAperteCliente[0].CHIAVE
        this.SchedaSelezionataPopup.Disponi();
        this.SchedaSelezionataPopup.VisualizzaTabRate = true
        this.PopupNotadiCredito = true
      }
    },

    OnClickRifiutaVaiAllaNotaDiCreditoAperta()
    {
      this.SchedaFattura.PopupNotaDiCreditoApertaCliente = false
      if((this.SchedaFattura.SchedaVociFattura.RitornaNumeroAnticipo() == null || 
               this.SchedaFattura.SchedaVociFattura.RitornaNumeroAnticipo() == 0) && 
               this.SchedaFattura.AnticipoCliente != null && 
               this.SchedaFattura.AnticipoCliente != 0 &&
               SystemInformation.AccessRights.VisibilitaNumeroAnticipo()) 
      {
        this.AnnullaPopup()
        this.SchedaFattura.PopupAnticipoCliente = true
      }
      else
      {
        this.PopupSchedaFattura = true
      }
    },

    OnClickNumera()
    {
      if(this.SchedaFattura.NoteDiCreditoAperteCliente != undefined && this.SchedaFattura.NoteDiCreditoAperteCliente.length != 0 && this.SchedaFattura.Dati.RATE_NON_PAGATE != 0)
      {
        this.AnnullaPopup()
        this.SchedaFattura.PopupNotaDiCreditoApertaCliente = true
      }
      else if((this.SchedaFattura.SchedaVociFattura.RitornaNumeroAnticipo() == null || 
               this.SchedaFattura.SchedaVociFattura.RitornaNumeroAnticipo() == 0) && 
               this.SchedaFattura.AnticipoCliente != null && 
               this.SchedaFattura.AnticipoCliente != 0 &&
               this.SchedaFattura.Dati.RATE_NON_PAGATE != 0 &&
               SystemInformation.AccessRights.VisibilitaNumeroAnticipo()) 
      {
        this.AnnullaPopup()
        this.SchedaFattura.PopupAnticipoCliente = true
      }
      else
      {
        this.PopupSchedaFattura = true
      }
    },

    OnClickRifiutaAnticipo()
    {
        this.AnnullaPopup()
        this.PopupSchedaFattura = true
    },

    ConfermaNumerazioneFattura()
    {
      if (this.NumerazioneAvviata) return;
      this.NumerazioneAvviata = true;

      this.PopupAttesaCalcolo = true
      this.AnnullaPopup()
      this.SchedaFattura.Dati.DATA = TZDateFunct.DateInHTMLInputFormat(new Date())

      let Self = this;

      this.SchedaFattura.RicalcolaRate(function()
      {
        Self.SchedaFattura.Registra(function()
        {
          Self.SchedaFattura.NumeraFattura(Self.SchedaFattura.Chiave, 
                                            function()                                                       
                                            {
                                              Self.NumerazioneAvviata = false
                                              Self.PopupAttesaCalcolo = false
                                              Self.SchedaFattura.Disponi(function()
                                              {
                                                Self.$emit('onChangeNodiAlbero');
                                              })
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              Self.NumerazioneAvviata = false
                                              Self.PopupAttesaCalcolo = false
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            })
        })
      })
    },

    ConfermaCorrezione()
    {
      var Self = this
      var ObjQuery = {Operazioni : []}
      ObjQuery.Operazioni.push({Query : 'CorreggiFattura',
                                        Parametri : { CHIAVE : this.SchedaFattura.Chiave }})

      SystemInformation.AdvQuery.PostSQL('Fatture',ObjQuery,
                                          function()
                                          { 
                                            Self.SchedaFattura.Disponi() 
                                          },
                                          SystemInformation.HandleError)
      this.AnnullaPopup()
    },

    AnnullaPopup()
    {
       this.PopupSchedaFattura   = false
       this.PopupCorreggiFattura = false
       this.SchedaFattura.PopupAnticipoCliente = false
    },

    OnAllegatiChange()
    {
      if(this.SchedaFattura.SchedaAllegati.Modificato())
         this.SchedaFattura.Dati.ModificaTabellaAllegati++
       else this.SchedaFattura.Dati.ModificaTabellaAllegati = 0;
    },

    OnVociFatturaChange()
    {
      if(this.SchedaFattura.SchedaVociFattura.Modificato())
      {
        this.ControlloSuperamentoSogliaEsenzioneIva()
        this.SchedaFattura.Dati.ModificaTabellaVoci++

        if(this.SchedaFattura.SegnaTotaleFattura != this.SchedaFattura.SchedaVociFattura.TotaleFattura)
        {
          this.SchedaFattura.RicalcolaRate()
          this.SchedaFattura.SegnaTotaleFattura = this.SchedaFattura.SchedaVociFattura.TotaleFattura
        }
      }
      else this.SchedaFattura.Dati.ModificaTabellaVoci = 0
    },

    ControlloSuperamentoSogliaEsenzioneIva()
    {
      if(this.SchedaFattura.SchedaVociFattura.SogliaEsenzioneIva)
      {
        if(this.SchedaFattura.SchedaVociFattura.SommaImponibile - this.SchedaFattura.SchedaVociFattura.Soglia >= 0)
        {
          this.SchedaFattura.Dati.NOTE_IN_FATTURA  = SystemInformation.Configurazioni.Impostazioni.NOTA_PER_SUPERAMENTO_SOGLIA_ESENZIONE_IVA
        } 
        else
        {
          this.SchedaFattura.Dati.NOTE_IN_FATTURA  = ''
        }
      }
    },

    OnRateFatturaChange()
    {
      if(this.SchedaFattura.SchedaRate.Modificato())
      {
        this.SchedaFattura.Dati.ModificaTabellaRate++

        let RataModificata = this.SchedaFattura.SchedaRate.LsRate.find(r => r.Modificato && r.Modificato())
        let ChiaveRata = RataModificata.Dati.Chiave

        this.SchedaFattura.Dati.ChiaveUltimaRataModificata = ChiaveRata
      }
      else this.SchedaFattura.Dati.ModificaTabellaRate = 0
    },

    OnEsigibilitaChange()
    { 
      this.SchedaFattura.OnEsigibilitaChange()
    },

    RicalcolaRate()
    {
      this.SchedaFattura.RicalcolaRate()
    },

    OnChangeRitenutaCertificata()
    {
      if(this.SchedaFattura.Dati.RitenutaCertificata)
         this.SchedaFattura.Dati.DATA_RITENUTA_CERT = TZDateFunct.DateInHTMLInputFormat(new Date())
      else
         this.SchedaFattura.Dati.DATA_RITENUTA_CERT = null
    },

    OnClickNessunDocumento()
    {
        if(this.SchedaFattura.Dati.DOCUMENTO_CORRELATO == DOCUMENTO_CORRELATO.NessunDocumento)
        {
          this.SchedaFattura.Dati.CIG                 = ''
          this.SchedaFattura.Dati.CUP                 = ''
          this.SchedaFattura.Dati.CONVENZIONE         = ''
          this.SchedaFattura.Dati.DOCUMENTO_NR        = ''
          this.SchedaFattura.Dati.VOCE_DI_RIFERIMENTO = ''
          this.SchedaFattura.Dati.DATA_DOCUMENTO      = ''
        }  
    },

    OnClickScaricaFileFattura()
    {
      var Self = this
      SystemInformation.AdvQuery.GetSQL('Fatture', { CHIAVE : this.SchedaFattura.Chiave },
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ScaricaFileFattura");
                                          if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                          {
                                            if(ArrayInfo.length == 1)
                                               saveAs(TZStringConvFunct.MoveStringToBlob(ArrayInfo[0].XML_BODY), 'Fattura_N_' + Self.SchedaFattura.Dati.NUMERO + '_DataInvio' + ArrayInfo[0].DATA_INVIO + '.xml'); 
                                            else 
                                            {
                                              Self.PopupScegliFileXML = true
                                              ArrayInfo.forEach(function(Elemento)
                                              {
                                                Elemento.DATA_INVIO = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(Elemento.DATA_INVIO))
                                                Self.ListaFileXML.push(Elemento)
                                              });
                                            }
                                          }                                           
                                        },SystemInformation.HandleError,
                                        'ScaricaFileFattura')
    },

    OnClickCorreggiFattura()
    {
      this.PopupCorreggiFattura = true         
    },

    OnClickDownloadFilesXML()
    {
      var Self = this
      for(var i = 0; i < this.ListaFileXML.length; i++)
        if(this.ListaFileXML[i].Selezionato)
          saveAs(TZStringConvFunct.MoveStringToBlob(Self.ListaFileXML[i].XML_BODY), 'Fattura_N_' + Self.SchedaFattura.Dati.NUMERO + '_DataInvio_' + Self.ListaFileXML[i].DATA_INVIO + '.xml'); 
      this.PopupScegliFileXML = false
    },

    OnChangeEntePubblico()
    {
      if(this.SchedaFattura.Dati.ENTE_PUBBLICO)
      {
        this.SchedaFattura.Dati.REVERSE_CHARGE  = false
        this.SchedaFattura.Dati.ESIGIBILITA_IVA           = FATT_ELE_ESIGIBILITA_IVA.Scissione
      }
      else this.SchedaFattura.Dati.ESIGIBILITA_IVA        = FATT_ELE_ESIGIBILITA_IVA.Immediata
    },

    OnChangeImpiantoReverseChange()
    {
      if(this.SchedaFattura.Dati.REVERSE_CHARGE)
      {
        this.SchedaFattura.Dati.ENTE_PUBBLICO   = false
        this.SchedaFattura.Dati.ESIGIBILITA_IVA = FATT_ELE_ESIGIBILITA_IVA.Immediata
      }
    },

    OnClickNuovo() 
    {
        if (this.MenuNuovo.length == 0)
        { 
            this.MenuStampa = []
        }
        else this.MenuNuovo = [];
    },

    OnNextMenuNuovo(AMenu) 
    {
      if (AMenu.SubMenu != undefined)
          this.MenuNuovo = AMenu.SubMenu;
      else 
      {
          AMenu.OnClick();
          this.MenuNuovo = [];
      }
    },

    OnClickScegliFattureDaScalare()
    {
      this.PopupFattureAnticipo = true;
    },

    OnClickAnnullaFattureDaScalare()
    {
      this.PopupFattureAnticipo = false;
    },

    FormattaImporto(Importo)
    {
      return SystemInformation.FormattaImporto(Importo)
    },

    FormattaData(Data)
    {
      return TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Date.parse(Data)));
    },

    AggiungiFattureAnticipoSelezionate()
    {
      var i = 0;
      
      while(i < this.SchedaFattura.Anticipi.length)
      {
        if(this.SchedaFattura.Anticipi[i].Selezionato)
        {
          this.SchedaFattura.SchedaVociFattura.AggiungiVoce(-1,
                                                                   this.SchedaFattura.Anticipi[i].Numero != null ?
                                                                                                                        'FATTURA DI ANTICIPO NUMERO ' + this.SchedaFattura.Anticipi[i].Numero + ' ' +'  DEL ' + ' ' + this.FormattaData(this.SchedaFattura.Anticipi[i].Data) :
                                                                                                                        'AVVISO DI FATTURA'  +  ' DEL ' + ' ' + this.FormattaData(this.SchedaFattura.Anticipi[i].Data),
                                                                   this.SchedaFattura.SchedaVociFattura.TotaleFattura < this.SchedaFattura.Anticipi[i].Importo ?
                                                                                                                                                                  (-1) *  this.SchedaFattura.SchedaVociFattura.SommaImponibile:
                                                                                                                                                                  (-1) * this.SchedaFattura.Anticipi[i].Importo.toFixed(2),
                                                                   1,
                                                                   'F',
                                                                   this.SchedaFattura.SchedaVociFattura.Dati.IvaSuggerita,
                                                                   0,
                                                                   this.SchedaFattura.SchedaVociFattura.IdDocumento,
                                                                   SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                                                                   '',
                                                                   this.SchedaFattura.Anticipi[i].Chiave,
                                                                   this.SchedaFattura.Anticipi[i].Numero != null ?
                                                                                                                        'Fattura di anticipo numero ' + this.SchedaFattura.Anticipi[i].Numero + ' del ' + this.FormattaData(this.SchedaFattura.Anticipi[i].Data) :
                                                                                                                        "Avviso di fattura del " + this.FormattaData(this.SchedaFattura.Anticipi[i].Data),
                                                                   this.SchedaFattura.SchedaVociFattura.RitornaNaturaPagamentoSuggerita()       
                                                           )
                                                      
          this.SchedaFattura.SchedaVociFattura.CalcoloTotaliFattura();
          this.SchedaFattura.Dati.ModificaTabellaVoci++;

          this.SchedaFattura.Anticipi.splice(i, 1);
        }
        else
        {
          i++;
        }
      }
      this.PopupFattureAnticipo = false;
    },

    ScollegaPreventivo(Preventivo)
    {
      var PosizionePreventivo = this.SchedaFattura.SchedaVociFattura.PreventiviCorrelati.indexOf(Preventivo);

      Preventivo.DaScollegare = true;
      this.SchedaFattura.SchedaVociFattura.PreventiviCorrelati.splice(PosizionePreventivo, 1);
      this.SchedaFattura.Dati.ModificaTabellaVoci++;
    },

    OnClickConfermaNCPopup()
    {
      this.PopupAttesaCalcolo = true
      this.PopupNotadiCredito = false
      this.SchedaSelezionataPopup.Registra(() => { this.SchedaFattura.Disponi(() => { this.$emit('onChangeNodiAlbero'); this.PopupAttesaCalcolo = false })})
    },

    OnClickVisualizzaNotaDiCredito(Nota)
    {
      this.SchedaSelezionataPopup = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
      this.SchedaSelezionataPopup.Nuovo();
      this.SchedaSelezionataPopup.Chiave = Nota.ID_NOTA
      this.SchedaSelezionataPopup.Disponi();
      this.PopupNotadiCredito = true
    },

    GetRichiestaNotaDiCreditoPopup()
    {
      if(this.SchedaFattura.Dati.RATE_TOTALI != this.SchedaFattura.Dati.RATE_NON_PAGATE)
        return 'ATTENZIONE: la fattura è parzialmente/totalmente pagata sarà necessario modificare la nota di credito, vuoi crearla?'
      return 'Vuoi generare la nota di credito?'
    },

    OnChangeCondizioniDiPagamentoCaricandoConferme(CondPagamento)
    {
      this.SchedaFattura.Dati.COND_PAGAMENTO = CondPagamento
    }

    // FormattaImporto(Importo)
    // {
    //   return (Importo/100)
    // }

    

   },
   
   beforeMount() 
   {
      if(this.SchedaFattura.AlberoMovimenti)
        this.Tabs.ActiveTab = 'VociFattura';
   },
}
</script>

<style scoped>

label {
 margin-bottom: 0px;
}
.text-highlighted {
  color: red;
}
</style>