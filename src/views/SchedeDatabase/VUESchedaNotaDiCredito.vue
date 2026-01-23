<template>
 <VUEConfirm :Popup="PopupNotaDiCredito" :Richiesta="'Vuoi numerare la nota di credito?'" @onClickConfermaPopup="ConfermaInvioNota" @onClickChiudiPopup="AnnullaInvioNota">
 </VUEConfirm>
 <VUEConfirm :Popup="PopupCorreggiNota" :Richiesta="'Vuoi correggere la nota di credito?'" @onClickConfermaPopup="ConfermaCorrezione" @onClickChiudiPopup="AnnullaPopup">
 </VUEConfirm>
  <VUEModal v-if="PopupScegliFileXML" :Titolo="'Lista file .xml'" :Altezza="'200px'" :Larghezza="'600px'"
            @onClickChiudiModal="OnClickChiudiPopupScegliFile">
        <template v-slot:Body>
              <div class="row wrapper" style="height:100%">
                <section class="panel panel-default" style="background-color:white;">
                  <div class="table-responsive" style="width:100%;height: 60%;">
                    <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                      <thead>
                        <tr>
                          <th style="width:7%"></th>
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
 <div class="ZMCorpoSchedeDati">
  <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in TabsVisibili" :Key="ATab.Id"
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
                <img v-if="ATab.Id == 'Nota di credito' && SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" src="@/assets/images/IconeAlbero/NotaDiCreditoInviata2.png" style="width:35px;height:35px;float:left;margin-top:-9px">  
                <img v-if="ATab.Id == 'Nota di credito' && !SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" src="@/assets/images/IconeAlbero/NotaDiCredito2.png" style="width:35px;height:35px;float:left;margin-top:-9px">  
             </a>
         </li>
      </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'Nota di credito'">
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;margin-left:15px;">
          <input class="form-control" style="width: 18px; height: auto; margin-top: 10px;float:left" type="checkbox" v-model="SchedaNotaDiCredito.Dati.INVIATA_TRAMITE_EMAIL"/>
          <text style="width: 150px; font-weight: bold; margin-left: 15px;float:left;margin-top:6px">Inviata tramite email</text>
        </div>
        <div style="float:right;margin-left:10px;margin-right:20px">
            <text style="font-weight: bold;">Data</text>
            <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="date" id="input-data" class="form-control" v-model="SchedaNotaDiCredito.Dati.DATA"/>
            <label v-if="SchedaNotaDiCredito.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div v-if="SchedaNotaDiCredito.Dati.NUMERO != 0" style="float:right;width:13%">
            <text style="float:left;font-weight: bold;padding-right: 10%">Nota di credito N.</text>
            <label style="float:left;font-size:15px;width:151px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaNotaDiCredito.Dati.NUMERO }}/{{ SchedaNotaDiCredito.Dati.ANNO }}</label>
        </div>
        <div v-else-if="!SchedaNotaDiCredito.DatiModificati()" style="float:right;margin-right:50px">
          <button style="margin-top:20px;width:115%" class="btn btn-sm btn-info" @click="OnClickNumera">Numera nota di credito</button>
        </div>
        <div v-else style="float:right;margin-right:10px;padding-top:24px;">
          <text style="font-weight: bold;">Avviso nota di credito</text>
        </div>  

        <div v-if="SchedaNotaDiCredito.Dati.ID_FATTURA_CREAZIONE != undefined && SchedaNotaDiCredito.Dati.ID_FATTURA_CREAZIONE != -1" style="float:right;width:13%;margin-left:10px;margin-right:10px">
          <text style="float:left;font-weight: bold;padding-right: 10%;">Generata dalla fattura</text>
          <label v-if="SchedaNotaDiCredito.Dati.NUMERO_FATTURA != -1 && SchedaNotaDiCredito.Dati.NUMERO_FATTURA != null" class="ZMLabel" style="font-size:15px;height:34px;padding-top:6px;padding-left: 14px;">{{ SchedaNotaDiCredito.Dati.NUMERO_FATTURA }}/{{ SchedaNotaDiCredito.Dati.ANNO_FATTURA}}</label>
          <label v-else class="ZMLabel" style="font-size:15px;height:34px;padding-top:6px;padding-left: 14px;">Avviso fatt. {{ SchedaNotaDiCredito.Dati.ID_FATTURA_CREAZIONE }}</label>
        </div>         
      </div>
      <div style="clear:both;padding-bottom:1%"></div>
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Intestatario</text>
        <hr style="margin-top:5px">
      </div>
      <div class="col-md-4" v-if="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" style="border:1px solid #b3dbff">
            <img src="@/assets/images/IconeAlbero/Inviata2.png" style="margin-top:6px;float:left;width:20%">
            <text style="float:left;margin-top:2%;font-weight: bold;font-size:15px;width:80%">NOTA TRASMESSA ALLO SDI</text>
      </div>
      <div class="col-md-4" v-if="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickScaricaFileNota">Scarica file xml</button>
      </div>
      <div class="col-md-4" v-if="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickCorreggiNota">Correggi nota</button>
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:50%">
          <text style="font-weight: bold; float:left; width:100%">Cliente
            <div style="float:right">
              <label>Ente pubblico&nbsp;</label>
            </div>
            <div style="float:right;margin-right:4px">
              <input :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="checkbox" v-model="SchedaNotaDiCredito.Dati.ENTE_PUBBLICO"/>
            </div>
          </text>
          <VUEInputClienti v-model="SchedaNotaDiCredito.Dati.ID_CLIENTE" 
                           @onUpdate="newValue => SchedaNotaDiCredito.Dati.ID_CLIENTE = newValue"
                           :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"/>
        </div>
        <div style="float:left;width:1%">&nbsp;</div> 
        <div style="float:left;width:49%">
          <text style="font-weight: bold; float:left; width:100%">Ragione Sociale </text>
          <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.RAGIONE_SOCIALE" type="text" class="form-control" placeholder="Ragione Sociale"/>
          <label v-if="SchedaNotaDiCredito.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>

      <div class="ZMNuovaRigaScheda">
       <div style="float:left;width:30%">
        <text style="font-weight: bold">Indirizzo</text>
        <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"  v-model="SchedaNotaDiCredito.Dati.INDIRIZZO_FATTURAZIONE" type="text" class="form-control" placeholder="Indirizzo">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"  type="text" class="form-control" maxlength="7" v-model="SchedaNotaDiCredito.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"  placeholder="CAP" v-model="SchedaNotaDiCredito.Dati.CAP_FATTURAZIONE"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:26%">
        <text style="font-weight: bold">Comune</text>
        <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"  v-model="SchedaNotaDiCredito.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:20%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
       </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:11%">
          <text style="font-weight: bold">Partita IVA</text>
          <VUEInputPartitaIVA :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.PARTITA_IVA" placeholder="Partita IVA"></VUEInputPartitaIVA>
          <label v-if="SchedaNotaDiCredito.Dati.PARTITA_IVA.trim() == '' && SchedaNotaDiCredito.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaNotaDiCredito.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:16%">
          <text style="font-weight: bold;float:left;width:100%">Codice Fiscale</text>
          <VUEInputCodiceFiscale :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.CODICE_FISCALE" style="width:100%;margin-right:10px; float:left" placeholder="Codice Fiscale"></VUEInputCodiceFiscale>
          <label v-if="SchedaNotaDiCredito.Dati.PARTITA_IVA.trim() == '' && SchedaNotaDiCredito.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaNotaDiCredito.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:8%">
            <label style="font-weight: bold;">Codice SDI</label>
            <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaNotaDiCredito.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
            <label v-if="SchedaNotaDiCredito.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
              {{(SchedaNotaDiCredito.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
            </label>
        </div>

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:9%">
            <label style="font-weight: bold;">Cod. uff. dest.</label>
            <input type="text" class="form-control" v-model="SchedaNotaDiCredito.Dati.COD_UFFICIO_DEST"
                    placeholder="Codice ufficio destinazione"
                    :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" 
                    maxlength="6"/>
            <label v-if="SchedaNotaDiCredito.Dati.COD_UFFICIO_DEST.length != 6 && SchedaNotaDiCredito.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
            </label>
        </div> 

        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:14%">
            <label style="font-weight: bold;">Nazione emittente</label>
            <VUEInputNazioni :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.NAZIONE_EM_PIVA" emptyElement="false"/>
            <label v-if="SchedaNotaDiCredito.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:26%">
          <label style="font-weight: bold;">PEC</label>
          <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"  type="email" class="form-control" v-model="SchedaNotaDiCredito.Dati.PEC" placeholder="PEC"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>
<div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold">Indirizzo di Destinazione</text>
        <hr style="margin-top:1px">
      </div>
      <div class="ZMNuovaRigaScheda" style="margin-top:-18px">
        <div style="float:left;width:50%">
          <text style="font-weight: bold">Destinazione</text>
          <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.DESTINAZIONE" type="text" class="form-control" placeholder="Destinazione">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <text style="font-weight: bold">Indirizzo</text>
          <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.INDIRIZZO_DESTINAZIONE" type="text" class="form-control" placeholder="Indirizzo">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:9%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" maxlength="7" v-model="SchedaNotaDiCredito.Dati.NR_CIVICO_DESTINAZIONE" placeholder="Nr. civico"/>
        </div> 
      </div>
       
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" placeholder="CAP" v-model="SchedaNotaDiCredito.Dati.CAP_DESTINAZIONE"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <text style="font-weight: bold">Comune</text>
          <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.COMUNE_DESTINAZIONE" type="text" class="form-control" placeholder="Comune">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.PROVINCIA_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <label style="font-weight: bold;">Nazione</label>
          <VUEInputNazioni :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.NAZIONE_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>
   </div>

   <div  v-if="Tabs.ActiveTab == 'VociNota'" style="width:100%;">
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Info contabili</text>
        <hr style="margin-top:1px">
      </div>
      <div class="ZMNuovaRigaScheda" style="margin-top:-20px">
        <div style="float:left;width:20%">
          <label style="font-weight: bold;">Cond. Pagamento</label>
          <VUEInputCondPagamenti :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.COND_PAGAMENTO"/>
          <label v-if="SchedaNotaDiCredito.Dati.COND_PAGAMENTO == null" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Causale</text>
          <VUEInputCausali :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.ID_CAUSALE"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Esigibilità IVA</text>
          <VUEInputEsigibilitaIVA :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" @change="OnEsigibilitaChange" v-model="SchedaNotaDiCredito.Dati.ESIGIBILITA_IVA"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Note</text>
          <input type="text" :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" class="form-control" v-model="SchedaNotaDiCredito.Dati.NOTE">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <text style="font-weight: bold">Ordine</text>
          <select style="float:left;width:100%;margin-right:5px" class="form-control" 
                  :disabled="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" 
                  @change="OnClickNessunDocumento()" 
                  v-model="SchedaNotaDiCredito.Dati.DOCUMENTO_CORRELATO">
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

      <div v-if="SchedaNotaDiCredito.Dati.DOCUMENTO_CORRELATO != DocumentoCorrelato.NessunDocumento"> 
        <div class="ZMSeparatoreScheda">       
          <text style="font-weight: bold;margin-bottom:-20px">Ordine</text>
          <hr style="margin-top:1px; margin-bottom: -5px">
        </div>
        <div class="ZMNuovaRigaScheda" style="padding-top:3px" >
          <div style="float:left;width:79%">
            <text style="font-weight: bold">Documento nr.</text>
            <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.DOCUMENTO_NR" type="text" class="form-control" placeholder="Documento nr.">
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:20%">
              <label style="font-weight: bold;">Data del documento</label>
              <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="date" class="form-control" v-model="SchedaNotaDiCredito.Dati.DATA_DOCUMENTO"/>
          </div>
        </div>
        <div class="ZMNuovaRigaScheda" >
          <div style="float:left;width:79%">
            <text style="font-weight: bold">Voce di riferimento</text>
            <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.VOCE_DI_RIFERIMENTO" type="text" class="form-control" placeholder="Voce di riferimento">
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:20%">
              <label style="font-weight: bold;">CIG</label>
              <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaNotaDiCredito.Dati.CIG" placeholder="CIG"/>
          </div>
        </div>
        <div class="ZMNuovaRigaScheda" >
          <div style="float:left;width:79%">
            <text style="font-weight: bold">Convenzione</text>
            <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" v-model="SchedaNotaDiCredito.Dati.CONVENZIONE" type="text" class="form-control" placeholder="Convenzione">
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:20%">
              <label style="font-weight: bold;">CUP</label>
              <input :readonly="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaNotaDiCredito.Dati.CUP" placeholder="CUP"/>
          </div>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Conto corrente / Riba</text>
        <hr style="margin-top:1px;margin-bottom:-0px">
      </div>
      <div class="ZMNuovaRigaScheda">
        <VUEInputContoRibaCorrente :InviataAlloSdi="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI" :ContoCorrente="SchedaNotaDiCredito.Dati.ContoCorrente"/>
      </div>
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;margin-bottom:-20px">Voci fattura</text>
        <hr style="margin-top:1px">
      </div>
      <div v-if="SchedaNotaDiCredito.Dati.ID_CLIENTE != -1">
        <VUEVociDocumentiEconomici :SchedaVociDocumentiEconomici="SchedaNotaDiCredito.SchedaVociNotaDiCredito"
                                   :TastoCreaNotaVisibile="false"
                                   :InviataAlloSdi="SchedaNotaDiCredito.Dati.INVIATA_ALLO_SDI"
                                   :IdCliente="SchedaNotaDiCredito.Dati.ID_CLIENTE"
                                   @onChange="OnVociNotaDiCreditoChange"/>    
      </div>
      <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE NELLA NOTA DI CREDITO</div>                                 
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>

    <div  v-if="Tabs.ActiveTab == 'LogNotaDiCredito'" style="height:50%">
        <VUELogDocumentiEconomici :SchedaLogDocumentiEconomici="SchedaNotaDiCredito.SchedaLogNotaDiCredito"/>
    </div>
    
   <div v-if="Tabs.ActiveTab == 'Rata'">
    <div class="ZMNuovaRigaScheda">
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Totali</text>
        <hr style="margin-top:5px;margin-bottom:0px">
      </div>
      <div class="col-md-4" style="float:right; width:38%; margin-right:20px">
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Imponibile </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.SommaImponibile) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Totale IVA </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.SommaIva) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a  style="float:left">Totale Ivato </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;"> {{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleIvato) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda">
          <a style="float:left">Ritenuta </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleRitenuta) }}</label>
        </div>
        <div class="ZMNuovaRigaScheda" v-if="SchedaNotaDiCredito.SchedaVociNotaDiCredito.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDaNoi || SchedaNotaDiCredito.SchedaVociNotaDiCredito.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDalCliente">
          <a style="float:left;margin-top:5px">Costo bollo </a>
            <input type="number" style="width:70%; float:right; text-align: right;" class="form-control" 
                    v-model="SchedaNotaDiCredito.SchedaVociNotaDiCredito.Dati.CostoBollo"
                    :readonly="true"
                    step="0.01">
        </div>
        <div class="ZMNuovaRigaScheda">
          <a v-if="!SchedaNotaDiCredito.SchedaVociNotaDiCredito.SplitPayment" style="float:left">Netto a pagare </a>
          <a v-else style="float:left">Totale </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleFattura) }}</label>
          <label v-if="SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleFattura < 0" class="ZMFormLabelError" style="float:right; text-align: right; font-size: 12px;">Il totale dev'essere positivo</label>
        </div>
        <div class="ZMNuovaRigaScheda" v-if="SchedaNotaDiCredito.SchedaVociNotaDiCredito.SplitPayment">
          <a style="float:left">Netto a pagare </a>
          <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleSplitPayment) }}</label>
        </div>
      </div>
    </div>
    <div class="ZMSeparatoreFiltri">&nbsp;</div>
    <div class="ZMSeparatoreFiltri">&nbsp;</div>
    

    <div class="ZMNuovaRigaScheda" style="margin-top:-20px">
      <VUERateNote :SchedaRateNota ="SchedaNotaDiCredito.SchedaRateNota"
                   :ID_CLIENTE ="SchedaNotaDiCredito.Dati.ID_CLIENTE"
                    @onChange="OnRateNotaChange()"/>
    </div>
    
    <!-- <div class="ZMSeparatoreScheda">       
      <text style="font-weight: bold;margin-bottom:-20px">Pagamento</text>
      <hr style="margin-top:1px">
    </div> -->
    <!-- <div class="ZMNuovaRigaScheda" style="margin-top:-20px">
      <div style="float:left;width:12%">
        <text style="font-weight: bold;">Data pagamento</text>
        <input type="date" id="input-data" class="form-control" v-model="SchedaNotaDiCredito.Dati.DATA_PAGAMENTO"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:25%">
        <label style="font-weight: bold;">Conto corrente</label>
        <select class="form-control" 
                v-model="SchedaNotaDiCredito.Dati.ID_CONTO_PAGAMENTO"
                style="cursor:default">
        <option v-for="SelectOption in ContiCasse" 
                :Key="SelectOption.CHIAVE"
                :value="SelectOption.CHIAVE"
                :selected="SelectOption.CHIAVE == SchedaNotaDiCredito.Dati.ID_CONTO_PAGAMENTO">
                {{SelectOption.DESCRIZIONE}}
        </option>
        </select>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:50%">
        <text style="font-weight: bold">Note pagamento</text>
        <input type="text" class="form-control" v-model="SchedaNotaDiCredito.Dati.NOTE_PAGAMENTO">
      </div>

      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:10%;text-align:center;">
        <text style="font-weight: bold">No prima nota</text>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
        <input type="checkbox" v-model="SchedaNotaDiCredito.Dati.NO_PRIMA_NOTA"/>
      </div>

    </div> -->
    <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>


   <div  v-if="Tabs.ActiveTab == 'Allegati'">
    <VUEAllegati :SchedaAllegati="SchedaNotaDiCredito.SchedaAllegati" 
                  NomeCampoModello="NoteDiCredito"
                 @onChange="OnAllegatiChange" />
  </div>
</div>
</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, FATT_ELE_ESIGIBILITA_IVA, DASHBOARD_FILTER_TYPES, DOCUMENTO_CORRELATO, RUOLI, PAGAMENTO_BOLLO } from '@/SystemInformation.js'
import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
import VUEVociDocumentiEconomici, {TSchedaVociDocumentiEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js'
import { ID_NODO_NOTE_DI_CREDITO } from '@/NodiVuoti'
import VUELogDocumentiEconomici, { TSchedaLogDocumentiEconomici } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogDocumentiEconomici.vue';
import VUEConfirm from '@/components/VUEConfirm.vue';
import { saveAs } from 'file-saver';
import VUEModal from '@/components/Slots/VUEModal.vue';
import VUERateNote, {TSchedaRateNota} from './ComponentSchedaNota/VUERateNote.vue';

export class TSchedaNotaDiCredito extends TSchedaGenerica
{

  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)
     this.SchedaVociNotaDiCredito = new TSchedaVociDocumentiEconomici()
     this.SchedaVociNotaDiCredito.ClearVociDocumentiEconomici()
     this.SchedaLogNotaDiCredito  = new TSchedaLogDocumentiEconomici()
     this.SchedaLogNotaDiCredito.ClearLogDocumentiEconomici()
     this.SchedaRateNota = new TSchedaRateNota();
     this.SchedaRateNota.ClearListaRate()
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
      this.Dati.PARTITA_IVA            = Scheda.Dati.PARTITA_IVA
      this.Dati.CODICE_FISCALE         = Scheda.Dati.CODICE_FISCALE
      this.Dati.COND_PAGAMENTO         = Scheda.Dati.COND_PAGAMENTO
      this.Dati.ID_CAUSALE                = Scheda.Dati.ID_CAUSALE
      this.Dati.NOTE                   = Scheda.Dati.NOTE_IN_FATTURA
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
      this.SchedaVociNotaDiCredito.SetDatiCliente(Scheda.Dati.IVA_SUGGERITA_CLIENTE,
                                                  Scheda.Dati.RITENUTA,
                                                  Scheda.Dati.NATURA_PAGAMENTO)
  }

  AssignSchedaFattura(Scheda)
  {
      this.Dati.ID_FATTURA_CREAZIONE   = Scheda.Chiave
      this.Dati.NUMERO_FATTURA         = Scheda.Dati.NUMERO
      this.Dati.ANNO_FATTURA           = (new Date(Scheda.Dati.DATA)).getFullYear()

      this.Dati.ID_CLIENTE             = Scheda.Dati.ID_CLIENTE
      this.Dati.RAGIONE_SOCIALE        = Scheda.Dati.RAGIONE_SOCIALE
      this.Dati.INDIRIZZO_FATTURAZIONE = Scheda.Dati.INDIRIZZO_FATTURAZIONE
      this.Dati.NR_CIVICO_FATTURAZIONE = Scheda.Dati.NR_CIVICO_FATTURAZIONE
      this.Dati.CAP_FATTURAZIONE       = Scheda.Dati.CAP_FATTURAZIONE
      this.Dati.COMUNE_FATTURAZIONE    = Scheda.Dati.COMUNE_FATTURAZIONE
      this.Dati.PROVINCIA_FATTURAZIONE = Scheda.Dati.PROVINCIA_FATTURAZIONE
      this.Dati.CAP_DESTINAZIONE       = Scheda.Dati.CAP_DESTINAZIONE
      this.Dati.DESTINAZIONE           = Scheda.Dati.DESTINAZIONE
      this.Dati.COMUNE_DESTINAZIONE    = Scheda.Dati.COMUNE_DESTINAZIONE
      this.Dati.PROVINCIA_DESTINAZIONE = Scheda.Dati.PROVINCIA_DESTINAZIONE
      this.Dati.INDIRIZZO_DESTINAZIONE = Scheda.Dati.INDIRIZZO_DESTINAZIONE
      this.Dati.NR_CIVICO_DESTINAZIONE = Scheda.Dati.NR_CIVICO_DESTINAZIONE
      this.Dati.PARTITA_IVA            = Scheda.Dati.PARTITA_IVA
      this.Dati.CODICE_FISCALE         = Scheda.Dati.CODICE_FISCALE
      this.Dati.COND_PAGAMENTO         = Scheda.Dati.COND_PAGAMENTO
      this.Dati.ID_CAUSALE                = Scheda.Dati.ID_CAUSALE
      this.Dati.NOTE                   = Scheda.Dati.NOTE_IN_FATTURA
      this.Dati.IBAN                   = Scheda.Dati.IBAN
      this.Dati.PEC                    = Scheda.Dati.PEC
      this.Dati.COD_ENTE_SDI           = Scheda.Dati.COD_ENTE_SDI
      this.Dati.ENTE_PUBBLICO          = Scheda.Dati.ENTE_PUBBLICO
      this.Dati.COD_UFFICIO_DEST       = Scheda.Dati.COD_UFFICIO_DEST
      this.Dati.NAZIONE_EM_PIVA        = Scheda.Dati.NAZIONE_EM_PIVA
      this.Dati.NAZIONE_DESTINAZIONE   = Scheda.Dati.NAZIONE_DESTINAZIONE
      
      this.Dati.DOCUMENTO_CORRELATO    = Scheda.Dati.DOCUMENTO_CORRELATO 
      this.Dati.CUP                    = Scheda.Dati.CUP
      this.Dati.CIG                    = Scheda.Dati.CIG
      this.Dati.CONVENZIONE            = Scheda.Dati.CONVENZIONE
      this.Dati.VOCE_DI_RIFERIMENTO    = Scheda.Dati.VOCE_DI_RIFERIMENTO
      this.Dati.DOCUMENTO_NR           = Scheda.Dati.DOCUMENTO_NR
      this.Dati.DATA_DOCUMENTO         = Scheda.Dati.DATA_DOCUMENTO

      // this.Dati.ContoCorrente.IBAN                   = Scheda.Dati.ContoCorrente.IBAN
      // this.Dati.ContoCorrente.ID_CONTO_CORRENTE      = Scheda.Dati.ContoCorrente.ID_CONTO_CORRENTE
      // this.Dati.ContoCorrente.BANCA                  = Scheda.Dati.ContoCorrente.BANCA
      // this.Dati.ContoCorrente.NR_CONTO               = Scheda.Dati.ContoCorrente.NUMERO_CONTO
      // this.Dati.ContoCorrente.SWIFT                  = Scheda.Dati.ContoCorrente.SWIFT
      // this.Dati.ContoCorrente.BIC                    = Scheda.Dati.ContoCorrente.BIC
      // this.Dati.ContoCorrente.CONTO_RIBA             = Scheda.Dati.ContoCorrente.CONTO_RIBA     
      this.Dati.ESIGIBILITA_IVA        = Scheda.Dati.ESIGIBILITA_IVA
      this.SchedaVociNotaDiCredito.SetDatiCliente(Scheda.SchedaVociFattura.IvaSuggerita,
                                                  Scheda.SchedaVociFattura.RitenutaAcconto)
      this.SchedaVociNotaDiCredito.CopyData(Scheda.SchedaVociFattura)
      this.SchedaRateNota.CopyDataDaFattura(Scheda)
  }

  CanRecord()
  {
    return (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            this.Dati.DATA != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            this.Dati.ID_CLIENTE != -1 &&
            (this.Dati.COD_UFFICIO_DEST.length == 6 || this.Dati.COD_UFFICIO_DEST == '') &&
            TZDateFunct.DateFromHTMLInput(this.Dati.DATA) > new Date(2020,1,1) &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            this.Dati.COND_PAGAMENTO != null &&
            (this.Dati.CODICE_FISCALE == '' || 
              TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaVociNotaDiCredito.AllDataOk() && 
            this.SchedaVociNotaDiCredito.VociPresenti() && this.SchedaRateNota.AllDataOk()
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;
    
    if (this.Dati.INVIATA_ALLO_SDI)
      return false;

    return true;
  }

  DatiStampabili()
  {
      return true
  }

  Stampa(OnSuccess)
  {
    SystemInformation.AdvQuery.ExecuteExternalScript('StampaNotaDiCredito', { Chiave : this.Chiave },
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

  Elimina(OnSuccess,OnError)
   {
      let Self = this
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                     {
                                        Query     : "EliminaVociTramiteNota",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaLogTramiteNota",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaAllegatiTramiteNota",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaRateTramiteNota",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaXMLTramiteNota",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_NOTA_DI_CREDITO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaRecordSaldiChiusureAnnuali",
                                        Parametri : {
                                                      CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_CLIENTE),
                                                      ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                    }
                                      }
                                    ]};

      if(Self.Dati.DATA_PAGAMENTO != '' && Self.Dati.DATA_PAGAMENTO != null)
      {
        ObjQuery.Operazioni.push({
                                  Query     : "EliminaRecordSaldiChiusureAnnuali",
                                  Parametri : {
                                                CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_CLIENTE),
                                                ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Self.Dati.DATA_PAGAMENTO)))
                                              }
                                });
      }

      
      this.AdvQuery.PostSQL('NoteDiCredito',ObjQuery,function()
      {
         
          let Parametri = {}
          Parametri.ListaChiaviFatture = Self.SchedaRateNota.OldChiaviFatture
          Parametri.ChiaveNota         = Self.Chiave
          Self.SchedaRateNota.LsRateNota.forEach(function(Rata)
          {
              if(Rata.Dati.IdFatturaCorrelata != -1 && Rata.Dati.IdFatturaCorrelata != null)
                Parametri.ListaChiaviFatture.push(Rata.Dati.IdFatturaCorrelata)
          })

          Parametri.ListaChiaviFatture = Parametri.ListaChiaviFatture.filter((Valore, Indice, Self) => 
          {
            return Self.indexOf(Valore) === Indice;
          });

         if(Self.SchedaRateNota != undefined && Self.SchedaRateNota.OldChiaviFatture.length > 0)
         {
            Self.AdvQuery.ExecuteExternalScript('ModificaRateFatturaXNotaDiCredito', Parametri, 
                                                function(Result)
                                                {  
                                                  if(Result == undefined)
                                                    SystemInformation.HandleError('Problema nel cambio delle rate della fattura','','');
                                                  if(OnSuccess != undefined)  
                                                    OnSuccess();
                                                },
                                                SystemInformation.HandleError) 
         }
        else 
        {
          if(OnSuccess != undefined)  
            OnSuccess()
        }
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
                                                  DATA_PAGAMENTO            : TSchedaGenerica.PrepareForRecordDate(this.Dati.DATA_PAGAMENTO),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(this.Dati.PARTITA_IVA),
                                                  NAZIONE_DESTINAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_DESTINAZIONE),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_EM_PIVA),
                                                  INDIRIZZO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_DESTINAZIONE),
                                                  NR_CIVICO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_DESTINAZIONE),
                                                  PROVINCIA_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_DESTINAZIONE),
                                                  COMUNE_DESTINAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_DESTINAZIONE),
                                                  CAP_DESTINAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_DESTINAZIONE),
                                                  COND_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.COND_PAGAMENTO),
                                                  NOTE                      : TSchedaGenerica.PrepareForRecordString(this.Dati.NOTE),
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.SchedaVociNotaDiCredito.RitornaRitenutaAcconto()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(this.Dati.ESIGIBILITA_IVA),
                                                  ID_CAUSALE                : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_CAUSALE),
                                                  IBAN                      : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.IBAN) : null,
                                                  BANCA                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BANCA) : null,
                                                  BIC                       : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BIC)   : null,
                                                  SWIFT                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.SWIFT) : null,
                                                  ID_CONTO_CORRENTE         : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                  ENTE_PUBBLICO             : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.ENTE_PUBBLICO),
                                                  COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_ENTE_SDI),
                                                  PEC                       : TSchedaGenerica.PrepareForRecordString(this.Dati.PEC),
                                                  COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_UFFICIO_DEST),
                                                  ID_CONTO_PAGAMENTO        : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_CONTO_PAGAMENTO),
                                                  INVIATA_ALLO_SDI          : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.INVIATA_ALLO_SDI),
                                                  CUP                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CUP),
                                                  CIG                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CIG),
                                                  DOCUMENTO_CORRELATO       : TSchedaGenerica.PrepareForRecordString(Self.Dati.DOCUMENTO_CORRELATO),
                                                  CONVENZIONE               : TSchedaGenerica.PrepareForRecordString(Self.Dati.CONVENZIONE),
                                                  VOCE_DI_RIFERIMENTO       : TSchedaGenerica.PrepareForRecordString(Self.Dati.VOCE_DI_RIFERIMENTO),
                                                  DOCUMENTO_NR              : TSchedaGenerica.PrepareForRecordString(Self.Dati.DOCUMENTO_NR),
                                                  DATA_DOCUMENTO            : TSchedaGenerica.PrepareForRecordString(Self.Dati.DATA_DOCUMENTO), 
                                                  NOTE_PAGAMENTO            : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE_PAGAMENTO), 
                                                  NO_PRIMA_NOTA             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.NO_PRIMA_NOTA), 
                                                  INVIATA_TRAMITE_EMAIL     : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.INVIATA_TRAMITE_EMAIL),
                                                  ID_FATTURA_CREAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ID_FATTURA_CREAZIONE),
                                               }
                                 });
        this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_NOTA_DI_CREDITO')
        this.SchedaRateNota.CalcolaRate(Self.SchedaVociNotaDiCredito.TotaleSplitPayment ? Self.SchedaVociNotaDiCredito.TotaleSplitPayment : Self.SchedaVociNotaDiCredito.TotaleFattura)
        this.SchedaRateNota.PrepareQueryParameters(ObjQuery.Operazioni);

                
        this.SchedaVociNotaDiCredito.PrepareQueryParameters(ObjQuery.Operazioni, 'ID_NOTA_DI_CREDITO')

        ObjQuery.Operazioni.push({
                                   Query     : "EliminaRecordSaldiChiusureAnnuali",
                                   Parametri : {
                                                 CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_CLIENTE),
                                                 ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Self.Dati.DATA)))
                                               }
                                 });

        if(Self.Dati.DATA_PAGAMENTO != '' && Self.Dati.DATA_PAGAMENTO != null)
        {
          ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_CLIENTE),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Self.Dati.DATA_PAGAMENTO)))
                                                }
                                  });
        }

        this.AdvQuery.PostSQL('NoteDiCredito',ObjQuery,function(Response)
        {

          let Parametri = {}
          Parametri.ListaChiaviFatture = Self.SchedaRateNota.OldChiaviFatture
          Parametri.ChiaveNota    = Self.IsNuovo()? Response.NewKey1 : Self.Chiave

          Self.SchedaRateNota.LsRateNota.forEach(function(Rata)
          {
              if(Rata.Dati.IdFatturaCorrelata != -1 && Rata.Dati.IdFatturaCorrelata != null)
                Parametri.ListaChiaviFatture.push(Rata.Dati.IdFatturaCorrelata)
          })

          Parametri.ListaChiaviFatture = Parametri.ListaChiaviFatture.filter((Valore, Indice, Self) => 
          {
            return Self.indexOf(Valore) === Indice;
          });

          if(Self.SchedaRateNota != undefined && Self.SchedaRateNota.OldChiaviFatture.length > 0)
          {
            
            Self.AdvQuery.ExecuteExternalScript('ModificaRateFatturaXNotaDiCredito', Parametri, 
                                                function(Result)
                                                {  
                                                  if(Result != undefined)
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
                                                  }
                                                  else SystemInformation.HandleError('Problema nel cambio delle rate della fattura','','');
                                                },
                                                SystemInformation.HandleError)  
          }
          else
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
          }
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });
      }
   }

   CaricaRiassunto(Riassunto)
   {
      this.IsNotaDiCredito          = true;
      this.Chiave                   = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociNotaDiCredito.SetIdDocumento(this.Chiave)
      this.SchedaRateNota.SetIdDocumento(this.Chiave);
      this.Dati.ID_CLIENTE          = Riassunto.ID_CLIENTE;
      this.Dati.ANNO                = Riassunto.ANNO
      this.Dati.INVIATA_ALLO_SDI    = TSchedaGenerica.DisponiFromBoolean(Riassunto.INVIATA_ALLO_SDI)
      this.Dati.NUMERO              = TSchedaGenerica.DisponiFromInteger(Riassunto.NUMERO)
      this.Dati.RAGIONE_SOCIALE     = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.Dati.DATA_PAGAMENTO      = TSchedaGenerica.DisponiFromDate(Riassunto.DATA_PAGAMENTO)
      this.PAGATO                   = TSchedaGenerica.DisponiFromInteger(Riassunto.PAGATO)
      this.TOTALE_NOTA              = Riassunto.TOTALE_NOTA //_dg_ SENZA Disponi perché NON arriva dal DataBase, bensì è CALCOLATO
      this.Dati.RATE_NON_PAGATE     = TSchedaGenerica.DisponiFromInteger(Riassunto.RATE_NON_PAGATE)
      this.Dati.RATE_TOTALI         = TSchedaGenerica.DisponiFromInteger(Riassunto.RATE_TOTALI)
      this.Dati.ModificaTabellaRate = 0
      this.RateNotePagate =  this.SchedaRateNota.NotaPagata();
      this.CreateSnapshot();

   }

   GetDescrizione()
    {
      let Descrizione = '';

      if(this.Dati.ANNO == 0 || this.Dati.NUMERO == 0)
        Descrizione += 'Avviso nota';
      else Descrizione += 'Nota Credito ' + this.Dati.NUMERO + '/' + this.Dati.ANNO;

      Descrizione += this.TOTALE_NOTA != 0 ? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.TOTALE_NOTA) : '';

      return Descrizione;
    }
   
    Clear()
    {
      this.SchedaAllegati.AssignDati([])
      this.SchedaLogNotaDiCredito.AssignDati([])
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociNotaDiCredito.SetIdDocumento(-1)
      this.SchedaRateNota.SetIdDocumento(-1);
      this.SchedaVociNotaDiCredito.AssignDati([], SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA, SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO,'ID_NOTA_DI_CREDITO')
      this.Dati = {
                      NUMERO                        : 0,
                      ID_CLIENTE                    : 0,
                      DATA                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
                      DATA_PAGAMENTO                : '',
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
                      COND_PAGAMENTO                : null,
                      NOTE                          : '',
                      ID_CAUSALE                    : -1,
                      ESIGIBILITA_IVA               : '',
                      ENTE_PUBBLICO                 : false,
                      COD_ENTE_SDI                  : '0000000',
                      PEC                           : '',
                      COD_UFFICIO_DEST              : '',
                      INVIATA_ALLO_SDI              : false,
                      ID_CONTO_PAGAMENTO            : -1,
                      CUP                           : '',
                      CIG                           : '',
                      DOCUMENTO_CORRELATO           : DOCUMENTO_CORRELATO.NessunDocumento,
                      CONVENZIONE                   : '',
                      VOCE_DI_RIFERIMENTO           : '',
                      DOCUMENTO_NR                  : '',
                      DATA_DOCUMENTO                : '',
                      NOTE_PAGAMENTO                : '',
                      NO_PRIMA_NOTA                 : false,
                      INVIATA_TRAMITE_EMAIL         : false,
                      ID_FATTURA_CREAZIONE          : -1,
                      NUMERO_FATTURA                : -1,
                      ANNO_FATTURA                  : -1,
                      ContoCorrente                 : { 
                                                        IBAN              : '',
                                                        BANCA             : '',
                                                        ID_CONTO_CORRENTE : -1,
                                                        NR_CONTO          : '',
                                                        CONTO_RIBA        : false,
                                                        BIC               : '',
                                                        SWIFT             : ''
                                                      },
                      // Dati allegati
                      ModificaTabellaAllegati       : false,
                      ModificaTabellaVoci           : false,
                      ModificaTabellaRate           : 0,
                  }
      super.Clear();

    }

    RicalcolaRate(OnSuccess)
    {
      var Self = this
      this.SchedaRateNota.Ricalcola(
                                      Self.Chiave, 
                                      parseFloat(this.SchedaVociNotaDiCredito.TotaleFattura),
                                      OnSuccess
                                   )
    }

    Disponi(OnSuccess)
    {
      var Self = this;
      Self.SchedaRateNota.SetIdDocumento(this.Chiave)
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('NoteDiCredito',{ CHIAVE : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo              = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayAllegati          = Self.AdvQuery.FindResults(Results,"AllegatiNoteDiCredito");
                                            let ArrayVociNotaDiCredito = Self.AdvQuery.FindResults(Results,"VociNoteDiCredito");
                                            let ArrayLogNotaDiCredito  = Self.AdvQuery.FindResults(Results,"LogNoteDiCredito");
                                            let ArrayRateNotaDiCredito = Self.AdvQuery.FindResults(Results, 'RateNotaDiCredito')

                                            if(ArrayInfo != undefined)
                                            {
                                              if(ArrayLogNotaDiCredito.length != 0)
                                                Self.SchedaLogNotaDiCredito.AssignDati(ArrayLogNotaDiCredito,'ID_NOTA_DI_CREDITO')
                                              if(ArrayAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayAllegati,'NOTA_DI_CREDITO')
                                              if(ArrayVociNotaDiCredito.length != 0)
                                                Self.SchedaVociNotaDiCredito.AssignDati(ArrayVociNotaDiCredito,
                                                                                        TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_SUGGERITA_CLIENTE) / 10,
                                                                                        TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA),
                                                                                        'ID_NOTA_DI_CREDITO',
                                                                                        TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NATURA_PAGAMENTO),
                                                                                        '',
                                                                                        '',
                                                                                        ArrayInfo[0].ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione? true : false)
                                              if(ArrayRateNotaDiCredito.length != 0)
                                                Self.SchedaRateNota.AssignDati(ArrayRateNotaDiCredito)
                                              
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
                                                              DATA_PAGAMENTO                : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_PAGAMENTO),
                                                              PARTITA_IVA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              CODICE_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              NAZIONE_EM_PIVA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                                                              NAZIONE_DESTINAZIONE          : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_DESTINAZIONE),
                                                              COMUNE_DESTINAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_DESTINAZIONE),
                                                              PROVINCIA_DESTINAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_DESTINAZIONE),
                                                              CAP_DESTINAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_DESTINAZIONE),
                                                              DESTINAZIONE                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESTINAZIONE),
                                                              INDIRIZZO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_DESTINAZIONE),
                                                              NR_CIVICO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_DESTINAZIONE),
                                                              COND_PAGAMENTO                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].COND_PAGAMENTO),
                                                              NOTE                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                              ID_CAUSALE                    : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CAUSALE),
                                                              ESIGIBILITA_IVA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                                                              COD_ENTE_SDI                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI),                                                              
                                                              PEC                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),                                                              
                                                              ENTE_PUBBLICO                 : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ENTE_PUBBLICO),   
                                                              COD_UFFICIO_DEST              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_UFFICIO_DEST),
                                                              INVIATA_ALLO_SDI              : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].INVIATA_ALLO_SDI),                                                                                                                           
                                                              ID_CONTO_PAGAMENTO            : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CONTO_PAGAMENTO),  
                                                              CUP                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CUP),
                                                              CIG                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CIG),
                                                              DOCUMENTO_CORRELATO           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DOCUMENTO_CORRELATO),
                                                              CONVENZIONE                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CONVENZIONE),
                                                              VOCE_DI_RIFERIMENTO           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].VOCE_DI_RIFERIMENTO),
                                                              DOCUMENTO_NR                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DOCUMENTO_NR),
                                                              DATA_DOCUMENTO                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA_DOCUMENTO),                                                                                                                         
                                                              NOTE_PAGAMENTO                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE_PAGAMENTO),
                                                              NO_PRIMA_NOTA                 : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].NO_PRIMA_NOTA),
                                                              INVIATA_TRAMITE_EMAIL         : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].INVIATA_TRAMITE_EMAIL),                                                                                                                    
                                                              ID_FATTURA_CREAZIONE          : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_FATTURA_CREAZIONE),                                                           
                                                              NUMERO_FATTURA                : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NUMERO_FATTURA),                                                           
                                                              ANNO_FATTURA                  : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ANNO_FATTURA), 
                                                              RATE_NON_PAGATE               : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_NON_PAGATE),
                                                              RATE_TOTALI                   : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_TOTALI),                                                          
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
                                                              ModificaTabellaRate           : 0,
                                                }

                                                Self.PAGATO = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PAGATO);

                                                if(Self.Dati.DATA != '')
                                                  Self.Dati.ANNO = new Date(Self.Dati.DATA).getFullYear();
                                                else Self.Dati.ANNO = null;
                                                Self.CreateSnapshot();
                                                
                                                SystemInformation.GetTotaliNotaDiCreditoFromServer(Self.Chiave, function(Totali)
                                                {
                                                  Self.TOTALE_NOTA = Totali[0].NettoAPagare
                                                  if(OnSuccess != undefined)
                                                    OnSuccess()
                                                })
                                              }
                                              else SystemInformation.HandleError('Nota eliminata')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio della note');
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
      OnSuccess([{
                   Name : DASHBOARD_FILTER_TYPES.Clienti,
                   Positions : [
                                  this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                  this.Dati.ID_CLIENTE,
                                  ID_NODO_NOTE_DI_CREDITO,
                                  Anno.getFullYear(),
                                  this.Chiave
                               ]
              }])
    }

    GetClassName()
    {
      return 'TSchedaNotaDiCredito';
    }

    GetImageIndex()
    {
      if (this.PAGATO == 1 || this.SchedaRateNota.NotaPagata())
      {
        if(this.Dati.INVIATA_ALLO_SDI)
          return 'NotaDiCreditoInviata.png'
        else return 'NotaDiCredito.png'
      }
      else if(this.Dati.RATE_TOTALI != this.Dati.RATE_NON_PAGATE)
      {
        if(this.Dati.INVIATA_ALLO_SDI)
          return 'NotaDiCreditoInviata_MezzaPagata.png'
        else return 'NotaDiCredito_MezzaPagata.png'
      }
      else
      {
        if(this.Dati.INVIATA_ALLO_SDI)
          return 'NotaDiCreditoInviata_NonPagata.png'
        else return 'NotaDiCredito_NonPagata.png'
      }
    }

  
    Invia(OggettoEmail, OnSuccess)
    {
      let Parametri        = OggettoEmail
      Parametri.Chiave     = this.Chiave
      Parametri.InvioEmail = true
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaNotaDiCredito', Parametri,
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

    RecuperaEmailCliente(OnSuccess)
    {
      var Self = this;
      this.ListaEmailCliente = ''
      this.ListaEmailAmm     = ''
      SystemInformation.AdvQuery.GetSQL('NoteDiCredito', { CHIAVE : this.Chiave },
                                          function(Results)
                                          {
                                            let ArrayEmailCliente = SystemInformation.AdvQuery.FindResults(Results,"ListaEmailCliente");
                                            let ArrayEmailAmm     = SystemInformation.AdvQuery.FindResults(Results,"ListaEmailAmministratore");
                                            if(ArrayEmailCliente != undefined && ArrayEmailAmm != undefined)
                                            {
                                              if(ArrayEmailCliente.length != 0)
                                                ArrayEmailCliente.forEach(function(Email)
                                                {
                                                  if(Email.EMAIL_CLIENTI)
                                                    Self.ListaEmailCliente += Email.EMAIL_CLIENTI + '; '
                                                })
                                              if(ArrayEmailAmm.length != 0)
                                                ArrayEmailAmm.forEach(function(Email)
                                                {
                                                  if(Email.EMAIL_AMMINISTRATORI)
                                                    Self.ListaEmailAmm += Email.EMAIL_AMMINISTRATORI + '; '
                                                })
                                              Self.ListaEmailAmm     = Self.ListaEmailAmm.substring(0, Self.ListaEmailAmm.length - 2)
                                              Self.ListaEmailCliente = Self.ListaEmailCliente.substring(0, Self.ListaEmailCliente.length - 2)
                                              OnSuccess()
                                            }
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                          'ListaEmailNotaDiCredito')
    }

    OnEsigibilitaChange()
    {
      if(this.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
      { 
        this.SchedaVociNotaDiCredito.SetSplitPayment(true);
        this.Dati.NOTE_IN_FATTURA = "FATTURA CON SPLIT PAYMENT";
      }
      else
      { 
        this.SchedaVociNotaDiCredito.SetSplitPayment(false)
        this.Dati.NOTE_IN_FATTURA = "";
      }

      this.SchedaVociNotaDiCredito.CalcoloTotaliFattura()
      this.RicalcolaRate()
    }
}

export default 
{
   components : 
   {
      VUEInputProvince,
      VUEInputCAP,
      VUEInputCondPagamenti,
      VUEInputClienti,
      VUEInputCodiceFiscale,
      VUEInputPartitaIVA,
      VUEAllegati,
      VUEVociDocumentiEconomici,
      VUEInputEsigibilitaIVA,
      VUEInputCausali,
      VUEInputContoRibaCorrente,
      VUELogDocumentiEconomici,
      VUEInputNazioni,
      VUEConfirm,
      VUEModal,
      VUERateNote
   },
   data()
   {
     return {  
              DeveloperMode               : SystemInformation.DeveloperMode,
              StatoItaliano               : SystemInformation.Configurazioni.StatoItaliano,
              PopupScegliFileXML          : false,
              PopupNotaDiCredito          : false,
              PopupCorreggiNota           : false,
              ListaFileXML                : [],
              ContiCasse                  : SystemInformation.GetLsContiCasse(),
              DocumentoCorrelato          : DOCUMENTO_CORRELATO,
              LsDocumentoCorrelato        : SystemInformation.GetLsTipiDocumentoCorrelato(),
              AvviataNumerazione          : false,
              CostantePagamentoBollo      : PAGAMENTO_BOLLO,
              VisibilitaLogVariazioni     : SystemInformation.AccessRights.VisibilitaLogVariazioni(),

              Tabs                 : {
                                        ActiveTab : 'Nota di credito',
                                        Tabs      : [
                                                      {
                                                        Caption : 'Nota di credito',
                                                        Id      : 'Nota di credito'
                                                      },
                                                      {
                                                        Caption : 'Voci Nota',
                                                        Id      : 'VociNota'
                                                      },
                                                      {
                                                        Caption : 'Rata',
                                                        Id      : 'Rata'
                                                      },
                                                      {
                                                        Caption  : 'Allegati',
                                                        Id       : 'Allegati'
                                                      },
                                                      {
                                                        Caption : 'Variazioni',
                                                        Id      : 'LogNotaDiCredito' 
                                                      },
                                                    ]
                                      }
            }
   },
   emits : ['onChangeNodiAlbero'],
   props : ['SchedaNotaDiCredito'],
   computed :
   {     
     CurrentCliente()
     {
       return this.SchedaNotaDiCredito.Dati.ID_CLIENTE
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

    CurrentTotaleNota()
    {
      return this.SchedaNotaDiCredito.SchedaVociNotaDiCredito.TotaleFattura
    },
         
    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaNotaDiCredito.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaNotaDiCredito.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },

    TabsVisibili()
    {
      return this.Tabs.Tabs.filter(tab => tab.Id !== 'LogNotaDiCredito' || this.VisibilitaLogVariazioni);
    }

   },
   watch:
   {
    CurrentCliente:
     {
        handler(NewValue,OldValue)
        {
            
            var Self = this;
            if(this.SchedaNotaDiCredito.DatiModificati()) 
                if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
                  SystemInformation.AdvQuery.GetSQL('Clienti', { CHIAVE : NewValue },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectCliente");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                                Self.SchedaNotaDiCredito.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                                Self.SchedaNotaDiCredito.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                                Self.SchedaNotaDiCredito.Dati.NR_CIVICO_FATTURAZIONE   = ArrayInfo[0].NR_CIVICO_FATTURAZIONE;
                                                Self.SchedaNotaDiCredito.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                                Self.SchedaNotaDiCredito.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                                Self.SchedaNotaDiCredito.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                                Self.SchedaNotaDiCredito.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;
                                                Self.SchedaNotaDiCredito.Dati.ESIGIBILITA_IVA          = ArrayInfo[0].ESIGIBILITA_IVA;
                                                Self.SchedaNotaDiCredito.Dati.INDIRIZZO_DESTINAZIONE   = ArrayInfo[0].INDIRIZZO_SPEDIZIONE;
                                                Self.SchedaNotaDiCredito.Dati.NR_CIVICO_DESTINAZIONE   = ArrayInfo[0].NR_CIVICO_SPEDIZIONE;
                                                Self.SchedaNotaDiCredito.Dati.COMUNE_DESTINAZIONE      = ArrayInfo[0].COMUNE_SPEDIZIONE;
                                                Self.SchedaNotaDiCredito.Dati.PROVINCIA_DESTINAZIONE   = ArrayInfo[0].PROVINCIA_SPEDIZIONE;
                                                Self.SchedaNotaDiCredito.Dati.NOTE                     = ArrayInfo[0].NOTE_IN_FATTURA;
                                                Self.SchedaNotaDiCredito.Dati.CAP_DESTINAZIONE         = ArrayInfo[0].CAP_SPEDIZIONE;
                                                Self.SchedaNotaDiCredito.Dati.DESTINAZIONE             = ArrayInfo[0].PRESSO;
                                                Self.SchedaNotaDiCredito.Dati.PEC                      = ArrayInfo[0].PEC
                                                Self.SchedaNotaDiCredito.Dati.COD_ENTE_SDI             = ArrayInfo[0].COD_ENTE_SDI
                                                Self.SchedaNotaDiCredito.Dati.ENTE_PUBBLICO            = ArrayInfo[0].ENTE_PUBBLICO == 'T'? true : false
                                                Self.SchedaNotaDiCredito.Dati.COD_UFFICIO_DEST         = ArrayInfo[0].COD_UFFICIO_DEST
                                                Self.SchedaNotaDiCredito.Dati.NAZIONE_DESTINAZIONE     = ArrayInfo[0].NAZIONE_SPEDIZIONE
                                                Self.SchedaNotaDiCredito.Dati.NAZIONE_EM_PIVA          = ArrayInfo[0].NAZIONE_EM_PIVA
                                                Self.SchedaNotaDiCredito.Dati.ContoCorrente = {
                                                                                                IBAN              : ArrayInfo[0].IBAN,
                                                                                                ID_CONTO_CORRENTE : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].ID_CONTO_CORRENTE : -1,
                                                                                                BANCA             : ArrayInfo[0].BANCA_APPOGGIO,
                                                                                                CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE == null? true : false,
                                                                                                NR_CONTO          : ArrayInfo[0].NR_CONTO,
                                                                                                BIC               : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].BIC_CONTO   : ArrayInfo[0].BIC,
                                                                                                SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE? ArrayInfo[0].SWIFT_CONTO : ArrayInfo[0].SWIFT,
                                                                                              } 
                                                Self.SchedaNotaDiCredito.Dati.COND_PAGAMENTO           = ArrayInfo[0].COND_PAGAMENTO
                                                if(ArrayInfo[0].PROVINCIA_FATTURAZIONE == null || ArrayInfo[0].PROVINCIA_FATTURAZIONE == undefined)
                                                    Self.SchedaNotaDiCredito.Dati.PROVINCIA_FATTURAZIONE = -1
                                                else Self.SchedaNotaDiCredito.Dati.PROVINCIA_FATTURAZIONE = parseInt(ArrayInfo[0].PROVINCIA_FATTURAZIONE);

                                                Self.SchedaNotaDiCredito.SchedaVociNotaDiCredito.SetDatiCliente(Self.SchedaNotaDiCredito.Dati.REVERSE_CHARGE? 0 : ArrayInfo[0].IVA_SUGGERITA_CLIENTE / 10,
                                                                                                                       ArrayInfo[0].RITENUTA / 10,
                                                                                                                       ArrayInfo[0].BOLLO, 
                                                                                                                       ArrayInfo[0].NATURA_PAGAMENTO)
                                              }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectCliente')
           
        }
     },

     CurrentTotaleNota:
     {
        handler(NewValue, OldValue)
        {
          if(this.SchedaNotaDiCredito.DatiModificati()) 
            if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
              this.RicalcolaRate()
        }
     },
   },
   methods: 
   {
    OnClickChiudiPopupScegliFile()
    {
      this.ListaFileXML       = []
      this.PopupScegliFileXML = false
    },

    OnAllegatiChange()
    {
      this.SchedaNotaDiCredito.Dati.ModificaTabellaAllegati = this.SchedaNotaDiCredito.SchedaAllegati.Modificato();
    },

    OnVociNotaDiCreditoChange()
    {
      this.SchedaNotaDiCredito.Dati.ModificaTabellaVoci = this.SchedaNotaDiCredito.SchedaVociNotaDiCredito.Modificato();
    },

    OnRateNotaChange()
    {
      if(this.SchedaNotaDiCredito.SchedaRateNota.Modificato())
      {

        this.SchedaNotaDiCredito.Dati.ModificaTabellaRate++

      }
      else this.SchedaNotaDiCredito.Dati.ModificaTabellaRate = 0
    },

    ConfermaInvioNota()
    {
      if(!this.AvviataNumerazione)
      {
        this.AvviataNumerazione = true
        var Self = this                              
        SystemInformation.AdvQuery.ExecuteExternalScript('NumeraNotaDiCredito', { ChiaveNota : this.SchedaNotaDiCredito.Chiave },
                                            function()
                                            {
                                              Self.AvviataNumerazione = false
                                              Self.SchedaNotaDiCredito.Disponi(function()
                                              {
                                                Self.$emit('onChangeNodiAlbero');
                                              })
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            })
        this.AnnullaInvioNota()
      }
    },

    AnnullaInvioNota()
    {
       this.PopupNotaDiCredito = false
    },

    OnClickNumera()
    {
      this.PopupNotaDiCredito = true
    },

    // OnClickCopiaDaPartitaIva()
    // {
    //   this.SchedaNotaDiCredito.Dati.CODICE_FISCALE = this.SchedaNotaDiCredito.Dati.PARTITA_IVA
    // },

    RicalcolaRate()
    {
      this.SchedaNotaDiCredito.RicalcolaRate()
    },

    OnClickCorreggiNota()
    {
      this.PopupCorreggiNota = true         
    },

    OnClickDownloadFilesXML()
    {
      var Self = this
      for(var i = 0; i < this.ListaFileXML.length; i++)
        if(this.ListaFileXML[i].Selezionato)
          saveAs(TZStringConvFunct.MoveStringToBlob(Self.ListaFileXML[i].XML_BODY), 'NotadiCredito-' + Self.SchedaNotaDiCredito.Dati.NUMERO + '_DataInvio_' + Self.ListaFileXML[i].DATA_INVIO + '.xml'); 
      this.PopupScegliFileXML = false
    },

    OnClickScaricaFileNota()
    {
      var Self = this
      SystemInformation.AdvQuery.GetSQL('NoteDiCredito', { CHIAVE : this.SchedaNotaDiCredito.Chiave },
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ScaricaFileNota");
                                          if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                          {
                                            if(ArrayInfo.length == 1)
                                               saveAs(TZStringConvFunct.MoveStringToBlob(ArrayInfo[0].XML_BODY), 'NotadiCredito_N_' + Self.SchedaNotaDiCredito.Dati.NUMERO + '_DataInvio' + TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ArrayInfo[0].DATA_INVIO)) + '.xml'); 
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
                                        'ScaricaFileNota')
    },
   
    ConfermaCorrezione()
    {
      var Self = this
      var ObjQuery = {Operazioni : []}
      ObjQuery.Operazioni.push({Query : 'CorreggiNota',
                                        Parametri : { CHIAVE : this.SchedaNotaDiCredito.Chiave }})

      SystemInformation.AdvQuery.PostSQL('NoteDiCredito',ObjQuery,
                                              function()
                                              { 
                                                Self.SchedaNotaDiCredito.Disponi() 
                                              },
                                              SystemInformation.HandleError)
      this.AnnullaPopup()
    },

    AnnullaPopup()
    {
       this.PopupCorreggiNota = false
    },

    OnClickNessunDocumento()
    {
      if(this.SchedaNotaDiCredito.Dati.DOCUMENTO_CORRELATO == DOCUMENTO_CORRELATO.NessunDocumento)
      {
        this.SchedaNotaDiCredito.Dati.CIG                 = ''
        this.SchedaNotaDiCredito.Dati.CUP                 = ''
        this.SchedaNotaDiCredito.Dati.CONVENZIONE         = ''
        this.SchedaNotaDiCredito.Dati.DOCUMENTO_NR        = ''
        this.SchedaNotaDiCredito.Dati.VOCE_DI_RIFERIMENTO = ''
        this.SchedaNotaDiCredito.Dati.DATA_DOCUMENTO      = ''
      }  
    },

    FormattaImporto(Importo)
    {
      return SystemInformation.FormattaImporto(Importo)
    },

    
    OnEsigibilitaChange()
    { 
      this.SchedaNotaDiCredito.OnEsigibilitaChange()
    },

    OnClickRate()
    {
      this.Tabs.ActiveTab = 'Rata';     
    },


   },                             
    
   
   beforeMount() 
   {
     if(this.SchedaNotaDiCredito != null && this.SchedaNotaDiCredito.VisualizzaTabRate)
      this.OnClickRate()
   },

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>