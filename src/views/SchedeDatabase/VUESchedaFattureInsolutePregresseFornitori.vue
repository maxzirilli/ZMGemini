<template>
<!-- <VUEModal v-if="PopupSceltaFattureInsolute" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
          :Titolo="'Scegli le fatture da incassare'" 
          :Altezza="'300px'" 
          :Larghezza="'600px'"
          @onClickChiudiModal="PopupSceltaFattureInsolute = false">
  <template v-slot:Body>
    <div class="col-md-12">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>    
                      <th style="width:7%">&nbsp;</th>
                      <th>Numero</th>
                      <th>Data</th>
                      <th>Importo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Riga in SchedaFattureInsolutePregresseFornitori.DataTable.Righe" :key="Riga.CHIAVE" style="font-size:15px">
                      <td>
                        <input type="checkbox" v-model="Riga.Selezionata">
                      </td>
                      <td> {{ Riga.Dati['NUMERO'].Valore }} </td>
                      <td> {{ Riga.Dati['DATA'].Valore }} </td>
                      <td> {{ Riga.Dati['IMPORTO'].Valore}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
    </div> 
  </template>
  <template v-slot:Footer>
    <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="PopupSceltaFattureInsolute = false" data-dismiss="modal">Annulla</button>
    <button v-if="ControlloSelezionati()" type="button" class="btn btn-success" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="OnClickConfermaPopupFattureInsolute" data-dismiss="modal">Conferma</button>
  </template>
</VUEModal> -->

<div class="ZMCorpoSchedeDati">
  <header class="panel-heading bg-light">
   <ul class="nav nav-tabs nav-justified">
    <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" :class="{ active: ATab.Id == Tabs.ActiveTab }">
     <a @click="Tabs.ActiveTab = ATab.Id">{{ ATab.Caption }}</a>
    </li>
   </ul>
  </header>
  <div style="height:5px;"></div>
  <div v-if="Tabs.ActiveTab == 'Tabella'">
    <a v-if="!SchedaFattureInsolutePregresseFornitori.ControlloRigheDataPagamento()" style="float:left;font-weight:bold;color:red;font-size:13px;margin-left:30px;margin-top:5px;margin-bottom:5px">Inserire sia la data pagamento sia il conto associato</a>
    <div class="ZMSeparatoreFiltri"></div>
    <VUEDataTable :DataTable="SchedaFattureInsolutePregresseFornitori.DataTable"
                  :NomeProgramma="'Gemini'" 
                  :PathLogo="require('../../assets/images/LogoGemini2.png')">
    </VUEDataTable>
    <div>
      <header class="panel-heading bg-light" style="margin-top:5px; margin-bottom:5px">
        <ul class="nav nav-tabs nav-justified">
          <li>
            <a>
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <!-- <button class="btn btn-sm btn-info" v-if="!SchedaFattureInsolutePregresseFornitori.DatiModificati()" style="float:right;margin-bottom:8px;width:20%" @click="OnClickIncassaFattureInsolute">Incassa</button>                             -->
                <a style="float:right;font-weight:bold;color:red;font-size:15px;margin-right:40px;margin-top:5px;margin-bottom:20px">Totale fatture insolute pregresse: {{ SommaInsoluti.toFixed(2)}}€</a>
              </div>
            </a>
          </li>
        </ul>
      </header>
    </div>
  </div>
 </div>
</template>

<script>
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js'
import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, RUOLI } from '@/SystemInformation';
// import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'

export class TSchedaFattureInsolutePregresseFornitori extends TSchedaGenerica
{
  constructor (AdvQuery, ChiaveFornitore)
  {
    super(AdvQuery);
    this.Chiave          = ChiaveFornitore
    this.ChiaveFornitore = ChiaveFornitore
  }

  OnInitialization()
  {
    this.DataTable = new TZDataTable('CHIAVE');
    this.DataTable.ClearColumns();
    var Colonna = this.DataTable.AddColumn('Numero',
                              TZDTableColumnType.typInteger,
                              'NUMERO',
                              "9%");
    Colonna.Necessario = true
    Colonna = this.DataTable.AddColumn('Data',
                              TZDTableColumnType.typDate,
                              'DATA',
                              "6%");
    Colonna.Necessario = true
    Colonna = this.DataTable.AddColumn('Importo',
                             TZDTableColumnType.typCentesimi,
                             'IMPORTO',
                             "13%");
    Colonna.Necessario = true
    this.DataTable.AddColumn('Data pagamento',
                             TZDTableColumnType.typDate,
                             'DATA_PAGAMENTO',
                             "8%");
    Colonna = this.DataTable.AddColumn('Conto corrente/cassa',
                                           TZDTableColumnType.typSelect,
                                           'ID_CONTO_PAGAMENTO',
                                           "30%");
    Colonna.Lista = SystemInformation.GetLsContiCasse()
    Colonna.DefaultValue = -1
    this.DataTable.AddColumn('Note',
                             TZDTableColumnType.typMemo,
                             'NOTE',
                             "30%");
    Colonna = this.DataTable.AddColumn('Movimento',
                                        TZDTableColumnType.typMemo,
                                        'DATA_MOVIMENTO',
                                        "9%");
    Colonna.ReadOnly     = true
    Colonna.DefaultValue = null
    Colonna.OnCalcoloValore = function(Dati)
    {
      if(Dati.DATA_MOVIMENTO.Valore != null)
      {
        let Data = new Date(Dati.DATA_MOVIMENTO.Valore)
        return 'Fatt. collegata ad un movimento del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy', Data)
      }
      return '-'
    }
    this.DataTable.AddColumn('No PN',
                             TZDTableColumnType.typBoolean,
                             'NO_PRIMA_NOTA',
                             "2%");
    
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
        return false;
    
    return true;
  }

  Clear()
  {
    this.DataTable.AssignDati([]);
    this.Dati = {
                  ModificaTabella : false,
                  EliminaRiga     : false
                }
    super.Clear();
  }

  Disponi(OnSuccess)
  {
    var Self = this;
    if(this.InEliminazione) return;
    this.AdvQuery.GetSQL('FattureInsolutePregresseFornitori',{ CHIAVE : this.ChiaveFornitore },
                                      function(Results)
                                      {
                                        if(Self.InEliminazione) return;
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectInsolutiFornitori");
                                        if(ArrayInfo != undefined)
                                        {
                                          Self.DataTable.AssignDati(ArrayInfo);
                                          Self.Dati.ModificaTabella = false

                                          for(let i = 0; i < Self.DataTable.Righe.length; i++)
                                          {
                                            if(Self.DataTable.Righe[i].Dati.DATA_MOVIMENTO['Valore'] != null)
                                            {
                                              Self.DataTable.Righe[i].RowReadOnly = true
                                            }
                                          }
                                        }
                                        else SystemInformation.HandleError('Impossibile ottenere la lista degli insoluti');

                                        if(OnSuccess != undefined)
                                          OnSuccess()
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      })
  }

  ControlloRigheDataPagamento()
  {
    for(let i = 0; i < this.DataTable.Righe.length; i++)
    {
      if(!this.DataTable.Righe[i].Dati['NO_PRIMA_NOTA'].Valore)
        if(this.DataTable.Righe[i].Dati['DATA_PAGAMENTO'].Valore != '')
          if(this.DataTable.Righe[i].Dati['ID_CONTO_PAGAMENTO'].Valore == -1)
            return false
      if(this.DataTable.Righe[i].Dati['ID_CONTO_PAGAMENTO'].Valore != -1)
        if(this.DataTable.Righe[i].Dati['DATA_PAGAMENTO'].Valore == '')
          return false
    }
    return true
  }

  CanRecord()
  {
    return this.DataTable.AllDataOk() &&
           this.ControlloRigheDataPagamento()
  }  

  Registra(OnSuccess,OnError)
  {
    var Self = this
    if(this.CanRecord())
    {
      var ObjQuery = { Operazioni : [] };
      this.DataTable.Righe.forEach(function(Riga)
      {              
        let Parametri =  Self.DataTable.PrepareQueryParameters(Riga) 

        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.ChiaveFornitore),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA'].Valore)))
                                                }
                                });

        if(Riga.Dati['DATA_PAGAMENTO'].Valore != '')
        {
          ObjQuery.Operazioni.push({
                                      Query     : "EliminaRecordSaldiChiusureAnnuali",
                                      Parametri : {
                                                    CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(Self.ChiaveFornitore),
                                                    ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_PAGAMENTO'].Valore)))
                                                  }
                                  });
        }

        if(Riga.Nuovo)
        {
            Parametri.ID_FORNITORE = Self.ChiaveFornitore
            ObjQuery.Operazioni.push({
                                      Query     : "InserisciInsoluto",
                                      Parametri : Parametri,
                                      ResetKeys : [1]
                                    })
        }
        else
        {
          if(Riga.Eliminato)
          {
              ObjQuery.Operazioni.push({
                                        Query     : "EliminaInsoluto",
                                        Parametri : { CHIAVE : Parametri.CHIAVE}
                                      })
          }
          else 
          {
            if(Riga.Modificato())
            {
                ObjQuery.Operazioni.push({
                                          Query     : "ModificaInsoluto",
                                          Parametri : Parametri
                                        })
            }
          }
        }
      })
    }

    this.AdvQuery.PostSQL('FattureInsolutePregresseFornitori',ObjQuery,function()
      {
        ObjQuery = {};
        Self.Dati.ModificaTabella    = false;
        Self.Dati.EliminaRiga        = false
        Self.CreateSnapshot();
        OnSuccess();
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
    }

  GetClassName()
  {
    return 'TSchedaFattureInsolutePregresseFornitori';
  }

  GetImageIndex()
  {
    return 'Fattura.png'
  }

  GetDescrizione()
  {
    return 'Pregresse'
  }
}

export default 
{
  props : ['SchedaFattureInsolutePregresseFornitori'],
  emits : ['onClickNuovoMovimentoFromFattureInsolute'],
  data()
  {
    return {
            Tabs: 
            {
             ActiveTab: 'Tabella',
             Tabs: [
                      {
                       Caption: 'Fatture insolute pregresse',
                       Id: 'Tabella'
                      }
                    ]
            },
            // PopupSceltaFattureInsolute : false
    }
  },
  components : 
  {
    VUEDataTable,
    // VUEModal
  },

  computed :
  {
    SommaInsoluti :
    {
      get()
      {
        let Result = 0;
        for(let i = 0; i < this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe.length; i++)
        {
          if(this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['DATA_PAGAMENTO'].Valore == '' && this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['DATA_MOVIMENTO'].Valore == null)
            if(this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['IMPORTO'].Valore != null &&
               this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['IMPORTO'].Valore != undefined &&
               this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['IMPORTO'].Valore != 0)
              Result += this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Dati['IMPORTO'].Valore
        }
        return Result
      }
    }
  },

  watch: {
    SchedaFattureInsolutePregresseFornitori :
    { 
        handler(NewValue,OldValue)
        {
          if(NewValue != OldValue && NewValue != undefined)
          {
              this.SchedaFattureInsolutePregresseFornitori.DataTable.AssignOnRowChange(() =>
              {
                this.SchedaFattureInsolutePregresseFornitori.Dati.ModificaTabella = true
              })

              this.SchedaFattureInsolutePregresseFornitori.DataTable.AssignOnRowDelete(() =>
              {
                this.SchedaFattureInsolutePregresseFornitori.Dati.ModificaTabella = true
              })
          } 
        },
        immediate : true
    }
  },

  methods:
  {
    OnClickIncassaFattureInsolute()
    {
      for(let i = 0; i < this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe.length; i++)
        this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Selezionata = false
      
      this.PopupSceltaFattureInsolute = true
    },

    OnClickConfermaPopupFattureInsolute()
    {
      var ListaSelezionati = []
      for(let i = 0; i < this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe.length; i++)
        if(this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Selezionata)
          ListaSelezionati.push(this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i])

      this.$emit('onClickNuovoMovimentoFromFattureInsolute', ListaSelezionati)
    },

    ControlloSelezionati()
    {
      for(let i = 0; i < this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe.length; i++)
        if(this.SchedaFattureInsolutePregresseFornitori.DataTable.Righe[i].Selezionata)
          return true
      return false
    }

  },

  beforeMount() 
  {
  },
}
</script>
