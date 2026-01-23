<template>
  <div class="ZMCorpoSchedeDati">
    <header class="panel-heading bg-light">
      <ul class="nav nav-tabs nav-justified">
        <li v-for="ATab in Tabs.Tabs" :key="ATab.Id" :class="{ active: ATab.Id == Tabs.ActiveTab }">
          <a @click="Tabs.ActiveTab = ATab.Id">
            {{ ATab.Caption }}
            <img v-if="ATab.Id == 'Documento di scarico'" 
                 src="@/assets/images/IconeAlbero/DDT2.png" 
                 style="width:35px;height:35px;float:left;margin-top:-9px">  
          </a>
        </li>
      </ul>
    </header>

    <div style="height:5px;"></div>

    <!-- Documento di scarico prodotti composti -->
    <div v-if="Tabs.ActiveTab == 'Documento di scarico'">
      <div class="ZMNuovaRigaScheda" style="height:10px">&nbsp;</div>

      <div class="col-md-6" style="float:right">
        <div style="float:right;margin-left:10px;width:30%">
          <text style="font-weight:bold;">Data</text>
          <input type="date" id="input-data" 
                 class="form-control" 
                 @change="OnChangeData" 
                 v-model="SchedaDocScaricoProdottiComposti.Dati.DATA_DOCUMENTO"/>
          <label v-if="SchedaDocScaricoProdottiComposti.Dati.DATA_DOCUMENTO == ''" 
                 class="ZMFormLabelError">Campo obbligatorio</label>
        </div>

        <div style="float:right;width:30%" v-if="SchedaDocScaricoProdottiComposti.Dati.NUMERO_DOCUMENTO != -1">
          <text style="float:left;font-weight:bold;padding-right:10%">D.D.T. n.</text>
          <div style="clear:both"></div>
          <label style="float:left;font-size:15px;width:151px;height:34px;padding-top:6px;padding-left:14px;" 
                 class="ZMLabel">{{ SchedaDocScaricoProdottiComposti.Dati.NUMERO_DOCUMENTO }}</label>
        </div>
      </div>

    </div>

      <!-- Voci Prodotti -->
      <div v-if="Tabs.ActiveTab == 'VociProdotti'">
        <div style="clear:both"></div>

        <div class="ZMSeparatoreScheda" style="margin-top:5px">
          <text style="font-weight:bold;">Note</text>
        </div>
        <div class="ZMNuovaRigaScheda">
          <text style="font-weight:bold">Note</text>
          <textarea style="resize:none" 
                    @input="SchedaDocScaricoProdottiComposti.CheckDimensioniNote" 
                    :style="{ height: SchedaDocScaricoProdottiComposti.AltezzaTextAreaNote ? SchedaDocScaricoProdottiComposti.AltezzaTextAreaNote : '34px' }" 
                    type="text" class="form-control" 
                    v-model="SchedaDocScaricoProdottiComposti.Dati.NOTE"/>
        </div>
    

      <!-- Magazzini -->
      <div v-if="ListaMagazzini.length > 1">
        <div style="clear:both"></div>
        <div class="ZMSeparatoreScheda" style="margin-top:5px">
          <text style="font-weight:bold;">Magazzini</text>
        </div>
        <div class="ZMNuovaRigaScheda">
          <text style="font-weight:bold">Magazzino</text>
          <select class="form-control" 
                  v-model="SchedaDocScaricoProdottiComposti.Dati.ID_MAGAZZINO"
                  style="width:25%;">
            <option v-for="Magazzino in ListaMagazzini" :key="Magazzino.CHIAVE" :value="Magazzino.CHIAVE">
              {{ Magazzino.DESCRIZIONE }}
            </option>
          </select>
        </div>
      </div>

      <div style="clear:both; padding-bottom:10px"></div>

      <VUEVociDocumentiNonEconomici 
        :SchedaVociDocumentiNonEconomici="SchedaDocScaricoProdottiComposti.SchedaVociDocScaricoProdottiComposti"
        NomeCampoDocumento="ID_DOCUMENTO"
        :IsSchedaScaricoProdotti="true"
        :IdMagazzino="SchedaDocScaricoProdottiComposti.Dati.ID_MAGAZZINO"
        @onChange="OnVociDocScaricoProdottiCompostiChange"/>    
     </div>

      <div v-if="Tabs.ActiveTab == 'Allegati'">
        <VUEAllegati 
          :SchedaAllegati="SchedaDocScaricoProdottiComposti.SchedaAllegati" 
          NomeCampoModello="DocumentiDiTrasporto"
          @onChange="OnAllegatiChange" />
      </div>
    <!-- </div> -->
  </div>
</template>



<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, RUOLI} from '@/SystemInformation.js'
import VUEVociDocumentiNonEconomici, {TSchedaVociDocumentiNonEconomici} from '@/views/SchedeDatabase/ComponentMultiScheda/VUEVociDocumentiNonEconomici.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'


export class TSchedaDocScaricoProdottiComposti extends TSchedaGenerica
{
  AltezzaTextAreaNote          = null
 
  CheckDimensioniNote()
  {
    let NrRigheNote          = this.Dati.NOTE != null?            this.Dati.NOTE.split("\n").length : 1; 
    this.AltezzaTextAreaNote = NrRigheNote          <= 1 ? '34px' : 
                               (NrRigheNote          > 7 ? (6 * 22 + 10) + 'px' : (NrRigheNote * 22 + 10) + 'px')
  }


  OnInitialization()
  {
     this.SchedaAllegati    = new TSchedaAllegati();
     this.SchedaAllegati.ClearAllegati()
     this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)
     this.SchedaVociDocScaricoProdottiComposti = new TSchedaVociDocumentiNonEconomici()
     this.SchedaVociDocScaricoProdottiComposti.ClearVociDocumentiNonEconomici()
    
  }

  CanRecord()
  {
    return  this.Dati.DATA_DOCUMENTO != '' && 
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaVociDocScaricoProdottiComposti.AllDataOk()
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
                                        Query     : "EliminaVoceTramiteDocScaricoProdotti",
                                        Parametri : { CHIAVE_DOCUMENTO : this.Chiave }
                                      },
                                      {
                                        Query     : "EliminaAllegatoTramiteDocScaricoProdotti",
                                        Parametri : { CHIAVE_DOCUMENTO : this.Chiave }
                                      },
                                      
                                      {
                                        Query     : "Elimina",
                                        Parametri : { CHIAVE_DOCUMENTO : this.Chiave }
                                      }
                                    ]};

      this.AdvQuery.PostSQL('DocScaricoProdottiComposti',ObjQuery,function()
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
                                                NUMERO_DOCUMENTO          : (this.Dati.NUMERO_DOCUMENTO == null || this.Dati.NUMERO_DOCUMENTO == undefined) ? -1 : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NUMERO_DOCUMENTO),
                                                DATA_DOCUMENTO            : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA_DOCUMENTO),
                                                NOTE                      : TSchedaGenerica.PrepareForRecordString(this.Dati.NOTE),
                                                ID_PRODOTTO               : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_PRODOTTO),
                                                ID_MAGAZZINO              : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_MAGAZZINO)
                                              }
                                });
                                
      this.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_DOCUMENTO')
      this.SchedaVociDocScaricoProdottiComposti.PrepareQueryParameters(ObjQuery.Operazioni,'ID_DOCUMENTO')

      this.AdvQuery.PostSQL('DocScaricoProdottiComposti',ObjQuery,function(Response)
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
      this.SchedaVociDocScaricoProdottiComposti.SetIdDocumento(this.Chiave)
      this.Dati.ANNO_DOCUMENTO            = Riassunto.ANNO_DOCUMENTO
      if(Riassunto.NUMERO_DOCUMENTO != null && Riassunto.NUMERO_DOCUMENTO != undefined)
          this.Dati.NUMERO_DOCUMENTO      = Riassunto.NUMERO_DOCUMENTO;
      else this.Dati.NUMERO_DOCUMENTO = -1;

      this.CreateSnapshot();
  }

  GetDescrizione()
  {
      return ('DOC n.' + this.Dati.NUMERO_DOCUMENTO + '/' + this.Dati.ANNO_DOCUMENTO);
  }
   
  Clear()
  {
    this.SchedaAllegati.AssignDati([])
    this.SchedaAllegati.SetIdDocumento(-1)
    this.SchedaVociDocScaricoProdottiComposti.AssignDati([],false,'ID_DOCUMENTO')
    this.SchedaVociDocScaricoProdottiComposti.SetIdDocumento(-1)
    this.Dati = {
                    NUMERO_DOCUMENTO                    : -1,
                    DATA_DOCUMENTO                      : TZDateFunct.DateInHTMLInputFormat(new Date()),
                    NOTE                                : '',
                    ID_PRODOTTO                         : -1,
                    // Dati allegati
                    ModificaTabellaAllegati             : false,
                    ModificaTabellaVoci                 : false,
                    ID_MAGAZZINO                        : SystemInformation.Configurazioni.Impostazioni.MAGAZZINO,
                }
    super.Clear();

  }

  Disponi(OnSuccess)
  {
    var Self = this;
    if(this.InEliminazione) return;
    this.AdvQuery.GetSQL('DocScaricoProdottiComposti',{ CHIAVE : Self.Chiave, ChiaveDocumento : this.Chiave },
                                      function(Results)
                                      {
                                          if(Self.InEliminazione) return;
                                          let ArrayInfo        = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                          let ArrayAllegati    = Self.AdvQuery.FindResults(Results,"AllegatiDoc");
                                          let ArrayVociDocScaricoProdottiComposti = Self.AdvQuery.FindResults(Results,"VociDocumento");
                                          if(ArrayInfo != undefined && ArrayAllegati != undefined && ArrayVociDocScaricoProdottiComposti != undefined)
                                          {
                                            if(ArrayAllegati.length != 0)
                                              Self.SchedaAllegati.AssignDati(ArrayAllegati,'Documento')
                                            if(ArrayVociDocScaricoProdottiComposti.length != 0)
                                              Self.SchedaVociDocScaricoProdottiComposti.AssignDati(ArrayVociDocScaricoProdottiComposti,
                                                                                              false,
                                                                                              'ID_DOCUMENTO')
                                            if(ArrayInfo.length != 0)
                                            {
                                              Self.Dati = { 
                                                            NUMERO_DOCUMENTO              : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].NUMERO_DOCUMENTO),
                                                            ANNO_DOCUMENTO                : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ANNO_DOCUMENTO),
                                                            DATA_DOCUMENTO                : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_DOCUMENTO),
                                                            NOTE                          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                            ID_PRODOTTO                   : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_PRODOTTO),
                                                            ModificaTabellaAllegati       : false,
                                                            ModificaTabellaVoci           : false,
                                                            ID_MAGAZZINO                  : (ArrayInfo[0].ID_MAGAZZINO == null || ArrayInfo[0].ID_MAGAZZINO == undefined) 
                                                                                            ? SystemInformation.Configurazioni.Impostazioni.MAGAZZINO 
                                                                                            : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].ID_MAGAZZINO),
                                              }
                                              if(Self.Dati.DATA_DOCUMENTO != '')
                                                Self.Dati.ANNO_DOCUMENTO = new Date(Self.Dati.DATA_DOCUMENTO).getFullYear();
                                              else Self.Dati.ANNO_DOCUMENTO = -1;
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

  GetClassName()
  {
    return 'TSchedaDocScaricoProdottiComposti';
  }

  GetImageIndex()
    {
      return 'Conferma.png'
    }

  DatiStampabili()
  {
    return true
  }

  // Stampa(OnSuccess)
  // {
  //   SystemInformation.AdvQuery.ExecuteExternalScript('StampaDDTUscente', { Chiave : this.Chiave },
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
      VUEAllegati,
      VUEVociDocumentiNonEconomici,
   },
   data()
   {
     return { 
              TotaleNota          : 0, 
              DeveloperMode       : SystemInformation.DeveloperMode,
              StatoItaliano       : SystemInformation.Configurazioni.StatoItaliano,
              ListaMagazzini      : SystemInformation.Configurazioni.Magazzini,
              
              Tabs                : {
                                       ActiveTab : 'Documento di scarico',
                                       Tabs      : [
                                                     {
                                                       Caption : 'Documento di scarico prodotti composti',
                                                       Id      : 'Documento di scarico'
                                                     },
                                                     {
                                                       Caption : 'Voci prodotti e info',
                                                       Id      : 'VociProdotti'
                                                     },
                                                     {
                                                       Caption: 'Allegati',
                                                       Id     : 'Allegati'
                                                     }
                                                   ]
                                     },
            }
   },
   props : ['SchedaDocScaricoProdottiComposti'],
   computed :
   {

  
   },
   watch:
   {
   },
   methods: 
   {
    OnFilialeSelected(FilialeSelezionata)
    {
      this.SchedaDocScaricoProdottiComposti.AssignDestinazioneFromFiliale(FilialeSelezionata)
    },


    OnAllegatiChange()
    {
      this.SchedaDocScaricoProdottiComposti.Dati.ModificaTabellaAllegati = this.SchedaDocScaricoProdottiComposti.SchedaAllegati.Modificato();
    },

    OnVociDocScaricoProdottiCompostiChange()
    {
      this.SchedaDocScaricoProdottiComposti.Dati.ModificaTabellaVoci = this.SchedaDocScaricoProdottiComposti.SchedaVociDocScaricoProdottiComposti.Modificato();
    },


   },      
    
   beforeMount() 
   {
     this.ActiveTab = 'Documento di scarico'
   },

}
</script>

<style scoped>
label {
 margin-bottom: 0px;
}
</style>