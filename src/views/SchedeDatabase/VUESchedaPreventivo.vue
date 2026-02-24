<template>
  <VUEModalInvioEmail :AttivazionePopup="PopupInviaEmail" 
                      :OggettoEmail="OggettoEmail"
                      :ListaEmailCliente="SchedaPreventivo.ListaEmailCliente"
                      @onClickChiudiModal="AnnullaInvia"
                      @onClickConfermaModal="ConfermaInvia">
  </VUEModalInvioEmail>

 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
    <ul class="nav nav-tabs nav-justified">
      <li v-for="ATab in TabsVisibili" :Key="ATab.Id"
          :class="{ active : ATab.Id == Tabs.ActiveTab }">
        <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
          <img v-if="ATab.Id == 'Conferma'" src="@/assets/images/IconeAlbero/Conferma2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
        </a>
      </li>
    </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'Conferma'">
      <div class="ZMNuovaRigaScheda">
        <div class="col-md-2" style="float:left;padding:0px">&nbsp;
        </div>
        <!-- <img style="float:left;margin-top:13px;margin-left:10px" src="@/assets/images/Confermato.png"> -->
        <div style="padding-top:10px;margin-left:10px;margin-left:50px;margin-top:12px;float:left">
          <div style="float:left;">
            <input type="checkbox" v-model="SchedaPreventivo.Dati.IS_INVIATO"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
             <label style="font-weight: bold;">&nbsp;Conferma inviata</label>
          </div>
        </div>
        <div style="float:left;width:4%;">&nbsp;</div>

        <img style="float:left;cursor:pointer; margin-left:30px; margin-top:5px; width:40px"  
             v-if="CurrentIsPaginaGestioneAnomalie && !SchedaPreventivo.Dati.IS_INVIATO" 
             src="@/assets/images/InvioEmail.png" 
             @click="OnClickInvioEmail"/>

        <div style="float:left;width:4%;">&nbsp;</div>
        
        
        <img style="float:left;cursor:pointer; margin-left:30px; margin-top:5px; width:40px"
              
              v-if="CurrentIsPaginaGestioneAnomalie"
              src="@/assets/images/Stampa.png"
              @click="OnClickStampa"/>

        <div v-if="SchedaPreventivo.Dati.ID_FATTURA != undefined && SchedaPreventivo.Dati.ID_FATTURA != -1" style="float:left;width:15%;margin-left:30px">
          <text style="float:left;font-weight: bold;padding-right: 10px">Corr. alla fattura N.</text>
          <label v-if="SchedaPreventivo.Dati.NUMERO_FATTURA" style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO_FATTURA }}/{{ SchedaPreventivo.Dati.ANNO_FATTURA }}</label>
          <label v-else style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">Avviso fattura</label>
        </div>
        <div style="float:left;width:15%;margin-left:20px" v-if="SchedaPreventivo.Dati.NUMERO_DDT != -1">
          <text style="float:left;font-weight: bold;padding-right: 10px">Corr. al DDT N.</text>
          <label style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO_DDT }}</label>
        </div>
        <div style="float:right;margin-left:10px">
            <text style="font-weight: bold;">Data</text>
            <input type="date" id="input-data" class="form-control" v-model="SchedaPreventivo.Dati.DATA" :disabled="VerificaSeDisabilitare()"/>
            <label v-if="SchedaPreventivo.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:right;margin-left:10px; width:6%">
            <text style="font-weight: bold;">Rev.</text>
            <input type="number" disabled step="0.1" class="form-control" v-model="SchedaPreventivo.Dati.NUMERO_REVISIONE"/>
        </div>
        <div v-if="SchedaPreventivo.Dati.NUMERO_PREVENTIVO != undefined && SchedaPreventivo.Dati.NUMERO_PREVENTIVO != -1" style="float:left;width:10%;margin-left:10px">
            <text style="float:left;font-weight: bold">&nbsp;Corr.al preventivo N.&nbsp;</text>
            <label style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO_PREVENTIVO }}/{{ SchedaPreventivo.Dati.ANNO_PREVENTIVO }} </label>
          </div>
         <div v-if="SchedaPreventivo.Dati.NUMERO != -1" style="float:right;width:13%;">
            <text style="float:left;font-weight: bold;padding-right: 10%">Conferma n.</text>
            <label style="float:left;font-size:15px;width:180px;height:34px;padding-top:6px;padding-left: 14px;" class="ZMLabel">{{ SchedaPreventivo.Dati.NUMERO }}/{{ SchedaPreventivo.Dati.ANNO }}</label>
         </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>      
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Intestatario</text>
        <hr style="margin-top:1px;margin-bottom: -3px">
      </div>
      <div class="col-md-3" style="float:right;margin-top:3px" v-if="!SchedaPreventivo.DatiModificati()">
          <div style="float:right;">
            <div class="btn-group open" >
              <button v-if="this.SchedaPreventivo.Dati.NUMERO_DDT == -1 || this.SchedaPreventivo.Dati.ID_FATTURA == -1"
                      type="button" 
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
              <ul v-if="MenuNuovo.length != 0" class="dropdown-menu pull-right" style="z-index: 9999;">
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

      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:50%">
          <text style="font-weight: bold; float:left; width:100%">Cliente
            <div style="float:right;">
              <label>Ente pubblico&nbsp;</label>
            </div>
            <div style="float:right;margin-right:4px">
              <input type="checkbox" v-model="SchedaPreventivo.Dati.ENTE_PUBBLICO" :disabled="VerificaSeDisabilitare()"/>
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
      </div>

      <VUEModalButtonRecapitiFiliali :styleForButton="'float:right; margin-right: 0px'"
                                     :IdCliente="SchedaPreventivo.Dati.ID_CLIENTE"
                                     @filiale-selected="OnFilialeSelected"/>

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
        <div style="float:left;width:33%">
          <label style="font-weight: bold;">Nazione</label>
          <VUEInputNazioni v-model="SchedaPreventivo.Dati.NAZIONE_DESTINAZIONE" emptyElement="true" :disabled="VerificaSeDisabilitare()"/>
        </div>
        <div style="float:left;width:1%"> &nbsp;</div>
          <div style="float:left; width:5%">
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
          <select :disabled="VerificaSeDisabilitare()" style="float:left;width:100%;margin-right:5px" class="form-control" @change="OnClickNessunDocumento()" v-model="SchedaPreventivo.Dati.DOCUMENTO_CORRELATO">
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
        <text style="font-weight: bold;margin-bottom:-20px">Voci fattura</text>
        <hr style="margin-top:1px">
      </div>
      <div v-if="SchedaPreventivo.Dati.ID_CLIENTE != -1">
        <VUEVociDocumentiEconomici :SchedaVociDocumentiEconomici = "SchedaPreventivo.SchedaVociPreventivo"
                                   :TastoCreaNotaVisibile="false"
                                   :IsSchedaPreventivo="true"
                                   :IdCliente="SchedaPreventivo.Dati.ID_CLIENTE"
                                   @onChange="OnVociPreventivoChange"
                                   :Disabilitato="VerificaSeDisabilitare()"/>
      </div>
    
      <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE NEL PREVENTIVO</div>                               
   </div>

   <div v-if="Tabs.ActiveTab == 'LogPreventivo'" style="height:50%">
      <VUELogDocumentiEconomici :SchedaLogDocumentiEconomici="SchedaPreventivo.SchedaLogPreventivo"/>
   </div>

   <div v-if="Tabs.ActiveTab == 'Allegati'">
    <VUEAllegati :SchedaAllegati="SchedaPreventivo.SchedaAllegati" 
                  NomeCampoModello="Preventivi"
                 @onChange="OnAllegatiChange" 
                 :disabled="VerificaSeDisabilitare()"/>
  </div>

</div>
</template>


<script>
import VUEModalButtonRecapitiFiliali from '@/components/VUEModalButtonRecapitiFiliali';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, FATT_ELE_ESIGIBILITA_IVA, 
                            DASHBOARD_FILTER_TYPES, 
                            DOCUMENTO_CORRELATO,
                            RUOLI } from '@/SystemInformation.js'
import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
import VUEVociDocumentiEconomici, {TSchedaVociDocumentiEconomici, TSingoloVociDocumentiEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { ID_NODO_CONFERME_PREVENTIVI } from '@/NodiVuoti'
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
import VUELogDocumentiEconomici, { TSchedaLogDocumentiEconomici } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogDocumentiEconomici.vue';
import VUEModalInvioEmail from '@/views/SchedeDatabase/ComponentMultiScheda/VUEModalInvioEmail.vue';

export class TSchedaPreventivo extends TSchedaGenerica
{
  
  constructor (AdvQuery) 
  {
    super(AdvQuery)
    this.__TriggerOnPreventivoConfermato = 0;
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
      if(Scheda.Dati.SPESE_INCASSO_DESCRIZIONE != '' && Scheda.Dati.SPESE_INCASSO_SUGGERITE != 0)
        this.SchedaVociPreventivo.SetSpesaIncasso(Scheda.Dati.SPESE_INCASSO_DESCRIZIONE, Scheda.Dati.SPESE_INCASSO_SUGGERITE * 100,
                                                  Scheda.Dati.ContoCorrente.CONTO_RIBA)
      this.SchedaVociPreventivo.SetDatiCliente(Scheda.Dati.IVA_SUGGERITA_CLIENTE,
                                               Scheda.Dati.RITENUTA,
                                               Scheda.Dati.NATURA_PAGAMENTO)
  }

  AssignPreventivoMultiparametrico(SchedaPreventivoMultiparametrico)
  {
    this.Dati.ID_CLIENTE                = SchedaPreventivoMultiparametrico.Dati.ID_CLIENTE
    this.Dati.RAGIONE_SOCIALE           = SchedaPreventivoMultiparametrico.Dati.RAGIONE_SOCIALE
    this.Dati.CODICE_FISCALE            = SchedaPreventivoMultiparametrico.Dati.CODICE_FISCALE
    this.Dati.INDIRIZZO_FATTURAZIONE    = SchedaPreventivoMultiparametrico.Dati.INDIRIZZO_FATTURAZIONE
    this.Dati.NR_CIVICO_FATTURAZIONE    = SchedaPreventivoMultiparametrico.Dati.NR_CIVICO_FATTURAZIONE
    this.Dati.CAP_FATTURAZIONE          = SchedaPreventivoMultiparametrico.Dati.CAP_FATTURAZIONE
    this.Dati.COMUNE_FATTURAZIONE       = SchedaPreventivoMultiparametrico.Dati.COMUNE_FATTURAZIONE
    this.Dati.PROVINCIA_FATTURAZIONE    = SchedaPreventivoMultiparametrico.Dati.PROVINCIA_FATTURAZIONE
    this.Dati.CAP_DESTINAZIONE          = SchedaPreventivoMultiparametrico.Dati.CAP_DESTINAZIONE
    this.Dati.DESTINAZIONE              = SchedaPreventivoMultiparametrico.Dati.DESTINAZIONE
    this.Dati.COMUNE_DESTINAZIONE       = SchedaPreventivoMultiparametrico.Dati.COMUNE_DESTINAZIONE
    this.Dati.PROVINCIA_DESTINAZIONE    = SchedaPreventivoMultiparametrico.Dati.PROVINCIA_DESTINAZIONE
    this.Dati.INDIRIZZO_DESTINAZIONE    = SchedaPreventivoMultiparametrico.Dati.INDIRIZZO_DESTINAZIONE
    this.Dati.NR_CIVICO_DESTINAZIONE    = SchedaPreventivoMultiparametrico.Dati.NR_CIVICO_DESTINAZIONE
    this.Dati.ID_ZONA_SPEDIZIONE        = SchedaPreventivoMultiparametrico.Dati.ID_ZONA_SPEDIZIONE == 0 ? -1 : SchedaPreventivoMultiparametrico.Dati.ID_ZONA_SPEDIZIONE
    this.Dati.PARTITA_IVA               = SchedaPreventivoMultiparametrico.Dati.PARTITA_IVA
    this.Dati.COND_PAGAMENTO            = SchedaPreventivoMultiparametrico.Dati.COND_PAGAMENTO
    this.Dati.PEC                       = SchedaPreventivoMultiparametrico.Dati.PEC
    this.Dati.COD_ENTE_SDI              = SchedaPreventivoMultiparametrico.Dati.COD_ENTE_SDI
    this.Dati.ENTE_PUBBLICO             = SchedaPreventivoMultiparametrico.Dati.ENTE_PUBBLICO
    this.Dati.COD_UFFICIO_DEST          = SchedaPreventivoMultiparametrico.Dati.COD_UFFICIO_DEST
    this.Dati.NAZIONE_DESTINAZIONE      = SchedaPreventivoMultiparametrico.Dati.NAZIONE_DESTINAZIONE
    this.Dati.NAZIONE_EM_PIVA           = SchedaPreventivoMultiparametrico.Dati.NAZIONE_EM_PIVA
    this.Dati.DATA                      = TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date());
    this.Dati.NOTE                      = SchedaPreventivoMultiparametrico.Dati.NOTE
    this.Dati.ESIGIBILITA_IVA           = SchedaPreventivoMultiparametrico.Dati.ESIGIBILITA_IVA
    this.Dati.CAUSALE                   = SchedaPreventivoMultiparametrico.Dati.CAUSALE
    this.Dati.CUP                       = SchedaPreventivoMultiparametrico.Dati.CUP
    this.Dati.CIG                       = SchedaPreventivoMultiparametrico.Dati.CIG
    this.Dati.DOCUMENTO_CORRELATO       = SchedaPreventivoMultiparametrico.Dati.DOCUMENTO_CORRELATO
    this.Dati.CONVENZIONE               = SchedaPreventivoMultiparametrico.Dati.CONVENZIONE
    this.Dati.VOCE_DI_RIFERIMENTO       = SchedaPreventivoMultiparametrico.Dati.VOCE_DI_RIFERIMENTO
    this.Dati.DOCUMENTO_NR              = SchedaPreventivoMultiparametrico.Dati.DOCUMENTO_NR
    this.Dati.DATA_DOCUMENTO            = SchedaPreventivoMultiparametrico.Dati.DATA_DOCUMENTO
    this.Dati.CONVENZIONE               = SchedaPreventivoMultiparametrico.Dati.CONVENZIONE
    this.Dati.PROGRAMMA_PROSSIMA_VISITA = SchedaPreventivoMultiparametrico.Dati.PROGRAMMA_PROSSIMA_VISITA
    this.Dati.ID_FILIALE                = SchedaPreventivoMultiparametrico.Dati.ID_FILIALE
    this.Dati.NUMERO_REVISIONE          = SchedaPreventivoMultiparametrico.Dati.NUMERO_REVISIONE
    this.Dati.NOME_FILIALE              = SchedaPreventivoMultiparametrico.Dati.NOME_FILIALE

    this.SchedaVociPreventivo.AssignDati( [],  //(SchedaPreventivoMultiparametrico.LsSoluzioniAccettate,
                                         SchedaPreventivoMultiparametrico.SchedaVociPreventivo.Dati.IvaSuggerita, 
                                         SchedaPreventivoMultiparametrico.SchedaVociPreventivo.Dati.RitenutaAcconto * 10,
                                         'ID_PREVENTIVO',
                                         -1)

    this.Dati.ContoCorrente          = {
                                          IBAN              : SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.IBAN) : TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.IBAN),
                                          ID_CONTO_CORRENTE : SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromListIndex(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE) : null,
                                          BANCA             : SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.BANCA) : TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.BANCA),
                                          CONTO_RIBA        : (SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null || SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != -1)? false : true,
                                          NR_CONTO          : TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.NR_CONTO),
                                          SWIFT             : SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.SWIFT) : TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.SWIFT),
                                          BIC               : SchedaPreventivoMultiparametrico.Dati.ContoCorrente.ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.ContoCorrente.BIC) : TSchedaGenerica.DisponiFromString(SchedaPreventivoMultiparametrico.Dati.BIC),
                                      },

    this.SchedaVociPreventivo.SetSoluzioniApprovate(SchedaPreventivoMultiparametrico.LsSoluzioniAccettate)

    this.CollegaPreventivoAllaConferma = SchedaPreventivoMultiparametrico
  }
  
  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)
     this.SchedaVociPreventivo = new TSchedaVociDocumentiEconomici()
     this.SchedaVociPreventivo.ClearVociDocumentiEconomici()
     this.SchedaLogPreventivo = new TSchedaLogDocumentiEconomici()
     this.SchedaLogPreventivo.ClearLogDocumentiEconomici()
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
            this.SchedaVociPreventivo.VociPresenti() &&
            (!this.Dati.PROGRAMMA_PROSSIMA_VISITA || this.Dati.ID_FILIALE != -1)
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;

    if(this.Dati.ID_FATTURA != -1 || this.Dati.ID_DDT_USCENTI != -1 || this.Dati.ID_DDT_ENTRANTI != -1)
      return false;
    return true;
  }

  Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                      {
                                        Query     : "EliminaVoceTramitePreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaLogTramitePreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaAllegatoTramitePreventivo",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaCollegamentiAiPreventiviMultiparametrici",
                                        Parametri : { CHIAVE : this.Chiave }
                                      },
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_PREVENTIVO : this.Chiave }
                                      }
                                    ]}
      this.AdvQuery.PostSQL('Preventivi',ObjQuery,function()
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
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.SchedaVociPreventivo.RitornaRitenutaAcconto()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(this.Dati.ESIGIBILITA_IVA),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CAUSALE),
                                                  IS_INVIATO                : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IS_INVIATO),
                                                  ID_ZONA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_ZONA_SPEDIZIONE),
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

                                                  COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_UFFICIO_DEST),
                                                  NUMERO_REVISIONE          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NUMERO_REVISIONE),
                                                  
                                                  PROGRAMMA_PROSSIMA_VISITA : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.PROGRAMMA_PROSSIMA_VISITA),
                                                  ID_FILIALE                : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_FILIALE),
                                                  NOME_FILIALE              : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NOME_FILIALE)
                                               }
                                 });
        this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_PREVENTIVO')
        this.SchedaVociPreventivo.PrepareQueryParameters(ObjQuery.Operazioni, 'ID_PREVENTIVO')

        if(this.CollegaPreventivoAllaConferma != undefined)
        {
            ObjQuery.Operazioni.push({
                                        Query     : "ModificaPrimoPreventivo",
                                        Parametri : {
                                                      CHIAVE_PREVENTIVO : this.CollegaPreventivoAllaConferma.Chiave,
                                                      PRIMO_PREVENTIVO  : this.CollegaPreventivoAllaConferma.PRIMO_PREVENTIVO,
                                                    }
            })
            
            ObjQuery.Operazioni.push({
                                        Query     : "CollegaConfermaAlPreventivo",
                                        Parametri : {
                                                      ID_PREVENTIVO_MULTI : this.CollegaPreventivoAllaConferma.Chiave,
                                                      CHIAVE              : undefined
                                                    }
            })
        }

        this.AdvQuery.PostSQL('Preventivi',ObjQuery,function(Response)
         {
            if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
              Self.SchedaAllegati.DeleteFileDaEliminare()
              
            ObjQuery = {};
            if(Self.Chiave == -1)
                Self.Chiave = Response.NewKey1;
            Self.Dati.ModificaTabellaAllegati = false;
            Self.Dati.ModificaTabellaVoci     = false
            Self.CreateSnapshot();
            
            if(OnSuccess != undefined)
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
      this.Dati.ID_FATTURA                = TSchedaGenerica.DisponiFromListIndex(Riassunto.ID_FATTURA)
      if(Riassunto.NUMERO != null && Riassunto.NUMERO != undefined)
          this.Dati.NUMERO = Riassunto.NUMERO;
      else this.Dati.NUMERO = -1;
      this.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.Dati.PROGRAMMA_PROSSIMA_VISITA = TSchedaGenerica.DisponiFromBoolean(Riassunto.PROGRAMMA_PROSSIMA_VISITA);
      this.Dati.NUMERO_DDT                = TSchedaGenerica.DisponiFromListIndex(Riassunto.NUMERO_DDT);
      this.Dati.ID_DDT_ENTRANTI           = TSchedaGenerica.DisponiFromListIndex(Riassunto.ID_DDT_ENTRANTI);
      this.Dati.ID_DDT_USCENTI            = TSchedaGenerica.DisponiFromListIndex(Riassunto.ID_DDT_USCENTI);
      this.Dati.NUMERO_PREVENTIVO         = TSchedaGenerica.DisponiFromListIndex(Riassunto.NUMERO_PREVENTIVO);
      this.Dati.ANNO_PREVENTIVO           = TSchedaGenerica.DisponiFromListIndex(Riassunto.ANNO_PREVENTIVO);
      this.CreateSnapshot();
   }

   GetDescrizione()
    {
      return('Conferma d\'Ordine '+ this.Dati.NUMERO + '/' + this.Dati.ANNO)
    }
   
    Clear()
    {
      this.SchedaAllegati.AssignDati([])
      this.SchedaLogPreventivo.AssignDati([])
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociPreventivo.SetIdDocumento(-1)
      this.SchedaVociPreventivo.AssignDati([], SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA, SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO,'ID_PREVENTIVO')
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
                      ID_ZONA_SPEDIZIONE            : -1,
                      DESTINAZIONE                  : '',
                      INDIRIZZO_DESTINAZIONE        : '',
                      NR_CIVICO_DESTINAZIONE        : '',
                      COND_PAGAMENTO                : null,
                      NOTE                          : '',
                      CAUSALE                       : -1,
                      ESIGIBILITA_IVA               : '',
                      IS_INVIATO                    : false,
                      ID_FATTURA                    : -1,
                      NUMERO_FATTURA                : -1,
                      ANNO_FATTURA                  : -1,
                      NUMERO_PREVENTIVO             : -1,
                      ANNO_PREVENTIVO               : -1,
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
                      ContoCorrente                 : { 
                                                        IBAN              : '',
                                                        BANCA             : '',
                                                        ID_CONTO_CORRENTE : -1,
                                                        NR_CONTO          : '',
                                                        CONTO_RIBA        : false,
                                                        BIC               : '',
                                                        SWIFT             : ''
                                                      },
                      NUMERO_DDT                    : -1,
                      ID_DDT_ENTRANTI               : -1,
                      ID_DDT_USCENTI                : -1,
                      // Dati allegati
                      ModificaTabellaAllegati       : false,
                      ModificaTabellaVoci           : false,
                      DatoPerEmit                   : false,
                      NUMERO_REVISIONE              : 1,
                      ListaAltreLavorazioni         : []
                  }
      super.Clear();

    }

    Disponi(OnSuccess)
    {
      var Self = this;
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('Preventivi',{ CHIAVE : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo           = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayAllegati       = Self.AdvQuery.FindResults(Results,"AllegatiPreventivo");
                                            let ArrayVociPreventivi = Self.AdvQuery.FindResults(Results,"VociPreventivo");
                                            let ArrayLogPreventivo  = Self.AdvQuery.FindResults(Results,"LogPreventivo");
                                            let ArrayAltreLavorazioni   = Self.AdvQuery.FindResults(Results,"AltreLavorazioni");
                                            if(ArrayInfo != undefined)
                                            {
                                              if(ArrayLogPreventivo.length != 0)
                                                Self.SchedaLogPreventivo.AssignDati(ArrayLogPreventivo,'ID_PREVENTIVO')
                                              if(ArrayAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayAllegati,'PREVENTIVO')
                                              if(ArrayVociPreventivi.length != 0)
                                                Self.SchedaVociPreventivo.AssignDati(ArrayVociPreventivi,
                                                                                     TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_SUGGERITA_CLIENTE) / 10,
                                                                                     TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA),
                                                                                     'ID_PREVENTIVO',
                                                                                     TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NATURA_PAGAMENTO),
                                                                                     TSchedaGenerica.DisponiFromString(ArrayInfo[0].BOLLO),
                                                                                     SystemInformation.Configurazioni.Impostazioni.COSTO_BOLLO_SUGGERITO,
                                                                                     ArrayInfo[0].ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione? true : false)
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
                                                              ID_ZONA_SPEDIZIONE            : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_ZONA_SPEDIZIONE),
                                                              COND_PAGAMENTO                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].COND_PAGAMENTO),
                                                              NOTE                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                              CAUSALE                       : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CAUSALE),
                                                              ESIGIBILITA_IVA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                                                              IS_INVIATO                    : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IS_INVIATO),
                                                              ID_FATTURA                    : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_FATTURA),
                                                              NUMERO_FATTURA                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_FATTURA),
                                                              ANNO_FATTURA                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ANNO_FATTURA),
                                                              NUMERO_PREVENTIVO             : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NUMERO_PREVENTIVO),
                                                              ANNO_PREVENTIVO               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ANNO_PREVENTIVO),
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
                                                              NUMERO_DDT                    : ArrayInfo[0].NUMERO_DDT != null ? TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_DDT) : -1,
                                                              ID_DDT_ENTRANTI               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_DDT_ENTRANTI),
                                                              ID_DDT_USCENTI                : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_DDT_USCENTI),
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
                                                              ListaAltreLavorazioni         : []
                                                            }
                                               
                                                if(ArrayAltreLavorazioni != 0)
                                                  Self.Dati.ListaAltreLavorazioni = ArrayAltreLavorazioni

                                                if(Self.Dati.DATA != '')
                                                  Self.Dati.ANNO = new Date(Self.Dati.DATA).getFullYear();
                                                else Self.Dati.ANNO = -1;
                                                Self.CreateSnapshot();
                                              }
                                              else SystemInformation.HandleError('Preventivo eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio del preventivo');

                                            if(OnSuccess != undefined)
                                              OnSuccess()
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
                                    ID_NODO_CONFERME_PREVENTIVI,
                                    Anno.getFullYear(),
                                    this.Chiave
                                ]
                }])
    }

    GetClassName()
    {
      return 'TSchedaPreventivo';
    }

    GetImageIndex()
    {
        if(this.Dati.ID_FATTURA != -1)
          return 'ConfermaLavorata.png';
        return 'Conferma.png';
    }

    DatiStampabili()
    {
        return true
    }

    Stampa(OnSuccess)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaConferma', { Chiave : this.Chiave },
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

    
    ApriPopupGeneraFattura()
    {
      this.PopupSchedaPreventivo = true
      this.__TriggerOnPreventivoConfermato++;
    }

    ConfermaGenerazione()
    {
       this.AnnullaGenerazione();
       // Cambio il valore per triggerare l'evento con un watch
       this.__TriggerOnPreventivoConfermato++;
       return;       
    }

    AnnullaGenerazione()
    {
      this.PopupSchedaPreventivo = false
      if(this.CollegaPreventivoAllaConferma != undefined)
        this.CollegaPreventivoAllaConferma.Disponi()
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
                                                Self.Dati.COD_ENTE_SDI             = ArrayInfo[0].COD_ENTE_SDI
                                                Self.Dati.ENTE_PUBBLICO            = ArrayInfo[0].ENTE_PUBBLICO == 'T'? true : false
                                                Self.Dati.COD_UFFICIO_DEST         = ArrayInfo[0].COD_UFFICIO_DEST
                                                Self.Dati.NAZIONE_DESTINAZIONE     = ArrayInfo[0].NAZIONE_SPEDIZIONE
                                                Self.Dati.NAZIONE_EM_PIVA          = ArrayInfo[0].NAZIONE_EM_PIVA
                                                Self.Dati.ZONA_SPEDIZIONE          = ArrayInfo[0].ZONA_SPEDIZIONE
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
                                                if(ArrayInfo[0].SPESE_INCASSO_DESCRIZIONE != '' && ArrayInfo[0].SPESE_INCASSO_SUGGERITE != 0)
                                                    Self.SchedaVociPreventivo.SetSpesaIncasso(ArrayInfo[0].SPESE_INCASSO_DESCRIZIONE,
                                                                                              ArrayInfo[0].SPESE_INCASSO_SUGGERITE,
                                                                                              Self.Dati.ContoCorrente.CONTO_RIBA)

                                                Self.SchedaVociPreventivo.SetDatiCliente(ArrayInfo[0].IVA_SUGGERITA_CLIENTE / 10,
                                                                                                                 ArrayInfo[0].RITENUTA / 10,
                                                                                                                 '',
                                                                                                                 ArrayInfo[0].NATURA_PAGAMENTO)  
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

    InserisciNuovaRigaVoceTramiteGenerazione(Descrizione, Quantita, Prezzo, Iva)
    {
      let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                    Descrizione,
                                                                    Prezzo,
                                                                    Quantita,
                                                                    'F',
                                                                    Iva, //  this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 0 : this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita,
                                                                    0.00,
                                                                    -1,
                                                                    SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
      this.SchedaVociPreventivo.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
      this.SchedaVociPreventivo.CalcoloTotaliFattura()
    }
  
    Invia(OggettoEmail, OnSuccess)
    {
      let Parametri        = OggettoEmail
      Parametri.Chiave     = this.Chiave
      Parametri.InvioEmail = true
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaConferma', Parametri,
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
      SystemInformation.AdvQuery.GetSQL('Preventivi', { CHIAVE : this.Chiave },
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

}

export default 
{
   components : 
   {
    VUEModalButtonRecapitiFiliali,
    VUEInputProvince,
    VUEInputZone,
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
    VUEModalInvioEmail,
},
   data()
   {
     return { 
              MenuNuovo             : [],
              MostraMenuNuovo       : true,
              DeveloperMode         : SystemInformation.DeveloperMode,
              StatoItaliano         : SystemInformation.Configurazioni.StatoItaliano,
              DocumentoCorrelato    : DOCUMENTO_CORRELATO,
              LsDocumentoCorrelato  : SystemInformation.GetLsTipiDocumentoCorrelato(),
              PopupInviaEmail       : false,
              VisibilitaLogVariazioni : SystemInformation.AccessRights.VisibilitaLogVariazioni(),

              OggettoEmail          : {
                                        CorpoEmail   : '',
                                        Cc           : '',
                                        Ccn          : '',
                                        Destinatario : '',
                                        Oggetto      : ''
                                      },
              Tabs                  : {
                                          ActiveTab : 'Conferma',
                                          Tabs      : [
                                                        {
                                                          Caption : 'Conferma',
                                                          Id      : 'Conferma'
                                                        },
                                                        {
                                                          Caption : 'Voci conferma',
                                                          Id      : 'VociPreventivo'
                                                        },
                                                        {
                                                          Caption: 'Allegati',
                                                          Id     : 'Allegati'
                                                        },
                                                        {
                                                          Caption : 'Variazioni',
                                                          Id      : 'LogPreventivo' 
                                                        },
                                                        {
                                                          Caption : 'Altre lavorazioni',
                                                          Id      : 'AltreLavorazioni' 
                                                        },
                                                      ]
                                      }
                  }
   },
   emits : ['onPreventivoConfermato', 'onClickNuovaFattura', 'onClickNuovaDDT', 'onClickNuovaDDTEntrante'],
   props : ['SchedaPreventivo', 'IsPaginaGestioneAnomalie'],
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

    
    TabsVisibili()
    {
      return this.Tabs.Tabs.filter(tab => 
                                   (tab.Id !== 'LogPreventivo' || this.VisibilitaLogVariazioni));
    }
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

     TriggerOnPreventivoConfermato :
     {
        handler()
        {
          this.$emit('onPreventivoConfermato',this.SchedaPreventivo);
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
    ConfermaInvia()
    {
      this.AnnullaInvia()
      var Self = this
      this.SchedaPreventivo.Invia(this.OggettoEmail, function(Answer)
                                        {
                                          if(Answer.Risposta == 'MAIL_INVIATA')
                                            alert('Invio mail effettuato con successo')
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
    },

    OnClickInvioEmail()
    {
      var Self  = this
      this.OggettoEmail.Destinatario = ''
      this.OggettoEmail.Oggetto      = ''
      this.OggettoEmail.Cc           = ''
      this.OggettoEmail.Ccn          = ''
      this.OggettoEmail.CorpoEmail   = ""

      this.SchedaPreventivo.RecuperaEmailCliente(function()
      {
        Self.PopupInviaEmail = true
        Self.OggettoEmail.Destinatario = Self.SchedaPreventivo.ListaEmailCliente
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




      // SystemInformation.AdvQuery.ExecuteExternalScript('StampaConferma', {Chiave : this.Chiave},
      //                             function(Result)
      //                                                 {  
      //                                                   if(Result.PDF != undefined)
      //                                                   {
      //                                                       var routeData = Self.$router.resolve({ name   : "IFrameStampe" });
      //                                                       var AWindow = window.open(routeData.href, "_blank");
      //                                                       AWindow.BodyPDF = 'data:application/pdf;base64,' + Result.PDF;
      //                                                   }
      //                                                   else SystemInformation.HandleError('Documento non presente','','');
      //                                                 },
      //                                                 SystemInformation.HandleError)
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

    OnVociPreventivoChange()
    {
      this.SchedaPreventivo.Dati.ModificaTabellaVoci = this.SchedaPreventivo.SchedaVociPreventivo.Modificato();
    },

    OnClickCopiaDaPartitaIva()
    {
      this.SchedaPreventivo.Dati.CODICE_FISCALE = this.SchedaPreventivo.Dati.PARTITA_IVA
    },

    OnClickNuovo()
    {
      var Self = this;
      var FatturaDaBanco = true
      
      if (this.MenuNuovo.length == 0)
      {
        this.MenuNuovo = []

        if(this.SchedaPreventivo.Dati.ID_DDT_USCENTI == -1)
        {
          this.MenuNuovo.unshift({
                                  Caption: "Nuovo documento di trasporto",
                                  OnClick: function () 
                                  {
                                    Self.$emit('onClickNuovaDDT', Self.SchedaPreventivo) 
                                  }
                                })
        }

        if(this.DirittiMagazzino)
          if(this.SchedaPreventivo.Dati.ID_DDT_ENTRANTI == -1)
          {
            this.MenuNuovo.unshift({
                                    Caption: "Nuovo documento di trasporto entrante",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovaDDTEntrante', Self.SchedaPreventivo) 
                                    }
                                  })
          }


        if(this.SchedaPreventivo.Dati.ID_FATTURA == -1)
        {
          this.MenuNuovo.unshift({
                                  Caption: "Nuova fattura da banco",
                                  OnClick: function () 
                                  {
                                    Self.$emit('onClickNuovaFattura', Self.SchedaPreventivo, FatturaDaBanco) 
                                  }
                                  })
          this.MenuNuovo.unshift({
                                  Caption: "Nuova fattura",
                                  OnClick: function () 
                                  {
                                    Self.$emit('onClickNuovaFattura', Self.SchedaPreventivo, !FatturaDaBanco) 
                                  }
                                  })

        }
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

      VerificaSeDisabilitare()
      {
        return !this.SchedaPreventivo.IsNuovo() && ( this.SchedaPreventivo.Dati.ID_FATTURA != -1);
      }

   },
   
  beforeMount() 
  {
    this.ActiveTab = 'Conferma'
  }

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>