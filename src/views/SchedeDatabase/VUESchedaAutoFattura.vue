<template>
 <VUEConfirm :Popup="PopupCorreggiAutoFattura" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
             :Richiesta="'Vuoi correggere l\'autofattura?'" @onClickConfermaPopup="ConfermaCorrezione" @onClickChiudiPopup="AnnullaPopup">
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
          <img v-if="ATab.Id == 'AutoFattura'" src="@/assets/images/IconeAlbero/AutoFattura2.png" style="width:35px;height:35px;float:left;margin-top:-7px">               
        </a>
      </li>
    </ul>
   </header>
   <div style="height:5px;"></div>

   <div  v-if="Tabs.ActiveTab == 'AutoFattura'">
      <div class="ZMNuovaRigaScheda">
        <div style="float:left; margin-left: 10px">
          <!-- <div style="clear:both;padding-bottom:1%"></div> -->
          <text style="font-weight: bold;">Tipologia</text>
          <select class="form-control"  v-model="SchedaAutoFattura.Dati.TIPO_AUTOFATTURA" :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI">
              <option :value="TipoAutofattura.ExtraComunitarie">TD17 - Extra comunitarie</option>
              <option :value="TipoAutofattura.IntraComunitarie">TD18 - Intra comunitarie</option>
              <option :value="TipoAutofattura.ExArticolo17">TD19 - Acquisto Beni (Ex Articolo 17)</option>
              <option :value="TipoAutofattura.IntegrFatture">TD20 - Regol. & Integraz. Fatture</option>
              <option :value="TipoAutofattura.Splafonamento">TD21 - Splafonamento</option>
              <option :value="TipoAutofattura.SanMarino">TD28 - San Marino</option>
          </select>
        </div>
        <div style="float:right;margin-left:10px">
            <text style="font-weight: bold;">Data</text>
            <input :readonly="SchedaAutoFattura.Dati.NUMERO != 0 || SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" type="date" id="input-data" class="form-control" v-model="SchedaAutoFattura.Dati.DATA"/>
            <label v-if="SchedaAutoFattura.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:right;margin-left:10px">
            <text style="font-weight: bold;">Numero del fornitore</text>
            <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaAutoFattura.Dati.NUMERO_DEL_FORNITORE"/>
            <label v-if="SchedaAutoFattura.Dati.NUMERO_DEL_FORNITORE == ''" class="ZMFormLabelError">Campo obbligatorio</label>        
        </div>
        <div v-if="SchedaAutoFattura.Dati.NUMERO != 0" style="float:right;width:13%">
            <text style="float:left;font-weight: bold;">Numero</text>
            <label style="float:left;font-size:15px;width:151px;height:34px;display:inline-block;padding-top:6px;padding-left: 14px;" class="ZMLabel">A{{ SchedaAutoFattura.Dati.NUMERO }}/{{ SchedaAutoFattura.Dati.ANNO }}</label>
        </div>

      </div>
      <div style="clear:both;padding-bottom:1%"></div>
      <div class="ZMSeparatoreScheda" >    
        <text style="font-weight: bold;">Intestatario</text>
        <hr style="margin-top:5px">
      </div>
      <div class="col-md-4" v-if="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" style="border:1px solid #b3dbff">
            <img src="@/assets/images/IconeAlbero/Inviata2.png" style="margin-top:6px;float:left;width:20%">
            <text style="float:left;margin-top:2%;font-weight: bold;font-size:15px;width:80%">AUTOFATTURA TRASMESSA ALLO SDI</text>
      </div>
      <div class="col-md-4" v-if="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickScaricaFileAutoFattura">Scarica file xml</button>
      </div>
      <div class="col-md-4" v-if="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI">
            <button class="btn btn-sm btn-info" style="height:36px;width:100%" @click="OnClickCorreggiAutoFattura">Correggi Autofattura</button>
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:34%">
          <text style="font-weight: bold; float:left; width:100%">Fornitore</text>
          <VUEInputFornitore v-model="SchedaAutoFattura.Dati.ID_FORNITORE" emptyElement="true" :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI"/>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:32%">
          <text style="font-weight: bold; float:left; width:100%">Ragione Sociale </text>
          <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.RAGIONE_SOCIALE" type="text" class="form-control" placeholder="Ragione Sociale"/>
          <label v-if="SchedaAutoFattura.Dati.RAGIONE_SOCIALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:32%">
          <label style="font-weight: bold;">Regime fiscale</label>
          <select v-model="SchedaAutoFattura.Dati.REGIME_FISCALE" class="form-control" :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI">
            <option v-for="SelectOption in LsRegimiFiscali" 
                          :Key="SelectOption.Id"
                          :value="SelectOption.Id"
                          :selected="SelectOption.Id == SchedaAutoFattura.Dati.REGIME_FISCALE">
                          {{SelectOption.Descrizione}}
            </option>       
          </select>
          <label v-if="SchedaAutoFattura.Dati.REGIME_FISCALE == ''" class="ZMFormLabelError">Campo obbligatorio</label> 
        </div>
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:20%;">
          <label style="font-weight: bold;">Tipo ritenuta</label>
          <select v-model="SchedaAutoFattura.Dati.TIPO_RITENUTA" class="form-control" :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI">
            <option v-for="SelectOption in LsTipoRitenuta" 
                          :Key="SelectOption.CHIAVE"
                          :value="SelectOption.CHIAVE"
                          :selected="SelectOption.CHIAVE == SchedaAutoFattura.Dati.TIPO_RITENUTA">
                          {{SelectOption.DESCRIZIONE}}
            </option>      
          </select>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:19%">
            <label style="font-weight: bold;">Codice SDI</label>
            <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaAutoFattura.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
            <label v-if="SchedaAutoFattura.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
              {{(SchedaAutoFattura.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
            </label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:59%">
          <label style="font-weight: bold;">PEC</label>
          <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" type="email" class="form-control" v-model="SchedaAutoFattura.Dati.PEC" placeholder="PEC"/>
        </div> 
      </div>
      <div style="clear:both">
       <div style="float:left;width:80%">
        <text style="font-weight: bold">Indirizzo</text>
        <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.INDIRIZZO_FATTURAZIONE" type="text" class="form-control" placeholder="Indirizzo">
       </div>
       <div style="float:left;width:1%;">&nbsp;</div>
       <div style="float:left;width:19%">
           <label style="font-weight: bold;">Civico</label>
           <input maxlength="7" :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" type="text" class="form-control" v-model="SchedaAutoFattura.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
       </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:40%">
          <text style="font-weight: bold">Comune</text>
          <input :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.COMUNE_FATTURAZIONE" type="text" class="form-control" placeholder="Comune">
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <label style="font-weight: bold;">Provincia</label>
          <VUEInputProvince :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true" style="cursor:default"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
          <label style="font-weight: bold;">CAP</label>
          <VUEInputCAP :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" placeholder="CAP" v-model="SchedaAutoFattura.Dati.CAP_FATTURAZIONE"/>
          <label v-if="SchedaAutoFattura.Dati.CAP_FATTURAZIONE == '' || SchedaAutoFattura.Dati.CAP_FATTURAZIONE.length != 5" class="ZMFormLabelError">Campo obbligatorio</label>
        </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:25%">
          <text style="font-weight: bold">Partita IVA</text>
          <VUEInputPartitaIVA :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.PARTITA_IVA" placeholder="Partita IVA"></VUEInputPartitaIVA>
          <label v-if="(SchedaAutoFattura.Dati.PARTITA_IVA.trim() == '' && SchedaAutoFattura.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
              class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaAutoFattura.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Codice Fiscale</label>
          <VUEInputCodiceFiscale :readonly="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.CODICE_FISCALE" style="float:left" placeholder="Codice Fiscale"></VUEInputCodiceFiscale>
          <label v-if="(SchedaAutoFattura.Dati.PARTITA_IVA.trim() == '' && SchedaAutoFattura.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
            class="ZMFormLabelError">Campo obbligatorio</label>
          <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaAutoFattura.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Nazione emittente</label>
          <VUEInputNazioni :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" v-model="SchedaAutoFattura.Dati.NAZIONE_EM_PIVA" emptyElement="false" style="cursor:default"/>
          <label v-if="SchedaAutoFattura.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">&nbsp;</label>
          <button :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" class="btn btn-sm btn-info" style="float:left;width:100%;cursor:default" @click="OnClickCopiaDaPartitaIva">Copia da Partita IVA</button>
        </div>
      </div>
      <div style="clear:both; padding-bottom:1%"></div>
   </div>

   <div  v-if="Tabs.ActiveTab == 'VociAutoFattura'" style="width:100%;">
    <div class="ZMSeparatoreScheda" >    
      <text style="font-weight: bold;">Voci</text>
    </div>
    <div class="ZMNuovaRigaScheda">
      <div style="float:left;width:50%">
        <text style="font-weight: bold">Esigibilità IVA</text>
        <VUEInputEsigibilitaIVA :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" @change="OnEsigibilitaChange" v-model="SchedaAutoFattura.Dati.ESIGIBILITA_IVA" style="cursor:default"/>
        <label v-if="SchedaAutoFattura.Dati.ESIGIBILITA_IVA == '' || SchedaAutoFattura.Dati.ESIGIBILITA_IVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:49%">
        <text style="font-weight: bold">Causale</text>
        <VUEInputCausali :disabled="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI" 
                          v-model="SchedaAutoFattura.Dati.CAUSALE" style="cursor:default"
                         :Riferimento="RiferimentiCausali.Fornitore"/>
      </div>
    </div>
    <div style="clear:both">&nbsp;</div>

    <div v-if="SchedaAutoFattura.Dati.ID_FORNITORE != -1">
      <VUEVociDocumentiEconomici :SchedaVociDocumentiEconomici = "SchedaAutoFattura.SchedaVociAutoFattura"
                                 :TastoCreaNotaVisibile="!SchedaAutoFattura.DatiModificati()"
                                 :InviataAlloSdi="SchedaAutoFattura.Dati.INVIATA_ALLO_SDI"
                                 @onChange="OnVociAutoFatturaChange"
                                 :IsSchedaFattura="false"
                                 :IdCliente="SchedaAutoFattura.Dati.ID_FORNITORE"
                                 :NascondiPulsanti="true"/>
    </div>
    <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;"> INSERIRE UN CLIENTE NELL' AUTOFATTURA</div>
    
   </div>

  <div  v-if="Tabs.ActiveTab == 'Allegati'">
  <VUEAllegati :SchedaAllegati="SchedaAutoFattura.SchedaAllegati" 
                NomeCampoModello="Autofatture"
                @onChange="OnAllegatiChange" />
  </div>
  <div  v-if="Tabs.ActiveTab == 'LogAutoFattura'" style="height:50%">
      <VUELogDocumentiEconomici :SchedaLogDocumentiEconomici="SchedaAutoFattura.SchedaLogAutoFattura"/>
   </div>
</div>
</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation,
         FATT_ELE_ESIGIBILITA_IVA,
         DASHBOARD_FILTER_TYPES,
         TIPO_AUTOFATTURA,
         RIFERIMENTO_CAUSALI,
         RUOLI,
         NOME_PROGRAMMA } from '@/SystemInformation.js'
import { TZFatturaElettronica,
         TZFattElettronicaRegimeFiscale,
         TZFatturaTipoRitenuta } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputPartitaIVA from '@/components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputCausali from '@/components/InputComponents/VUEInputCausali.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUELogDocumentiEconomici, { TSchedaLogDocumentiEconomici } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogDocumentiEconomici.vue';
import VUEVociDocumentiEconomici, {TSchedaVociDocumentiEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { ID_NODO_AUTOFATTURE } from '@/NodiVuoti'
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js'
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { saveAs } from 'file-saver';
import VUEModal from '@/components/Slots/VUEModal.vue';

export class TSchedaAutoFattura extends TSchedaGenerica
{
  OnInitialization()
  {
     this.SchedaAllegati        = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaVociAutoFattura = new TSchedaVociDocumentiEconomici()
     this.SchedaVociAutoFattura.ClearVociDocumentiEconomici()
     this.SchedaLogAutoFattura  = new TSchedaLogDocumentiEconomici()
     this.SchedaLogAutoFattura.ClearLogDocumentiEconomici()
  }

  CanRecord()
  {
    return (this.Dati.PARTITA_IVA.trim() != '' || this.Dati.CODICE_FISCALE.trim() != '' || SystemInformation.DeveloperMode) &&
            this.Dati.DATA != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            this.Dati.CAP_FATTURAZIONE != '' &&
            (this.Dati.ESIGIBILITA_IVA != '' && this.Dati.ESIGIBILITA_IVA != -1) &&
            this.Dati.CAP_FATTURAZIONE.length == 5 &&
            this.Dati.NUMERO_DEL_FORNITORE.trim() != '' &&
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            TZDateFunct.DateFromHTMLInput(this.Dati.DATA) > new Date(2020,1,1) &&
            this.Dati.RAGIONE_SOCIALE != '' &&
            (this.Dati.CODICE_FISCALE == '' || 
              (TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || SystemInformation.DeveloperMode) || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              (TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || SystemInformation.DeveloperMode) || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&            
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaVociAutoFattura.AllDataOk() &&
            this.SchedaVociAutoFattura.VociPresenti() 
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;

    return true;
  }

  Elimina(OnSuccess,OnError)
  {
    this.InEliminazione = true;
    var ObjQuery = { Operazioni : [ 
                                    
                                    {
                                      Query     : "EliminaVociTramiteAutoFattura",
                                      Parametri : { CHIAVE_AUTOFATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaAllegatiTramiteAutoFattura",
                                      Parametri : { CHIAVE_AUTOFATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaLogTramiteAutoFattura",
                                      Parametri : { CHIAVE_AUTOFATTURA : this.Chiave }
                                    },
                                    {
                                      Query     : "Elimina",
                                      Parametri : { CHIAVE_AUTOFATTURA : this.Chiave }
                                    },
                                  ]};
    this.AdvQuery.PostSQL('Autofatture',ObjQuery,function()
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
                                                  NUMERO_DEL_FORNITORE      : TSchedaGenerica.PrepareForRecordString(this.Dati.NUMERO_DEL_FORNITORE),
                                                  ID_FORNITORE              : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_FORNITORE),
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(this.Dati.RAGIONE_SOCIALE),
                                                  REGIME_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.REGIME_FISCALE),
                                                  TIPO_RITENUTA             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.TIPO_RITENUTA),
                                                  CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CODICE_FISCALE),
                                                  INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_FATTURAZIONE),
                                                  NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_FATTURAZIONE),
                                                  PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_FATTURAZIONE),
                                                  COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_FATTURAZIONE),
                                                  CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_FATTURAZIONE),
                                                  DATA                      : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA),
                                                  PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(this.Dati.PARTITA_IVA),
                                                  NAZIONE_EM_PIVA           : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_EM_PIVA),
                                                  RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.SchedaVociAutoFattura.RitornaRitenutaAcconto()),
                                                  ESIGIBILITA_IVA           : TSchedaGenerica.PrepareForRecordString(this.Dati.ESIGIBILITA_IVA),
                                                  CAUSALE                   : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CAUSALE),
                                                  INVIATA_ALLO_SDI          : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.INVIATA_ALLO_SDI),
                                                  ENTE_PUBBLICO             : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.ENTE_PUBBLICO),
                                                  COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(this.Dati.COD_ENTE_SDI),
                                                  PEC                       : TSchedaGenerica.PrepareForRecordString(this.Dati.PEC),
                                                  TIPO_AUTOFATTURA          : TSchedaGenerica.PrepareForRecordString(this.Dati.TIPO_AUTOFATTURA),
                                               }
                                 });
        this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_AUTOFATTURA')
        this.SchedaVociAutoFattura.PrepareQueryParameters(ObjQuery.Operazioni, 'ID_AUTOFATTURA')
        this.AdvQuery.PostSQL('Autofatture',ObjQuery,function(Response)
         {
          if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
            Self.SchedaAllegati.DeleteFileDaEliminare()
           ObjQuery = {};
           if(Self.Chiave == -1)
              Self.Chiave = Response.NewKey1;
           Self.Dati.ModificaTabellaAllegati = 0;
           Self.Dati.ModificaTabellaVoci     = 0
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
      this.Chiave                   = Riassunto.CHIAVE;
      this.SchedaAllegati.SetIdDocumento(this.Chiave)
      this.SchedaVociAutoFattura.SetIdDocumento(this.Chiave)
      this.Dati.ID_FORNITORE        = Riassunto.ID_FORNITORE;
      this.Dati.ANNO                = Riassunto.ANNO
      if(Riassunto.NUMERO != null && Riassunto.NUMERO != undefined)
          this.Dati.NUMERO          = Riassunto.NUMERO;
      else this.Dati.NUMERO         = 0;
      this.Dati.RAGIONE_SOCIALE     = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE)
      this.TOTALE                   = Riassunto.TOTALE //_dg_ SENZA Disponi perché NON arriva dal DataBase, bensì è CALCOLATO
      this.CreateSnapshot();
    }

    GetDescrizione()
    {
      let Descrizione = 'Autofat.ra A' + this.Dati.NUMERO + '/' + this.Dati.ANNO

      Descrizione    += this.TOTALE != null && this.TOTALE != 0 ? ' - Tot: ' + TZEconomicFunct.EuropeanCurrencyFormat(this.TOTALE) : '';

      return Descrizione
    }
   
    Clear()
    {
      this.SchedaAllegati.SetIdDocumento(-1)
      this.SchedaVociAutoFattura.SetIdDocumento(-1)
      this.SchedaLogAutoFattura.AssignDati([])
      this.SchedaAllegati.AssignDati([])
      this.SchedaVociAutoFattura.AssignDati([], 0, 0, 'ID_AUTOFATTURA', -1, undefined ,undefined)
      this.Dati = {
                      NUMERO                        : 0,
                      NUMERO_DEL_FORNITORE          : '',
                      ID_FORNITORE                  : 0,
                      DATA                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
                      RAGIONE_SOCIALE               : '',
                      REGIME_FISCALE                : TZFattElettronicaRegimeFiscale.ID_FATT_REGIME_FISCALE_RF01,
                      TIPO_RITENUTA                 : TZFatturaTipoRitenuta.ritPersonaGiuridica,
                      INDIRIZZO_FATTURAZIONE        : '',
                      NR_CIVICO_FATTURAZIONE        : '',
                      COMUNE_FATTURAZIONE           : '',
                      PROVINCIA_FATTURAZIONE        : -1,
                      CAP_FATTURAZIONE              : '',
                      PARTITA_IVA                   : '',
                      CODICE_FISCALE                : '',
                      INVIATA_ALLO_SDI              : false,
                      ENTE_PUBBLICO                 : false,
                      COD_ENTE_SDI                  : '0000000',
                      PEC                           : '',
                      CAUSALE                       : '',
                      NAZIONE_EM_PIVA               : SystemInformation.Configurazioni.StatoItaliano,
                      ESIGIBILITA_IVA               : '',
                      TIPO_AUTOFATTURA              : TIPO_AUTOFATTURA.ExtraComunitarie,
                      // Dati allegati
                      ModificaTabellaAllegati       : 0,
                      ModificaTabellaVoci           : 0
                  }
      super.Clear();

    }

    Disponi(OnSuccess)
    {
      var Self = this;
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('Autofatture',{ CHIAVE : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo            = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayAllegati        = Self.AdvQuery.FindResults(Results,"AllegatiAutoFattura");
                                            let ArrayVociAutoFattura = Self.AdvQuery.FindResults(Results,"VociAutoFattura");
                                            let ArrayLogAutoFattura  = Self.AdvQuery.FindResults(Results,"LogAutoFattura");
                                            if(ArrayInfo != undefined)
                                            {
                                              if(ArrayLogAutoFattura.length != 0)
                                                Self.SchedaLogAutoFattura.AssignDati(ArrayLogAutoFattura,'ID_AUTOFATTURA')
                                              
                                              if(ArrayAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayAllegati)
                                              if(ArrayVociAutoFattura.length != 0)
                                                Self.SchedaVociAutoFattura.AssignDati(ArrayVociAutoFattura,
                                                                                      ArrayInfo[0].IVA_FORNITORE? TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_FORNITORE) / 10 : 0,
                                                                                      TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA),
                                                                                      'ID_AUTOFATTURA',
                                                                                      TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NATURA_PAGAMENTO),
                                                                                      undefined,
                                                                                      undefined,
                                                                                      ArrayInfo[0].ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione? true : false)
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = { 
                                                              NUMERO                        : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO),
                                                              NUMERO_DEL_FORNITORE          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NUMERO_DEL_FORNITORE),
                                                              ID_FORNITORE                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_FORNITORE),
                                                              RAGIONE_SOCIALE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              REGIME_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].REGIME_FISCALE),
                                                              TIPO_RITENUTA                 : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TIPO_RITENUTA),
                                                              INDIRIZZO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                              NR_CIVICO_FATTURAZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                              COMUNE_FATTURAZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                              PROVINCIA_FATTURAZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                              CAP_FATTURAZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                              DATA                          : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA),
                                                              PARTITA_IVA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              CODICE_FISCALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              NAZIONE_EM_PIVA               : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                                                              CAUSALE                       : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CAUSALE),
                                                              ESIGIBILITA_IVA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                                                              INVIATA_ALLO_SDI              : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].INVIATA_ALLO_SDI),                                                              
                                                              COD_ENTE_SDI                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI),                                                              
                                                              PEC                           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),                                                              
                                                              TIPO_AUTOFATTURA              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].TIPO_AUTOFATTURA),
                                                             
                                                              ModificaTabellaAllegati       : 0,
                                                              ModificaTabellaVoci           : 0,
                                                }
                                                if(Self.Dati.DATA != '')
                                                  Self.Dati.ANNO = new Date(Self.Dati.DATA).getFullYear();
                                                else Self.Dati.ANNO = null;
                                                Self.CreateSnapshot();

                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('AutoFattura eliminata')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio della AutoFattura');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
    }

    GetClassName()
    {
      return 'TSchedaAutoFattura';
    }

    GetImageIndex()
    {
       return 'AutoFattura.png'
    }

   GetFiltriAbilitati(OnSuccess)
   {
      var Anno = new Date(this.Dati.DATA)
      OnSuccess([{
                   Name : DASHBOARD_FILTER_TYPES.Fornitore,
                   Positions : [
                                  this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                  this.Dati.ID_FORNITORE,
                                  ID_NODO_AUTOFATTURE,
                                  Anno.getFullYear(),
                                  this.Chiave
                               ]
              }])
   }

    // DatiStampabili()
    // {
    //   return true
    // }

    // Stampa(OnSuccess)
    // {
    //   SystemInformation.AdvQuery.ExecuteExternalScript('StampaAutoFattura', { Chiave : this.Chiave },
    //                                                   function(Result)
    //                                                   {  
    //                                                     if(Result.PDF != undefined)
    //                                                       OnSuccess('data:application/pdf;base64,' + Result.PDF);
    //                                                     else SystemInformation.HandleError('Documento non presente','','');
    //                                                   },
    //                                                   function(HTTPError,SubHTTPError,Response)
    //                                                   {
    //                                                     SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
    //                                                   })   
    // }    
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
    VUEVociDocumentiEconomici,
    VUEInputEsigibilitaIVA,
    VUEInputCausali,
    VUEInputNazioni,
    VUEConfirm,
    VUEModal,
    VUELogDocumentiEconomici,
    VUEInputFornitore,
   },
   data()
   {
     return { 
              TotaleAutoFattura         : 0, 
              DeveloperMode             : SystemInformation.DeveloperMode,
              StatoItaliano             : SystemInformation.Configurazioni.StatoItaliano,
              PopupCorreggiAutoFattura  : false,
              PopupScegliFileXML        : false,
              TipoAutofattura           : TIPO_AUTOFATTURA,
              ListaFileXML              : [],
              RiferimentiCausali        : RIFERIMENTO_CAUSALI,
              LsRegimiFiscali           : TZFatturaElettronica.GetLsRegimiFiscali(),
              LsTipoRitenuta            : TZFatturaElettronica.GetLsTipoRitenuta(),
              VisibilitaLogVariazioni   : SystemInformation.AccessRights.VisibilitaLogVariazioni(),
              NomeProgramma             : NOME_PROGRAMMA,

              Tabs                      : {
                                            ActiveTab : 'AutoFattura',
                                            Tabs      : [
                                                          {
                                                            Caption : 'Autofattura',
                                                            Id      : 'AutoFattura'
                                                          },
                                                          {
                                                            Caption : 'Voci',
                                                            Id      : 'VociAutoFattura'
                                                          },
                                                          {
                                                            Caption: 'Allegati',
                                                            Id     : 'Allegati'
                                                          },
                                                          {
                                                            Caption : 'Variazioni',
                                                            Id      : 'LogAutoFattura' 
                                                          }
                                                        ]
                                          }
            }
   },
   props : ['SchedaAutoFattura'],
   computed :
   {
     CurrentFornitore()
     {
       return this.SchedaAutoFattura.Dati.ID_FORNITORE
     },

    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaAutoFattura.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaAutoFattura.Dati.CODICE_FISCALE);
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
    
    TabsVisibili()
    {
      return this.Tabs.Tabs.filter(tab => tab.Id !== 'LogAutoFattura' || this.VisibilitaLogVariazioni);
    }
   },
   
   watch:
   {
    CurrentFornitore:
     {
        handler(NewValue,OldValue)
        {
            
            var Self = this;
            if(this.SchedaAutoFattura.DatiModificati()) 
                if(NewValue != OldValue && NewValue != undefined && NewValue != -1)
                  SystemInformation.AdvQuery.GetSQL('Fornitori', { CHIAVE : NewValue },
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectFornitore");
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                              Self.SchedaAutoFattura.Dati.RAGIONE_SOCIALE          = ArrayInfo[0].RAGIONE_SOCIALE;
                                              Self.SchedaAutoFattura.Dati.REGIME_FISCALE           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].REGIME_FISCALE);
                                              Self.SchedaAutoFattura.Dati.TIPO_RITENUTA            = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TIPO_RITENUTA);
                                              Self.SchedaAutoFattura.Dati.INDIRIZZO_FATTURAZIONE   = ArrayInfo[0].INDIRIZZO_FATTURAZIONE;
                                              Self.SchedaAutoFattura.Dati.NR_CIVICO_FATTURAZIONE   = ArrayInfo[0].NR_CIVICO_FATTURAZIONE;
                                              Self.SchedaAutoFattura.Dati.PARTITA_IVA              = ArrayInfo[0].PARTITA_IVA;
                                              Self.SchedaAutoFattura.Dati.COMUNE_FATTURAZIONE      = ArrayInfo[0].COMUNE_FATTURAZIONE;
                                              Self.SchedaAutoFattura.Dati.CAP_FATTURAZIONE         = ArrayInfo[0].CAP_FATTURAZIONE;
                                              Self.SchedaAutoFattura.Dati.CODICE_FISCALE           = ArrayInfo[0].CODICE_FISCALE;
                                              Self.SchedaAutoFattura.Dati.PEC                      = ArrayInfo[0].PEC
                                              Self.SchedaAutoFattura.Dati.NAZIONE_EM_PIVA          = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].EMITTENTE_PIVA)
                                              Self.SchedaAutoFattura.Dati.TIPO_AUTOFATTURA         = ArrayInfo[0].TIPO_AUTOFATTURA
                                              Self.SchedaAutoFattura.Dati.PROVINCIA_FATTURAZIONE   = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE);

                                              Self.SchedaAutoFattura.SchedaVociAutoFattura.SetDatiCliente(ArrayInfo[0].IVA_FORNITORE / 10,
                                                                                                          ArrayInfo[0].RITENUTA / 10)
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

    OnClickChiudiPopupScegliFile()
    {
      this.ListaFileXML       = []
      this.PopupScegliFileXML = false
    },

    ConfermaPopup()
    {
       var Self = this;
       SystemInformation.AdvQuery.ExecuteExternalScript('NumeraAutoFattura', { ChiaveAutoFattura : this.SchedaAutoFattura.Chiave },
                                                        function()
                                                        {
                                                              Self.SchedaAutoFattura.Disponi()
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        })  
       this.AnnullaPopup()
    },

    ConfermaCorrezione()
    {
      var Self = this
      var ObjQuery = {Operazioni : []}
      ObjQuery.Operazioni.push({Query : 'CorreggiAutoFattura',
                                        Parametri : { CHIAVE : this.SchedaAutoFattura.Chiave }})

      SystemInformation.AdvQuery.PostSQL('Autofatture',ObjQuery,
                                              function()
                                              { 
                                                Self.SchedaAutoFattura.Disponi() 
                                              },
                                              SystemInformation.HandleError)
      this.AnnullaPopup()
    },

    AnnullaPopup()
    {
       this.PopupCorreggiAutoFattura = false
    },

    OnAllegatiChange()
    {
      if(this.SchedaAutoFattura.SchedaAllegati.Modificato())
         this.SchedaAutoFattura.Dati.ModificaTabellaAllegati++
       else this.SchedaAutoFattura.Dati.ModificaTabellaAllegati = 0;
    },

    OnVociAutoFatturaChange()
    { 
      if(this.SchedaAutoFattura.SchedaVociAutoFattura.Modificato())
         this.SchedaAutoFattura.Dati.ModificaTabellaVoci++
      else this.SchedaAutoFattura.Dati.ModificaTabellaVoci = 0
    },

    OnEsigibilitaChange()
    { 
      if(this.SchedaAutoFattura.Dati.ESIGIBILITA_IVA == FATT_ELE_ESIGIBILITA_IVA.Scissione)
        this.SchedaAutoFattura.SchedaVociAutoFattura.SetSplitPayment(true)
      else this.SchedaAutoFattura.SchedaVociAutoFattura.SetSplitPayment(false)

      this.SchedaAutoFattura.SchedaVociAutoFattura.CalcoloTotaliFattura()
    },

    OnClickCopiaDaPartitaIva()
    {
      this.SchedaAutoFattura.Dati.CODICE_FISCALE = this.SchedaAutoFattura.Dati.PARTITA_IVA
    },

    OnClickScaricaFileAutoFattura()
    {
      var Self = this
      SystemInformation.AdvQuery.GetSQL('Autofatture', { CHIAVE : this.SchedaAutoFattura.Chiave },
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ScaricaFileAutoFattura");
                                          if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                          {
                                            if(ArrayInfo.length == 1)
                                               saveAs(TZStringConvFunct.MoveStringToBlob(ArrayInfo[0].XML_BODY), 'Autofattura_N_' + Self.SchedaAutoFattura.Dati.NUMERO + '_DataInvio' + TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ArrayInfo[0].DATA_INVIO)) + '.xml'); 
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
                                        'ScaricaFileAutoFattura')
    },

    OnClickCorreggiAutoFattura()
    {
      this.PopupCorreggiAutoFattura = true         
    },

    OnClickDownloadFilesXML()
    {
      var Self = this
      for(var i = 0; i < this.ListaFileXML.length; i++)
        if(this.ListaFileXML[i].Selezionato)
          saveAs(TZStringConvFunct.MoveStringToBlob(Self.ListaFileXML[i].XML_BODY), 'Autofattura-' + Self.SchedaAutoFattura.Dati.NUMERO + '_DataInvio_' + Self.ListaFileXML[i].DATA_INVIO + '.xml'); 
      this.PopupScegliFileXML = false
    }

   },                             
    
   
   beforeMount() 
   {
     this.ActiveTab = 'AutoFattura'
   },

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>