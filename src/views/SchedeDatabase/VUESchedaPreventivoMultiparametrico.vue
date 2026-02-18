<template>

  <VUEConfirm :Popup="PopupIncrementaRevisione" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Sei sicuro di voler incrementare il numero di revisione?'" 
              @onClickConfermaPopup="ConfermaIncrementaRevisione"
              @onClickChiudiPopup="AnnullaIncrementaRevisione">
  </VUEConfirm>

  <VUEModal v-if="PopupRevisione" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'600px'" :Larghezza="'1500px'" 
            @onClickChiudiModal="PopupRevisione = false" @onClickConfermaModal="ConfermaPreventivoMultiParametrico">
            <template v-slot:Body>
              <div class="col-md-12">                
                <div style="display: flex; align-items: center;" >
                  <span style="font-size: large;font-weight: bold;">{{ TestoPopupRevisione }}</span>
                  <select class="form-control" v-model="RevisioneScelta" style="cursor:default; margin-left: 3%; width: 6%;">
                    <option v-for="Revisione in ListaRevisioni" 
                            :Key="Revisione.Chiave"
                            :value="Revisione.Chiave">{{Revisione.NumeroRevisione}}
                    </option>
                  </select>
                </div>
                <div style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;"> 
                  <VUESchedaPreventivoMultiparametrico :SchedaPreventivo="RevisioneSelezionata" :IsReadOnly="true" :VisualizzazioneConfermaPreventivo="false"/> 
                </div>
              </div>
            </template>
            <template v-slot:Footer>
              <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="PopupRevisione = false" data-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaPreventivoMultiParametrico" data-dismiss="modal">Conferma</button>
            </template>
  </VUEModal>

  <VUEModal v-if="PopupVisualizzaRevisione" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'600px'" :Larghezza="'1500px'" 
            @onClickChiudiModal="PopupVisualizzaRevisione = false">
            <template v-slot:Body>
              <div class="col-md-12">                
                <div style="display: flex; align-items: center;" >
                  <span style="font-size: large;font-weight: bold;">{{ TestoPopupVisualizzaRevisione }}</span>
                  <select class="form-control" v-model="RevisioneScelta" style="cursor:default; margin-left: 3%; width: 6%;">
                    <option v-for="Revisione in ListaRevisioni" 
                            :Key="Revisione.Chiave"
                            :value="Revisione.Chiave">{{Revisione.NumeroRevisione}}
                    </option>
                  </select>
                </div>
                <div style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;"> 
                  <VUESchedaPreventivoMultiparametrico :SchedaPreventivo="RevisioneSelezionata" :IsReadOnly="true" :VisualizzazioneConfermaPreventivo="false"/> 
                </div>
              </div>
            </template>
            <template v-slot:Footer>
              <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="PopupVisualizzaRevisione = false" data-dismiss="modal">Chiudi</button>
            </template>
  </VUEModal>

  <VUEModal v-if="SchedaPreventivo.PopupSchedaPreventivo" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'100px'" :Larghezza="'500px'"
            @onClickChiudiModal="SchedaPreventivo.PopupSchedaPreventivo = false">
    <template v-slot:Body>
      <p>Vuoi generare la fattura?</p>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:5px;font-weight:bold;width:20%" @click="SchedaPreventivo.AnnullaGenerazione()" data-dismiss="modal">No</button>
      <button type="button" class="btn btn-warning" style="float:right;font-weight:bold;width:30%" @click="SchedaPreventivo.ConfermaGenerazioneDaBanco()" data-dismiss="modal">Sì, ma da banco</button>
      <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="SchedaPreventivo.ConfermaGenerazione()" data-dismiss="modal">Sì</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupGenerazioneConfermaDOrdine" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Scegli soluzioni'" :Altezza="'400px'" :Larghezza="'700px'"
            @onClickChiudiModal="OnClickAnnullaGenerazioneConfermaDOrdine">
    <template v-slot:Body>

      <div v-for="(Soluzione, index) in OggettoPerPopupGenerazioneConferma.Dati.LsSoluzioni" :key="index">
        <div style="font-size:20px;font-weight:bold">Scelta N. {{ index + 1 }}
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>  
        <div class="col-md-9">
          <div v-for="(Voce, index1) in Soluzione.Dati.LsVociSoluzioni" :key="index1">
            <div style="font-size:16px;"> &#x2022; {{ Voce.Dati.Quantita }} {{ Voce.Dati.Descrizione }}</div>
          </div>    
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-success" 
                  style="margin-left:5px;font-weight:bold" 
                  @click="OnClickConfermaSceltaPopup(Soluzione)" data-dismiss="modal">Conferma scelta</button>
        </div>

        <div class="ZMSeparatoreFiltri" style="height:30px">&nbsp;</div>      
      </div>

    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:5px;font-weight:bold;width:20%" @click="OnClickAnnullaGenerazioneConfermaDOrdine" data-dismiss="modal">Annulla</button>
    </template>
  </VUEModal>

  <VUEModalInvioEmail :AttivazionePopup="PopupInviaEmail" 
                      :OggettoEmail="OggettoEmail"
                      :ListaEmailCliente="SchedaPreventivo.ListaEmailCliente"
                      @onClickChiudiModal="AnnullaInvia"
                      @onClickConfermaModal="ConfermaInvia">
  </VUEModalInvioEmail>

  <VUEModal v-if="SchedaPreventivo.ScegliAllegato" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Scegli allegato'" :Altezza="'500px'" :Larghezza="'1000px'" @onClickChiudiModal="AnnullaSceltaAllegati">
    <template v-slot:Body>
      <div class="row wrapper" style="padding-top:0px">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:350px;width:100%;">
            <table class="table table-striped b-t b-light" style="width:100%;">
              <thead>
                <tr>
                  <th>
                    <button class="btn btn-success" style="font-weight:bold" type="button" @click="OnClickSelezionaTuttiAllegati" >
                      {{ IsAnyAllegatoSelected ? 'Nessuno' : 'Tutti' }}
                    </button>
                  </th>
                  <th style="width:95%; font-size: medium;">Allegato</th>
                </tr>
              </thead>
              
              <tbody >
                <tr v-for="Allegato in SchedaPreventivo.SchedaAllegati.LsAllegati" :key="Allegato.Dati.Chiave">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0;margin-top:50%; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    <input type="checkbox" v-model="Allegato.Selezionato"/>
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0;margin-top:50%; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Allegato.Dati.Descrizione }}
                  </td>
                </tr>
              </tbody>

            </table>
          </div>
          
        </section>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:5px;font-weight:bold;width:20%" @click="AnnullaSceltaAllegati" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaInviaAllegato" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>


 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
    <ul class="nav nav-tabs nav-justified">
      <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
          :class="{ active : ATab.Id == Tabs.ActiveTab }">
        <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
          <img v-if="ATab.Id == 'Preventivo' && SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA != -1" src="@/assets/images/IconeAlbero/PreventivoMultiApprovato.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
          <img v-else-if="ATab.Id == 'Preventivo'" src="@/assets/images/IconeAlbero/Preventivo2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
        </a>
      </li>
    </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'Preventivo'">
      <div class="ZMNuovaRigaScheda">

        <div v-if="SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA != -1">
          <div class="col-md-4" style="border:1px solid #b3dbff;margin-bottom:10px">
            <img style="float:left;margin-top:5px;margin-right:10px;width:10%" src="@/assets/images/IconeAlbero/PreventivoMultiApprovato.png">
            <text style="float:left;margin-top:10px;margin-bottom:5px;font-weight: bold;font-size:20px;width:85%;color:#91c26e">PREVENTIVO CONFERMATO </text>
          </div>
        </div>

        <button v-if="SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA == -1 && !SchedaPreventivo.IsNuovo() && !SchedaPreventivo.DatiModificati() && !SchedaPreventivo.Dati.RIFIUTATO && (VisualizzazioneConfermaPreventivo || VisualizzazioneConfermaPreventivo == undefined)" 
                class="btn btn-sm btn-success" 
                style="float: left; margin-top:17px;margin-left:40px" 
                @click="OnClickConfermaPreventivoMultiParametrico">Conferma preventivo</button>

        <div style="padding-top:10px;margin-left:10px;margin-left:20px;margin-top:12px;float:left">
          <div style="float:left;">
            <input type="checkbox" v-model="SchedaPreventivo.Dati.IS_INVIATO" :disabled="IsReadOnly"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
             <label style="font-weight: bold;">&nbsp;Preventivo inviato</label>
          </div>
        </div>

        <div style="padding-top:10px;margin-left:10px;margin-left:20px;margin-top:12px;float:left" v-if="SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA == -1">
          <div style="float:left;">
            <input type="checkbox" v-model="SchedaPreventivo.Dati.RIFIUTATO" :disabled="IsReadOnly"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
             <label style="font-weight: bold;">&nbsp;Preventivo rifiutato</label>
          </div>
        </div>

        <div v-if="CurrentIsPaginaGestioneAnomalie && !SchedaPreventivo.Dati.IS_INVIATO" 
             style="float:left;width:4%;">&nbsp;</div>

        <img style="float:left;cursor:pointer; margin-left:30px; margin-top:5px; width:40px"  
             v-if="CurrentIsPaginaGestioneAnomalie && !SchedaPreventivo.Dati.IS_INVIATO && !SchedaPreventivo.IsNuovo() && !SchedaPreventivo.DatiModificati()" 
             src="@/assets/images/InvioEmail.png" 
             @click="OnClickInvioEmail"/>

        <div style="float:left;width:4%;">&nbsp;</div>
        
        
        <img style="float:left;cursor:pointer; margin-left:30px; margin-top:5px; width:40px"
              
              v-if="CurrentIsPaginaGestioneAnomalie"
              src="@/assets/images/Stampa.png"
              @click="OnClickStampa"/>

        <div v-if="SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA != -1" style="float:left;width:15%;margin-left:15px">
          <text style="float:left;font-weight: bold;padding-right: 10px">Corr. alla conferma N.</text>
          <label style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO_CONFERMA }}</label>
        </div>
        <div style="float:right;margin-left:10px">
            <text style="font-weight: bold;">Data</text>
            <input type="date" id="input-data" class="form-control" v-model="SchedaPreventivo.Dati.DATA" :disabled="VerificaSeDisabilitare()"/>
            <label v-if="SchedaPreventivo.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:right;margin-left:10px; width:6%">
            <text style="font-weight: bold;">Rev.</text>
            <input type="number" step="0.1" class="form-control" v-model="SchedaPreventivo.Dati.NUMERO_REVISIONE" disabled />
        </div>
        <div v-if="!SchedaPreventivo.Dati.NascondiIncrementaRevisione && SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA == -1" style="float:right; width: 2%">
          <div class="m-b-xs" style="margin-top:15px">
            <button class="btn btn-sm btn-success" :disabled="IsReadOnly" style="float: left;" @click="OnClickIncrementaRevisione">+</button>
          </div>
        </div>
        <div v-if="SchedaPreventivo.Dati.NUMERO_REVISIONE > 1 && !SchedaPreventivo.DatiModificati()" style="float:right; width: 2%;margin-right:10px">
          <div class="m-b-xs" style="margin-top:15px">
            <button class="btn btn-sm btn-info" :disabled="IsReadOnly" style="float: left;" @click="OnClickVisualizzaRevisioni">
              <i class="fa fa-copy"/>
            </button>
          </div>
        </div>
        <div style="float:right;width:1%;">&nbsp;</div>
        <div style="float:right;width:12%;">
          <label style="font-weight: bold;">&nbsp;Generato in</label>
          <label class="ZMLabelInput">{{ MeseGenerazione}}</label>
        </div>
        <div v-if="SchedaPreventivo.Dati.NUMERO != -1" style="float:right;width:13%;">
          <text style="float:left;font-weight: bold;padding-right: 10%">Preventivo N.</text>
          <label style="float:left;font-size:15px;width:151px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO }}/{{ SchedaPreventivo.Dati.ANNO }}</label>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>      
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Intestatario</text>
        <hr style="margin-top:1px;margin-bottom: -3px">
      </div>

      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:50%">
          <text style="font-weight: bold; float:left; width:100%">Cliente
            <div style="float:right;">
              <label>Ente pubblico&nbsp;</label>
            </div>
            <div style="float:right;margin-right:4px">
              <input type="checkbox"  v-model="SchedaPreventivo.Dati.ENTE_PUBBLICO" :disabled="VerificaSeDisabilitare() || IsReadOnly"/>
            </div>
          </text>
          <VUEInputClienti v-model="SchedaPreventivo.Dati.ID_CLIENTE" 
                           @onUpdate="newValue => SchedaPreventivo.Dati.ID_CLIENTE = newValue"
                           :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:49%">
          <text style="font-weight: bold; float:left; width:100%">Ragione Sociale </text>
          <input v-model="SchedaPreventivo.Dati.RAGIONE_SOCIALE" type="text" class="form-control" placeholder="Ragione Sociale" :disabled="VerificaSeDisabilitare()"/>
          <label v-if="SchedaPreventivo.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>
      <div class="ZMNuovaRigaScheda">
       <div style="float:left;width:30%">
        <text style="font-weight: bold">Indirizzo</text>
        <input v-model="SchedaPreventivo.Dati.INDIRIZZO_FATTURAZIONE" type="text" class="form-control" placeholder="Indirizzo" :disabled="VerificaSeDisabilitare()">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
           <label style="font-weight: bold;">Civico</label>
           <input type="text" class="form-control" maxlength="7" v-model="SchedaPreventivo.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico" :disabled="VerificaSeDisabilitare()"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
            <VUEInputCAP placeholder="CAP" v-model="SchedaPreventivo.Dati.CAP_FATTURAZIONE" :disabled="VerificaSeDisabilitare()"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:26%">
        <text style="font-weight: bold">Comune</text>
        <input v-model="SchedaPreventivo.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune" :disabled="VerificaSeDisabilitare()">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:20%">
          <label style="font-weight: bold;">Provincia</label>
            <VUEInputProvince v-model="SchedaPreventivo.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true" :disabled="VerificaSeDisabilitare()"/>
       </div> 
      </div>


      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:11%">
          <text style="font-weight: bold">Partita IVA</text>
          <VUEInputPartitaIVA v-model="SchedaPreventivo.Dati.PARTITA_IVA" placeholder="Partita IVA" :disabled="VerificaSeDisabilitare()"></VUEInputPartitaIVA>
          <label v-if="(SchedaPreventivo.Dati.PARTITA_IVA.trim() == '' && SchedaPreventivo.Dati.CODICE_FISCALE.trim() == '')  && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaPreventivo.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:16%">
          <label style="font-weight: bold;">Codice Fiscale</label>
          <VUEInputCodiceFiscale v-model="SchedaPreventivo.Dati.CODICE_FISCALE" style="float:left" placeholder="Codice Fiscale" :disabled="VerificaSeDisabilitare()"></VUEInputCodiceFiscale>
          <label v-if="SchedaPreventivo.Dati.PARTITA_IVA.trim() == '' && SchedaPreventivo.Dati.CODICE_FISCALE.trim() == ''  && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErroreCodiceFiscale != ''  && !DeveloperMode && SchedaPreventivo.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:8%">
            <label style="font-weight: bold;">Codice SDI</label>
            <input type="text" class="form-control" v-model="SchedaPreventivo.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7" :disabled="VerificaSeDisabilitare()"/>
            <label v-if="SchedaPreventivo.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
              {{(SchedaPreventivo.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
            </label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:9%">
            <label style="font-weight: bold;">Cod. uff. dest.</label>
            <input type="text" class="form-control" v-model="SchedaPreventivo.Dati.COD_UFFICIO_DEST"
                    placeholder="Codice ufficio destinazione"
                    maxlength="6"
                    :disabled="VerificaSeDisabilitare()"/>
            <label v-if="SchedaPreventivo.Dati.COD_UFFICIO_DEST.length != 6 && SchedaPreventivo.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
            </label>
        </div> 

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:14%">
            <label style="font-weight: bold;">Nazione emittente</label>
            <VUEInputNazioni v-model="SchedaPreventivo.Dati.NAZIONE_EM_PIVA" emptyElement="false" style="cursor:default" :disabled="VerificaSeDisabilitare()"/>
            <label v-if="SchedaPreventivo.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:26%">
          <label style="font-weight: bold;">PEC</label>
          <input type="email" class="form-control" v-model="SchedaPreventivo.Dati.PEC" placeholder="PEC" :disabled="VerificaSeDisabilitare()"/> 
        </div>
      </div>
      
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold">Indirizzo di Destinazione</text>
        <hr style="margin-top:1px">
      </div>

      <div v-if="SchedaPreventivo.Dati.ID_FILIALE">
        <div style="float:left;width:50%;">
          <text style="font-weight: bold">Filiale: </text><text>{{ SchedaPreventivo.Dati.NOME_FILIALE }}</text>
          
        </div>
        <div style="float:left; margin-top: 5px;">
          <div style="float:left; margin-left: 17px;">
            <input type="checkbox" v-model="SchedaPreventivo.Dati.PROGRAMMA_PROSSIMA_VISITA" :disabled="VerificaSeDisabilitare()"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
            <label style="font-weight: bold;">&nbsp;Programma prossima visita</label>
            <label v-if="SchedaPreventivo.Dati.PROGRAMMA_PROSSIMA_VISITA == true && SchedaPreventivo.Dati.ID_FILIALE == -1" class="ZMFormLabelError">Inserire filiale</label> 
          </div>
        </div>
      </div>

      <VUEModalButtonRecapitiFiliali :styleForButton="'float:right; margin-right: 0px'"
                                     :IdCliente="SchedaPreventivo.Dati.ID_CLIENTE"
                                     @filiale-selected="OnFilialeSelected"
                                     :IsDisabled="VerificaSeDisabilitare()"/>

      <div class="ZMNuovaRigaScheda" style="margin-top:-18px">
        <div style="float:left;width:50%">
          <text style="font-weight: bold">Destinazione</text>
          <input v-model="SchedaPreventivo.Dati.DESTINAZIONE" type="text" class="form-control" placeholder="Destinazione" :disabled="VerificaSeDisabilitare()">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <text style="font-weight: bold">Indirizzo</text>
          <input v-model="SchedaPreventivo.Dati.INDIRIZZO_DESTINAZIONE" type="text" class="form-control" placeholder="Indirizzo" :disabled="VerificaSeDisabilitare()">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:9%">
           <label style="font-weight: bold;">Civico</label>
           <input type="text" class="form-control" maxlength="7" v-model="SchedaPreventivo.Dati.NR_CIVICO_DESTINAZIONE" placeholder="Nr. civico" :disabled="VerificaSeDisabilitare()"/>
        </div> 
      </div>
       
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP placeholder="CAP" v-model="SchedaPreventivo.Dati.CAP_DESTINAZIONE" :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <text style="font-weight: bold">Comune</text>
          <input v-model="SchedaPreventivo.Dati.COMUNE_DESTINAZIONE" type="text" class="form-control" placeholder="Comune" :disabled="VerificaSeDisabilitare()"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Provincia</label>
            <VUEInputProvince v-model="SchedaPreventivo.Dati.PROVINCIA_DESTINAZIONE" emptyElement="true" :disabled="VerificaSeDisabilitare()"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:32%">
          <label style="font-weight: bold;">Nazione</label>
          <VUEInputNazioni v-model="SchedaPreventivo.Dati.NAZIONE_DESTINAZIONE" emptyElement="true" :disabled="VerificaSeDisabilitare()"/>
        </div>
         <div style="float:left;width:1%"> &nbsp;</div>
          <div style="float:left; width:6%">
            <label style="font-weight: bold;">Zona</label>
            <VUEInputZone v-model="SchedaPreventivo.Dati.ID_ZONA_SPEDIZIONE" emptyElement="true" :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>
   </div>

   <div v-if="Tabs.ActiveTab == 'VociPreventivo'" style="width:100%;">
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Info contabili</text>
        <hr style="margin-top:1px">
      </div>
      <div class="ZMNuovaRigaScheda" style="margin-top:-20px">
        <div style="float:left;width:20%">
          <label style="font-weight: bold;">Cond. Pagamento</label>
          <VUEInputCondPagamenti v-model="SchedaPreventivo.Dati.COND_PAGAMENTO" :disabled="VerificaSeDisabilitare()"/>
          <label v-if="SchedaPreventivo.Dati.COND_PAGAMENTO == null" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Causale</text>
          <VUEInputCausali v-model="SchedaPreventivo.Dati.CAUSALE" :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Esigibilità IVA</text>
          <VUEInputEsigibilitaIVA v-model="SchedaPreventivo.Dati.ESIGIBILITA_IVA" :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Note</text>
          <input type="text" class="form-control" v-model="SchedaPreventivo.Dati.NOTE" :disabled="VerificaSeDisabilitare()">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Ordine</text>
          <select style="float:left;width:100%;margin-right:5px" :disabled="VerificaSeDisabilitare()" class="form-control" @change="OnClickNessunDocumento()" v-model="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO">
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

      <div class="ZMSeparatoreScheda" v-if="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">       
        <text style="font-weight: bold;margin-bottom:-20px">Ordine</text>
        <hr style="margin-top:1px; margin-bottom: -5px">
      </div>
      <div class="ZMNuovaRigaScheda" style="padding-top:3px" v-if="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Documento nr.</text>
          <input v-model="SchedaPreventivo.Dati.DOCUMENTO_NR" type="text" class="form-control" placeholder="Documento nr.">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">Data del documento</label>
            <input type="date" class="form-control" v-model="SchedaPreventivo.Dati.DATA_DOCUMENTO"/>
        </div>
      </div>
      <div class="ZMNuovaRigaScheda" v-if="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Voce di riferimento</text>
          <input v-model="SchedaPreventivo.Dati.VOCE_DI_RIFERIMENTO" type="text" class="form-control" placeholder="Voce di riferimento">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">CIG</label>
            <input type="text" class="form-control" v-model="SchedaPreventivo.Dati.CIG" placeholder="CIG"/>
        </div>
      </div>
      <div class="ZMNuovaRigaScheda" v-if="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento">
        <div style="float:left;width:79%">
          <text style="font-weight: bold">Convenzione</text>
          <input v-model="SchedaPreventivo.Dati.CONVENZIONE" type="text" class="form-control" placeholder="Convenzione">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">CUP</label>
            <input type="text" class="form-control" v-model="SchedaPreventivo.Dati.CUP" placeholder="CUP"/>
        </div>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Conto corrente / Riba</text>
        <hr style="margin-top:1px;margin-bottom:-0px">
      </div>
      <div class="ZMNuovaRigaScheda">
        <VUEInputContoRibaCorrente :ContoCorrente="SchedaPreventivo.Dati.ContoCorrente" :Disabilitato="VerificaSeDisabilitare()"/>
      </div>
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Sezioni</text>
        <hr style="margin-top:1px">
      </div>
      <div v-if="SchedaPreventivo.Dati.ID_CLIENTE != -1">

        <VUEVociPreventivoMultiparametrico :SchedaVociPreventivoMultiparametrico = "SchedaPreventivo.SchedaVociPreventivo"
                                           :SchedaPreventivo = "SchedaPreventivo"
                                           :IsSchedaPreventivo ="true"
                                           :IdCliente="SchedaPreventivo.Dati.ID_CLIENTE"
                                           @onChange ="OnSezioniPreventivoChange"
                                           :Disabilitato ="VerificaSeDisabilitare()"
                                           :DiventatoConfermaDOrdine ="SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA != -1">
        </VUEVociPreventivoMultiparametrico>
      </div>
    
      <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE</div>                               
   </div>

   <!-- <div v-if="Tabs.ActiveTab == 'LogPreventivo'" style="height:50%">
      <VUELogDocumentiEconomici :SchedaLogDocumentiEconomici="SchedaPreventivo.SchedaLogPreventivo"/>
   </div> -->

   <div v-if="Tabs.ActiveTab == 'Allegati'">
    <VUEAllegati :SchedaAllegati="SchedaPreventivo.SchedaAllegati" 
                  NomeCampoModello="PreventiviMultiparametrici"
                 @onChange="OnAllegatiChange" 
                 :disabled="VerificaSeDisabilitare()"/>
  </div>

</div>
</template>


<script>
import VUEModalButtonRecapitiFiliali from '@/components/VUEModalButtonRecapitiFiliali';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation,DASHBOARD_FILTER_TYPES, DOCUMENTO_CORRELATO,RUOLI,NOME_PROGRAMMA } from '@/SystemInformation.js'
import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
import VUEVociPreventivoMultiparametrico, { TSchedaVociPreventivoMultiparametrico } from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociPreventivoMultiparametrico.vue';
import VUEAllegati, { TSchedaAllegati } from'../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { ID_NODO_PREVENTIVI_MULTI, ID_NODO_PREVENTIVI } from '@/NodiVuoti'
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
// import VUELogDocumentiEconomici, { TSchedaLogDocumentiEconomici } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogDocumentiEconomici.vue';
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import VUEModalInvioEmail from '@/views/SchedeDatabase/ComponentMultiScheda/VUEModalInvioEmail.vue';

export class TSchedaPreventivoMultiparametrico extends TSchedaGenerica
{
  
  constructor (AdvQuery) 
  {
    super(AdvQuery)
    this.__TriggerOnPreventivoConfermato        = 0;
    this.IsPreventivoMultiparametrico           = true; 
    this.ScegliAllegato                         = false;
  }

  AssignDestinazioneFromFiliale(Filiale)
  {
    this.Dati.ID_FILIALE               = Filiale.CHIAVE;
    this.Dati.NOME_FILIALE             = Filiale.NOME;

    this.Dati.DESTINAZIONE             = Filiale.NOME;
    this.Dati.INDIRIZZO_DESTINAZIONE   = Filiale.INDIRIZZO;
    this.Dati.NR_CIVICO_DESTINAZIONE   = Filiale.NR_CIVICO;
    this.Dati.CAP_DESTINAZIONE         = Filiale.CAP;
    this.Dati.COMUNE_DESTINAZIONE      = Filiale.COMUNE;
    this.Dati.PROVINCIA_DESTINAZIONE   = Filiale.PROVINCIA;
    this.Dati.NAZIONE_DESTINAZIONE     = Filiale.NAZIONE;
    this.Dati.ID_ZONA_SPEDIZIONE       = Filiale.ZONA;
  }

  AssignSchedaCliente(Scheda)
  {
      this.Dati.ID_CLIENTE             = Scheda.Chiave
      this.Dati.RAGIONE_SOCIALE        = Scheda.Dati.RAGIONE_SOCIALE
      this.Dati.INDIRIZZO_FATTURAZIONE = Scheda.Dati.INDIRIZZO_FATTURAZIONE
      this.Dati.NR_CIVICO_FATTURAZIONE = Scheda.Dati.NR_CIVICO_FATTURAZIONE
      this.Dati.CAP_FATTURAZIONE       = Scheda.Dati.CAP_FATTURAZIONE
      this.Dati.COMUNE_FATTURAZIONE    = Scheda.Dati.COMUNE_FATTURAZIONE
      this.Dati.PROVINCIA_FATTURAZIONE = Scheda.Dati.PROVINCIA_FATTURAZIONE
      this.Dati.CAP_DESTINAZIONE       = Scheda.Dati.CAP_SPEDIZIONE
      this.Dati.DESTINAZIONE           = Scheda.Dati.PRESSO
      this.Dati.COMUNE_DESTINAZIONE    = Scheda.Dati.COMUNE_SPEDIZIONE
      this.Dati.PROVINCIA_DESTINAZIONE = Scheda.Dati.PROVINCIA_SPEDIZIONE
      this.Dati.INDIRIZZO_DESTINAZIONE = Scheda.Dati.INDIRIZZO_SPEDIZIONE
      this.Dati.NR_CIVICO_DESTINAZIONE = Scheda.Dati.NR_CIVICO_SPEDIZIONE
      this.Dati.ZONA_SPEDIZIONE        = Scheda.Dati.ZONA_SPEDIZIONE
      this.Dati.PARTITA_IVA            = Scheda.Dati.PARTITA_IVA
      this.Dati.CODICE_FISCALE         = Scheda.Dati.CODICE_FISCALE
      this.Dati.COND_PAGAMENTO         = Scheda.Dati.COND_PAGAMENTO
      this.Dati.PEC                    = Scheda.Dati.PEC
      this.Dati.COD_ENTE_SDI           = Scheda.Dati.COD_ENTE_SDI
      this.Dati.ENTE_PUBBLICO          = Scheda.Dati.ENTE_PUBBLICO
      this.Dati.COD_UFFICIO_DEST       = Scheda.Dati.COD_UFFICIO_DEST
      this.Dati.NAZIONE_DESTINAZIONE   = Scheda.Dati.NAZIONE_SPEDIZIONE
      this.Dati.NAZIONE_EM_PIVA        = Scheda.Dati.NAZIONE_EM_PIVA
      this.Dati.ContoCorrente.IBAN                   = Scheda.Dati.ContoCorrente.IBAN
      this.Dati.ContoCorrente.ID_CONTO_CORRENTE      = Scheda.Dati.ContoCorrente.ID_CONTO_CORRENTE
      this.Dati.ContoCorrente.BANCA                  = Scheda.Dati.ContoCorrente.BANCA
      this.Dati.ContoCorrente.NR_CONTO               = Scheda.Dati.ContoCorrente.NUMERO_CONTO
      this.Dati.ContoCorrente.SWIFT                  = Scheda.Dati.ContoCorrente.SWIFT
      this.Dati.ContoCorrente.BIC                    = Scheda.Dati.ContoCorrente.BIC
      this.Dati.ContoCorrente.CONTO_RIBA             = Scheda.Dati.ContoCorrente.CONTO_RIBA     
      this.Dati.CAUSALE                = Scheda.Dati.CAUSALE
      this.Dati.NOTE                   = Scheda.Dati.NOTE_IN_FATTURA
      this.Dati.ESIGIBILITA_IVA        = Scheda.Dati.ESIGIBILITA_IVA
      this.SchedaVociPreventivo.SetDatiCliente(Scheda.Dati.IVA_SUGGERITA_CLIENTE,
                                               Scheda.Dati.RITENUTA,
                                               Scheda.Dati.NATURA_PAGAMENTO)
      this.SchedaVociPreventivo.InserisciUnaNuovaSezione()
  }

  InserisciNuovaRigaVoceTramiteGenerazionePreventivi(Descrizione, Prezzo, Quantita, Iva)
  {
    this.SchedaVociPreventivo.CheckPresenzaSezioneSoluzione(Descrizione, Prezzo, Quantita, Iva) 
  }
  
  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)

     this.SchedaVociPreventivo = new TSchedaVociPreventivoMultiparametrico()
     this.SchedaVociPreventivo.ClearVociDocumentiEconomici()
    //  this.SchedaLogPreventivo = new TSchedaLogDocumentiEconomici()
    //  this.SchedaLogPreventivo.ClearLogDocumentiEconomici()
  }

  CanRecord()
  {
    return (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            this.Dati.DATA != '' && 
            this.Dati.ID_CLIENTE != -1 &&
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            this.Dati.COND_PAGAMENTO != null &&
            (this.Dati.COD_UFFICIO_DEST.length == 6 || this.Dati.COD_UFFICIO_DEST.length == '') &&
            (this.Dati.CODICE_FISCALE == '' || 
              TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaVociPreventivo.AllDataOk() &&
            (!this.Dati.PROGRAMMA_PROSSIMA_VISITA || (this.Dati.ID_FILIALE != -1))
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;

    if(this.Dati.ID_PREVENTIVO_CONFERMA != -1)
      return false;
    return true;
  }

  Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                      // {
                                      //   Query     : "EliminaLogTramitePreventivo",
                                      //   Parametri : { CHIAVE : this.Chiave }
                                      // },
                                      {
                                        Query     : "EliminaAllegatoTramitePreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaVociSoluzioniCollegateAlPreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaSoluzioniCollegateAlPreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaSezioniCollegateAlPreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_PREVENTIVO : this.Chiave }
                                      }
                                    ]};
      this.AdvQuery.PostSQL('PreventiviMultiparametrici',ObjQuery,function()
      {
          OnSuccess();
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
   }

   Registra(OnSuccess, OnError, GeneratoDaGeneratore = false)
   {
      var Self = this
      if(this.CanRecord() || GeneratoDaGeneratore)
      {
        var ObjQuery = { Operazioni : [] };

        ObjQuery.Operazioni.push({
                                   Query     : this.IsNuovo() ? "Inserisci" : "Modifica",
                                   Parametri : {
                                                  CHIAVE                    : this.IsNuovo() ? undefined : Self.Chiave, 
                                                  ID_CLIENTE                : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_CLIENTE),
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(this.Dati.RAGIONE_SOCIALE),
                                                  CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CODICE_FISCALE),
                                                  DESTINAZIONE              : TSchedaGenerica.PrepareForRecordString(this.Dati.DESTINAZIONE),
                                                  INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_FATTURAZIONE),
                                                  NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_FATTURAZIONE),
                                                  PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_FATTURAZIONE),
                                                  COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_FATTURAZIONE),
                                                  CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_FATTURAZIONE),
                                                  DATA                      : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA),
                                                  ANNO                      : this.Dati.ANNO != null ? TSchedaGenerica.PrepareForRecordInteger(this.Dati.ANNO) : new Date(Self.Dati.DATA).getFullYear(),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(this.Dati.PARTITA_IVA),
                                                  NAZIONE_DESTINAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_DESTINAZIONE),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_EM_PIVA),
                                                  INDIRIZZO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_DESTINAZIONE),
                                                  NR_CIVICO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_DESTINAZIONE),
                                                  PROVINCIA_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_DESTINAZIONE),
                                                  COMUNE_DESTINAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_DESTINAZIONE),
                                                  CAP_DESTINAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_DESTINAZIONE),
                                                  ID_ZONA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_ZONA_SPEDIZIONE),
                                                  COND_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.COND_PAGAMENTO),
                                                  NOTE                      : TSchedaGenerica.PrepareForRecordString(this.Dati.NOTE),
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.SchedaVociPreventivo.RitornaRitenutaAcconto()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(this.Dati.ESIGIBILITA_IVA),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CAUSALE),
                                                  IS_INVIATO                : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IS_INVIATO),
                                                  RIFIUTATO                 : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.RIFIUTATO),
                                                  IBAN                      : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.IBAN)  : null,
                                                  BANCA                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BANCA) : null,
                                                  BIC                       : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BIC)   : null,
                                                  SWIFT                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.SWIFT) : null,
                                                  ID_CONTO_CORRENTE         : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                  ENTE_PUBBLICO             : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.ENTE_PUBBLICO),
                                                  COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_ENTE_SDI),
                                                  PEC                       : TSchedaGenerica.PrepareForRecordString(this.Dati.PEC), 
                                                  CUP                       : TSchedaGenerica.PrepareForRecordString(this.Dati.CUP),
                                                  CIG                       : TSchedaGenerica.PrepareForRecordString(this.Dati.CIG),
                                                  DOCUMENTO_CORRELATO       : TSchedaGenerica.PrepareForRecordString(this.Dati.DOCUMENTO_CORRELATO),
                                                  CONVENZIONE               : TSchedaGenerica.PrepareForRecordString(this.Dati.CONVENZIONE),
                                                  VOCE_DI_RIFERIMENTO       : TSchedaGenerica.PrepareForRecordString(this.Dati.VOCE_DI_RIFERIMENTO),
                                                  DOCUMENTO_NR              : TSchedaGenerica.PrepareForRecordString(this.Dati.DOCUMENTO_NR),
                                                  DATA_DOCUMENTO            : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA_DOCUMENTO),                                                  
                                                  MESE_GENERAZIONE          : this.Dati.MESE_GENERAZIONE != 0 && this.Dati.MESE_GENERAZIONE != null? 
                                                                              TSchedaGenerica.PrepareForRecordInteger(this.Dati.MESE_GENERAZIONE) : null, 

                                                  COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_UFFICIO_DEST),
                                                  NUMERO_REVISIONE          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NUMERO_REVISIONE),
                                                  
                                                  PROGRAMMA_PROSSIMA_VISITA : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.PROGRAMMA_PROSSIMA_VISITA),
                                                  ID_FILIALE                : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_FILIALE),

                                               }
                                 });
        this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_PREVENTIVO_MULTI')
        
        this.SchedaVociPreventivo.PrepareQueryParameters(ObjQuery.Operazioni)

        if(Self.Dati.NascondiIncrementaRevisione)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "InserisciStoricoRevisione",
                                      Parametri : {
                                                  CHIAVE                    : undefined, 
                                                  PRIMO_PREVENTIVO          : Self.Chiave, 
                                                  NUMERO                    : TSchedaGenerica.PrepareForRecordInteger(Self.CopiaDati.Dati.NUMERO),
                                                  ID_CLIENTE                : TSchedaGenerica.PrepareForRecordInteger(Self.CopiaDati.Dati.ID_CLIENTE),
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.RAGIONE_SOCIALE),
                                                  CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CODICE_FISCALE),
                                                  DESTINAZIONE              : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.DESTINAZIONE),
                                                  INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.INDIRIZZO_FATTURAZIONE),
                                                  NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.NR_CIVICO_FATTURAZIONE),
                                                  PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.PROVINCIA_FATTURAZIONE),
                                                  COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.COMUNE_FATTURAZIONE),
                                                  CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CAP_FATTURAZIONE),
                                                  DATA                      : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.DATA),
                                                  ANNO                      : Self.CopiaDati.Dati.ANNO != null ? TSchedaGenerica.PrepareForRecordInteger(Self.CopiaDati.Dati.ANNO) : new Date(Self.CopiaDati.Dati.DATA).getFullYear(),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.PARTITA_IVA),
                                                  NAZIONE_DESTINAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.NAZIONE_DESTINAZIONE),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.NAZIONE_EM_PIVA),
                                                  INDIRIZZO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.INDIRIZZO_DESTINAZIONE),
                                                  NR_CIVICO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.NR_CIVICO_DESTINAZIONE),
                                                  PROVINCIA_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.PROVINCIA_DESTINAZIONE),
                                                  COMUNE_DESTINAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.COMUNE_DESTINAZIONE),
                                                  CAP_DESTINAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CAP_DESTINAZIONE),
                                                  ID_ZONA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_ZONA_SPEDIZIONE),
                                                  COND_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.COND_PAGAMENTO),
                                                  NOTE                      : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.NOTE),
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.SchedaVociPreventivo.RitornaRitenutaAcconto()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.ESIGIBILITA_IVA),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.CAUSALE),
                                                  IS_INVIATO                : TSchedaGenerica.PrepareForRecordBoolean(Self.CopiaDati.Dati.IS_INVIATO),
                                                  RIFIUTATO                 : TSchedaGenerica.PrepareForRecordBoolean(Self.CopiaDati.Dati.RIFIUTATO),
                                                  IBAN                      : Self.CopiaDati.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.ContoCorrente.IBAN)  : null,
                                                  BANCA                     : Self.CopiaDati.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.ContoCorrente.BANCA) : null,
                                                  BIC                       : Self.CopiaDati.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.ContoCorrente.BIC)   : null,
                                                  SWIFT                     : Self.CopiaDati.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.ContoCorrente.SWIFT) : null,
                                                  ID_CONTO_CORRENTE         : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                  ENTE_PUBBLICO             : TSchedaGenerica.PrepareForRecordBoolean(Self.CopiaDati.Dati.ENTE_PUBBLICO),
                                                  COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.COD_ENTE_SDI),
                                                  PEC                       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.PEC), 
                                                  CUP                       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CUP),
                                                  CIG                       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CIG),
                                                  DOCUMENTO_CORRELATO       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.DOCUMENTO_CORRELATO),
                                                  CONVENZIONE               : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.CONVENZIONE),
                                                  VOCE_DI_RIFERIMENTO       : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.VOCE_DI_RIFERIMENTO),
                                                  DOCUMENTO_NR              : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.DOCUMENTO_NR),
                                                  DATA_DOCUMENTO            : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.DATA_DOCUMENTO),                                                  
                                                  COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(Self.CopiaDati.Dati.COD_UFFICIO_DEST),
                                                  NUMERO_REVISIONE          : TSchedaGenerica.PrepareForRecordInteger(Self.CopiaDati.Dati.NUMERO_REVISIONE),
                                                  PROGRAMMA_PROSSIMA_VISITA : TSchedaGenerica.PrepareForRecordBoolean(Self.CopiaDati.Dati.PROGRAMMA_PROSSIMA_VISITA),
                                                  ID_FILIALE                : TSchedaGenerica.PrepareForRecordListIndex(Self.CopiaDati.Dati.ID_FILIALE),
                                               }
                                    });

          ObjQuery.Operazioni.push({
                                      Query     : "InserisciAllegatoRevisione",
                                      Parametri : {
                                                    CHIAVE               : Self.Chiave
                                                  }
          })

          let ListaSezioni = Self.CopiaDati.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico

          ListaSezioni.forEach((Sezione)=>
                              {
                                ObjQuery.Operazioni.push({
                                                          Query     : "InserisciSezione",
                                                          Parametri : {
                                                                        CHIAVE               : -1,
                                                                        ID_PREVENTIVO_MULTI  : undefined
                                                                      },
                                                          ResetKeys : [2]
                                })

                                Sezione.Dati.LsSoluzioni.forEach((Soluzioni)=>
                                                                {
                                                                  ObjQuery.Operazioni.push({
                                                                                              Query     : "InserisciSoluzione",
                                                                                              Parametri : {
                                                                                                            CHIAVE              : -1,
                                                                                                            ID_SEZIONE          : undefined,
                                                                                                            ID_PREVENTIVO_MULTI : undefined
                                                                                                          },
                                                                                              ResetKeys : [3]
                                                                  })

                                                                  Soluzioni.Dati.LsVociSoluzioni.forEach((Soluzione, Ordinamento)=>
                                                                                      { 

                                                                                        ObjQuery.Operazioni.push({
                                                                                                                    Query     : "InserisciVoceSoluzione",
                                                                                                                    Parametri : {
                                                                                                                                  CHIAVE               : -1,
                                                                                                                                  ID_SOLUZIONE         : undefined,
                                                                                                                                  ID_PREVENTIVO_MULTI  : undefined,
                                                                                                                                  DESCRIZIONE          : TSchedaGenerica.PrepareForRecordString(Soluzione.CopiaDati.Descrizione),
                                                                                                                                  ORDINAMENTO          : (Ordinamento + 1),
                                                                                                                                  UNITA_DI_MISURA      : TSchedaGenerica.PrepareForRecordListIndex(Soluzione.CopiaDati.UnitaDiMisura),
                                                                                                                                  IMPORTO              : Soluzione.CopiaDati.Importo_Ivato == 'T' ? (TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.Ivato * 100)) : (TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.Imponibile * 100)),
                                                                                                                                  QUANTITA             : TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.Quantita * 100),
                                                                                                                                  IVA                  : TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.Iva * 100),
                                                                                                                                  SCONTO               : TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.Sconto * 100),
                                                                                                                                  CODICE_PRODOTTO      : TSchedaGenerica.PrepareForRecordString(Soluzione.CopiaDati.CodiceProdotto),
                                                                                                                                  NATURA_PAGAMENTO     : TSchedaGenerica.PrepareForRecordInteger(Soluzione.CopiaDati.NaturaPagamento),
                                                                                                                                  ID_PRODOTTO          : TSchedaGenerica.PrepareForRecordListIndex(Soluzione.CopiaDati.IdProdotto),  

                                                                                                                                },
                                                                                                                    ResetKeys : [4],
                                                                                                                  
                                                                                                                 
                                                                                                                })
                                                                                      })

                                                                })
                              })
        }
        
        this.AdvQuery.PostSQL('PreventiviMultiparametrici',ObjQuery,function(Response)
         {
            if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
              Self.SchedaAllegati.DeleteFileDaEliminare()
              
            ObjQuery = {};
            if(Self.Chiave == -1)
                Self.Chiave = Response.NewKey1;
            Self.Dati.ModificaTabellaAllegati = false;
            Self.Dati.ModificaTabellaVoci     = false
            Self.CreateSnapshot();

            OnSuccess();
         },
         function(HTTPError,SubHTTPError,Response)
         {
           OnError(HTTPError,SubHTTPError,Response);
         });
      }
   }

   CaricaRiassunto(Riassunto)
   {
      this.IsPreventivo                   = true;
      this.Chiave                         = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociPreventivo.SetIdDocumento(this.Chiave)
      this.Dati.ID_CLIENTE                = Riassunto.ID_CLIENTE;
      this.Dati.ANNO                      = Riassunto.ANNO
      this.Dati.DATA                      = Riassunto.DATA != undefined? TSchedaGenerica.DisponiFromDate(Riassunto.DATA) : TZDateFunct.DateInHTMLInputFormat(new Date())
      if(Riassunto.NUMERO != null && Riassunto.NUMERO != undefined)
        this.Dati.NUMERO = Riassunto.NUMERO;
      else this.Dati.NUMERO = -1;
      this.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.Dati.PROGRAMMA_PROSSIMA_VISITA = TSchedaGenerica.DisponiFromBoolean(Riassunto.PROGRAMMA_PROSSIMA_VISITA);
      this.Dati.ID_PREVENTIVO_CONFERMA    = TSchedaGenerica.DisponiFromListIndex(Riassunto.ID_PREVENTIVO_CONFERMA);
      this.CreateSnapshot();
   }

    GetDescrizione()
    {
      if(this.Dati.ANNO == -1)
          return ('Avviso preventivo');
      return ('Preventivo ' + this.Dati.NUMERO + '/' + this.Dati.ANNO);
    }
   
    Clear()
    {
      this.SchedaAllegati.AssignDati([])
      // this.SchedaLogPreventivo.AssignDati([])
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociPreventivo.SetIdDocumento(-1)
      this.SchedaVociPreventivo.AssignDati([], SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA / 10, SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO, -1)

      this.Dati = {
                      NUMERO                        : -1,
                      ID_CLIENTE                    : -1,
                      DATA                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
                      RAGIONE_SOCIALE               : '',
                      INDIRIZZO_FATTURAZIONE        : '',
                      NR_CIVICO_FATTURAZIONE        : '',
                      COMUNE_FATTURAZIONE           : '',
                      PROVINCIA_FATTURAZIONE        : -1,
                      CAP_FATTURAZIONE              : '',
                      PARTITA_IVA                   : '',
                      CODICE_FISCALE                : '',
                      NAZIONE_DESTINAZIONE          : SystemInformation.Configurazioni.StatoItaliano,
                      NAZIONE_EM_PIVA               : SystemInformation.Configurazioni.StatoItaliano,
                      COMUNE_DESTINAZIONE           : '',
                      PROVINCIA_DESTINAZIONE        : -1,
                      CAP_DESTINAZIONE              : '',
                      DESTINAZIONE                  : '',
                      INDIRIZZO_DESTINAZIONE        : '',
                      NR_CIVICO_DESTINAZIONE        : '',
                      ID_ZONA_SPEDIZIONE            : -1,
                      COND_PAGAMENTO                : null,
                      NOTE                          : '',
                      CAUSALE                       : -1,
                      ESIGIBILITA_IVA               : '',
                      IS_INVIATO                    : false,
                      RIFIUTATO                     : false,
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
                      MESE_GENERAZIONE              : null,

                      ContoCorrente                 : { 
                                                        IBAN              : '',
                                                        BANCA             : '',
                                                        ID_CONTO_CORRENTE : -1,
                                                        NR_CONTO          : '',
                                                        CONTO_RIBA        : false,
                                                        BIC               : '',
                                                        SWIFT             : ''
                                                      },
                      ID_PREVENTIVO_CONFERMA        : -1,
                      NUMERO_CONFERMA               : -1,
                      // Dati allegati
                      ModificaTabellaAllegati       : false,
                      ModificaTabellaVoci           : false,
                      DatoPerEmit                   : false,
                      NascondiIncrementaRevisione   : false,
                      NUMERO_REVISIONE              : 1,
                      DescrizioniAllegati           : [],
                      PosizioneAllegati             : [],
                  }
      super.Clear();

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
      if(this.Dati.DATA_CREAZIONE != null || this.Dati.DATA_CREAZIONE != undefined)
         return 'Manuale' + ' ' + this.Dati.DATA_CREAZIONE
      else return 'Manuale'
    }

    Disponi(OnSuccess)
    {
      var Self = this;
      if(this.InEliminazione) return;
        this.AdvQuery.GetSQL('PreventiviMultiparametrici',{ CHIAVE : this.Chiave },
                              function(Results)
                              {
                                if(Self.InEliminazione) return;
                                let ArrayInfo           = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                let ArrayAllegati       = Self.AdvQuery.FindResults(Results,"AllegatiPreventivo");
                                // let ArrayVociPreventivi = Self.AdvQuery.FindResults(Results,"VociPreventivo");
                                // let ArrayLogPreventivo  = Self.AdvQuery.FindResults(Results,"LogPreventivo");
                                let ArraySezioni        = Self.AdvQuery.FindResults(Results,"SezioniPreventivo");

                                if(ArrayInfo != undefined && ArraySezioni != undefined)
                                {
                                  if(ArraySezioni.length != 0)
                                  {
                                    Self.SchedaVociPreventivo.SetIdDocumento(Self.Chiave)
                                    Self.SchedaVociPreventivo.AssignDatiSezioni(ArraySezioni,
                                                                                TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_SUGGERITA_CLIENTE) / 10,
                                                                                TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA) / 10)
                                  }

                                  // if(ArrayLogPreventivo.length != 0)
                                  //   Self.SchedaLogPreventivo.AssignDati(ArrayLogPreventivo,'ID_PREVENTIVO')
                                  if(ArrayAllegati.length != 0)
                                  {
                                    Self.SchedaAllegati.SetIdDocumento(Self.Chiave)
                                    Self.SchedaAllegati.AssignDati(ArrayAllegati,'PREVENTIVO')
                                  }
                                  
                                  if(ArrayInfo.length != 0)
                                  {
                                    Self.PRIMO_PREVENTIVO = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].PRIMO_PREVENTIVO) == 0? null : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].PRIMO_PREVENTIVO)

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
                                                  ANNO                          : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ANNO),
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
                                                  ID_ZONA_SPEDIZIONE            : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_ZONA_SPEDIZIONE),
                                                  COND_PAGAMENTO                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].COND_PAGAMENTO),
                                                  NOTE                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                  CAUSALE                       : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CAUSALE),
                                                  ESIGIBILITA_IVA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                                                  IS_INVIATO                    : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IS_INVIATO),
                                                  RIFIUTATO                     : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].RIFIUTATO),
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
                                                  NUMERO_REVISIONE              : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_REVISIONE),
                                                  PROGRAMMA_PROSSIMA_VISITA     : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].PROGRAMMA_PROSSIMA_VISITA),
                                                  ID_FILIALE                    : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_FILIALE),
                                                  NOME_FILIALE                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOME_FILIALE), 
                                                  ID_PREVENTIVO_CONFERMA        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_PREVENTIVO_CONFERMA),
                                                  NUMERO_CONFERMA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NUMERO_CONFERMA),
                                                  MESE_GENERAZIONE              : ArrayInfo[0].MESE_GENERAZIONE == 0? null : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].MESE_GENERAZIONE),
                                                  DATA_CREAZIONE                : ArrayInfo[0].DATA_CREAZIONE == null ? ' ' : 
                                                                                  TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ArrayInfo[0].DATA_CREAZIONE)),

                                                  ContoCorrente                 : {
                                                                                      IBAN              : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                                                      ID_CONTO_CORRENTE : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CONTO_CORRENTE),
                                                                                      BANCA             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA),
                                                                                      CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE != null? false : true,
                                                                                      NR_CONTO          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CONTO),
                                                                                      SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT),
                                                                                      BIC               : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC),
                                                                                  },
                                                  ModificaTabellaAllegati       : false,
                                                  ModificaTabellaVoci           : false,
                                                  NascondiIncrementaRevisione   : false,
                                                  DescrizioniAllegati           : [],
                                                  PosizioneAllegati             : [],
                                                }

                                    Self.CreateSnapshot();
                                    
                                    Self.CopiaDati = JSON.parse(JSON.stringify(Self))
                                    
                                    if(OnSuccess != undefined)
                                      OnSuccess()
                                  }
                                  else SystemInformation.HandleError('Preventivo eliminato')
                                }
                                else SystemInformation.HandleError('Impossibile ottenere il dettaglio del preventivo');
                              },
                              function(HTTPError,SubHTTPError,Response)
                              {
                                SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                              },
                              'SelectDettaglio')
    }

    GetFiltriAbilitati(OnSuccess)
    {
      var Anno = new Date(this.Dati.DATA)
      if(this.CreazioneConferma)
      {
        OnSuccess([{
                      Name : DASHBOARD_FILTER_TYPES.Clienti,
                      Positions : [
                                      this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                      this.Dati.ID_CLIENTE,
                                      ID_NODO_PREVENTIVI,
                                      Anno.getFullYear(),
                                      this.CreazioneConferma
                                  ]
                  }])  
      }
      else
      {
        OnSuccess([{
                      Name : DASHBOARD_FILTER_TYPES.Clienti,
                      Positions : [
                                      this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                      this.Dati.ID_CLIENTE,
                                      ID_NODO_PREVENTIVI_MULTI,
                                      Anno.getFullYear(),
                                      this.Chiave
                                  ]
                  }])        
      }
    }

    GetClassName()
    {
      return 'TSchedaPreventivoMultiparametrico';
    }

    GetImageIndex()
    {
      if(this.Dati.ID_PREVENTIVO_CONFERMA != -1)
        return 'PreventivoMultiApprovato2.png';
      return 'Preventivo.png';
    }

    DatiStampabili()
    {
        return true
    }

    Stampa(OnSuccess, ChiavePreventivoMulti = null)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaPreventivo', { Chiave : ChiavePreventivoMulti != null ? ChiavePreventivoMulti : this.Chiave },
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

    
    CaricaDatiCliente(ChiaveCliente, OnSuccess)
    {
      var Self = this
      SystemInformation.AdvQuery.GetSQL('Clienti', { CHIAVE : ChiaveCliente },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectCliente");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                                Self.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                                Self.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                                Self.Dati.NR_CIVICO_FATTURAZIONE   = ArrayInfo[0].NR_CIVICO_FATTURAZIONE;
                                                Self.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                                Self.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                                Self.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                                Self.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;
                                                Self.Dati.ESIGIBILITA_IVA          = ArrayInfo[0].ESIGIBILITA_IVA;
                                                Self.Dati.INDIRIZZO_DESTINAZIONE   = ArrayInfo[0].INDIRIZZO_SPEDIZIONE;
                                                Self.Dati.NR_CIVICO_DESTINAZIONE   = ArrayInfo[0].NR_CIVICO_SPEDIZIONE;
                                                Self.Dati.COMUNE_DESTINAZIONE      = ArrayInfo[0].COMUNE_SPEDIZIONE;
                                                Self.Dati.PROVINCIA_DESTINAZIONE   = ArrayInfo[0].PROVINCIA_SPEDIZIONE;
                                                Self.Dati.CAP_DESTINAZIONE         = ArrayInfo[0].CAP_SPEDIZIONE;
                                                Self.Dati.NOTE                     = ArrayInfo[0].NOTE_IN_FATTURA;
                                                Self.Dati.PEC                      = ArrayInfo[0].PEC
                                                Self.Dati.ID_ZONA_SPEDIZIONE       = ArrayInfo[0].ZONA_SPEDIZIONE
                                                Self.Dati.COD_ENTE_SDI             = ArrayInfo[0].COD_ENTE_SDI
                                                Self.Dati.ENTE_PUBBLICO            = ArrayInfo[0].ENTE_PUBBLICO == 'T'? true : false
                                                Self.Dati.COD_UFFICIO_DEST         = ArrayInfo[0].COD_UFFICIO_DEST
                                                Self.Dati.NAZIONE_DESTINAZIONE     = ArrayInfo[0].NAZIONE_SPEDIZIONE
                                                Self.Dati.NAZIONE_EM_PIVA          = ArrayInfo[0].NAZIONE_EM_PIVA
                                                Self.Dati.ContoCorrente = {
                                                                            IBAN              : ArrayInfo[0].IBAN,
                                                                            ID_CONTO_CORRENTE : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].ID_CONTO_CORRENTE : -1,
                                                                            BANCA             : ArrayInfo[0].BANCA_APPOGGIO,
                                                                            CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE == null? true : false,
                                                                            NR_CONTO          : ArrayInfo[0].NR_CONTO,
                                                                            BIC               : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].BIC_CONTO   : ArrayInfo[0].BIC,
                                                                            SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].SWIFT_CONTO : ArrayInfo[0].SWIFT,
                                                                          } 
                                                Self.Dati.DESTINAZIONE             = ArrayInfo[0].PRESSO;
                                                Self.Dati.COND_PAGAMENTO           = ArrayInfo[0].COND_PAGAMENTO
                                                if(ArrayInfo[0].PROVINCIA_FATTURAZIONE == null || ArrayInfo[0].PROVINCIA_FATTURAZIONE == undefined)
                                                    Self.Dati.PROVINCIA_FATTURAZIONE = -1
                                                else Self.Dati.PROVINCIA_FATTURAZIONE  = parseInt(ArrayInfo[0].PROVINCIA_FATTURAZIONE);

                                                Self.SchedaVociPreventivo.SetDatiCliente(ArrayInfo[0].IVA_SUGGERITA_CLIENTE / 10,
                                                                                                                 ArrayInfo[0].RITENUTA / 10,
                                                                                                                 ArrayInfo[0].NATURA_PAGAMENTO)  
                                                Self.SchedaVociPreventivo.InserisciUnaNuovaSezione()
                                                
                                                if(OnSuccess != undefined)
                                                  OnSuccess()                                              
                                              }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectCliente')
    }

    Invia(OggettoEmail, OnSuccess)
    {
      let Parametri        = OggettoEmail
      Parametri.Chiave     = this.Chiave
      Parametri.InvioEmail = true
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaPreventivo', Parametri,
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

    GestionePopupAllegati(OggettoEmail, OnSuccess)
    {
      for(let i = 0; i < this.SchedaAllegati.LsAllegati.length; i++)
      {
        if(this.SchedaAllegati.LsAllegati[i].Selezionato)
        {
          this.SchedaAllegati.LsAllegati[i].Selezionato = false
        }
      }

      if(this.SchedaAllegati.LsAllegati.length != 0)
      { 
        this.ScegliAllegato = true
        
        this.OggettoEmail   = OggettoEmail
        this.OnSuccessGestionePopup = OnSuccess
      }
      else 
      {
        this.Invia(OggettoEmail, OnSuccess)      
      }
    }

    DatiInviabili()
    {
      return true
    }

    RecuperaEmailCliente(OnSuccess)
    {
      var Self = this;
      this.ListaEmailCliente = ''
      this.ListaEmailAmm     = ''
      SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici', { CHIAVE : this.Chiave },
                                          function(Results)
                                          {
                                            let ArrayEmailCliente = SystemInformation.AdvQuery.FindResults(Results,"ListaEmailCliente");
                                            if(ArrayEmailCliente != undefined)
                                            {
                                              if(ArrayEmailCliente.length != 0)
                                                ArrayEmailCliente.forEach(function(Email)
                                                {
                                                  if(Email.EMAIL_CLIENTI)
                                                    Self.ListaEmailCliente += Email.EMAIL_CLIENTI + '; '
                                                })
                                              Self.ListaEmailCliente = Self.ListaEmailCliente.substring(0, Self.ListaEmailCliente.length - 2)
                                              OnSuccess()
                                            }
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                          'ListaEmailPreventivo')
    }

    ApriPopupGeneraFattura()
    {
      this.PopupSchedaPreventivo = true
    }
    ConfermaGenerazione()
    {
       this.AnnullaGenerazione();
       this.__TriggerOnPreventivoConfermato++;
       return;      
    }

    AnnullaGenerazione()
    {
      this.PopupSchedaPreventivo = false
    }

    ConservaFlagMailInviata(OnSuccess, OnError)
    {
      var Self = this
      var ObjQuery = { Operazioni : [{
                                        Query     : "UpdateConservaIsInviato",
                                        Parametri : {
                                                      CHIAVE : Self.Chiave
                                                    }
                                    }] 
                     };

      this.AdvQuery.PostSQL('PreventiviMultiparametrici',ObjQuery,function()
      {
        Self.Disponi(OnSuccess)
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
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
    VUEVociPreventivoMultiparametrico,
    VUEInputEsigibilitaIVA,
    VUEInputCausali,
    VUEInputContoRibaCorrente,
    // VUELogDocumentiEconomici,
    VUEInputNazioni,
    VUEInputZone,
    VUEModal,
    VUEConfirm,
    VUEModalInvioEmail,
},
   data()
   {
     return { 
              TotalePreventivo                   : 0,
              Stato                              : '', 
              DeveloperMode                      : SystemInformation.DeveloperMode,
              StatoItaliano                      : SystemInformation.Configurazioni.StatoItaliano,
              DocumentoCorrelato                 : DOCUMENTO_CORRELATO,
              LsDocumentoCorrelato               : SystemInformation.GetLsTipiDocumentoCorrelato(),
              PopupInviaEmail                    : false,
              PopupGenerazioneConfermaDOrdine    : false,
              PopupIncrementaRevisione           : false,
              PopupRevisione                     : false,
              TestoPopupRevisione                : '',
              TestoPopupVisualizzaRevisione      : '',
              PopupVisualizzaRevisione           : false,
              ConteggioSezioniPreventivo         : 0,
              OggettoPerPopupGenerazioneConferma : null,
              ListaSoluzioni                     : [],
              ListaRevisioni                     : [],
              SchedaPreventivoCreataDaConferma   : null,
              RevisioneScelta                    : null,
              RevisioneSelezionata               : null,
              NomeProgramma                      : NOME_PROGRAMMA,
              OggettoEmail                       : {
                                                    CorpoEmail   : '',
                                                    Cc           : '',
                                                    Ccn          : '',
                                                    Destinatario : '',
                                                    Oggetto      : ''
                                                  },
              Tabs                              : {
                                                      ActiveTab : 'Preventivo',
                                                      Tabs      : [
                                                                    {
                                                                      Caption : 'Preventivo',
                                                                      Id      : 'Preventivo'
                                                                    },
                                                                    {
                                                                      Caption : 'Voci Preventivo',
                                                                      Id      : 'VociPreventivo'
                                                                    },
                                                                    {
                                                                      Caption: 'Allegati',
                                                                      Id     : 'Allegati'
                                                                    },
                                                                    // {
                                                                    //   Caption : 'Variazioni',
                                                                    //   Id      : 'LogPreventivo' 
                                                                    // },
                                                                  ]
                                                  }
                  }
   },
   emits : ['onPreventivoConfermato'],
   props : ['SchedaPreventivo', 'IsPaginaGestioneAnomalie', 'IsReadOnly', 'VisualizzazioneConfermaPreventivo'],
   computed :
   {
    CurrentIsPaginaGestioneAnomalie : 
    {
      get()
      {
        if(this.IsPaginaGestioneAnomalie != undefined)
          return this.IsPaginaGestioneAnomalie;
        return false
      }
    },

    CurrentCliente()
    {
      return this.SchedaPreventivo.Dati.ID_CLIENTE
    },

    TriggerOnPreventivoConfermato()
    {
      return this.SchedaPreventivo.__TriggerOnPreventivoConfermato
    },

    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaPreventivo.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },

    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaPreventivo.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },

    IsAnyAllegatoSelected()
    {
      return this.SchedaPreventivo.SchedaAllegati.LsAllegati.some(allegato => allegato.Selezionato)
    },

    MeseGenerazione :
     {
        get()
        {
          var Mese = this.SchedaPreventivo.GetMeseGenerazione()
          return Mese
        }
     },

   },
   watch:
   {
    CurrentCliente:
     {
        handler(NewValue,OldValue)
        {
          if(this.SchedaPreventivo.DatiModificati()) 
            if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
              this.SchedaPreventivo.CaricaDatiCliente(NewValue)
        }
     },

    RevisioneScelta :
     {
         handler()
         {
           this.OnChangeRevisione();
         }
     },

     TriggerOnPreventivoConfermato :
     {
        handler()
        {
          this.$emit('onPreventivoConfermato', this.SchedaPreventivoCreataDaConferma);
        }
     },
   },

   methods: 
   {
      OnFilialeSelected(FilialeSelezionata)
      {
        this.SchedaPreventivo.AssignDestinazioneFromFiliale(FilialeSelezionata);
      },

      OnAllegatiChange()
      {
        this.SchedaPreventivo.Dati.ModificaTabellaAllegati = this.SchedaPreventivo.SchedaAllegati.Modificato();
      },
      AnnullaInvia()
      {
        this.PopupInviaEmail = false
      },

      AnnullaSceltaAllegati()
      {
        this.SchedaPreventivo.ScegliAllegato = false
      },

      ConfermaInvia()
      {
        this.AnnullaInvia()
        var Self = this

        if(this.SchedaPreventivo.SchedaAllegati.LsAllegati.length != 0)
        {
          this.SchedaPreventivo.ScegliAllegato = true
          this.SchedaPreventivo.OggettoEmail = this.OggettoEmail
        }
        
        if(!this.SchedaPreventivo.ScegliAllegato)
        {
          this.SchedaPreventivo.Invia(this.OggettoEmail, function(Answer)
                                          {
                                            if(Answer.Risposta == 'MAIL_INVIATA')
                                            {
                                              Self.SchedaPreventivo.ConservaFlagMailInviata()
                                              alert('Invio mail effettuato con successo')
                                            }
                                            else 
                                            {
                                              if(Answer.ErroreMail != undefined)
                                                  alert('Errore invio mail')
                                              // if (Answer.MessaggioUtente)
                                              //     alert("L'account selezionato non esiste o non ha alcuna mail collegata")                   
                                            }
                                            Self.OggettoEmail.CorpoEmail   = ''
                                            Self.OggettoEmail.Cc           = ''
                                            Self.OggettoEmail.Ccn          = ''
                                            Self.OggettoEmail.Destinatario = ''
                                            Self.OggettoEmail.Oggetto      = ''
                                          })
        }
      },

      ConfermaInviaAllegato()
      {
        this.AnnullaInvia()
        
        if(this.CurrentIsPaginaGestioneAnomalie)
        {
          let Preventivo = this.SchedaPreventivo

          Preventivo.OggettoEmail.AllegatiMail = []

          if(Preventivo.SchedaAllegati.LsAllegati.length != 0 )
          {
            for(let i = 0; i < Preventivo.SchedaAllegati.LsAllegati.length; i++)
            {
              if(Preventivo.SchedaAllegati.LsAllegati[i].Selezionato)
              {
                Preventivo.OggettoEmail.AllegatiMail.push(Preventivo.SchedaAllegati.LsAllegati[i])
              }
            }
          }

          if(Preventivo.OnSuccessGestionePopup == undefined)
          {
            let Self = this
            Preventivo.OnSuccessGestionePopup = function(Answer)
                                                {
                                                  if(Answer.Risposta == 'MAIL_INVIATA')
                                                  {
                                                    Preventivo.ConservaFlagMailInviata()
                                                    alert('Invio mail effettuato con successo')
                                                  }
                                                  else 
                                                  {
                                                    if(Answer.ErroreMail != undefined)
                                                        alert('Errore invio mail')
                                                    // if (Answer.MessaggioUtente)
                                                    //     alert("L'account selezionato non esiste o non ha alcuna mail collegata")                   
                                                  }
                                                  Self.OggettoEmail.CorpoEmail   = ''
                                                  Self.OggettoEmail.Cc           = ''
                                                  Self.OggettoEmail.Ccn          = ''
                                                  Self.OggettoEmail.Destinatario = ''
                                                  Self.OggettoEmail.Oggetto      = ''
                                                  Self.SchedaPreventivo.OggettoEmail = null;
                                                }
          }

          Preventivo.Invia(Preventivo.OggettoEmail, Preventivo.OnSuccessGestionePopup)
          Preventivo.ScegliAllegato = false

        }
        else
        {
          this.SchedaPreventivo.OggettoEmail.AllegatiMail = []
          
          if(this.SchedaPreventivo.SchedaAllegati.LsAllegati.length != 0 )
          {
            for(let i = 0; i < this.SchedaPreventivo.SchedaAllegati.LsAllegati.length; i++)
            {
              if(this.SchedaPreventivo.SchedaAllegati.LsAllegati[i].Selezionato)
              {
                this.SchedaPreventivo.OggettoEmail.AllegatiMail.push(this.SchedaPreventivo.SchedaAllegati.LsAllegati[i])
              }
            }
          }

          this.SchedaPreventivo.Invia(this.SchedaPreventivo.OggettoEmail, this.SchedaPreventivo.OnSuccessGestionePopup)
          this.SchedaPreventivo.ScegliAllegato = false

        }
      },

      OnClickSelezionaTuttiAllegati()
      {
        if (this.SchedaPreventivo.SchedaAllegati?.LsAllegati)
        {
          if (this.IsAnyAllegatoSelected)
            this.SchedaPreventivo.SchedaAllegati.LsAllegati.forEach(allegato => {allegato.Selezionato = false})
          
          else this.SchedaPreventivo.SchedaAllegati.LsAllegati.forEach(allegato => {allegato.Selezionato = true})
        }
      }, 

      OnClickInvioEmail()
      {
        var Self  = this
        this.OggettoEmail.Destinatario = ''
        this.OggettoEmail.Oggetto      = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_PREVENTIVI_DA_ANOMALIE
        this.OggettoEmail.Cc           = ''
        this.OggettoEmail.Ccn          = ''
        this.OggettoEmail.CorpoEmail   = SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE

        if(SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE.includes('[TOKEN_INDIRIZZO_FILIALE]'))
          this.OggettoEmail.CorpoEmail   = SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE.replace('[TOKEN_INDIRIZZO_FILIALE]', this.SchedaPreventivo.Dati.INDIRIZZO_DESTINAZIONE + " " + this.SchedaPreventivo.Dati.NR_CIVICO_DESTINAZIONE + ", " + this.SchedaPreventivo.Dati.COMUNE_DESTINAZIONE +" (" +  this.SchedaPreventivo.Dati.CAP_DESTINAZIONE + ") ")
        else
          this.OggettoEmail.CorpoEmail   = SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE

        this.SchedaPreventivo.RecuperaEmailCliente(function()
        {
          Self.OggettoEmail.Destinatario = Self.SchedaPreventivo.ListaEmailCliente

          if(Self.SchedaPreventivo.ListaEmailCliente == '' && Self.SchedaPreventivo.ListaEmailAmm != '')
            Self.OggettoEmail.Destinatario = Self.SchedaPreventivo.ListaEmailAmm

          Self.PopupInviaEmail = true
        })
      },

      OnClickStampa()
      {
        var Self = this
        this.SchedaPreventivo.Stampa(
                                            function(BodyPDF)
                                            {
                                              if(BodyPDF != undefined)
                                              {
                                                var routeData = Self.$router.resolve({ name   : "IFrameStampe" });
                                                var AWindow = window.open(routeData.href, "_blank");
                                                AWindow.BodyPDF = BodyPDF;
                                              }
                                            }
                                            )
      },

      OnClickNessunDocumento()
      {
          if(this.SchedaPreventivo.Dati.DOCUMENTO_CORRELATO == DOCUMENTO_CORRELATO.NessunDocumento)
          {
            this.SchedaPreventivo.Dati.CIG                 = ''
            this.SchedaPreventivo.Dati.CUP                 = ''
            this.SchedaPreventivo.Dati.CONVENZIONE         = ''
            this.SchedaPreventivo.Dati.DOCUMENTO_NR        = ''
            this.SchedaPreventivo.Dati.VOCE_DI_RIFERIMENTO = ''
            this.SchedaPreventivo.Dati.DATA_DOCUMENTO      = ''
          }  
      },

      OnSezioniPreventivoChange()
      {
        this.SchedaPreventivo.Dati.ModificaTabellaVoci = this.SchedaPreventivo.SchedaVociPreventivo.Modificato();
      },

      OnClickCopiaDaPartitaIva()
      {
        this.SchedaPreventivo.Dati.CODICE_FISCALE = this.SchedaPreventivo.Dati.PARTITA_IVA
      },
      
      VerificaSeDisabilitare()
      {
        return this.SchedaPreventivo.Dati.ID_PREVENTIVO_CONFERMA != -1 || this.IsReadOnly
      },

      ConfermaIncrementaRevisione()
      {
        this.SchedaPreventivo.Dati.NUMERO_REVISIONE++
        this.SchedaPreventivo.Dati.IS_INVIATO = false
        this.SchedaPreventivo.Dati.DATA = TZDateFunct.DateInHTMLInputFormat(new Date())
        this.SchedaPreventivo.Dati.NascondiIncrementaRevisione = true
        this.PopupIncrementaRevisione = false
      },

      OnClickIncrementaRevisione()
      {
        this.PopupIncrementaRevisione = true;
      },

      AnnullaIncrementaRevisione()
      {
        this.PopupIncrementaRevisione = false;
      },

      OnChangeRevisione(OnSuccess)
      {
        this.RevisioneSelezionata.Nuovo();
        this.RevisioneSelezionata.Chiave = this.RevisioneScelta;
        this.RevisioneSelezionata.Disponi(OnSuccess);
      },

      OnClickConfermaPreventivoMultiParametrico()
      {
        let ObjDatiNodo           = JSON.parse(this.SchedaPreventivo.Snapshot)
        this.RevisioneScelta      = this.SchedaPreventivo.Chiave
        this.RevisioneSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
        let Self = this
        this.OnChangeRevisione(function()
        {
          Self.CaricaRevisioni(ObjDatiNodo.NUMERO, ObjDatiNodo.ANNO)      
        })
      },

      ConfermaPreventivoMultiParametrico()
      {
        this.PopupRevisione = false
        this.ListaSoluzioni = []
        this.SchedaPreventivo.LsSoluzioniAccettate = []
        this.HandleSoluzioni()
      },

      CaricaRevisioni(NumeroPreventivo, AnnoPreventivo)
      {
        let Self = this
        SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',{ NUMERO : NumeroPreventivo, ANNO : AnnoPreventivo },
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectRevisione");
                                          if(ArrayInfo != undefined)
                                          {  
                                            if(ArrayInfo.length > 1)
                                            {
                                              Self.TestoPopupRevisione = 'Scegli la revisione che desideri confermare'
                                              Self.PopupRevisione = true
                                              Self.ListaRevisioni = []
                                              for (let i = 0; i < ArrayInfo.length; i++) 
                                              {
                                                let Preventivo = ArrayInfo[i]
                                                Self.ListaRevisioni.push(
                                                                          {
                                                                            Chiave          : Preventivo.CHIAVE,
                                                                            NumeroRevisione : Preventivo.NUMERO_REVISIONE ? Preventivo.NUMERO_REVISIONE : -1,
                                                                          }
                                                                        )
                                              }
                                            }
                                            else 
                                            {
                                              Self.ConfermaPreventivoMultiParametrico()
                                            }
                                          }
                                          else console.error('Impossibile ottenere la lista delle revisioni');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        }, 
                                        'SelectRevisione')
      },

      OnClickVisualizzaRevisioni()
      {
        let ObjDatiNodo           = JSON.parse(this.SchedaPreventivo.Snapshot)
        this.RevisioneScelta      = this.SchedaPreventivo.Chiave
        this.RevisioneSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
        let Self = this
        this.OnChangeRevisione(function()
        {
          Self.VisualizzaRevisioni(ObjDatiNodo.NUMERO, ObjDatiNodo.ANNO)      
        })
      },

      VisualizzaRevisioni(NumeroPreventivo, AnnoPreventivo)
      {
        let Self = this
        SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',{ NUMERO : NumeroPreventivo, ANNO : AnnoPreventivo },
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectRevisione");
                                          if(ArrayInfo != undefined)
                                          {  
                                              Self.ListaRevisioni = []
                                              for (let i = 0; i < ArrayInfo.length; i++) 
                                              {
                                                let Preventivo = ArrayInfo[i]
                                                Self.ListaRevisioni.push(
                                                                          {
                                                                            Chiave          : Preventivo.CHIAVE,
                                                                            NumeroRevisione : Preventivo.NUMERO_REVISIONE ? Preventivo.NUMERO_REVISIONE : -1,
                                                                          }
                                                                        )
                                              }
                                              Self.TestoPopupVisualizzaRevisione = 'Revisioni presenti'
                                              Self.PopupVisualizzaRevisione      = true
                                          }
                                          else console.error('Impossibile ottenere la lista delle revisioni');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        }, 
                                        'SelectRevisione')
      },

      OnClickAnnullaGenerazioneConfermaDOrdine()
      {
        this.PopupGenerazioneConfermaDOrdine = false;
      },

      HandleSoluzioni()
      {
        if (this.RevisioneSelezionata.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico.length == 1 && this.RevisioneSelezionata.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni.length == 1)
        {
         this.SchedaPreventivo.LsSoluzioniAccettate.push(this.RevisioneSelezionata.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni[0]);
         this.TutteLeScelteEffettuate()
        }
        else 
        {
         if( this.ConteggioSezioniPreventivo < this.RevisioneSelezionata.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico.length )
          this.VisualizzaPopup(this.RevisioneSelezionata.SchedaVociPreventivo.LsSezioniPreventivoMultiparametrico[this.ConteggioSezioniPreventivo])
         else this.TutteLeScelteEffettuate()
        }
      },

      VisualizzaPopup(OggettoSceltaSoluzione)
      {
        this.OggettoPerPopupGenerazioneConferma = OggettoSceltaSoluzione
        this.PopupGenerazioneConfermaDOrdine    = true
      },

      TutteLeScelteEffettuate()
      {
        this.SchedaPreventivo.Dati.NUMERO_REVISIONE = this.RevisioneSelezionata.Dati.NUMERO_REVISIONE
        this.SchedaPreventivo.Chiave                = this.RevisioneScelta
        this.SchedaPreventivo.PRIMO_PREVENTIVO      = this.RevisioneSelezionata.PRIMO_PREVENTIVO
        this.$emit('onPreventivoConfermato', this.SchedaPreventivo);
      },

      CollegaPreventivoAllaConferma(ChiavePreventivo, OnSuccess)
      {
        var ObjQuery = { Operazioni : [
                                        {
                                          Query     : "CollegaConfermaAlPreventivo",
                                          Parametri : { 
                                                        CHIAVE        : this.SchedaPreventivo.Chiave,
                                                        ID_PREVENTIVO : ChiavePreventivo
                                                      }
                                        }
                                        ]}
        SystemInformation.AdvQuery.PostSQL('PreventiviMultiparametrici', ObjQuery, function()
        {
          if(OnSuccess)
            OnSuccess()
        },
        SystemInformation.HandleError);
      },

      OnClickConfermaSceltaPopup(Soluzione)
      {
        this.SchedaPreventivo.LsSoluzioniAccettate.push(Soluzione)
        this.ConteggioSezioniPreventivo++
        this.HandleSoluzioni()
      }
   },
   
  beforeMount() 
  {
    this.ActiveTab = 'Preventivo'
  }

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>