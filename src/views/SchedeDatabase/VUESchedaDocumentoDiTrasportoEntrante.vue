<template>
 <div class="ZMCorpoSchedeDati">
  <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="ATab.Id == 'Documento di trasporto entrante'" src="@/assets/images/IconeAlbero/DDT_entrante.png" style="width:35px;height:35px;float:left;margin-top:-9px">  
             </a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'Documento di trasporto entrante'">
     <div class="ZMNuovaRigaScheda" style="height:10px">&nbsp;</div>
      <div v-if="!SchedaDocumentoDiTrasportoEntrante.DatiModificati() && SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1">
        <div class="col-md-6" style="border:1px solid #b3dbff;margin-bottom:10px;margin-left: 10px">
            <text style="float:left;margin-top:1%;margin-bottom:1%;font-weight: bold;font-size:15px;width:90%">Questo documento è stato utilizzato per la fattura N° {{ SchedaDocumentoDiTrasportoEntrante.NumeroFattura != null? SchedaDocumentoDiTrasportoEntrante.NumeroFattura : SchedaDocumentoDiTrasportoEntrante.ChiaveFattura }}</text>
        </div>
        <!-- <div class="col-md-5">
            <button class="btn btn-sm btn-info" style="height:36px;width:40%" @click="OnClickGoToImportazione">Vai alla fattura</button>
        </div> -->
      </div>
      <div class="col-md-6" style="float:right">
        <div style="float:right;width:25%">
            <text v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'" style="font-weight: bold;">Data doc. cliente</text>
            <text v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'" style="font-weight: bold;">Data doc. fornitore</text>
            <input type="date" id="input-data" :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.DATA_DOCUMENTO_ENTRANTE"/>
        </div>
        <div style="float:right;width:1%;">&nbsp;</div>
        <div style="float:right;width:24%">
          <text v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'" style="font-weight: bold">N. doc. cliente</text>
          <text v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'" style="font-weight: bold">N. doc. fornitore</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NUMERO_DOCUMENTO_ENTRANTE" type="text" class="form-control" placeholder="Numero">
        </div>
        <div style="float:right;width:1%;">&nbsp;</div>
        <div style="float:right;width:24%">
          <text style="font-weight: bold;">Data</text>
          <input type="date" id="input-data" :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.DATA_DDT"/>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.DATA_DDT == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
       <div style="float:right;width:1%;">&nbsp;</div>
       <div style="float:right;width:24%" v-if="SchedaDocumentoDiTrasportoEntrante.Dati.NUMERO_DDT != -1" >
          <text style="float:left;font-weight: bold;padding-right: 10%">D.D.T. n.</text>
          <div style="clear:both"></div>
          <label style="float:left;font-size:15px;width:151px;height:28px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaDocumentoDiTrasportoEntrante.Dati.NUMERO_DDT }}</label>
          <br>
        </div> 
      </div>

      <div class="col-md-6">  
        <div style="float:left;width:95%;margin-top: 13px;">
        <div class="btn-group m-b-sm" style="width:74%;float:left;margin-right:5px" v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'">
          <button :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" @click="OnClickFornitore" type="button" class="btn btn-default" style="width:50%;float:right">Fornitore</button>
          <button :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="button" class="btn btn-info" style="width:50%;float:right">Cliente</button>
        </div>
        <div class="btn-group m-b-sm" style="width:74%;float:left;margin-right:5px" v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'">
          <button :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="button" class="btn btn-info" style="width:50%;float:right">Fornitore </button>
          <button :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" @click="OnClickCliente" type="button" class="btn btn-default" style="width:50%;float:right;">Cliente</button>
        </div>
        </div>
      </div>        
      
      <div v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'" class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Cliente</text>
        <hr style="margin-top:5px">
      </div>

      <div v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'" class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Fornitore</text>
        <hr style="margin-top:5px">
      </div>
      <div style="padding-top:10px;margin-left:10px;margin-left:50px;margin-top:12px;float:left"> </div> 

      <div class="col-md-6" style="float:right" v-if="!SchedaDocumentoDiTrasportoEntrante.DatiModificati() && !SchedaDocumentoDiTrasportoEntrante.IsNuovo() && SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA == -1">
        <div style="float:right;">
          <div class="btn-group open" >
            <button type="button" 
                    class="btn btn-default" 
                    :class="{'data-toggle' : this.MenuNuovo.length != 0}" 
                    aria-expanded="true" 
                    style="width:120px;z-index: 9999"
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

      <div v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'" class="ZMNuovaRigaScheda">
        <div style="float:left; width:50%">
          <text style="font-weight: bold; float:left; width:50%">Cliente</text>
          <VUEInputClienti @onUpdate="newValue => SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE = newValue" :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE"/>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE == -1 && SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>
      <div v-if="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'" class="ZMNuovaRigaScheda">
        <div style="float:left; width:50%">
          <text style="font-weight: bold; float:left; width:50%">Fornitore</text>
          <VUEInputFornitore :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE" emptyElement="true"/>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE == -1 && SchedaDocumentoDiTrasportoEntrante.Selezionato == 'F'" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left; width:49%">
        <text style="font-weight: bold">Ragione Sociale </text>
        <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.RAGIONE_SOCIALE" type="text" class="form-control" placeholder="Ragione Sociale"/>
        <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
      </div>
      <div class="ZMSeparatoreFiltri"></div>
      <div class="ZMSeparatoreScheda" style="margin-top:3px; margin-bottom:-15px">       
        <text style="font-weight: bold;">Sede</text>
        <hr style="margin-top:5px">
      </div>
      <div class="ZMNuovaRigaScheda">
       <div style="float:left;width:30%">
        <text style="font-weight: bold">Indirizzo</text>
        <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_FATTURAZIONE" type="text" class="form-control" placeholder="Indirizzo">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="text" maxlength="7" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" placeholder="CAP" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.CAP_FATTURAZIONE"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:26%">
        <text style="font-weight: bold">Comune</text>
        <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:20%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true" style="cursor:default"/>
       </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:25%">
          <text style="font-weight: bold">Partita IVA</text>
          <VUEInputPartitaIVA :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA" placeholder="Partita IVA"></VUEInputPartitaIVA>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA.trim() == '' && SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
                 class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:26%">
          <label style="font-weight: bold;">Codice Fiscale</label>
          <VUEInputCodiceFiscale :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE" style="float:left" placeholder="Codice Fiscale"></VUEInputCodiceFiscale>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA.trim() == '' && SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
            class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:26%">
          <label style="font-weight: bold;">Nazione emittente</label>
          <VUEInputNazioni :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA" emptyElement="false" style="cursor:default"/>
          <label v-if="SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:20%">
            <label style="font-weight: bold;">&nbsp;</label>
            <button :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" class="btn btn-sm btn-info" style="float:left;width:100%" @click="OnClickCopiaDaPartitaIva">Copia da Partita IVA</button>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri"></div>
      <div class="ZMSeparatoreScheda" style="margin-top:15px">       
        <text style="font-weight: bold;margin-bottom:-20px">Indirizzo di Destinazione</text>
        <hr style="margin-top:5px">
      </div>

      <VUEModalButtonRecapitiFiliali :styleForButton="'float:right; margin-right: 0px'"
                                     :IdCliente="SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE"
                                     @filiale-selected="OnFilialeSelected"   />

      <div class="ZMNuovaRigaScheda" style="margin-top:-18px">
        <div style="float:left;width:50%">
          <text style="font-weight: bold">Destinazione</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.DESTINAZIONE" type="text" class="form-control" placeholder="Destinazione">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <text style="font-weight: bold">Indirizzo</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_DESTINAZIONE" type="text" class="form-control" placeholder="Indirizzo">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:9%">
           <label style="font-weight: bold;">Civico</label>
           <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="text" maxlength="7" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NR_CIVICO_DESTINAZIONE" placeholder="Nr. civico"/>
        </div> 
      </div>
       
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:10%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" placeholder="CAP" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.CAP_DESTINAZIONE"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <text style="font-weight: bold">Comune</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_DESTINAZIONE" type="text" class="form-control" placeholder="Comune">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <label style="font-weight: bold;">Nazione</label>
          <VUEInputNazioni :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_DESTINAZIONE" emptyElement="true" style="cursor:default"/>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
      </div>
   </div>

   <div v-if="Tabs.ActiveTab == 'VociDDT'">
    <div v-if="SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE != -1 || SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE != -1">
      <div class="ZMSeparatoreScheda" style="margin-top:5px">    
        <text style="font-weight: bold;">Info</text>
        <hr style="margin-top:5px">
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="width:25%; float:left">
          <text style="font-weight: bold">Tras. a mezzo</text>
          <select :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.TRASPORTO_A_MEZZO">
            <option value="">-</option>
            <option value="MITTENTE">Mittente</option>
            <option value="DESTINATARIO">Destinatario</option>
            <option value="VETTORE">Vettore</option>
          </select>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Causale</text>
          <VUEInputCausali :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" 
                            v-model="SchedaDocumentoDiTrasportoEntrante.Dati.CAUSALE"
                            :Riferimento="SchedaDocumentoDiTrasportoEntrante.Selezionato == 'C'? RiferimentiCausali.Cliente : RiferimentiCausali.Fornitore"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Aspetto beni</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="text" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.ASPETTO_BENI">
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Porto</text>
          
          <input v-if="!VisibilitaOpzioniPortoDDT" :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="text" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PORTO">

          <select v-else class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PORTO" :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1">
            <option :value="''">-</option>
            <option value="Porto franco">Porto franco</option>
            <option value="Porto assegnato">Porto assegnato</option>
          </select>
        
        </div>
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="width:25%; float:left">
            <text style="font-weight: bold">Numero colli</text>
            <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="number" min="1" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NUMERO_COLLI">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Peso in Kg.</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="number" min="0" step="0.01" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.PESO">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Data ritiro</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="date" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.DATA_INIZIO">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="width:24%; float:left">
          <text style="font-weight: bold">Ora ritiro</text>
          <input :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" type="time" class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.ORA_INIZIO">
        </div>
      </div>
      <div style="clear:both"></div>
      <div class="ZMSeparatoreScheda" style="margin-top:5px">    
        <text style="font-weight: bold;">Note</text>
      </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold">Note</text>
        <textarea :readonly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1" 
                  style="resize:none" 
                  type="text" 
                  @input="SchedaDocumentoDiTrasportoEntrante.CheckDimensioniNote" 
                  :style="{height : SchedaDocumentoDiTrasportoEntrante.AltezzaTextAreaNote? SchedaDocumentoDiTrasportoEntrante.AltezzaTextAreaNote : '34px'}" 
                  class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.NOTE"/>
      </div>

      <div v-if="ListaMagazzini.length > 1">
        <div style="clear:both"></div>
        <div class="ZMSeparatoreScheda" style="margin-top:5px">    
          <text style="font-weight: bold;">Magazzini</text>
        </div>
        <div class="ZMNuovaRigaScheda">
          <text style="font-weight: bold">Magazzino</text>
          <select class="form-control" v-model="SchedaDocumentoDiTrasportoEntrante.Dati.ID_MAGAZZINO"
                  :disabled="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1"
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
      <VUEVociDocumentiNonEconomici :SchedaVociDocumentiNonEconomici = "SchedaDocumentoDiTrasportoEntrante.SchedaVociDocumentoDiTrasportoEntrante"
                                    NomeCampoDocumento="ID_DDT"
                                    :IsSchedaDDTentrante="true"
                                    @onChange="OnVociDocumentoDiTrasportoChange"
                                    :IdCliente="SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE"
                                    :ReadOnly="SchedaDocumentoDiTrasportoEntrante.Dati.ID_FATTURA != -1"/>    
    </div>
    <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE O UN FORNITORE NELLA DDT</div>

   </div>

   <div  v-if="Tabs.ActiveTab == 'Allegati'">
    <VUEAllegati :SchedaAllegati="SchedaDocumentoDiTrasportoEntrante.SchedaAllegati" 
                 NomeCampoModello="DocumentiDiTrasporto"
                 @onChange="OnAllegatiChange" />
  </div>
</div>
</template>


<script>
import VUEModalButtonRecapitiFiliali from '@/components/VUEModalButtonRecapitiFiliali.vue'
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES, RIFERIMENTO_CAUSALI } from '@/SystemInformation.js'
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEVociDocumentiNonEconomici, {TSchedaVociDocumentiNonEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiNonEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { ID_NODO_DDT_ENTRANTE_FORNITORE, ID_NODO_DDT_ENTRANTE } from '@/NodiVuoti'


export class TSchedaDocumentoDiTrasportoEntrante extends TSchedaGenerica
{
  AltezzaTextAreaNote = null

  CheckDimensioniNote()
  {
    let NrRigheNote          = this.Dati.NOTE != null?            this.Dati.NOTE.split("\n").length : 1; 
    this.AltezzaTextAreaNote = NrRigheNote          <= 1 ? '34px' : 
                               (NrRigheNote          > 7 ? (6 * 22 + 10) + 'px' : (NrRigheNote * 22 + 10) + 'px')
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


  AssignSchedaFromPreventivo(Scheda)
  {
    this.BloccoWatchIdCliente        = true
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
    this.Dati.NOTE                   = Scheda.Dati.NOTE
    this.Dati.CAUSALE                = Scheda.Dati.CAUSALE
    this.Dati.NAZIONE_DESTINAZIONE   = Scheda.Dati.NAZIONE_DESTINAZIONE
    this.Dati.NAZIONE_EM_PIVA        = Scheda.Dati.NAZIONE_EM_PIVA
    this.SchedaVociDocumentoDiTrasportoEntrante.CopyData(Scheda.SchedaVociPreventivo)

    this.CollegaDDTAlPreventivo(Scheda.Chiave)
  }

  CollegaDDTAlPreventivo(ChiavePreventivo)
  {
    this.ChiavePreventivo = ChiavePreventivo
  }

  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)
     this.SchedaVociDocumentoDiTrasportoEntrante = new TSchedaVociDocumentiNonEconomici()
     this.SchedaVociDocumentoDiTrasportoEntrante.ClearVociDocumentiNonEconomici()
  
  }

  CanRecord()
  {
    return  (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            (this.Dati.CODICE_FISCALE == '' || 
              TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.Dati.DATA_DDT != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            (this.Dati.ID_CLIENTE != -1 || this.Dati.ID_FORNITORE != -1) &&
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaVociDocumentoDiTrasportoEntrante.AllDataOk()
  }

  DatiEliminabili()
  {
    return true;
  }

  Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                      {
                                        Query     : "EliminaVoceTramiteDDT",
                                        Parametri : { CHIAVE_DDT : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaAllegatoTramiteDDT",
                                        Parametri : { CHIAVE_DDT : this.Chiave }
                                      },
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_DDT : this.Chiave }
                                      }
                                    ]};
      this.AdvQuery.PostSQL('DocumentiDiTrasportoEntranti',ObjQuery,function()
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
        var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                                   Query     : this.IsNuovo() ? "Inserisci" : "Modifica",
                                   Parametri : {
                                                  CHIAVE                    : this.IsNuovo() ? undefined : Self.Chiave, 
                                                  NUMERO_DDT                : (this.Dati.NUMERO_DDT == null || this.Dati.NUMERO_DDT == undefined) ? -1 : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NUMERO_DDT),
                                                  ID_CLIENTE                : this.Dati.ID_CLIENTE == -1? null : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_CLIENTE),
                                                  ID_FORNITORE              : this.Dati.ID_FORNITORE == -1? null : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_FORNITORE),
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(this.Dati.RAGIONE_SOCIALE),
                                                  INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_FATTURAZIONE),
                                                  NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_FATTURAZIONE),
                                                  PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_FATTURAZIONE),
                                                  COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_FATTURAZIONE),
                                                  CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_FATTURAZIONE),
                                                  DATA_DDT                  : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA_DDT),
                                                  TRASPORTO_A_MEZZO         : TSchedaGenerica.PrepareForRecordString(this.Dati.TRASPORTO_A_MEZZO, true),
                                                  ASPETTO_BENI              : TSchedaGenerica.PrepareForRecordString(this.Dati.ASPETTO_BENI),
                                                  NUMERO_COLLI              : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NUMERO_COLLI),
                                                  PORTO                     : TSchedaGenerica.PrepareForRecordString(this.Dati.PORTO),
                                                  ORA_INIZIO                : this.Dati.ORA_INIZIO == ''? null : TSchedaGenerica.PrepareForRecordString(this.Dati.ORA_INIZIO),
                                                  DATA_INIZIO               : this.Dati.DATA_INIZIO == ''? null : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA_INIZIO),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(this.Dati.PARTITA_IVA),
                                                  CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CODICE_FISCALE),
                                                  NAZIONE_DESTINAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_DESTINAZIONE),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_EM_PIVA),
                                                  INDIRIZZO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_DESTINAZIONE),
                                                  NR_CIVICO_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_DESTINAZIONE),
                                                  DESTINAZIONE              : TSchedaGenerica.PrepareForRecordString(this.Dati.DESTINAZIONE),
                                                  PROVINCIA_DESTINAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_DESTINAZIONE),
                                                  COMUNE_DESTINAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_DESTINAZIONE),
                                                  CAP_DESTINAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_DESTINAZIONE),
                                                  NOTE                      : TSchedaGenerica.PrepareForRecordString(this.Dati.NOTE),
                                                  PESO                      : TSchedaGenerica.PrepareForRecordInteger(this.Dati.PESO),
                                                  STATO_DESTINAZIONE        : TSchedaGenerica.PrepareForRecordString(this.Dati.STATO_DESTINAZIONE),
                                                  NON_FATTURARE             : TSchedaGenerica.PrepareForRecordString(this.Dati.NON_FATTURARE),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CAUSALE),
                                                  ID_MAGAZZINO              : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_MAGAZZINO),
                                                  NUMERO_DOCUMENTO_ENTRANTE : TSchedaGenerica.PrepareForRecordString(this.Dati.NUMERO_DOCUMENTO_ENTRANTE),
                                               }
                                 });
                                 
        this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_DDT')
        this.SchedaVociDocumentoDiTrasportoEntrante.PrepareQueryParameters(ObjQuery.Operazioni,'ID_DDT')

        if(Self.ChiavePreventivo)
        {
          ObjQuery.Operazioni.push({
                                  Query     : "CollegaDDTAlPreventivo",
                                  Parametri : {
                                                CHIAVE_DDT   : undefined,
                                                CHIAVE_PREVENTIVO : Self.ChiavePreventivo
                                              }
                                });
        }

        this.AdvQuery.PostSQL('DocumentiDiTrasportoEntranti',ObjQuery,function(Response)
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

   CaricaRiassunto(Riassunto)
   {
      this.Chiave                   = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociDocumentoDiTrasportoEntrante.SetIdDocumento(this.Chiave)
      if(Riassunto.ID_CLIENTE == '' || Riassunto.ID_CLIENTE == -1)
        this.Dati.ID_FORNITORE      = Riassunto.ID_FORNITORE
      else 
      {
         if(Riassunto.ID_FORNITORE == '' || Riassunto.ID_FORNITORE == null)
            this.Dati.ID_CLIENTE        = Riassunto.ID_CLIENTE;
      }
      this.Dati.ID_FATTURA          = Riassunto.ID_FATTURA == null || Riassunto.ID_FATTURA == undefined ? -1 : Riassunto.ID_FATTURA 
      this.Dati.ANNO_DDT            = Riassunto.ANNO_DDT
      if(Riassunto.NUMERO_DDT != null && Riassunto.NUMERO_DDT != undefined)
          this.Dati.NUMERO_DDT      = Riassunto.NUMERO_DDT;
      else this.Dati.NUMERO_DDT = -1;

      this.Dati.RAGIONE_SOCIALE     = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.CreateSnapshot();
  }

  AssignSchedaCliente(Scheda)
  {
    this.Dati.ID_CLIENTE             = Scheda.Chiave
  }

  GetDescrizione()
  {
      if(this.ANNO_DDT == -1)
         return ('Avviso DDT');
      else return ('DDT ' + this.Dati.NUMERO_DDT + '/' + this.Dati.ANNO_DDT);
  }
   
    Clear()
    {
      this.SchedaAllegati.AssignDati([])
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociDocumentoDiTrasportoEntrante.AssignDati([],false,'ID_DDT')
      this.SchedaVociDocumentoDiTrasportoEntrante.SetIdDocumento(-1)
      this.Dati = {
                      NUMERO_DDT                    : -1,
                      ID_CLIENTE                    : -1,
                      ID_FORNITORE                  : -1,
                      DATA_DDT                      : TZDateFunct.DateInHTMLInputFormat(new Date()),
                      RAGIONE_SOCIALE               : '',
                      INDIRIZZO_FATTURAZIONE        : '',
                      NR_CIVICO_FATTURAZIONE        : '',
                      COMUNE_FATTURAZIONE           : '',
                      PROVINCIA_FATTURAZIONE        : -1,
                      CAP_FATTURAZIONE              : '',
                      TRASPORTO_A_MEZZO             : '',
                      ASPETTO_BENI                  : '',
                      NUMERO_COLLI                  : 1,
                      PORTO                         : '',
                      ORA_INIZIO                    : '',
                      DATA_INIZIO                   : '',
                      PARTITA_IVA                   : '',
                      CODICE_FISCALE                : '',
                      NAZIONE_EM_PIVA               : SystemInformation.Configurazioni.StatoItaliano,
                      NAZIONE_DESTINAZIONE          : SystemInformation.Configurazioni.StatoItaliano,
                      COMUNE_DESTINAZIONE           : '',
                      PROVINCIA_DESTINAZIONE        : -1,
                      CAP_DESTINAZIONE              : '',
                      INDIRIZZO_DESTINAZIONE        : '',
                      NR_CIVICO_DESTINAZIONE        : '',
                      DESTINAZIONE                  : '',
                      PESO                          : 0,
                      STATO_DESTINAZIONE            : '',
                      NON_FATTURARE                 : '',
                      NOTE                          : '',
                      CAUSALE                       : -1,
                      ID_FATTURA                    : -1,
                      ID_MAGAZZINO                  : SystemInformation.Configurazioni.Impostazioni.MAGAZZINO,
                      NUMERO_DOCUMENTO_ENTRANTE     : '',
                      // Dati allegati
                      ModificaTabellaAllegati       : false,
                      ModificaTabellaVoci           : false,
                  }
      this.ChiaveFattura = null
      this.NumeroFattura = null
      this.Selezionato   = 'C'
      super.Clear();

    }

    Disponi(OnSuccess)
    {
      var Self = this;
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',{ CHIAVE : Self.Chiave, ChiaveDDT : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo        = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayAllegati    = Self.AdvQuery.FindResults(Results,"AllegatiDDT");
                                            let ArrayVociDocumentoDiTrasportoEntrante = Self.AdvQuery.FindResults(Results,"VociDDT");
                                            if(ArrayInfo != undefined)
                                            {
                                              if(ArrayAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayAllegati,'DDT')
                                              if(ArrayVociDocumentoDiTrasportoEntrante.length != 0)
                                                Self.SchedaVociDocumentoDiTrasportoEntrante.AssignDati(ArrayVociDocumentoDiTrasportoEntrante,
                                                                                               false,
                                                                                               'ID_DDT')
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = { 
                                                              NUMERO_DDT                    : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_DDT),
                                                              ID_CLIENTE                    : ArrayInfo[0].ID_CLIENTE == undefined? -1 : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_CLIENTE),
                                                              ID_FORNITORE                  : ArrayInfo[0].ID_FORNITORE == undefined? -1 :TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_FORNITORE),
                                                              ANNO_DDT                      : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ANNO_DDT),
                                                              RAGIONE_SOCIALE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              INDIRIZZO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                              NR_CIVICO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                              COMUNE_FATTURAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                              PROVINCIA_FATTURAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                              CAP_FATTURAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                              DATA_DDT                      : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_DDT),
                                                              TRASPORTO_A_MEZZO             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].TRASPORTO_A_MEZZO, true),
                                                              PARTITA_IVA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              CODICE_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              NAZIONE_DESTINAZIONE          : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_DESTINAZIONE),
                                                              NAZIONE_EM_PIVA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                                                              INDIRIZZO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_DESTINAZIONE),
                                                              NR_CIVICO_DESTINAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_DESTINAZIONE),
                                                              COMUNE_DESTINAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_DESTINAZIONE),
                                                              PROVINCIA_DESTINAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_DESTINAZIONE),
                                                              CAP_DESTINAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_DESTINAZIONE),
                                                              DESTINAZIONE                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESTINAZIONE),
                                                              ASPETTO_BENI                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ASPETTO_BENI),
                                                              NOTE                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                              CAUSALE                       : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CAUSALE),
                                                              NUMERO_COLLI                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_COLLI),
                                                              PORTO                         : SystemInformation.AccessRights.VisibilitaOpzioniPortoDDT()? 
                                                                                              TSchedaGenerica.DisponiFromString(ArrayInfo[0].PORTO).charAt(0).toUpperCase() + TSchedaGenerica.DisponiFromString(ArrayInfo[0].PORTO).slice(1).toLowerCase() : 
                                                                                              TSchedaGenerica.DisponiFromString(ArrayInfo[0].PORTO),
                                                              ORA_INIZIO                    : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ORA_INIZIO),
                                                              DATA_INIZIO                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA_INIZIO),
                                                              FILIALE                       : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].FILIALE),
                                                              PESO                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PESO),
                                                              STATO_DESTINAZIONE            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].STATO_DESTINAZIONE),
                                                              NON_FATTURARE                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NON_FATTURARE),
                                                              ID_FATTURA                    : (ArrayInfo[0].ID_FATTURA == null || ArrayInfo[0].ID_FATTURA == undefined) ? -1 : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_FATTURA),
                                                              ID_MAGAZZINO                  : (ArrayInfo[0].ID_MAGAZZINO == null || ArrayInfo[0].ID_MAGAZZINO == undefined) 
                                                                                              ? SystemInformation.Configurazioni.Impostazioni.MAGAZZINO 
                                                                                              : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_MAGAZZINO),
                                                              NUMERO_DOCUMENTO_ENTRANTE     : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NUMERO_DOCUMENTO_ENTRANTE),
                                                              ModificaTabellaAllegati       : false,
                                                              ModificaTabellaVoci           : false,
                                                }
                                                if(Self.Dati.ID_CLIENTE != -1)
                                                  Self.Selezionato = 'C'
                                                else Self.Selezionato = 'F'
                                                if(Self.Dati.DATA_DDT != '')
                                                  Self.Dati.ANNO_DDT = new Date(Self.Dati.DATA_DDT).getFullYear();
                                                else Self.Dati.ANNO_DDT = -1;
                                                Self.NumeroFattura = ArrayInfo[0].NUMERO_FATTURA
                                                Self.ChiaveFattura = ArrayInfo[0].CHIAVE_FATTURA
                                                Self.CreateSnapshot();
                                                Self.CheckDimensioniNote()
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('DDT eliminata')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio della DDT');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
    }

    GetFiltriAbilitati(OnSuccess)
    {
      var Anno = new Date(this.Dati.DATA_DDT)
      if(this.Dati.ID_CLIENTE != -1)
      {
        OnSuccess([{
                    Name : DASHBOARD_FILTER_TYPES.Clienti,
                    Positions : [
                                    this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                    this.Dati.ID_CLIENTE,
                                    ID_NODO_DDT_ENTRANTE,
                                    Anno.getFullYear(),
                                    this.Chiave
                                ]
                }])
      }
      else
      {
        if(this.Dati.ID_FORNITORE != -1)
        {
          OnSuccess([{
                      Name : DASHBOARD_FILTER_TYPES.Fornitore,
                      Positions : [
                                      this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                      this.Dati.ID_FORNITORE,
                                      ID_NODO_DDT_ENTRANTE_FORNITORE,
                                      Anno.getFullYear(),
                                      this.Chiave
                                  ]
                  }])        
        }
      }
    }

    GetClassName()
    {
      return 'TSchedaDocumentoDiTrasportoEntrante';
    }

    GetImageIndex()
   {
      return "DDT_entrante2.png";
   }

    DatiStampabili()
    {
      return true
    }

    Stampa(OnSuccess)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaDDTEntrante', { Chiave : this.Chiave },
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
  
}

export default 
{
   components : 
   {
      VUEModalButtonRecapitiFiliali,
      VUEInputProvince,
      VUEInputCAP,
      VUEInputClienti,
      VUEInputPartitaIVA,
      VUEAllegati,
      VUEVociDocumentiNonEconomici,
      VUEInputFornitore,
      VUEInputCodiceFiscale,
      VUEInputCausali,
      VUEInputNazioni,
   },
   data()
   {
     return { 
              TotaleNota          : 0, 
              MenuNuovo           :  [],
              LsStatiDDT          : SystemInformation.GetLsStatoDDT(),
              DeveloperMode       : SystemInformation.DeveloperMode,
              StatoItaliano       : SystemInformation.Configurazioni.StatoItaliano,
              RiferimentiCausali  : RIFERIMENTO_CAUSALI,
              VisibilitaOpzioniPortoDDT : SystemInformation.AccessRights.VisibilitaOpzioniPortoDDT(),
              ListaMagazzini      : SystemInformation.Configurazioni.Magazzini,
              
              Tabs               : {
                                      ActiveTab : 'Documento di trasporto entrante',
                                      Tabs      : [
                                                    {
                                                      Caption : 'Documento di trasporto entrante',
                                                      Id      : 'Documento di trasporto entrante'
                                                    },
                                                    {
                                                      Caption : 'Voci DDT e info',
                                                      Id      : 'VociDDT'
                                                    },
                                                    {
                                                    Caption: 'Allegati',
                                                    Id     : 'Allegati'
                                                    }
                                                  ]
                                    },
            }
   },
   props : ['SchedaDocumentoDiTrasportoEntrante'],
   emits : ['onClickNuovaFattura'],
   computed :
   {
     CurrentCliente()
     {
       return this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE
     },

     CurrentFornitore()
     {
      return this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE
     },

    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },
   },
   watch:
   {
    CurrentCliente:
     {
        immediate : true,
        handler(NewValue,OldValue)
        {
            var Self = this;
            if(this.SchedaDocumentoDiTrasportoEntrante.DatiModificati()) 
              if(this.SchedaDocumentoDiTrasportoEntrante.BloccoWatchIdCliente == undefined) 
                if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
                  SystemInformation.AdvQuery.GetSQL('Clienti', { CHIAVE : NewValue },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectCliente");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NR_CIVICO_FATTURAZIONE   = ArrayInfo[0].NR_CIVICO_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;
                                                
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_DESTINAZIONE   = ArrayInfo[0].INDIRIZZO_SPEDIZIONE != ''? ArrayInfo[0].INDIRIZZO_SPEDIZIONE : ArrayInfo[0].INDIRIZZO_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NR_CIVICO_DESTINAZIONE   = ArrayInfo[0].NR_CIVICO_SPEDIZIONE != ''? ArrayInfo[0].NR_CIVICO_SPEDIZIONE : ArrayInfo[0].NR_CIVICO_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_DESTINAZIONE      = ArrayInfo[0].COMUNE_SPEDIZIONE != ''? ArrayInfo[0].COMUNE_SPEDIZIONE : ArrayInfo[0].COMUNE_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.DESTINAZIONE             = ArrayInfo[0].PRESSO != ''? ArrayInfo[0].PRESSO : ArrayInfo[0].RAGIONE_SOCIALE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CAP_DESTINAZIONE         = ArrayInfo[0].CAP_SPEDIZIONE != ''?
                                                                                                                ArrayInfo[0].CAP_SPEDIZIONE :
                                                                                                                ArrayInfo[0].CAP_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_DESTINAZIONE     = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE) != -1?
                                                                                                                TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE) : 
                                                                                                                TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA) 
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA          = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA)
                                                
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_FATTURAZIONE  = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE);                                                
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_DESTINAZIONE  = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE) != -1?
                                                                                                               TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE) : 
                                                                                                               TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE)
                                              }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectCliente')
           
        }
     },

     CurrentFornitore:
     {
      immediate : true,
      handler(NewValue,OldValue)
        {
            
            var Self = this;
            if(this.SchedaDocumentoDiTrasportoEntrante.DatiModificati()) 
              if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
                  SystemInformation.AdvQuery.GetSQL('Fornitori', { CHIAVE : NewValue },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"WatchFornitore");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;

                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.INDIRIZZO_DESTINAZIONE   = ArrayInfo[0].INDIRIZZO_SPEDIZIONE != ''? ArrayInfo[0].INDIRIZZO_SPEDIZIONE : ArrayInfo[0].INDIRIZZO_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NR_CIVICO_DESTINAZIONE   = ArrayInfo[0].NR_CIVICO_SPEDIZIONE != ''? ArrayInfo[0].NR_CIVICO_SPEDIZIONE : ArrayInfo[0].NR_CIVICO_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.COMUNE_DESTINAZIONE      = ArrayInfo[0].COMUNE_SPEDIZIONE != ''? ArrayInfo[0].COMUNE_SPEDIZIONE : ArrayInfo[0].COMUNE_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.DESTINAZIONE             = ArrayInfo[0].PRESSO != ''? ArrayInfo[0].PRESSO : ArrayInfo[0].RAGIONE_SOCIALE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.CAP_DESTINAZIONE         = ArrayInfo[0].CAP_SPEDIZIONE != ''?
                                                                                                                ArrayInfo[0].CAP_SPEDIZIONE :
                                                                                                                ArrayInfo[0].CAP_FATTURAZIONE
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_DESTINAZIONE     = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE) != -1?
                                                                                                                TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE) : 
                                                                                                                TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].EMITTENTE_PIVA) 
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.NAZIONE_EM_PIVA          = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].EMITTENTE_PIVA)
                                                
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_FATTURAZIONE  = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE);                                                
                                                Self.SchedaDocumentoDiTrasportoEntrante.Dati.PROVINCIA_DESTINAZIONE  = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE) != -1?
                                                                                                               TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE) : 
                                                                                                               TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE)                                               
                                              }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'WatchFornitore')
           
        }
     },
   },
   methods: 
   {
    OnFilialeSelected(FilialeSelezionata)
    {
      this.SchedaDocumentoDiTrasportoEntrante.AssignDestinazioneFromFiliale(FilialeSelezionata)
    },

    OnAllegatiChange()
    {
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ModificaTabellaAllegati = this.SchedaDocumentoDiTrasportoEntrante.SchedaAllegati.Modificato();
    },

    OnVociDocumentoDiTrasportoChange()
    {
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ModificaTabellaVoci = this.SchedaDocumentoDiTrasportoEntrante.SchedaVociDocumentoDiTrasportoEntrante.Modificato();
    },

    OnClickCopiaDaPartitaIva()
    {
      this.SchedaDocumentoDiTrasportoEntrante.Dati.CODICE_FISCALE = this.SchedaDocumentoDiTrasportoEntrante.Dati.PARTITA_IVA
    },

    OnClickFornitore()
    {
      this.SchedaDocumentoDiTrasportoEntrante.Selezionato       = 'F'
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE = -1
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE   = -1
      this.SchedaDocumentoDiTrasportoEntrante.Dati.CAUSALE      = -1
    },

    OnClickCliente()
    {
      this.SchedaDocumentoDiTrasportoEntrante.Selezionato       = 'C'
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE   = -1
      this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE = -1
      this.SchedaDocumentoDiTrasportoEntrante.Dati.CAUSALE      = -1
    },

    OnNextMenuNuovo(AMenu) 
    {
      if (AMenu.SubMenu != undefined)
          this.MenuNuovo = AMenu.SubMenu;
      else {
          AMenu.OnClick();
          this.MenuNuovo = [];
      }
    },

    OnClickNuovo() 
    {
        var Self = this;
        if (this.MenuNuovo.length == 0)
        { 
            this.MenuNuovo = [
                                {
                                  Caption: "Nuova fattura",
                                  OnClick: function () 
                                  {
                                    Self.$emit('onClickNuovaFattura', Self.SchedaDocumentoDiTrasportoEntrante) 
                                  }
                                }
                              ]
        }
        else this.MenuNuovo = [];
    },

   },                             
    
   
   beforeMount() 
   {
     this.ActiveTab = 'Documento di trasporto entrante'
     if(this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE == null || this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_CLIENTE == -1)
        this.SchedaDocumentoDiTrasportoEntrante.Selezionato = 'F'
     if(this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE == null || this.SchedaDocumentoDiTrasportoEntrante.Dati.ID_FORNITORE == -1)
        this.SchedaDocumentoDiTrasportoEntrante.Selezionato = 'C'
    },

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>