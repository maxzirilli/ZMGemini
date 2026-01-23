<template>
<!-- <VUEModal v-if="PopupSceltaFattureInsolute" 
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
                    <tr v-for="Riga in SchedaFattureInsolutePregresse.DataTable.Righe" :key="Riga.CHIAVE" style="font-size:15px">
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
    <a v-if="!SchedaFattureInsolutePregresse.ControlloRigheDataPagamento()" style="float:left;font-weight:bold;color:red;font-size:13px;margin-left:30px;margin-top:5px;margin-bottom:5px">Inserire sia la data pagamento sia il conto associato</a>
    <div class="ZMSeparatoreFiltri"></div>
    <VUEDataTable :DataTable="SchedaFattureInsolutePregresse.DataTable"
                  @onChange="OnDataChanged">
    </VUEDataTable>
    <div>
      <header class="panel-heading bg-light" style="margin-top:5px; margin-bottom:5px">
        <ul class="nav nav-tabs nav-justified">
          <li>
            <a>
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                <!-- <button class="btn btn-sm btn-info" v-if="!SchedaFattureInsolutePregresse.DatiModificati()" style="float:right;margin-bottom:8px;width:20%" @click="OnClickIncassaFattureInsolute">Incassa</button>                             -->
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
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable.js'
import VUEDataTable from '@/components/VUEDataTable.vue'
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation } from '@/SystemInformation';
// import VUEModal from '@/components/Slots/VUEModal.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { TAdvQueryOggettoBlob,  OPERAZIONE_BLOB } from '../../../../../../../../Librerie/VUE/ZAdvQuery'

export class TSchedaFattureInsolutePregresse extends TSchedaGenerica
{
  constructor (AdvQuery, ChiaveCliente, RagioneSocialeCliente)
  {
    super(AdvQuery);
    this.Chiave                 = ChiaveCliente
    this.ChiaveCliente          = ChiaveCliente
    this.RagioneSocialeCliente  = RagioneSocialeCliente
    this.PosizioniIniziali      = []
  }

  OnInitialization()
  {
    this.DataTable = new TZDataTable('CHIAVE');
    this.DataTable.ClearColumns();
    var Colonna = this.DataTable.AddColumn('Numero',
                              TZDTableColumnType.typInteger,
                              'NUMERO',
                              "9%");
    Colonna.Necessario     = true
    Colonna = this.DataTable.AddColumn('Data',
                              TZDTableColumnType.typDate,
                              'DATA',
                              "6%");
    Colonna.Necessario     = true
    Colonna = this.DataTable.AddColumn('Importo',
                             TZDTableColumnType.typCentesimi,
                             'IMPORTO',
                             "13%");
    Colonna.Necessario     = true
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
                             '30%');

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

    Colonna = this.DataTable.AddColumn('Allegato',
                             TZDTableColumnType.typAllegati,
                             'ALLEGATO',
                             "5%");
    Colonna.DefaultValue = null

    
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
    this.AdvQuery.GetSQL('FattureInsolutePregresse',{ CHIAVE : this.ChiaveCliente },
                                      function(Results)
                                      {
                                        if(Self.InEliminazione) return;  

                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectInsolutiCliente");
                                        if(ArrayInfo != undefined)
                                        {
                                          Self.DataTable.AssignDati(ArrayInfo);

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
      var ObjQuery = { Operazioni : [] }
      this.DataTable.Righe.forEach(function(Riga)
      {              
        let Parametri =  Self.DataTable.PrepareQueryParameters(Riga)

        if (Riga.Nuovo || Riga.Modificato())
        {
          ObjQuery.Operazioni.push({
                                      Query     : "EliminaRecordSaldiChiusureAnnuali",
                                      Parametri : {
                                                    CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.ChiaveCliente),
                                                    ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA'].Valore)))
                                                  }
                                  });

          if(Riga.Dati['DATA_PAGAMENTO'].Valore != '')
          {
            ObjQuery.Operazioni.push({
                                        Query     : "EliminaRecordSaldiChiusureAnnuali",
                                        Parametri : {
                                                      CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(Self.ChiaveCliente),
                                                      ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(Riga.Dati['DATA_PAGAMENTO'].Valore)))
                                                    }
                                    });
          }
        }

        if(Riga.Nuovo)
        {
          Parametri.ID_CLIENTE = Self.ChiaveCliente

          if(Riga.Dati['ALLEGATO'].PosizioneAllegato == null)
          {
            ObjQuery.Operazioni.push({
                                      Query     : "InserisciInsoluto",
                                      Parametri : Parametri,
                                      ResetKeys : [1]
                                    });
          }
          else if(Riga.Dati['ALLEGATO'].PosizioneAllegato != null)
          {
            Parametri.ALLEGATO = new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Inserimento,
                                                          TSchedaGenerica.PrepareForRecordString(Riga.Dati['ALLEGATO'].FileBase64[1], true),
                                                          Riga.Dati['ALLEGATO'].FileBase64[0],
                                                          '',
                                                          'ID_FATTURA_PREGRESSA'),
            ObjQuery.Operazioni.push({
                                      Query     : "InserisciInsolutoPiuAllegato",
                                      Parametri : Parametri,
                                      ResetKeys : [1]
                                    });
          }
        }
        else if(Riga.Eliminato)
        {
          ObjQuery.Operazioni.push({
                                    Query     : "EliminaInsoluto",
                                    Parametri : { CHIAVE : Parametri.CHIAVE}
                                  });

          if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != null)
          {
            let Posizione = Riga.Dati['ALLEGATO'].PosAllegatoIniziale.split(';')
            let NomeFile = Posizione[1]
            let Type = NomeFile.slice(-4)

            ObjQuery.Operazioni.push({
                                        Query     : "EliminaAllegato",
                                        Parametri : { CHIAVE : Parametri.CHIAVE,
                                                      ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Cancellazione,
                                                                                          '', 
                                                                                          Type, 
                                                                                          NomeFile, 
                                                                                          'ID_FATTURA_PREGRESSA' ),
                                                    }
                                      });
          }
        }
        else if(Riga.Modificato())
        {
          // ObjQuery.Operazioni.push({
          //                             Query     : "ModificaInsoluto",
          //                             Parametri : Parametri
          //                           });

          if(Riga.Modificato() && Riga.Dati['ALLEGATO'].PosizioneAllegato == null)
          {
            if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale == Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                          Query     : "ModificaInsoluto",
                                          Parametri : Parametri
                                        });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                          Query     : "ModificaInsoluto",
                                          Parametri : Parametri
                                        });

              let Posizione = Riga.Dati['ALLEGATO'].PosAllegatoIniziale.split(';')
              let NomeFile = Posizione[1]
              let Type = NomeFile.slice(-4)

              ObjQuery.Operazioni.push({
                                          Query     : "EliminaAllegato",
                                          Parametri : { CHIAVE : Parametri.CHIAVE,
                                                        ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Cancellazione,
                                                                                            '', 
                                                                                            Type, 
                                                                                            NomeFile, 
                                                                                            'ID_FATTURA_PREGRESSA' ),
                                                      }
                                        });
            }
          }
          else if(Riga.Modificato() && Riga.Dati['ALLEGATO'].PosizioneAllegato != null)
          {
            if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale == null)
            {
              ObjQuery.Operazioni.push({
                                      Query     : "ModificaInsoluto",
                                      Parametri : Parametri
                                    });

              ObjQuery.Operazioni.push({
                                        Query     : "InserisciAllegato",
                                        Parametri : { CHIAVE : Parametri.CHIAVE,
                                                      ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Inserimento,
                                                                                          TSchedaGenerica.PrepareForRecordString(Riga.Dati['ALLEGATO'].FileBase64[1], true),
                                                                                          Riga.Dati['ALLEGATO'].FileBase64[0],
                                                                                          '',
                                                                                          'ID_FATTURA_PREGRESSA'),
                                                    }
                                      });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != null && 
                    Riga.Dati['ALLEGATO'].PosAllegatoIniziale != Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                      Query     : "ModificaInsoluto",
                                      Parametri : Parametri
                                    });

              let Posizione = Riga.Dati['ALLEGATO'].PosAllegatoIniziale.split(';')

              ObjQuery.Operazioni.push({
                                        Query     : "ModificaAllegato",
                                        Parametri : { CHIAVE : Parametri.CHIAVE,
                                                      ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Update,
                                                                                          TSchedaGenerica.PrepareForRecordString(Riga.Dati['ALLEGATO'].FileBase64[1], true),
                                                                                          Riga.Dati['ALLEGATO'].FileBase64[0],
                                                                                          Posizione[1],
                                                                                          'ID_FATTURA_PREGRESSA'),
                                                    }
                                      });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != null && 
                    Riga.Dati['ALLEGATO'].PosAllegatoIniziale == Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                        Query     : "ModificaInsoluto",
                                        Parametri : Parametri
                                      });
            }
          }
          else if(Riga.Dati['ALLEGATO'].PosizioneAllegato == null)
          {
            if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale == Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                          Query     : "ModificaInsoluto",
                                          Parametri : Parametri
                                        });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              let Posizione = Riga.Dati['ALLEGATO'].PosAllegatoIniziale.split(';')
              let NomeFile = Posizione[1]
              let Type = NomeFile.slice(-4)

              ObjQuery.Operazioni.push({
                                          Query     : "EliminaAllegato",
                                          Parametri : { CHIAVE : Parametri.CHIAVE,
                                                        ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Cancellazione,
                                                                                            '', 
                                                                                            Type, 
                                                                                            NomeFile, 
                                                                                            'ID_FATTURA_PREGRESSA' ),
                                                      }
                                        });
            }
          }
          else if(Riga.Dati['ALLEGATO'].PosizioneAllegato != null)
          {
            if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale == null)
            {
              ObjQuery.Operazioni.push({
                                        Query     : "InserisciAllegato",
                                        Parametri : { CHIAVE : Parametri.CHIAVE,
                                                      ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Inserimento,
                                                                                          TSchedaGenerica.PrepareForRecordString(Riga.Dati['ALLEGATO'].FileBase64[1], true),
                                                                                          Riga.Dati['ALLEGATO'].FileBase64[0],
                                                                                          '',
                                                                                          'ID_FATTURA_PREGRESSA'),
                                                    }
                                      });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != null && 
                    Riga.Dati['ALLEGATO'].PosAllegatoIniziale != Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              let Posizione = Riga.Dati['ALLEGATO'].PosAllegatoIniziale.split(';')

              ObjQuery.Operazioni.push({
                                        Query     : "ModificaAllegato",
                                        Parametri : { CHIAVE : Parametri.CHIAVE,
                                                      ALLEGATO : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Update,
                                                                                          TSchedaGenerica.PrepareForRecordString(Riga.Dati['ALLEGATO'].FileBase64[1], true),
                                                                                          Riga.Dati['ALLEGATO'].FileBase64[0],
                                                                                          Posizione[1],
                                                                                          'ID_FATTURA_PREGRESSA'),
                                                    }
                                      });
            }
            else if(Riga.Dati['ALLEGATO'].PosAllegatoIniziale != null && 
                    Riga.Dati['ALLEGATO'].PosAllegatoIniziale == Riga.Dati['ALLEGATO'].PosizioneAllegato)
            {
              ObjQuery.Operazioni.push({
                                        Query     : "ModificaInsoluto",
                                        Parametri : Parametri
                                      });
            }
          }
        }
      });
    }

    this.AdvQuery.PostSQL('FattureInsolutePregresse',ObjQuery,function()
      {
        ObjQuery = {}
        Self.Dati.ModificaTabella    = false
        Self.Dati.EliminaRiga        = false
        Self.CreateSnapshot()
        OnSuccess()
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response)
      })
  }

  GetClassName()
  {
    return 'TSchedaFattureInsolutePregresse';
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
  props : ['SchedaFattureInsolutePregresse'],
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
        for(let i = 0; i < this.SchedaFattureInsolutePregresse.DataTable.Righe.length; i++)
        {
          if(this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['DATA_PAGAMENTO'].Valore == '' && this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['DATA_MOVIMENTO'].Valore == null)
            if(this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['IMPORTO'].Valore != null &&
               this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['IMPORTO'].Valore != undefined &&
               this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['IMPORTO'].Valore != 0)
              Result += this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Dati['IMPORTO'].Valore
        }
        return Result
      }
    }
  },

  methods:
  {
    OnDataChanged()
    {
       this.SchedaFattureInsolutePregresse.Dati.ModificaTabella = this.SchedaFattureInsolutePregresse.DataTable.Modificato();
    },

    // OnClickIncassaFattureInsolute()
    // {
    //   for(let i = 0; i < this.SchedaFattureInsolutePregresse.DataTable.Righe.length; i++)
    //     this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Selezionata = false
      
    //   this.PopupSceltaFattureInsolute = true
    // },

    // OnClickConfermaPopupFattureInsolute()
    // {
    //   var ListaSelezionati = []
    //   for(let i = 0; i < this.SchedaFattureInsolutePregresse.DataTable.Righe.length; i++)
    //     if(this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Selezionata)
    //       ListaSelezionati.push(this.SchedaFattureInsolutePregresse.DataTable.Righe[i])

    //   this.$emit('onClickNuovoMovimentoFromFattureInsolute', ListaSelezionati)
    // },

    ControlloSelezionati()
    {
      for(let i = 0; i < this.SchedaFattureInsolutePregresse.DataTable.Righe.length; i++)
        if(this.SchedaFattureInsolutePregresse.DataTable.Righe[i].Selezionata)
          return true
      return false
    }
  },

  beforeMount() 
  {
  },
}
</script>
