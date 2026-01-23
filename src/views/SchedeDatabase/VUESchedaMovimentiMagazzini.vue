<template>
 
  <VUEModal v-if="PopupLsProdotti" :Titolo="'Lista Prodotti '" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="PopupLsProdotti=false">
    <template v-slot:Body>
          <input type="text" style="width:76%;float:left" class="input-sm form-control" placeholder="Cerca per descrizione" v-model="FiltroProdottiDescrizione">
        <div style="clear:both;width:1%;height:10px">&nbsp;</div>
       
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%;position: sticky; top: 0;"></th>
                      <th style="width:78%;position: sticky; top: 0;">Descrizione</th>
                      <th style="width:12%;position: sticky; top: 0;">Quantità</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Prodotto in ProdottiFiltrati" :key="Prodotto.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="Prodotto.Presente">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.DESCRIZIONE }}
                      </td>
                       <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.QUANTITA_MAGAZZINO / 100 }}
                      </td>
                    </tr>
                    <tr v-if="ProdottiFiltrati.length == NumeroMassimoProdotti">
                      <td colspan="7" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:right;vertical-align: middle;color:red">
                        Sono presenti più di 100 prodotti...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaProdotti" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="ProdottiSelezionati" class="btn btn-success" @click="OnClickConfermaProdotti" style="float:right;margin-right:20px; width:20%">Conferma</button>    
    </template>
  </VUEModal>  

 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/Trasferimento2.png" style="width:40px;height:40px;float:left;margin-top:-8px;">  
             </a>
         </li>
       </ul>
   </header>

   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'Generale'">
    <div class="ZMNuovaRigaScheda"> 
      <div style="float:left;margin-left:3px;margin-bottom: 1%;">
        <text style="font-weight: bold;">Data</text>
        <input type="date" id="input-data" class="form-control" v-model="CurrentSchedaMovimentiMagazzini.Dati.DATA"/>
        <label v-if="CurrentSchedaMovimentiMagazzini.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
      </div> 
    </div>

    <div class="ZMSeparatoreScheda">Dal magazzino</div>
    <div class="ZMNuovaRigaScheda">      
      <div style="float:left;width:40%;margin-bottom: 1%;">
        <VUEInputMagazzini v-model="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE" @onUpdate="newValue => CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE = newValue"/>
        <label v-if="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        <label v-if="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE == CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE && CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE != -1" class="ZMFormLabelError">I magazzini devoni essere differenti</label>
      </div>
    </div>
     
    <div class="ZMSeparatoreScheda">Al magazzino</div>
    <div class="ZMNuovaRigaScheda">
      
      <div style="float:left;width:40%;margin-bottom: 1%;">
        <VUEInputMagazzini v-model="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE" @onUpdate="newValue => CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE = newValue"/>
        <label v-if="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE == -1" class="ZMFormLabelError">Campo obbligatorio</label>
        <label v-if="CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE == CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE && CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE != -1" class="ZMFormLabelError">I magazzini devoni essere differenti</label>
      </div>
    </div>

    <div class="ZMSeparatoreScheda">Descrizione</div>
    <div class="ZMNuovaRigaScheda">
      <textarea type="text" style="resize: none; height: 100px;" class="form-control" v-model="CurrentSchedaMovimentiMagazzini.Dati.DESCRIZIONE" placeholder="Descrizione" />
    </div>
    <br>
     
    <div class="ZMSeparatoreScheda">Prodotti</div>
    <div class="ZMNuovaRigaScheda">
      
      <div class="row wrapper" style="padding-right:3px;padding-left:3px;padding-bottom:0px;padding-top:0px">
        <div class="col-sm-12 m-b-xs">
          <button class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaProdotti">Aggiungi prodotto</button>
        </div>
      </div>
      
      <label v-if="CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.length == 0" class="ZMFormLabelError">Inserisci almeno un prodotto</label>  

      <div class="row wrapper" style="padding-top:5px;padding-bottom:5px">
        <section class="panel panel-default" style="background-color:white; width: 98%; margin-left: 1%;">
          <div class="table-responsive" style="max-height:350px;width:100%;height: 60%; ">
            <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
              <thead>
                <tr>
                  <th style="width:65%">Descrizione Prodotto</th>
                  <th style="width:14%">Quantità</th>
                  <th style="width:1%">&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="Prodotto in ProdottiNonEliminati" :key="Prodotto.CHIAVE">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Prodotto.DESCRIZIONE_PRODOTTO }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    <input type="number" min="0" v-model="Prodotto.QUANTITA_PRODOTTO" class="form-control" step="0.01"/>
                    <label v-if="Prodotto.QUANTITA_PRODOTTO == 0" class="ZMFormLabelError">Inserisci la quantità</label>  
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                     <i class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaProdotti(Prodotto)"></i>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

    </div>

    </div>
    <div class="ZMSeparatoreFiltri"></div>
 </div>

</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES } from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEInputMagazzini from '@/components/InputComponents/VUEInputMagazzini.vue';
import VUEModal from '@/components/Slots/VUEModal.vue';

export class TSchedaMovimentiMagazzini extends TSchedaGenerica
{
  AssignDati()
  {
  }
  
  OnInitialization()
  {
  }

  GetClassName()
  {
    return 'TSchedaMovimentiMagazzini';
  }

  CanRecord()
  {
    return  this.Dati.DATA != '' &&
            this.Dati.ID_MAGAZZINO_CEDENTE != -1 &&
            this.Dati.ID_MAGAZZINO_RICEVENTE != -1 &&
            this.Dati.ID_MAGAZZINO_CEDENTE != this.Dati.ID_MAGAZZINO_RICEVENTE &&
            this.ControlloQuantita()
  }

  ControlloQuantita()
  {
    if(this.Dati.ListaProdottiMovimentati.length != 0)
    {
      for (let i = 0; i < this.Dati.ListaProdottiMovimentati.length; i++) 
      {
        let Prodotto = this.Dati.ListaProdottiMovimentati[i];

        if(Prodotto.QUANTITA_PRODOTTO == 0)
          return false
        
      }
    }
    else return false

    return true
  }
  
  GetDescrizione()
  {
    let Data = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA))
      return ('Trasferimento - ' + Data)
  }

  DatiEliminabili()
  {
    return true;
  }

  Elimina(OnSuccess,OnError)
  {
    this.InEliminazione = true;
    var ObjQuery = { Operazioni : [] };

    for(var i = 0; i < this.Dati.ListaProdottiMovimentati.length; i++)
    {
      let CurrentProdotto = this.Dati.ListaProdottiMovimentati[i]

      if(CurrentProdotto.CHIAVE != -1)
      {
        ObjQuery.Operazioni.push({
                                    Query     : "DeleteMovimentiProdotto",
                                    Parametri : {
                                                  CHIAVE : CurrentProdotto.CHIAVE                               
                                                }
                                });
      }
    }

    ObjQuery.Operazioni.push({
                                Query     : "DeleteMovimentoMagazzino",
                                Parametri : { 
                                              CHIAVE_MOVIMENTO_MAGAZZINO : this.Chiave 
                                            }
                            });

    this.AdvQuery.PostSQL('MovimentiMagazzini',ObjQuery,function()
    {
      OnSuccess();
    },
    function(HTTPError,SubHTTPError,Response)
    {
      OnError(HTTPError,SubHTTPError,Response);
    });
  }

  GetImageIndex()
  {
     return 'Trasferimento.png'
  }

  CaricaRiassunto(Riassunto)
  {
     this.Chiave                        = Riassunto.CHIAVE;
     this.Dati.DATA                     = Riassunto.DATA
     this.Dati.ID_MAGAZZINO_CEDENTE     = Riassunto.ID_MAGAZZINO_CEDENTE
     this.Dati.ID_MAGAZZINO_RICEVENTE   = Riassunto.ID_MAGAZZINO_RICEVENTE
     this.Dati.DESCRIZIONE              = Riassunto.DESCRIZIONE
     this.Dati.ListaProdottiMovimentati = []
     this.CreateSnapshot();
  }

  Clear()
  {
     this.Dati                 = { 
                                    DATA                        : TZDateFunct.DateInHTMLInputFormat(new Date()),
                                    ID_MAGAZZINO_CEDENTE        : -1,
                                    ID_MAGAZZINO_RICEVENTE      : -1,
                                    DESCRIZIONE                 : '',
                                    ListaProdottiMovimentati    : []
                                 }                                 
     super.Clear();
  }

  Registra(OnSuccess,OnError)
  {
    if(this.CanRecord())
    {
      var ObjQuery = { Operazioni : [] };

      for(var i = 0; i < this.Dati.ListaProdottiMovimentati.length; i++)
      {
        let CurrentProdotto = this.Dati.ListaProdottiMovimentati[i]

        if(CurrentProdotto.Eliminato && CurrentProdotto.CHIAVE != -1)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "DeleteMovimentiProdotto",
                                      Parametri : {
                                                    CHIAVE : CurrentProdotto.CHIAVE                               
                                                  }
                                  });
        }
      }

      ObjQuery.Operazioni.push({
                                 Query     : this.IsNuovo() ? "InsertMovimentiMagazzini" : "UpdateMovimentiMagazzini",
                                 Parametri : {
                                                CHIAVE                 : this.Chiave, 
                                                DATA                   : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA),
                                                ID_MAGAZZINO_CEDENTE   : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_MAGAZZINO_CEDENTE),
                                                ID_MAGAZZINO_RICEVENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_MAGAZZINO_RICEVENTE),
                                                DESCRIZIONE            : TSchedaGenerica.PrepareForRecordString(this.Dati.DESCRIZIONE)                                   
                                             }
                               });

      for(var j = 0; j < this.Dati.ListaProdottiMovimentati.length; j++)
      {
        let CurrentProdotto = this.Dati.ListaProdottiMovimentati[j]

        if(!CurrentProdotto.Eliminato)
        {
          ObjQuery.Operazioni.push({
                                      Query     : CurrentProdotto.CHIAVE == -1 ? "InsertMovimentiProdotto" : "UpdateMovimentiProdotto",
                                      Parametri : {
                                                    CHIAVE                 : CurrentProdotto.CHIAVE != -1 ? CurrentProdotto.CHIAVE : undefined,
                                                    ID_MOVIMENTO_MAGAZZINO : !this.IsNuovo() ? this.Chiave : null,
                                                    ID_PRODOTTO            : CurrentProdotto.ID_PRODOTTO,
                                                    QUANTITA_PRODOTTO      : CurrentProdotto.QUANTITA_PRODOTTO * 100                               
                                                  },
                                      ResetKeys : [2]
                                  });
        }
      }

      var Self = this

      this.AdvQuery.PostSQL('MovimentiMagazzini',ObjQuery,function(Response)
      {
        ObjQuery = {};
        if(Self.Chiave == -1)
        {
          Self.Chiave = Response.NewKey1; 
        }

         Self.CreateSnapshot();
         OnSuccess();
      },
      function(HTTPError,SubHTTPError,Response)
      {
        OnError(HTTPError,SubHTTPError,Response);
      });
    }
  }

  Nuovo()
  {
     super.Nuovo()
  }

  Disponi(OnSuccess)
  {
    var Self = this

    if(Self.InEliminazione) return
    Self.AdvQuery.GetSQL('MovimentiMagazzini',{ CHIAVE : Self.Chiave },
                                      function(Results)
                                      {
                                        if(Self.InEliminazione) return;
                                        
                                        let ArrayInfo         = Self.AdvQuery.FindResults(Results,"SelectMovimentiMagazzini");
                                        let ArrayInfoProdotti = Self.AdvQuery.FindResults(Results,"SelectPodottiMovimentatiMagazzini");

                                        if(ArrayInfo != undefined)
                                        {
                                          if(ArrayInfo.length != 0)
                                          {
                                            Self.Dati = { 
                                                          CHIAVE                    : Self.Chiave, 
                                                          DATA                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA),
                                                          ID_MAGAZZINO_CEDENTE      : ArrayInfo[0].ID_MAGAZZINO_CEDENTE   != undefined? TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_MAGAZZINO_CEDENTE)   : -1,
                                                          ID_MAGAZZINO_RICEVENTE    : ArrayInfo[0].ID_MAGAZZINO_RICEVENTE != undefined? TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_MAGAZZINO_RICEVENTE) : -1,
                                                          DESCRIZIONE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE),
                                                          ListaProdottiMovimentati  : []
                                                        }

                                            if(ArrayInfoProdotti != undefined)
                                            {
                                              if(ArrayInfoProdotti.length != 0)
                                              {
                                                for(var i = 0; i < ArrayInfoProdotti.length; i++)
                                                {
                                                  let Prodotto =  {
                                                                    CHIAVE               : ArrayInfoProdotti[i].CHIAVE,
                                                                    DESCRIZIONE_PRODOTTO : ArrayInfoProdotti[i].DESCRIZIONE,
                                                                    QUANTITA_PRODOTTO    : ArrayInfoProdotti[i].QUANTITA_PRODOTTO / 100,
                                                                    ID_PRODOTTO          : ArrayInfoProdotti[i].ID_PRODOTTO,
                                                                    Eliminato            : false
                                                                  }
    
                                                  Self.Dati.ListaProdottiMovimentati.push(Prodotto)
                                                }
                                              }
                                            }

                                            Self.CreateSnapshot();
                                          }
                                          else SystemInformation.HandleError('Movimento magazzino eliminato')
                                        }
                                        else SystemInformation.HandleError('Impossibile ottenere il dettaglio del movimento magazzino');

                                        if(OnSuccess != undefined)
                                          OnSuccess()
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },
                                      'SelectDettaglioMovimentiMagazzini')
  }

  GetFiltriAbilitati(OnSuccess)
  {
    OnSuccess([{
                Name : DASHBOARD_FILTER_TYPES.MovimentiMagazzini,
                Positions : []
            }])   
  }

  DatiStampabili()
  {
    return true
  }

  Stampa(OnSuccess)
  {
    let Parametri = {}
    Parametri.Titolo = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)) + 
                       " - Movimento da magazzino \"" + 
                       SystemInformation.GetDescrizioneMagazzino(this.Dati.ID_MAGAZZINO_CEDENTE) +
                       "\" a magazzino \"" + 
                       SystemInformation.GetDescrizioneMagazzino(this.Dati.ID_MAGAZZINO_RICEVENTE) + "\"";
                       
    Parametri.Descrizione   = []

    this.Dati.ListaProdottiMovimentati.forEach(function(Prodotto)
    {
      Parametri.Descrizione.push("• n. " + Prodotto.QUANTITA_PRODOTTO + " - " + Prodotto.DESCRIZIONE_PRODOTTO)
    })

    SystemInformation.AdvQuery.ExecuteExternalScript('StampaDocumentoGenerico', Parametri,
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
    VUEInputMagazzini,
    VUEModal
  },
  data()
  {
      return { 
                Tabs                     : {
                                             ActiveTab : 'Generale',
                                             Tabs      : [
                                                           {
                                                             Caption : 'Generale',
                                                             Id      : 'Generale'
                                                           },
                                                         ]
                                           },                       
              ListaProdotti              : [],
              PopupLsProdotti            : false,
              NumeroMassimoProdotti      : 100,
              FiltroProdottiDescrizione  : '',
             };
  },
  props : ['SchedaMovimentiMagazzini'],
  computed :
  {
      CurrentSchedaMovimentiMagazzini : 
      {
         get()
         {
           return this.SchedaMovimentiMagazzini;
         }
      },

      ProdottiSelezionati :
      {
        get()
        {
          for(var i = 0; i < this.ListaProdotti.length; i++)
          if(this.ListaProdotti[i].Presente)
            return true
          return false
        }
      },

      ProdottiFiltrati :
      {
        get()
        {
          var FiltroDescr      = this.FiltroProdottiDescrizione.toUpperCase().trim();
          var ListaParoleDescr = FiltroDescr.split(' ')
          var ListaRighe       = []
          let Self = this

          if( FiltroDescr == '')
          {
            ListaRighe = this.ListaProdotti.slice(0, this.NumeroMassimoProdotti)
            return ListaRighe
          }
          else 
          {
            ListaRighe = this.ListaProdotti.filter(function(Prodotto)
                                                  {
                                                    // if( FiltroDescr != '')
                                                    // {
                                                    //     if(Self.FiltraPerDescrizione(Prodotto, ListaParoleDescr))
                                                    //       return true;
                                                    //      return false
                                                    // }
                                                    
                                                    if(FiltroDescr != '')
                                                    {
                                                      return Self.FiltraPerDescrizione(Prodotto, ListaParoleDescr)
                                                    }
                                                    return false;
                                                  })
            ListaRighe = ListaRighe.slice(0, this.NumeroMassimoProdotti) 
            return ListaRighe
          }
        }
      },

      ProdottiNonEliminati :
      {
        get()
        {
          return this.CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.filter(Prodotto => !Prodotto.Eliminato)
        }
      }
      
    },

    methods: 
    {      

      OnClickAnnullaProdotti()
      {
        for(let i = 0; i < this.ListaProdotti.length; i++)
        { 
          if(this.ListaProdotti[i].Presente)
            this.ListaProdotti[i].Presente = false
        }

        this.PopupLsProdotti = false
      },
      
      OnClickConfermaProdotti()
      {

        for(let i = 0; i < this.ListaProdotti.length; i++)
        { 
          let CurrentProdotto = this.ListaProdotti[i]

          if(CurrentProdotto.Presente)
          {            
            let ProdottoAggiunto = {
                                      CHIAVE               : -1,
                                      DESCRIZIONE_PRODOTTO : CurrentProdotto.DESCRIZIONE,
                                      ID_PRODOTTO          : CurrentProdotto.CHIAVE,
                                      QUANTITA_PRODOTTO    : 0,
                                      Eliminato            : false
                                    }
            this.CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.push(ProdottoAggiunto)
            CurrentProdotto.Presente = false
          }
        }

        this.PopupLsProdotti = false
      },
      
      OnClickEliminaProdotti(Prodotto)
      {
        Prodotto.Eliminato = true
      },

      FiltraPerDescrizione(Prodotto, ListaParoleDescr)
      {
        let ListaParoleProdotto = Prodotto.DESCRIZIONE.split(' ')
        let Trovato             = false
        for(let i = 0; i < ListaParoleDescr.length; i++)
        {
          for(let j = 0; j < ListaParoleProdotto.length; j++)
          {
            if(ListaParoleProdotto[j].includes(ListaParoleDescr[i]))
            {
              Trovato = true
            }
          }
          if(!Trovato)
          {
            return false
          }
          Trovato = false
        }
        return true
      },

      OnClickListaProdotti()
      {
        this.ListaProdotti   = null
        
        var Self = this;        
        SystemInformation.AdvQuery.GetSQL('MovimentiMagazzini',{CHIAVE : this.CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE}, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaProdottiMagazzino");
                                              //  console.log(ArrayInfo)
                                               if (ArrayInfo != undefined) 
                                              {
                                                Self.ListaProdotti = ArrayInfo
                                                Self.PopupLsProdotti = true
                                              }
                                             
                                              else SystemInformation.HandleError('Impossibile ottenere lista dei prodotti');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'ListaProdottiMagazzino')
      },
    },
    
    watch :
    {
      'CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_CEDENTE' : 
      {
        handler()
        {
          this.CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.forEach(Prodotto => 
                                                                                  {
                                                                                    Prodotto.Eliminato = true
                                                                                  });
        },
      },

      'CurrentSchedaMovimentiMagazzini.Dati.ID_MAGAZZINO_RICEVENTE' : 
      {
        handler()
        {
          let ListaProdottiSelezionati = []
          this.CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.forEach(Prodotto => 
                                                                                  {
                                                                                    if(!Prodotto.Eliminato)
                                                                                    {
                                                                                      if(Prodotto.CHIAVE != -1)
                                                                                        ListaProdottiSelezionati.push({
                                                                                                                        CHIAVE               : -1,
                                                                                                                        DESCRIZIONE_PRODOTTO : Prodotto.DESCRIZIONE_PRODOTTO,
                                                                                                                        QUANTITA_PRODOTTO    : Prodotto.QUANTITA_PRODOTTO,
                                                                                                                        ID_PRODOTTO          : Prodotto.ID_PRODOTTO,
                                                                                                                        Eliminato            : Prodotto.Eliminato
                                                                                                                      })
                                                                                      Prodotto.Eliminato = true
                                                                                    }
                                                                                  });
          ListaProdottiSelezionati.forEach(ProdottoSelezionato => 
                                          {
                                            this.CurrentSchedaMovimentiMagazzini.Dati.ListaProdottiMovimentati.push(ProdottoSelezionato)
                                          });
        },
      },
    },

    beforeMount() 
    {
      this.ActiveTab = 'Generale'
    },
 }
</script>
