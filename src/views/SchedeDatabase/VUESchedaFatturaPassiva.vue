<template>
 <div class="ZMCorpoSchedeDati">
  <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
               <img v-if="SchedaFatturaPassiva.Dati.IS_FATTURA && ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/FatturaPassiva2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
               <img v-else-if="ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/NotaDiCreditoPassiva2.png" style="width:40px;height:40px;float:left;margin-top:-11px">  
             </a>
        </li>
       </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'Generale'">
      <div class="ZMNuovaRigaScheda">
        <div style="float:right;margin-left:10px">
            <text style="font-weight: bold;">Data</text>
            <input type="date" id="input-data" class="form-control" v-model="SchedaFatturaPassiva.Dati.DATA"/>
            <label v-if="SchedaFatturaPassiva.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:right;width:15%">
            <text v-if="SchedaFatturaPassiva.Dati.IS_FATTURA" style="float:left;font-weight: bold;padding-right: 10%">Fattura N.</text>
            <text v-if="!SchedaFatturaPassiva.Dati.IS_FATTURA" style="float:left;font-weight: bold;padding-right: 10%">Nota di credito N.</text>
            <input type="text" class="form-control" v-model="SchedaFatturaPassiva.Dati.NUMERO"/>
            <label v-if="SchedaFatturaPassiva.Dati.NUMERO == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            <div class="ZMNuovaRigaScheda" style="margin-top:3%">  
              <input style="float:left;padding-right: 10%" type="checkbox" v-model="SchedaFatturaPassiva.Dati.RICEVUTA" @click="OnClickRicevuta"/>
              <text style="float:left;font-weight: bold;padding-right: 10%">&nbsp;&nbsp;Ricevuta d'acquisto</text>
            </div>
        </div>
        <div style="float:left;width:55%;margin-top: 18px; margin-right: 10px;">
          <div class="btn-group m-b-sm" style="width:44%;float:left;margin-left:10px" v-if="SchedaFatturaPassiva.Dati.IS_FATTURA">
            <button @click="OnClickTipologiaDocumento" type="button" class="btn btn-default" style="width:50%;float:right">Nota di credito</button>
            <button type="button" class="btn btn-info" style="width:50%;float:left">Fattura</button>
          </div>
          <div class="btn-group m-b-sm" style="width:44%;float:left;margin-left:10px" v-if="!SchedaFatturaPassiva.Dati.IS_FATTURA">
            <button type="button" class="btn btn-info" style="width:50%;float:right">Nota di credito </button>
            <button @click="OnClickTipologiaDocumento" type="button" class="btn btn-default" style="width:50%;float:left;">Fattura</button>
          </div>
        </div>
      </div>
      <div class="ZMSeparatoreScheda" style="margin-top:7%">       
        <text style="font-weight: bold;">Generalità o Ragione Sociale Fornitore</text>
      </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold; float:left; width:100%">Fornitore</text>
        <VUEInputFornitore v-model="SchedaFatturaPassiva.Dati.ID_FORNITORE" placeholder="Fornitore"/>
        <label v-if="SchedaFatturaPassiva.Dati.ID_FORNITORE == -1" class="ZMFormLabelError">Campo obbligatorio</label>
      </div>
      <div class="ZMNuovaRigaScheda" style="padding-bottom:15px">
        <text style="font-weight: bold; float:left; width:100%">Ragione Sociale</text>
        <input v-model="SchedaFatturaPassiva.Dati.RAGIONE_SOCIALE" placeholder="Ragione Sociale" type="text" class="form-control"/>
        <label v-if="SchedaFatturaPassiva.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
      </div>
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Indirizzo di Fatturazione</text>
      </div>
      <div style="clear:both">
       <div style="float:left;width:79%">
           <label style="font-weight: bold;">Indirizzo</label>
           <input type="text" class="form-control" v-model="SchedaFatturaPassiva.Dati.INDIRIZZO_FATTURAZIONE" placeholder="Indirizzo"/>
       </div> 
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:20%">
           <label style="font-weight: bold;">Civico</label>
           <input type="text" class="form-control" maxlength="7" v-model="SchedaFatturaPassiva.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
       </div> 
   </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold">Comune</text>
        <input v-model="SchedaFatturaPassiva.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune">
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:79%">
            <label style="font-weight: bold;">Provincia</label>
            <VUEInputProvince v-model="SchedaFatturaPassiva.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:20%">
            <label style="font-weight: bold;">CAP</label>
            <VUEInputCAP placeholder="CAP" v-model="SchedaFatturaPassiva.Dati.CAP_FATTURAZIONE"/>
        </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
       <div style="float:left;width:40%">
        <text style="font-weight: bold">Partita IVA</text>
        <VUEInputPartitaIVA v-model="SchedaFatturaPassiva.Dati.PARTITA_IVA" placeholder="Partita IVA"></VUEInputPartitaIVA>
        <label v-if="SchedaFatturaPassiva.Dati.PARTITA_IVA.trim() == '' && SchedaFatturaPassiva.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
            class="ZMFormLabelError">Campo obbligatorio</label>
        <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
       </div>
       <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:59%">
          <label style="font-weight: bold;">Nazione emittente</label>
          <VUEInputNazioni v-model="SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA" emptyElement="false"/>
          <label v-if="SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
      </div>
      <div class="ZMNuovaRigaScheda">       
        <text style="font-weight: bold;float:left;width:100%">Codice Fiscale</text>
        <VUEInputCodiceFiscale v-model="SchedaFatturaPassiva.Dati.CODICE_FISCALE" style="width:81%;margin-right:10px; float:left" placeholder="Codice Fiscale"></VUEInputCodiceFiscale>
        <button class="btn btn-sm btn-info" style="float:left; width:17%" @click="OnClickCopiaDaPartitaIva">Copia da Partita IVA</button>
        <label v-if="SchedaFatturaPassiva.Dati.PARTITA_IVA.trim() == '' && SchedaFatturaPassiva.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
            class="ZMFormLabelError">Campo obbligatorio</label>
        <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
      </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold; float:left; width:100%">Oggetto </text>
        <input v-model="SchedaFatturaPassiva.Dati.OGGETTO" type="text" class="form-control" placeholder="Oggetto"/>
      </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold; float:left; width:100%">IBAN </text>
        <input v-model="SchedaFatturaPassiva.Dati.IBAN" type="text" class="form-control" placeholder="IBAN"/>
        <br>
      </div>

      <div class="ZMNuovaRigaScheda">
        <div class="col-md-5">
            
        </div>
        <!-- <div class="col-md-5" style=" float:right; width:70%; margin-right:10px">
          <div class="col-md-5" style=" float:right;">
            <div class="ZMNuovaRigaScheda">
              <input v-model="SchedaFatturaPassiva.Dati.TOTALE_FATTURA" class="form-control" type="number" style="text-align: right;">  
              <label v-if="SchedaFatturaPassiva.Dati.TOTALE_FATTURA == ''" class="ZMFormLabelError">Inserire il totale della fattura</label>
            </div> 
            
            <div class="ZMNuovaRigaScheda"></div>

            <div class="ZMNuovaRigaScheda">
              <label class="ZMLabel" style="text-align: right; padding-right:25px">{{ ConteggioRitenuta().toFixed(2) }}</label>
            </div>

            <div class="ZMNuovaRigaScheda">
              <button @click="OnClickRicalcolaTotaliFatturaPassiva" type="button" class="btn btn-info" style="float:right; margin-top:7px">Ricalcola totali</button>
            </div>
             <div class="ZMNuovaRigaScheda">
            <label class="ZMLabel" style="; float:right; text-align: right;">{{ TotaleRitenuta }}</label>  
          </div>       
          <div class="ZMNuovaRigaScheda">
            <label class="ZMLabel" style="float:right; text-align: right;">{{ SchedaFatturaPassiva.Dati.TOTALE_FATTURA - TotaleRitenuta }}</label>  
          </div>-->
          <!-- </div>
          <div class="col-md-4" style=" float:right;">
            <div class="ZMNuovaRigaScheda">
              <div style="float:left; margin-top:7px">
                <a style="float:left">Totale Fattura </a>
                <label v-if="SchedaFatturaPassiva.Dati.TOTALE_FATTURA == ''" class="ZMFormLabelError"></label>
              </div>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a style="float:left;margin-top:7px">Ritenuta </a>
            </div>
          </div>
        </div> -->
      </div>
      <div style="clear:both;height:1%"></div>
   </div>

   <div  v-if="Tabs.ActiveTab == 'Allegati'">
    <VUEAllegati :SchedaAllegati="SchedaFatturaPassiva.SchedaAllegati" 
                 NomeCampoModello="FatturePassive"
                 @onChange="OnAllegatiChange" />
  </div>

  <div v-if="Tabs.ActiveTab == 'VociFatturaPassiva'">
    <div v-if="ListaMagazzini.length > 1">
      <div style="clear:both"></div>
      <div class="ZMSeparatoreScheda" style="margin-top:5px">    
        <text style="font-weight: bold;">Magazzini</text>
      </div>
      <div class="ZMNuovaRigaScheda">
        <text style="font-weight: bold">Magazzino</text>
        <select class="form-control" v-model="SchedaFatturaPassiva.Dati.ID_MAGAZZINO"
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

    <VUEDataTable :NumeroRighePerPagina="50" :DataTable="SchedaFatturaPassiva.DataTable" @onChange="OnDataChanged" style="background:#d0e9ff"></VUEDataTable>
    <div class="ZMNuovaRigaScheda" style="background-color:#d2e8ff;margin-top:20px">
    
        <div class="col-md-4" style="float:right;">
          <div class="ZMNuovaRigaScheda">
            <input v-model="SchedaFatturaPassiva.Dati.TOTALE_FATTURA" class="form-control" type="number" style="text-align: right;">
            <label v-if="SchedaFatturaPassiva.Dati.TOTALE_FATTURA == ''" class="ZMFormLabelError">Inserire il totale della fattura</label>
          </div>

          <div class="ZMNuovaRigaScheda"></div>

          <div class="ZMNuovaRigaScheda">
            <label class="ZMLabel" style="text-align: right; padding-right:25px">{{ ConteggioRitenuta().toFixed(2) }}</label>
          </div>
          <button @click="OnClickRicalcolaTotaliFatturaPassiva"
                    type="button"
                    class="btn btn-info" style="margin-top: 10px;">
                    Ricalcola totali
          </button>
        </div>


        <div class="col-md-3" style="float:right;">
          <div class="ZMNuovaRigaScheda">
            <div style="float:left; margin-top:7px">
              <a style="float:left">Totale Fattura</a>
              <label v-if="SchedaFatturaPassiva.Dati.TOTALE_FATTURA == ''" class="ZMFormLabelError"></label>
            </div>
          </div>

          <div class="ZMNuovaRigaScheda">
            <a style="float:left;margin-top:7px">Ritenuta</a>
          </div>

          <label class="ZMLabel"
            :style="{ color: CalcolaTotaleVociFatturaPassiva() !== SchedaFatturaPassiva.Dati.TOTALE_FATTURA ? 'red' : 'black',
                      backgroundColor: 'transparent',
                      margin: '0',
                      whiteSpace: 'nowrap', 
                      marginTop: '20px', 
                      fontSize: 'inherit' }">
            Totale calcolato dalle voci: {{ CalcolaTotaleVociFatturaPassiva().toFixed(2)}} €
          </label>
        </div>
    </div>
    <div class="ZMNuovaRigaScheda" style="background-color:#d2e8ff;height:30px">
    </div>
  </div>

  <div  v-if="Tabs.ActiveTab == 'DatiFiscali'">
    <br>
    <div class="ZMSeparatoreScheda">       
      <text style="font-weight: bold;">Ritenute</text>
      <hr style="margin-top:5px">
    </div>
    <VUEDataTable :DataTable="SchedaFatturaPassiva.DataTableRitenute" @onChange="OnRitenuteChanged" style="background:#d0e9ff"></VUEDataTable>
    <div class="ZMSeparatoreScheda">       
      <text style="font-weight: bold;">Casse previdenziali</text>
      <hr style="margin-top:5px">
    </div>
    <VUEDataTable :DataTable="SchedaFatturaPassiva.DataTableCasse" @onChange="OnCasseChanged" style="background:#d0e9ff"></VUEDataTable>
    <div class="ZMSeparatoreScheda">       
      <text style="font-weight: bold;">Riepiloghi aliquote</text>
      <hr style="margin-top:5px">
    </div>
    <VUEDataTable :DataTable="SchedaFatturaPassiva.DataTableAliquote" @onChange="OnAliquotaChanged" style="background:#d0e9ff"></VUEDataTable>
  </div>

  <div  v-if="Tabs.ActiveTab == 'Rate'">
    <br>
    <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Rate</text>
    </div>
    <label style="font-size: 14px;" v-if="!SchedaFatturaPassiva.ContoObbligatorioInserito()"
            class="ZMFormLabelError">Inserire Conto/Cassa</label>
    <VUEDataTable :DataTable="SchedaFatturaPassiva.DataTableRate" @onChange="OnRateChanged" style="background:#d0e9ff"></VUEDataTable>
    <br>
    <br>
    <br>
  </div>


 </div>
</template>

<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES, RUOLI } from '@/SystemInformation.js'
import { TZFatturaElettronica } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEDataTable from '@/components/VUEDataTable.vue'
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable.js'
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { ID_NODO_FATTURE_PASSIVE, ID_NODO_NOTE_DI_CREDITO_PASSIVE } from '@/NodiVuoti'

export class TSchedaFatturaPassiva extends TSchedaGenerica
{
  
  OnInitialization()
  {
    this.SchedaAllegati    = new TSchedaAllegati();
    this.SchedaAllegati.ClearAllegati()
    this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)

    //DataTable voci_fatture_passive
    this.DataTable = new TZDataTable('CHIAVE');
    this.DataTable.ClearColumns();
    this.DataTable.LarghezzaColonneRestantiInPixels = 200;
    var Colonna = this.DataTable.AddColumn('Num. Linea',
                              TZDTableColumnType.typInteger,
                              'NUMERO_LINEA',
                              "7%");
    Colonna.Nascosta     = true;
    Colonna.Progressivo  = true;  
    // Colonna.Necessario  = true;
    Colonna = this.DataTable.AddColumn('Qnt',
                                  TZDTableColumnType.typInteger,
                                  'QUANTITA',
                                  "8%");
    // Colonna.Necessario = true;                                 
    this.DataTable.AddColumn('Bene/Servizio',
                              TZDTableColumnType.typString,
                              'BENE_SERVIZIO',
                              "29%");
    this.DataTable.AddColumn('Codice articolo',
                              TZDTableColumnType.typString,
                              'CODICE_ARTICOLO',
                              "13%");
    this.DataTable.AddColumn('Prezzo',
                              TZDTableColumnType.typMillesimi,
                              'PREZZO',
                              "9%");
    this.DataTable.AddColumn('Sconto[%]',
                              TZDTableColumnType.typCentesimi,
                              'SCONTO1',
                              "6%");
    Colonna = this.DataTable.AddColumn('Sconto[%]',
                              TZDTableColumnType.typCentesimi,
                              'SCONTO2',
                              "6%");
    Colonna = this.DataTable.AddColumn('Sconto[%]',
                              TZDTableColumnType.typCentesimi,
                              'SCONTO3',
                              "6%");
    this.DataTable.AddColumn('Totale',
                              TZDTableColumnType.typMillesimi,
                              'TOTALE',
                              "10%");
    this.DataTable.AddColumn('IVA',
                              TZDTableColumnType.typCentesimi,
                              'IVA',
                              "7%");
    // this.DataTable.AddColumn('Note',
    //                           TZDTableColumnType.typString,
    //                           'NOTE',
    //                           "25%");

    //DataTable RATE
    this.DataTableRate = new TZDataTable('CHIAVE');
    this.DataTableRate.ClearColumns();
    var ColonnaRate = this.DataTableRate.AddColumn('Data Scadenza',
                                      TZDTableColumnType.typDate,
                                      'DATA_SCADENZA',
                                      "16%");
    ColonnaRate.Necessario  = true;
    this.DataTableRate.AddColumn('Importo',
                                TZDTableColumnType.typMillesimi,
                                'IMPORTO',
                                "10%");
    this.DataTableRate.AddColumn('Data Pagamento',
                                TZDTableColumnType.typDate,
                                'DATA_PAGAMENTO',
                                "16%")
    ColonnaRate = this.DataTableRate.AddColumn('Modalità Pagamento',
                                                TZDTableColumnType.typSelect,
                                                'ID_MOD_PAGAMENTO',
                                                "20%");
    ColonnaRate.DefaultValue  = -1
    ColonnaRate.DefaultSelect = true
    ColonnaRate.Lista         = SystemInformation.Configurazioni.ModalitaPagamenti
    ColonnaRate = this.DataTableRate.AddColumn('Conto/Cassa',
                                TZDTableColumnType.typSelect,
                                'ID_CONTO_CASSA',
                                "18%");            
    ColonnaRate.DefaultValue   = -1
    ColonnaRate.DefaultSelect  = true
    ColonnaRate.Lista          = SystemInformation.GetLsContiCasse(false)
    ColonnaRate.IsDisabledWhen = function(Dati)
    {
      if(Dati['ID_CONTO_CASSA'].Valore != -1 && Dati['NO_PRIMA_NOTA'].Valore)
        Dati['ID_CONTO_CASSA'].Valore = -1

      return Dati['NO_PRIMA_NOTA'].Valore
    }
    this.DataTableRate.AddColumn('Note',
                                  TZDTableColumnType.typMemo,
                                  'NOTE',
                                  '30%');
    this.DataTableRate.AddColumn('No PN',
                                  TZDTableColumnType.typBoolean,
                                  'NO_PRIMA_NOTA',
                                  "2%");
    ColonnaRate = this.DataTableRate.AddColumn('Movimento',
                                          TZDTableColumnType.typMemo,
                                          'DATA_MOVIMENTO',
                                          "20%");
    ColonnaRate.ReadOnly        = true
    ColonnaRate.DefaultValue    = null
    ColonnaRate.OnCalcoloValore = function(Dati)
    {
      if(Dati.DATA_MOVIMENTO.Valore != null)
      {
        let Data = new Date(Dati.DATA_MOVIMENTO.Valore)
        return 'Fatt. collegata ad un movimento del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy', Data)
      }
      return '-'
    }

    //DataTable RITENUTE
    this.DataTableRitenute = new TZDataTable('CHIAVE');
    this.DataTableRitenute.ClearColumns();
    var ColonnaRitenute = this.DataTableRitenute.AddColumn('Tipo',
                                                            TZDTableColumnType.typSelect,
                                                            'TIPO',
                                                            "30%");
    ColonnaRitenute.Lista = TZFatturaElettronica.GetLsTipoRitenuta()
    this.DataTableRitenute.AddColumn('Aliquota [%]',
                                      TZDTableColumnType.typCentesimi,
                                      'ALIQUOTA',
                                      "30%")
    this.DataTableRitenute.AddColumn('Importo',
                                      TZDTableColumnType.typCentesimi,
                                      'IMPORTO',
                                      "20%");

    //DATA TABLE CASSE
    this.DataTableCasse = new TZDataTable('CHIAVE');
    this.DataTableCasse.ClearColumns();
    var ColonnaCasse = this.DataTableCasse.AddColumn('Tipo',
                                                      TZDTableColumnType.typSelect,
                                                      'TIPO',
                                                      "30%");
    ColonnaCasse.Lista = TZFatturaElettronica.GetLsTipoCassaPrevidenziale()
    ColonnaCasse.DefaultValue = -1
    this.DataTableCasse.AddColumn('Aliquota',
                                  TZDTableColumnType.typCentesimi,
                                  'ALIQUOTA',
                                  "30%")
    this.DataTableCasse.AddColumn('Importo',
                                  TZDTableColumnType.typCentesimi,
                                  'IMPORTO',
                                  "20%");
     this.DataTableCasse.AddColumn('IMPOSTA',
                                  TZDTableColumnType.typCentesimi,
                                  'IMPOSTA',
                                  "20%");
    //DATA TABLE RIPILOGHI ALIQUOTE
    this.DataTableAliquote = new TZDataTable('CHIAVE');
    this.DataTableAliquote.ClearColumns();
    this.DataTableAliquote.AddColumn('IVA',
                                                            TZDTableColumnType.typCentesimi,
                                                            'IVA',
                                                            "10%");
    this.DataTableAliquote.AddColumn('Arrotondamento',
                                      TZDTableColumnType.typCentesimi,
                                      'ARROTONDAMENTO',
                                      "30%")
    this.DataTableAliquote.AddColumn('Imponibile',
                                      TZDTableColumnType.typCentesimi,
                                      'IMPONIBILE',
                                      "30%");
     this.DataTableAliquote.AddColumn('Imposta',
                                      TZDTableColumnType.typCentesimi,
                                      'IMPOSTA',
                                      "30%");
  }

  ContoObbligatorioInserito()
  {
    for(let i = 0; i < this.DataTableRate.Righe.length; i++)
      if(this.DataTableRate.Righe[i].Dati['DATA_PAGAMENTO'].Valore != '' && this.DataTableRate.Righe[i].Dati['ID_CONTO_CASSA'].Valore == -1 && !this.DataTableRate.Righe[i].Dati['NO_PRIMA_NOTA'].Valore) 
        return false
        
    return true
  }

  CanRecord()
  {
    return (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            this.Dati.DATA != '' && 
            this.ContoObbligatorioInserito() &&
            this.Dati.NUMERO != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            this.Dati.TOTALE_FATTURA != '' &&
            this.SchedaAllegati.AllDataOk() &&
            this.DataTable.AllDataOk() && 
            this.DataTableRate.AllDataOk() &&
            this.DataTableRitenute.AllDataOk() &&
            this.DataTableAliquote.AllDataOk() &&
            this.DataTableCasse.AllDataOk()
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
        return false;
    
    return true;
  }

  Clear()
  {
    this.SchedaAllegati.AssignDati([])
    this.SchedaAllegati.SetIdDocumento(-1)
    this.DataTable.AssignDati([]);
    this.DataTableRate.AssignDati([]);
    this.DataTableRitenute.AssignDati([]);
    this.DataTableAliquote.AssignDati([]);
    this.DataTableCasse.AssignDati([]);
    this.Dati = {
                    ID_FORNITORE                  : -1,
                    NUMERO                        : '',
                    RAGIONE_SOCIALE               : '',
                    DATA                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
                    OGGETTO                       : '',
                    INDIRIZZO_FATTURAZIONE        : '',
                    NR_CIVICO_FATTURAZIONE        : '',
                    COMUNE_FATTURAZIONE           : '',
                    PROVINCIA_FATTURAZIONE        : -1,
                    CAP_FATTURAZIONE              : '',
                    NAZIONE_EM_PIVA               : SystemInformation.Configurazioni.StatoItaliano,
                    PARTITA_IVA                   : '',
                    CODICE_FISCALE                : '',
                    SOSPESO                       : '',
                    TOTALE_RITENUTA               : '',
                    TOTALE_FATTURA                : '',
                    IMPORTO_NETTO                 : '',
                    IBAN                          : '',
                    IS_FATTURA                    : true,
                    RICEVUTA                      : false,
                    // Dati allegati
                    ModificaTabellaAllegati       : false,
                    ModificaTabellaVoci           : false,
                    ModificaTabellaRate           : false,
                    ModificaTabellaCasse          : false,
                    ModificaTabellaAliquote       : false,
                    ModificaTabellaRitenute       : false,
                    ID_MAGAZZINO                  : SystemInformation.Configurazioni.Impostazioni.MAGAZZINO,
                }
    super.Clear();
  }

  RicalcoloRate(OnSuccess)
  {
    let TotaleRate  = 0
    let TotRitenuta = this.ConteggioRitenuta()
    this.DataTableRate.Righe.forEach(function(Riga)
    { 
      if(!Riga.Eliminato)
        TotaleRate += Riga.Dati['IMPORTO'].Valore
    })  

    if(this.Dati.TOTALE_FATTURA - TotRitenuta - TotaleRate != 0 && this.Dati.TOTALE_FATTURA - TotRitenuta - TotaleRate > 0)
    {
      this.DataTableRate.AddRowsOnTop(1)
      this.DataTableRate.Righe[0].Dati['DATA_SCADENZA'].Valore = TZDateFunct.DateInHTMLInputFormat(new Date())
      this.DataTableRate.Righe[0].Dati['IMPORTO'].Valore       = this.Dati.TOTALE_FATTURA - TotRitenuta - TotaleRate
      this.Dati.ModificaTabellaRate = true
    }

    if(OnSuccess != undefined)
      OnSuccess()
  }

  ConteggioRitenuta()
  {
    let Result = 0
    if(this.DataTableRitenute.Righe.length != 0)
      for(let i = 0; i < this.DataTableRitenute.Righe.length; i++)
        Result += this.DataTableRitenute.Righe[i].Dati['IMPORTO'].Valore
    return Result
  }

  Registra(OnSuccess,OnError)
  {
    var Self = this
    if(this.CanRecord())
    {
      this.RicalcoloRate(function()
      {
          var ObjQuery = { Operazioni : [] };
          ObjQuery.Operazioni.push({
                                      Query     : Self.IsNuovo() ? "Inserisci" : "Modifica",
                                      Parametri : {
                                                      CHIAVE                    : Self.IsNuovo() ? undefined : Self.Chiave, 
                                                      NUMERO                    : TSchedaGenerica.PrepareForRecordString(Self.Dati.NUMERO), 
                                                      ID_FORNITORE              : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                                      RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.RAGIONE_SOCIALE),
                                                      CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(Self.Dati.CODICE_FISCALE),
                                                      OGGETTO                   : TSchedaGenerica.PrepareForRecordString(Self.Dati.OGGETTO),
                                                      INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_FATTURAZIONE),
                                                      NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_FATTURAZIONE),
                                                      PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_FATTURAZIONE),
                                                      COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_FATTURAZIONE),
                                                      CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_FATTURAZIONE),
                                                      DATA                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.DATA),
                                                      NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_EM_PIVA),
                                                      PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(Self.Dati.PARTITA_IVA),
                                                      SOSPESO                   : TSchedaGenerica.PrepareForRecordString(Self.Dati.SOSPESO),
                                                      TOTALE_FATTURA            : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.TOTALE_FATTURA * 100), //_dg_NTS_ Il TOTALE_FATTURA della tabella MySQL fatture_passive è in CENTESIMI
                                                      //TOTALE_RITENUTA           : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.TOTALE_RITENUTA * 100),
                                                      //IMPORTO_NETTO             : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.IMPORTO_NETTO * 100),
                                                      IBAN                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.IBAN),
                                                      IS_FATTURA                : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.IS_FATTURA),
                                                      RICEVUTA                  : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.RICEVUTA),
                                                      ID_MAGAZZINO              : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_MAGAZZINO)
                                                  }
                                    });
            
            Self.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_FATTURA_PASSIVA')

            Self.DataTable.Righe.forEach(function(Riga)
            {              
              let Parametri =  Self.DataTable.PrepareQueryParameters(Riga) 

              if(Self.Chiave != -1)    
                  Parametri.CHIAVE_FATTURA_PASSIVA = Self.Chiave;

              if(Riga.Nuovo)
                  ObjQuery.Operazioni.push({
                                            Query     : "InserisciVoceFatturaPassiva",
                                            Parametri : Parametri,
                                            ResetKeys : [2]
                                          })
              else
              {
                if(Riga.Eliminato)
                    ObjQuery.Operazioni.push({
                                              Query     : "EliminaVoceFatturaPassiva",
                                              Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                            })
                else 
                {
                  if(Riga.Modificato())
                      ObjQuery.Operazioni.push({
                                                Query     : "ModificaVoceFatturaPassiva",
                                                Parametri : Parametri
                                              })
                }
              }
          });

          Self.DataTableRate.Righe.forEach(function(Riga)
          {              
              let Parametri =  Self.DataTableRate.PrepareQueryParameters(Riga) 

              ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_SCADENZA'].Valore)))
                                                }
                                });

              if(Riga.Dati['DATA_PAGAMENTO'].Valore != '')
              {
                ObjQuery.Operazioni.push({
                                            Query     : "EliminaRecordSaldiChiusureAnnuali",
                                            Parametri : {
                                                          CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                                          ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_PAGAMENTO'].Valore)))
                                                        }
                                        });
              }

              if(Self.Chiave != -1)    
                  Parametri.CHIAVE_FATTURA_PASSIVA = Self.Chiave;

              if(Riga.Nuovo)
                  ObjQuery.Operazioni.push({
                                            Query     : "InserisciRateFatturaPassiva",
                                            Parametri : Parametri,
                                            ResetKeys : [2]
                                          })
              else
              {
                if(Riga.Eliminato)
                    ObjQuery.Operazioni.push({
                                              Query     : "EliminaRateFatturaPassiva",
                                              Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                            })
                else 
                {
                  if(Riga.Modificato())
                      ObjQuery.Operazioni.push({
                                                Query     : "ModificaRateFatturaPassiva",
                                                Parametri : Parametri
                                              })
                }
              }
          });

          Self.DataTableRitenute.Righe.forEach(function(Riga)
          {              
              let Parametri =  Self.DataTableRitenute.PrepareQueryParameters(Riga) 
              if(Self.Chiave != -1)    
                  Parametri.CHIAVE_FATTURA_PASSIVA = Self.Chiave;

              if(Riga.Nuovo)
                  ObjQuery.Operazioni.push({
                                            Query     : "InserisciRitenute",
                                            Parametri : Parametri,
                                            ResetKeys : [2]
                                          })
              else
              {
                if(Riga.Eliminato)
                    ObjQuery.Operazioni.push({
                                              Query     : "EliminaRitenute",
                                              Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                            })
                else 
                {
                  if(Riga.Modificato())
                      ObjQuery.Operazioni.push({
                                                Query     : "ModificaRitenute",
                                                Parametri : Parametri
                                              })
                }
              }
          });

          Self.DataTableCasse.Righe.forEach(function(Riga)
          {              
              let Parametri =  Self.DataTableCasse.PrepareQueryParameters(Riga) 
              if(Self.Chiave != -1)    
                  Parametri.CHIAVE_FATTURA_PASSIVA = Self.Chiave;

              if(Riga.Nuovo)
                  ObjQuery.Operazioni.push({
                                            Query     : "InserisciCassePrevidenziali",
                                            Parametri : Parametri,
                                            ResetKeys : [2]
                                          })
              else
              {
                if(Riga.Eliminato)
                    ObjQuery.Operazioni.push({
                                              Query     : "EliminaCassePrevidenziali",
                                              Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                            })
                else 
                {
                  if(Riga.Modificato())
                      ObjQuery.Operazioni.push({
                                                Query     : "ModificaCassePrevidenziali",
                                                Parametri : Parametri
                                              })
                }
              }
          });

          Self.DataTableAliquote.Righe.forEach(function(Riga)
          {              
            let Parametri =  Self.DataTableAliquote.PrepareQueryParameters(Riga) 
            if(Self.Chiave != -1)    
                Parametri.CHIAVE_FATTURA_PASSIVA = Self.Chiave;

            if(Riga.Nuovo)
                ObjQuery.Operazioni.push({
                                          Query     : "InserisciRiepiloghiAliquote",
                                          Parametri : Parametri,
                                          ResetKeys : [2]
                                        })
            else
            {
              if(Riga.Eliminato)
                  ObjQuery.Operazioni.push({
                                            Query     : "EliminaRiepiloghiAliquote",
                                            Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                          })
              else 
              {
                if(Riga.Modificato())
                    ObjQuery.Operazioni.push({
                                              Query     : "ModificaRiepiloghiAliquote",
                                              Parametri : Parametri
                                            })
              }
            }
          });

          ObjQuery.Operazioni.push({
                                      Query     : "EliminaRecordSaldiChiusureAnnuali",
                                      Parametri : {
                                                    CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                                    ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Self.Dati.DATA)))
                                                  }
                                    });

          Self.AdvQuery.PostSQL('FatturePassive',ObjQuery,function(Response)
          {
            if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
              Self.SchedaAllegati.DeleteFileDaEliminare()
            ObjQuery = {};
            if(Self.Chiave == -1)
                Self.Chiave = Response.NewKey1;
            Self.Dati.ModificaTabellaAllegati = false;
            Self.Dati.ModificaTabellaVoci     = false;
            Self.Dati.ModificaTabellaRate     = false;
            Self.Dati.ModificaTabellaRitenute = false;
            Self.Dati.ModificaTabellaCasse    = false;
            Self.Dati.ModificaTabellaAliquote = false;
            Self.CreateSnapshot();
            OnSuccess();
          },
          function(HTTPError,SubHTTPError,Response)
          {
            OnError(HTTPError,SubHTTPError,Response);
          });
      })
    }
  }

   CaricaRiassunto(Riassunto)
   {
      this.Chiave                   = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.Dati.ID_FORNITORE        = Riassunto.ID_FORNITORE;
      this.Dati.ANNO                = Riassunto.ANNO
      if(Riassunto.NUMERO != null && Riassunto.NUMERO != undefined)
          this.Dati.NUMERO = Riassunto.NUMERO;
      else this.Dati.NUMERO = -1;
      this.Dati.RAGIONE_SOCIALE     = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.Dati.IS_FATTURA          = TSchedaGenerica.DisponiFromBoolean(Riassunto.IS_FATTURA)
      this.Dati.TOTALE_FATTURA      = TSchedaGenerica.DisponiFromInteger(Riassunto.TOTALE_FATTURA) / 100,
      this.Dati.RATE_NON_PAGATE     = Riassunto.RATE_NON_PAGATE
      this.Dati.RATE_TOTALI         = Riassunto.RATE_TOTALI
      this.TOTALE_RATE              = TSchedaGenerica.DisponiFromInteger(Riassunto.TOTALE_RATE) / 100
      this.CreateSnapshot();
   }

   GetDescrizione()
   {
      if(this.Dati.ANNO == -1)
      {
        if(this.Dati.IS_FATTURA)
          return ('Avviso fattura' + (this.Dati.TOTALE_FATTURA != null? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.Dati.TOTALE_FATTURA) : ''))
        else return ('Avviso nota di credito' + (this.Dati.TOTALE_FATTURA != null? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.Dati.TOTALE_FATTURA) : ''))
      }
      else
      {
        if(this.Dati.IS_FATTURA)
          return ('Fat.ra ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + (this.Dati.TOTALE_FATTURA != null? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.Dati.TOTALE_FATTURA) : ''));
        else return ('Nota di credito ' + this.Dati.NUMERO + '/' + this.Dati.ANNO + (this.Dati.TOTALE_FATTURA != null? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.Dati.TOTALE_FATTURA) : ''));
      }
   }

   GetClassName()
   {
     return 'TSchedaFatturaPassiva';
   }

   GetImageIndex()
   {
      if(this.Dati.RATE_NON_PAGATE == 0)
      {
        return 'NotaDiCreditoPassiva_Pagata.png'
      }
      else if(this.Dati.RATE_TOTALI != this.Dati.RATE_NON_PAGATE)
      {
        return 'MezzoPagatoPassiva.png'
      }  
      else 
      {
       return 'NotaDiCreditoPassiva_NonPagata.png'
      }
   }

   Disponi(OnSuccess)
   {
      if(this.PulsanteAlberoPremuto)
         this.TriggerRate++
      var Self = this;
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('FatturePassive',{ CHIAVE : this.Chiave, ChiaveFatturaPassiva : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo         = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayAllegati     = Self.AdvQuery.FindResults(Results,"AllegatiFatturaPassiva");
                                            let ArrayInfoVoci     = Self.AdvQuery.FindResults(Results,"VociFatturaPassiva");
                                            let ArrayInfoRate     = Self.AdvQuery.FindResults(Results,"RateFatturaPassiva");
                                            let ArrayInfoAliquota = Self.AdvQuery.FindResults(Results,"RiepiloghiFatturaPassiva");
                                            let ArrayInfoCassa    = Self.AdvQuery.FindResults(Results,"CassePrevidenzialiFattPass");
                                            let ArrayInfoRitenute = Self.AdvQuery.FindResults(Results,"RitenuteFatturaPassiva");
                                            if(ArrayInfo != undefined)
                                            {
                                              Self.DataTable.AssignDati(ArrayInfoVoci);
                                              Self.DataTableRate.AssignDati(ArrayInfoRate);
                                              Self.DataTableRitenute.AssignDati(ArrayInfoRitenute);
                                              Self.DataTableAliquote.AssignDati(ArrayInfoAliquota);
                                              Self.DataTableCasse.AssignDati(ArrayInfoCassa);
                                              if(ArrayAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayAllegati,'FATTURA_PASSIVA')
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = { 
                                                              NUMERO                        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NUMERO),
                                                              ID_FORNITORE                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_FORNITORE),
                                                              RAGIONE_SOCIALE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              INDIRIZZO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                              NR_CIVICO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                              COMUNE_FATTURAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                              PROVINCIA_FATTURAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                              CAP_FATTURAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                              DATA                          : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA),
                                                              NAZIONE_EM_PIVA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                                                              PARTITA_IVA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              CODICE_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              SOSPESO                       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SOSPESO),
                                                              OGGETTO                       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].OGGETTO),
                                                              TOTALE_FATTURA                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TOTALE_FATTURA) / 100,
                                                              //TOTALE_RITENUTA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].TOTALE_RITENUTA / 100),
                                                              //IMPORTO_NETTO                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IMPORTO_NETTO / 100),
                                                              IBAN                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                              IS_FATTURA                    : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IS_FATTURA),
                                                              RICEVUTA                      : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].RICEVUTA),
                                                              RATE_NON_PAGATE               : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_NON_PAGATE),
                                                              RATE_TOTALI                   : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RATE_TOTALI),
                                                              ModificaTabellaAllegati       : false,
                                                              ModificaTabellaVoci           : false,
                                                              ModificaTabellaRate           : false,
                                                              ModificaTabellaRitenute       : false,
                                                              ModificaTabellaAliquote       : false,
                                                              ModificaTabellaCasse          : false,
                                                              ID_MAGAZZINO                  : (ArrayInfo[0].ID_MAGAZZINO == null || ArrayInfo[0].ID_MAGAZZINO == undefined) 
                                                                                              ? SystemInformation.Configurazioni.Impostazioni.MAGAZZINO 
                                                                                              : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_MAGAZZINO),
                                                }
                                                if(Self.Dati.DATA != '')
                                                  Self.Dati.ANNO = new Date(Self.Dati.DATA).getFullYear();
                                                else Self.Dati.ANNO = -1;

                                                for(let i = 0; i < Self.DataTableRate.Righe.length; i++)
                                                {
                                                  if(Self.DataTableRate.Righe[i].Dati.DATA_MOVIMENTO['Valore'] != null)
                                                  {
                                                    Self.DataTableRate.Righe[i].RowReadOnly = true
                                                  }
                                                }
                                                Self.CreateSnapshot();
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Fattura eliminata')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio della fattura passiva');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
   }

   Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      let Self = this
      var ObjQuery = { Operazioni : [ {
                                        Query     : "EliminaFatturePassiveImportate",
                                        Parametri : { ID_FATTURA_PASSIVA : this.Chiave }
                                      },                                    
                                      {
                                        Query     : "EliminaAllegatiTramiteFatturaPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaVociTramiteFatturaPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaRitenuteTramiteFatturaPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      }, 
                                      {
                                        Query     : "EliminaCassePrevTramiteFattPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      }, 
                                      {
                                        Query     : "EliminaAliquoteTramiteFatturaPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaRateTramiteFatturaPassiva",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      },   
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_FATTURA_PASSIVA : this.Chiave }
                                      }, 
                                      {
                                        Query     : "EliminaRecordSaldiChiusureAnnuali",
                                        Parametri : {
                                                      CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_FORNITORE),
                                                      ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                    }
                                      }
                                    ]};
      
      this.DataTableRate.Righe.forEach(function(Riga)
      {              
          ObjQuery.Operazioni.push({
                                Query     : "EliminaRecordSaldiChiusureAnnuali",
                                Parametri : {
                                              CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                              ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_SCADENZA'].Valore)))
                                            }
                            });

          if(Riga.Dati['DATA_PAGAMENTO'].Valore != '')
          {
            ObjQuery.Operazioni.push({
                                        Query     : "EliminaRecordSaldiChiusureAnnuali",
                                        Parametri : {
                                                      CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_FORNITORE),
                                                      ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_PAGAMENTO'].Valore)))
                                                    }
                                    });
          }
      })

      this.AdvQuery.PostSQL('FatturePassive',ObjQuery,function()
      {
          OnSuccess();
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
   }

    GetFiltriAbilitati(OnSuccess)
    {
      var Anno = new Date(this.Dati.DATA)
      if(this.Dati.IS_FATTURA)
      {
        OnSuccess([{
                    Name : DASHBOARD_FILTER_TYPES.Fornitore,
                    Positions : [
                                      this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                      this.Dati.ID_FORNITORE,
                                      ID_NODO_FATTURE_PASSIVE,
                                      Anno.getFullYear(),
                                      this.Chiave
                                ]
                }])
      }
      else
      {
        if(!this.Dati.IS_FATTURA)
        {
            OnSuccess([{
                        Name : DASHBOARD_FILTER_TYPES.Fornitore,
                        Positions : [
                                          this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                          this.Dati.ID_FORNITORE,
                                          ID_NODO_NOTE_DI_CREDITO_PASSIVE,
                                          Anno.getFullYear(),
                                          this.Chiave
                                    ]
                    }])          
        }
      }
    }  
    
    DatiStampabili()
    {
      return true
    }

    Stampa(OnSuccess)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaFatturaPassiva', { Chiave : this.Chiave },
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
    VUEInputProvince,
    VUEInputCAP,
    VUEInputCodiceFiscale,
    VUEInputPartitaIVA,
    VUEAllegati,
    VUEDataTable,
    VUEInputFornitore,
    VUEInputNazioni,
   },

   data()
   {
     return { 
              CondPagamento       : SystemInformation.Configurazioni.CondPagamenti,
              DeveloperMode       : SystemInformation.DeveloperMode,
              StatoItaliano       : SystemInformation.Configurazioni.StatoItaliano,
              ListaMagazzini      : SystemInformation.Configurazioni.Magazzini,
              Tabs            : {
                                    ActiveTab : 'Generale',
                                    ModificheDaApplicare : false,
                                    Tabs      : [
                                                  {
                                                    Caption : 'Generale',
                                                    Id      : 'Generale'
                                                  },
                                                  {
                                                    Caption : 'Rate',
                                                    Id      : 'Rate'
                                                  },
                                                  {
                                                    Caption : 'Voci Fattura',
                                                    Id      : 'VociFatturaPassiva'
                                                  },
                                                  {
                                                    Caption : 'Dati Fiscali',
                                                    Id      : 'DatiFiscali'
                                                  },
                                                  {
                                                   Caption: 'Allegati',
                                                   Id     : 'Allegati'
                                                  }
                                                ]
                                 },
     }
  

  },
            
   
   props : ['SchedaFatturaPassiva'],
   computed :
   {
    CurrentTriggerRateFatturePassive :
     {
        get()
        {
          return this.SchedaFatturaPassiva.TriggerRate;
        }
     },

    CurrentFornitore()
    {
      return this.SchedaFatturaPassiva.Dati.ID_FORNITORE
    },

    TotaleRitenuta :
    {
      get()
      {
        var Result = 0
        for(let i = 0; i < this.SchedaFatturaPassiva.DataTableRitenute.length; i++)
            Result += this.SchedaFatturaPassiva.DataTableRitenute[i].IMPORTO;
        return Result;
      }
    },
    
    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaFatturaPassiva.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaFatturaPassiva.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },
  },
  watch:
  {
    CurrentTriggerRateFatturePassive : 
     {
        handler(NewValue,OldValue)
        {
          if(NewValue != OldValue)
            if(OldValue != undefined || NewValue != 0)
              if(this.SchedaFatturaPassiva.PulsanteAlberoPremuto)
              {
                this.OnClickRate()    
                this.SchedaFatturaPassiva.PulsanteAlberoPremuto = false
              }
        },
        immediate : true
     },

    CurrentFornitore:
     {
        handler(NewValue,OldValue)
        {
            
            var Self = this;
            if(this.SchedaFatturaPassiva.DatiModificati()) 
                if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
                  SystemInformation.AdvQuery.GetSQL('Fornitori', { CHIAVE : NewValue },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectFornitore");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                                Self.SchedaFatturaPassiva.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                                Self.SchedaFatturaPassiva.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                                Self.SchedaFatturaPassiva.Dati.NR_CIVICO_FATTURAZIONE   = ArrayInfo[0].NR_CIVICO_FATTURAZIONE;
                                                Self.SchedaFatturaPassiva.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                                Self.SchedaFatturaPassiva.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                                Self.SchedaFatturaPassiva.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                                Self.SchedaFatturaPassiva.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;
                                                Self.SchedaFatturaPassiva.Dati.IBAN                     = ArrayInfo[0].IBAN;
                                                Self.SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA          = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].EMITTENTE_PIVA);
                                                if(Self.SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA == 0)
                                                  Self.SchedaFatturaPassiva.Dati.NAZIONE_EM_PIVA = -1
                                                if(ArrayInfo[0].PROVINCIA_FATTURAZIONE == null || ArrayInfo[0].PROVINCIA_FATTURAZIONE == undefined)
                                                    Self.SchedaFatturaPassiva.Dati.PROVINCIA_FATTURAZIONE = -1
                                                else Self.SchedaFatturaPassiva.Dati.PROVINCIA_FATTURAZIONE  = parseInt(ArrayInfo[0].PROVINCIA_FATTURAZIONE);  
                                              }
                                                  
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectFornitore')
           
        }
     },
  },
  methods: 
  {  
    OnClickRicevuta()
    {
      if (this.SchedaFatturaPassiva.Dati.RICEVUTA)
        this.SchedaFatturaPassiva.Dati.RICEVUTA = false
      else this.SchedaFatturaPassiva.Dati.RICEVUTA = true
    },

    OnAllegatiChange()
    {
      this.SchedaFatturaPassiva.Dati.ModificaTabellaAllegati = this.SchedaFatturaPassiva.SchedaAllegati.Modificato();
    },

    OnClickCopiaDaPartitaIva()
    {
      this.SchedaFatturaPassiva.Dati.CODICE_FISCALE = this.SchedaFatturaPassiva.Dati.PARTITA_IVA
    },

    OnDataChanged()
    {
       this.SchedaFatturaPassiva.Dati.ModificaTabellaVoci = this.SchedaFatturaPassiva.DataTable.Modificato();
    },

    OnRateChanged()
    {
      this.SchedaFatturaPassiva.Dati.ModificaTabellaRate = this.SchedaFatturaPassiva.DataTableRate.Modificato();
    },

    OnRitenuteChanged()
    {
      this.SchedaFatturaPassiva.Dati.ModificaTabellaRitenute = this.SchedaFatturaPassiva.DataTableRitenute.Modificato();
    },

    OnCasseChanged()
    {
      this.SchedaFatturaPassiva.Dati.ModificaTabellaCasse = this.SchedaFatturaPassiva.DataTableCasse.Modificato();
    },

    OnAliquotaChanged()
    {
      this.SchedaFatturaPassiva.Dati.ModificaTabellaAliquote = this.SchedaFatturaPassiva.DataTableAliquote.Modificato();
    },

    OnClickTipologiaDocumento()
    {
      if(this.SchedaFatturaPassiva.Dati.IS_FATTURA)
        this.SchedaFatturaPassiva.Dati.IS_FATTURA = false
      else this.SchedaFatturaPassiva.Dati.IS_FATTURA = true
    },

    ConteggioRitenuta()
    {
      let Result = 0
      if(this.SchedaFatturaPassiva.DataTableRitenute.Righe.length != 0)
      {
        for(let i = 0; i < this.SchedaFatturaPassiva.DataTableRitenute.Righe.length; i++)
        {
          Result += this.SchedaFatturaPassiva.DataTableRitenute.Righe[i].Dati['IMPORTO'].Valore
        }
      }
      return Result
    },

    ControlloRataPagata()
    {
      let Result = false 
      for(let i = 0; i < this.SchedaFatturaPassiva.DataTableRate.Righe.length; i++)
      {
        if(this.SchedaFatturaPassiva.DataTableRate.Righe[i].Dati['DATA_PAGAMENTO'].Valore != '')
          Result = true
      }
      return Result
    },

    OnClickRicalcolaTotaliFatturaPassiva()
    {
      if(this.SchedaFatturaPassiva.DataTable.Righe.length != 0)
      {
        if(!this.ControlloRataPagata())
        {
          this.SchedaFatturaPassiva.Dati.TOTALE_FATTURA = 0
          let Self = this
          this.SchedaFatturaPassiva.DataTable.Righe.forEach(function(Voce)
          { 
            Self.SchedaFatturaPassiva.Dati.TOTALE_FATTURA += Voce.Dati['TOTALE'].Valore
          })
          this.SchedaFatturaPassiva.Dati.TOTALE_FATTURA += this.ConteggioRitenuta()
          this.SchedaFatturaPassiva.Dati.TOTALE_FATTURA = Math.round( this.SchedaFatturaPassiva.Dati.TOTALE_FATTURA * 1e2 ) / 1e2;
          this.AggiungiRataDalTotale()
        }
      }
    },

    CalcolaTotaleVociFatturaPassiva()
    {
      var Totale = 0;

      if (this.SchedaFatturaPassiva.DataTable.Righe.length != 0)
      {
        this.SchedaFatturaPassiva.DataTable.Righe.forEach(function(Voce) 
        {
          Totale += Voce.Dati['TOTALE'].Valore;
        });
      }

      Totale = Math.round(Totale * 100) / 100;

      return Totale;
    },



    AggiungiRataDalTotale()
    {
      this.SchedaFatturaPassiva.DataTableRate.Righe.forEach(function(Voce)
      {
        Voce.Eliminato = true
      })
      this.SchedaFatturaPassiva.DataTableRate.AddRowsOnTop(1)
      this.SchedaFatturaPassiva.DataTableRate.Righe[0].Dati['DATA_SCADENZA'].Valore = this.SchedaFatturaPassiva.Dati.DATA
      this.SchedaFatturaPassiva.DataTableRate.Righe[0].Dati['IMPORTO'].Valore       = this.SchedaFatturaPassiva.Dati.TOTALE_FATTURA
      this.SchedaFatturaPassiva.Dati.ModificaTabellaRate = true
    },
    
    OnClickRate()
    {

      this.Tabs.ActiveTab = 'Rate';     

      setTimeout(this.ScrollToRate, 250);
    },
  },                             
    
   
  beforeMount() 
  {
    if(this.SchedaFatturaPassiva.AlberoMovimenti)
    this.ActiveTab = 'Generale'
  },

}

</script>


<style scoped>
label 
{
 margin-bottom: 0px;
}
</style>