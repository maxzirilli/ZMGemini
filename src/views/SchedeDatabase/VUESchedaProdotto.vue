<template>
 <VUEModal v-if="PopupCancellaProdottiSemplici" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'100px'" :Larghezza="'500px'"
          @onClickChiudiModal="OnClickAnnullaCancellaProdottiSemplici">
    <template v-slot:Body>
      <div class="form-row">
      <div class="col-md-12">
          <label style="margin-left:5%;width:80%;float:left;margin-top:7px;font-size:16px">I prodotti semplici saranno eliminati.</label>
      </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickAnnullaCancellaProdottiSemplici" data-dismiss="modal">Chiudi</button>
    </template>
  </VUEModal>

 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
          <li v-for="ATab in TabsFiltrati" :Key="ATab.Id"
              :class="{ active : ATab.Id == Tabs.ActiveTab }">
              <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/Prodotto2.png" style="width:40px;height:40px;float:left;margin-top:-8px">  
              </a>
          </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'Generale'">

      <div class="ZMNuovaRigaScheda" style="padding-top:5px">
          <div style="float:left;margin-right:5px">
              <input style="float:left;margin-top:2px" type="checkbox" v-model="SchedaProdotto.Dati.PRODOTTO_COMPOSTO" @click="EliminaProdottoSemplice"/>
          </div>
          <div style="float:left;">
              <label style="font-weight: bold;">Prodotto composto&nbsp;&nbsp;</label>
          </div>
      </div>

      <div class="ZMNuovaRigaScheda" style="padding-top:5px">
          <div style="float:left;width:59%">
              <label style="font-weight: bold;">Descrizione </label>
              <input type="text" class="form-control" v-model="SchedaProdotto.Dati.DESCRIZIONE" placeholder="Descrizione"/>
              <label v-if="SchedaProdotto.Dati.DESCRIZIONE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">Settore</label>
              <select class="form-control" v-model="SchedaProdotto.Dati.ID_SETTORE">
                <option :selected="SchedaProdotto.Dati.ID_SETTORE == -1" value="-1">-</option>
                <option v-for="SelectOption in ListaSettori" 
                        :Key="SelectOption.CHIAVE"
                        :value="SelectOption.CHIAVE"
                        :selected="SelectOption.CHIAVE == SchedaProdotto.Dati.ID_SETTORE">
                        {{SelectOption.DESCRIZIONE}}
                </option>
              </select>    
              <label v-if="SchedaProdotto.Dati.ID_SETTORE == -1" class="ZMFormLabelError">Campo obbligatorio</label>
          </div> 
      </div>
      <div class="ZMNuovaRigaScheda" style="padding-top:5px">
          <div style="float:left;width:20%">
              <label style="font-weight: bold;">Prezzo imponibile [€]</label>
              <input type="number" class="form-control" v-model="SchedaProdotto.Dati.PREZZO_IMPONIBILE" placeholder="Prezzo imponibile" step="0.01"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">IVA </label>
              <input type="number" class="form-control" v-model="SchedaProdotto.Dati.IVA" placeholder="IVA" step="0.1"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">Prezzo ultimo acquisto</label>
              <input type="number" class="form-control" v-model="SchedaProdotto.Dati.PREZZO_ULTIMO_ACQUISTO" placeholder="Prezzo ultimo acquisto" step="0.01"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">Data ultimo acquisto</label>
              <input type="date" class="form-control" v-model="SchedaProdotto.Dati.DATA_ULTIMO_ACQUISTO"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">Unità di misura</label>
              <VUEInputUdm v-model="SchedaProdotto.Dati.UNITA_DI_MISURA" class="form-control" />
              <!-- <label v-if="SchedaProdotto.Dati.UNITA_DI_MISURA == -1" class="ZMFormLabelError">Campo obbligatorio</label> -->
          </div> 
      </div>
      <!-- <div class="ZMNuovaRigaScheda" style="padding-top:5px">
        <div v-if="!SchedaProdotto.Dati.PRODOTTO_COMPOSTO" style="float:left;width:20%">
            <label style="font-weight: bold;">Qnt sug.</label>
            <input type="number" class="form-control" v-model="SchedaProdotto.Dati.QUANTITA_SUGGERITA"/>
        </div> 

      </div> -->
   </div>

   <div v-if="Tabs.ActiveTab == 'QntMagazzino'" style="height:50%">
      <div class="panel panel-default">
        <div class="table-responsive" style="max-height: 500px;">
          <table class="table table-striped b-t b-light">
            <thead>
              <tr>
                <th style="width:50%; text-align:left; padding:8px 10px;">Magazzino</th>
                <th style="width:10%; text-align:center; padding:8px 10px;">Qnt magazzino</th>
                <th style="width:10%; text-align:center; padding:8px 10px;">Soglia allarme</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="Magazzino in FiltroCerca.slice((Paginazione.NrPagina-1)*Paginazione.NrRighePerPagina, Paginazione.NrPagina*Paginazione.NrRighePerPagina)" 
                  :key="Magazzino.CHIAVE">
                <td style="padding:6px 10px; vertical-align: middle;">
                  <label class="form-control" style="margin:0; padding:4px 8px;">
                    {{ Magazzino.DESCRIZIONE }}
                  </label>
                </td>

                <td style="padding:6px 10px; text-align:center; vertical-align: middle;">
                  <input class="form-control" type="number" 
                        v-model.number="Magazzino.QUANTITA_MAGAZZINO" 
                        min="0"
                        style="width:80px; text-align:center; margin:0 auto;">
                </td>

                <td style="padding:6px 10px; text-align:center; vertical-align: middle;">
                  <input class="form-control" type="number" 
                        v-model.number="Magazzino.SOGLIA_ALLARME" 
                        min="0"
                        style="width:80px; text-align:center; margin:0 auto;">
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <footer class="panel-footer">
          <div class="row">
            <div class="col-sm-4 hidden-xs">
              <!-- spazio per eventuali info -->
            </div>
            <div class="col-sm-4 text-center">
              <small class="text-muted inline m-t-sm m-b-sm">
                {{TestoPaginazione}}
              </small>
            </div>
            <div class="col-sm-4 text-right text-center-xs">
              <ul class="pagination pagination-sm m-t-none m-b-none">
                <li><a @click="OnClickFirstPage()"><i class="fa fa-backward"></i></a></li>
                <li><a @click="PortaIndietroGruppoPagine()"><i :class="{ ZMDisabled : !BackGruppoPagineVisibile }" class="fa fa-chevron-left"></i></a></li>
                <li v-for="(Pagina,index) in PagineVisibili" :key="index" :class="{ active : IsActivePage(index) }">
                  <a @click="OnClickChangePage(index)">{{Pagina}}</a>
                </li>
                <li><a @click="PortaAvantiGruppoPagine()"><i :class="{ ZMDisabled : !ForwardGruppoPagineVisibile }" class="fa fa-chevron-right"></i></a></li>
                <li><a @click="OnClickLastPage()"><i class="fa fa-forward"></i></a></li>
              </ul>
            </div>
          </div>
        </footer>
      </div>

   </div>
    
   <div v-if="Tabs.ActiveTab == 'LogProdotto'" style="height:50%">
      <VUELogProdotti :SchedaLogProdotti="SchedaProdotto.SchedaLogProdotto"/>
   </div>
    
   <div v-if="SchedaProdotto.Dati.PRODOTTO_COMPOSTO">
      <div v-if="Tabs.ActiveTab == 'ProdottoComposto'">
          <VUEDataTable :DataTable="SchedaProdotto.DataTableProdottiMontaggio"
                        :NomeProgramma="'Gemini'" 
                        :PathLogo="require('../../assets/images/LogoGemini2.png')"/>
      </div>
    </div>

   <footer class="ZMNuovaRigaScheda" style="height: 2px;">&nbsp;</footer>
 </div>
</template>

<script>
 import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
 import { SystemInformation, DASHBOARD_FILTER_TYPES, NOME_PROGRAMMA} from '@/SystemInformation.js' //, RUOLI 
 import VUEInputUdm from '@/components/InputComponents/VUEInputUdm.vue'
 import VUELogProdotti, { TSchedaLogProdotti } from '@/views/SchedeDatabase/ComponentMultiScheda/VUELogProdotti.vue';
 import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js'
 import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
 import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
 import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
 

 export class TSchedaProdotto extends TSchedaGenerica
 {
    GetClassName()
    {
     return 'TSchedaProdotto';
    }

    CaricaRiassunto(Riassunto)
    {
        this.Chiave                 = Riassunto.CHIAVE;
        this.Dati.DESCRIZIONE       = TSchedaGenerica.DisponiFromString(Riassunto.DESCRIZIONE);
        this.Dati.PRODOTTO_COMPOSTO = TSchedaGenerica.DisponiFromBoolean(Riassunto.PRODOTTO_COMPOSTO);
        this.CreateSnapshot();
    }

    GetDescrizione()
    {
        return this.Dati.DESCRIZIONE;
    }

    CanRecord()
    {
      return this.Dati.DESCRIZIONE.trim() != '' &&
              this.Dati.ID_SETTORE != -1 &&
              this.DataTableProdottiMontaggio.AllDataOk();
    }

    Registra(OnSuccess,OnError)
    {
      if(this.CanRecord())
      {
        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                  Query     : this.IsNuovo() ? "Insert" : "Update",
                                  Parametri : {
                                                  CHIAVE                  : this.IsNuovo() ? undefined : this.Chiave, 
                                                  DESCRIZIONE             : TSchedaGenerica.PrepareForRecordString(this.Dati.DESCRIZIONE),
                                                  PREZZO_IMPONIBILE       : TSchedaGenerica.PrepareForRecordInteger(this.Dati.PREZZO_IMPONIBILE * 100) ,
                                                  IVA                     : TSchedaGenerica.PrepareForRecordInteger(this.Dati.IVA * 10) ,
                                                  ID_SETTORE              : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_SETTORE),
                                                  UNITA_DI_MISURA         : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.UNITA_DI_MISURA),
                                                  DATA_ULTIMO_ACQUISTO    : TSchedaGenerica.PrepareForRecordDate(this.Dati.DATA_ULTIMO_ACQUISTO),
                                                  PREZZO_ULTIMO_ACQUISTO  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.PREZZO_ULTIMO_ACQUISTO * 100),
                                                  QUANTITA_SUGGERITA      : TSchedaGenerica.PrepareForRecordInteger(this.Dati.QUANTITA_SUGGERITA),
                                                  PRODOTTO_COMPOSTO       : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.PRODOTTO_COMPOSTO),
                                              }
                                });

        var Self = this;

        for(let n = 0; n < Self.Dati.ListaMagazzini.length; n++)
        {
          if(Self.IsNuovo())
          {
            if((Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO != 0) || (Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME != 0))
            {
              ObjQuery.Operazioni.push(
              {
                  Query: "InsertQntMagazzino",
                  Parametri:{
                              CHIAVE: undefined,
                              ID_PRODOTTO: undefined,
                              ID_MAGAZZINO: Self.Dati.ListaMagazzini[n].CHIAVE,
                              QUANTITA_MAGAZZINO: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO * 100),
                              SOGLIA_ALLARME: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME * 100)
                            },
                  ResetKeys: [2]
              });
            }
          }
          else
          {
            if((Self.Dati.ListaMagazzini[n].OldQntMagazzino != 0 || Self.Dati.ListaMagazzini[n].OldSogliaAllarme != 0) &&
                (Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO == 0 && Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME == 0))
            {
              ObjQuery.Operazioni.push(
              {
                Query: "DeleteRecordQntMagazzino",
                Parametri:{
                            CHIAVE: Self.Dati.ListaMagazzini[n].CHIAVE_QNT
                          }
              });
            }
            else if(
                (Self.Dati.ListaMagazzini[n].OldQntMagazzino == 0 && Self.Dati.ListaMagazzini[n].OldSogliaAllarme == 0 && 
                 (Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO != 0 || Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME != 0))
            )
            {
              ObjQuery.Operazioni.push(
              {
                Query: "InsertQntMagazzino",
                Parametri:{
                            CHIAVE: undefined,
                            ID_PRODOTTO: Self.Chiave,
                            ID_MAGAZZINO: Self.Dati.ListaMagazzini[n].CHIAVE,
                            QUANTITA_MAGAZZINO: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO * 100),
                            SOGLIA_ALLARME: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME * 100)
                          },
                ResetKeys: [2]
              });
            }
            else if(
                (Self.Dati.ListaMagazzini[n].OldQntMagazzino != Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO) ||
                (Self.Dati.ListaMagazzini[n].OldSogliaAllarme != Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME)
            )
            {
              ObjQuery.Operazioni.push(
              {
                Query: "UpdateQntMagazzino",
                Parametri:{
                            CHIAVE: Self.Dati.ListaMagazzini[n].CHIAVE_QNT,
                            ID_PRODOTTO: Self.Chiave,
                            ID_MAGAZZINO: Self.Dati.ListaMagazzini[n].CHIAVE,
                            QUANTITA_MAGAZZINO: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].QUANTITA_MAGAZZINO * 100),
                            SOGLIA_ALLARME: TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ListaMagazzini[n].SOGLIA_ALLARME * 100)
                          }
              });
            }
          }
        }

        // INSERIMENTO LOG SENZA TRIGGER
        for(let k = 0; k < Self.Dati.ListaMagazzini.length; k++)
        {
          if(!Self.IsNuovo() && Self.Dati.ListaMagazzini[k].OldQntMagazzino != Self.Dati.ListaMagazzini[k].QUANTITA_MAGAZZINO)
          {
            let Parametri = {}

            let DeltaQNT = Self.Dati.ListaMagazzini[k].QUANTITA_MAGAZZINO - Self.Dati.ListaMagazzini[k].OldQntMagazzino

            Parametri.CHIAVE        = undefined
            Parametri.QUANTITA      = DeltaQNT * 100
            let Data = new Date()
            Parametri.DATA          = TZDateFunct.FormatDateTime('yyyy-mm-dd hh:nn:ss', Data)
            Parametri.DESCRIZIONE   = 'Modificata quantità in magazzino ' + Self.Dati.ListaMagazzini[k].DESCRIZIONE
            Parametri.ID_PRODOTTO   = Self.Chiave,
          
            ObjQuery.Operazioni.push({
                                        Query     : "InsertLogQntMagazzino",
                                        Parametri : Parametri,
                                        ResetKeys : [2]
                                      })
          }
        }
        
 
        this.DataTableProdottiMontaggio.Righe.forEach(function(Riga)
        {
            let Parametri = Self.DataTableProdottiMontaggio.PrepareQueryParameters(Riga)
            
            if(Riga.Nuovo)
            {
              if(!Self.IsNuovo())
                Parametri.ID_MONTAGGIO = Self.Chiave;

                ObjQuery.Operazioni.push({
                                          Query     : "InsertProdottiMontaggio",
                                          Parametri : Parametri,
                                          ResetKeys : [2]
                                        })
            }
            else
            {
              if(Riga.Eliminato || Self.Dati.PRODOTTO_COMPOSTO == false)
                  ObjQuery.Operazioni.push({
                                            Query     : "DeleteProdottiMontaggio",
                                            Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                          })
              else 
              {
                if(Riga.Modificato())
                {
                    Parametri.ID_MONTAGGIO = Self.Chiave;
                    ObjQuery.Operazioni.push({
                                              Query     : "UpdateProdottiMontaggio",
                                              Parametri : Parametri
                                            })
                }
              }
            }
        });

        this.AdvQuery.PostSQL('Magazzino',ObjQuery,function(Response)
        {
            SystemInformation.GetConfigurazioni(function()
            {
              ObjQuery = {};
              Self.Dati.ModificaTabellaProdottiMontaggio = false
              if(Self.Chiave == -1)
                  Self.Chiave = Response.NewKey1;
              Self.CreateSnapshot();
              OnSuccess();
              
            })
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });

      }
    }


    DatiEliminabili()
    {
      return true;
    }

    Elimina(OnSuccess,OnError)
    {
        this.InEliminazione = true;
        var ObjQuery = {Operazioni:[]}
        this.DataTableProdottiMontaggio.Righe.forEach(function(Riga)
        {
          ObjQuery.Operazioni.push ({
                                      Query : "DeleteProdottiMontaggio",
                                      Parametri : { CHIAVE : Riga.Dati['CHIAVE']}
                                    });
        });
        ObjQuery.Operazioni.push ({
                                      Query     : "DeleteQntMagazzinoProdotti",
                                      Parametri : { CHIAVE : this.Chiave }
                                 })
        ObjQuery.Operazioni.push ({
                                      Query     : "DeleteLogProdotti",
                                      Parametri : { CHIAVE : this.Chiave }
                                 })
        ObjQuery.Operazioni.push ({
                                      Query     : "Delete",
                                      Parametri : { CHIAVE : this.Chiave }
                                 })

        this.AdvQuery.PostSQL('Magazzino',ObjQuery,function()
        {
            OnSuccess();
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });
    }   

    Disponi(OnSuccess)
    {
        var Self = this;
        if(this.InEliminazione) return;
        this.AdvQuery.GetSQL('Magazzino',{ ChiaveProdotto : Self.Chiave },
                                          function(Results)
                                          {
                                              if(Self.InEliminazione) return;
                                              let ArrayLogProdotto  = Self.AdvQuery.FindResults(Results,"LogProdotto");
                                              let ArrayInfo = Self.AdvQuery.FindResults(Results,"SelectProdotto");
                                              let ArrayProdottiMontaggio = Self.AdvQuery.FindResults(Results,"SelectProdottiMontaggio");
                                              let ArrayMagazziniQntProdotti = Self.AdvQuery.FindResults(Results,"SelectMagazziniQntProdotti");

                                              if(ArrayInfo != undefined && ArrayLogProdotto != undefined && ArrayProdottiMontaggio != undefined)
                                              {
                                                Self.SchedaLogProdotto.AssignDati(ArrayLogProdotto,'ID_PRODOTTO')
                                                Self.DataTableProdottiMontaggio.AssignDati(ArrayProdottiMontaggio)
                                                
                                                if(ArrayInfo.length != 0)
                                                {
                                                  Self.Dati = { 
                                                                DESCRIZIONE             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE),
                                                                PREZZO_IMPONIBILE       : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].PREZZO_IMPONIBILE)  / 100,
                                                                IVA                     : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA)  / 10,
                                                                ID_SETTORE              : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_SETTORE),                                                               
                                                                UNITA_DI_MISURA         : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].UNITA_DI_MISURA),                                                               
                                                                PREZZO_ULTIMO_ACQUISTO  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].PREZZO_ULTIMO_ACQUISTO) / 100,                                                               
                                                                QUANTITA_SUGGERITA      : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].QUANTITA_SUGGERITA),                                                               
                                                                DATA_ULTIMO_ACQUISTO    : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_ULTIMO_ACQUISTO),                                                               
                                                                PRODOTTO_COMPOSTO       : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].PRODOTTO_COMPOSTO),
                                                                ModificaTabellaProdottiMontaggio : false,
                                                                ListaMagazzini          : []
                                                              }
                                                  
                                                  Self.Dati.ListaMagazzini = []

                                                  if(ArrayMagazziniQntProdotti.length > 0)
                                                  {
                                                    for(let k = 0; k < ArrayMagazziniQntProdotti.length; k++)
                                                    {
                                                      let Magazzino = ArrayMagazziniQntProdotti[k];

                                                      Magazzino.QUANTITA_MAGAZZINO = (Magazzino.QUANTITA_MAGAZZINO != null && Magazzino.QUANTITA_MAGAZZINO != undefined) 
                                                                                      ? TSchedaGenerica.DisponiFromInteger(Magazzino.QUANTITA_MAGAZZINO) / 100
                                                                                      : 0;

                                                      Magazzino.SOGLIA_ALLARME = (Magazzino.SOGLIA_ALLARME != null && Magazzino.SOGLIA_ALLARME != undefined) 
                                                                                  ? TSchedaGenerica.DisponiFromInteger(Magazzino.SOGLIA_ALLARME) / 100
                                                                                  : 0;

                                                      Magazzino.OldQntMagazzino   = Magazzino.QUANTITA_MAGAZZINO;
                                                      Magazzino.OldSogliaAllarme  = Magazzino.SOGLIA_ALLARME;

                                                      Self.Dati.ListaMagazzini.push(Magazzino);
                                                    }
                                                  }

                                                  Self.CreateSnapshot();
                                                }

                                                else SystemInformation.HandleError('Prodotto eliminato')
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere il dettaglio del prodotto');

                                              if(OnSuccess != undefined)
                                                OnSuccess()
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                          'SelectDettaglio')
    }

    Clear()
    {
        this.DataTableProdottiMontaggio.AssignDati([]);
        this.SchedaLogProdotto.AssignDati([])

        this.Dati = { 
                      DESCRIZIONE             : '',
                      ID_SETTORE              : -1,
                      UNITA_DI_MISURA         : -1,
                      PREZZO_IMPONIBILE       : 0,
                      DATA_ULTIMO_ACQUISTO    : '',
                      PREZZO_ULTIMO_ACQUISTO  : '',
                      QUANTITA_SUGGERITA      : 1,
                      IVA                     : SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA / 10,
                      PRODOTTO_COMPOSTO       : false,
                      ModificaTabellaProdottiMontaggio : false,
                      ListaMagazzini          : this.GetMagazziniDefault()
        }
        super.Clear();
    }

    GetMagazziniDefault()
    {
      let Magazzini = SystemInformation.Configurazioni.Magazzini;

      let Result = [];

      for (let i = 0; i < Magazzini.length; i++)
      {
        let OggettoMagazzino = {};

        OggettoMagazzino.CHIAVE             = Magazzini[i].CHIAVE;
        OggettoMagazzino.DESCRIZIONE        = Magazzini[i].DESCRIZIONE;
        OggettoMagazzino.QUANTITA_MAGAZZINO = 0;
        OggettoMagazzino.SOGLIA_ALLARME     = 0;
        OggettoMagazzino.OldQntMagazzino    = 0;
        OggettoMagazzino.OldSogliaAllarme   = 0;

        Result.push(OggettoMagazzino);
      }

      return Result;
    }

    GetImageIndex() 
    {
     return this.Dati.PRODOTTO_COMPOSTO ? 'ProdottoComposto.png' : 'Prodotto.png'
    }

    GetFiltriAbilitati(OnSuccess)
    {
      OnSuccess([{
                        Name : DASHBOARD_FILTER_TYPES.Prodotti,
                        Positions : [
                                        this.Dati.ID_SETTORE,
                                        this.Dati.DESCRIZIONE.substring(0,1).toUpperCase(),
                                        this.Chiave
                                    ]
              }])
    }

    OnInitialization()
    {
      this.SchedaLogProdotto = new TSchedaLogProdotti()
      this.SchedaLogProdotto.ClearLogProdotti()

      // table prodotti composti
      this.DataTableProdottiMontaggio = new TZDataTable('CHIAVE');
      this.DataTableProdottiMontaggio.ClearColumns();
      var Colonna = this.DataTableProdottiMontaggio.AddColumn('Quantità',
                                                              TZDTableColumnType.typInteger,
                                                              'QUANTITA',
                                                              "20%")
      Colonna.DefaultValue = 1
      Colonna = this.DataTableProdottiMontaggio.AddColumn('Prodotto',
                                                          TZDTableColumnType.typSelect,
                                                          'PRODOTTO_SEMPLICE',
                                                          "80%");
      Colonna.DefaultValue = -1
      Colonna.Necessario   = true
      Colonna.Lista        = SystemInformation.GetProdottiSemplici()
      Colonna.Autocomplete = true
    }
 }

 export default 
 {
    components : 
    {
      VUEInputUdm,
      VUELogProdotti,
      VUEDataTable,
      VUEModal
    },
    data()
    {
      return { 
               ListaSettori         : [],
               NomeProgramma        : NOME_PROGRAMMA,
               PopupCancellaProdottiSemplici : false,
               Tabs                 : {
                                       ActiveTab    : 'Generale',
                                       Tabs         : [
                                                       {
                                                         Caption : 'Generale',
                                                         Id      : 'Generale'
                                                       },
                                                       {
                                                         Caption : 'Quantità magazzino',
                                                         Id      : 'QntMagazzino'
                                                       },
                                                       {
                                                         Caption : 'Log',
                                                         Id      : 'LogProdotto'
                                                       },
                                                       {
                                                         Caption : 'Prodotto composto',
                                                         Id      : 'ProdottoComposto'
                                                       }
                                                      ]
                                      },
               Paginazione: {
                              NrRighePerPagina  : 10,
                              NrPagina          : 1,
                              StartGruppoPagine : 1,
                            }
             };
    },
    props : ['SchedaProdotto'],
    computed:
    {

      TabsFiltrati() 
      {
         return this.SchedaProdotto?.Dati?.PRODOTTO_COMPOSTO
         ? this.Tabs.Tabs
         : this.Tabs.Tabs.filter(tab => tab.Id !== 'ProdottoComposto');
      },

      FiltroCerca()
      {
        return this.SchedaProdotto?.Dati.ListaMagazzini || [];
      },

      TestoPaginazione()
      {
        let Totale = this.FiltroCerca.length;
        let Start = Totale === 0 ? 0 : (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighePerPagina + 1;
        let End = Math.min(this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina, Totale);
        return "Visualizzati " + Start + ' - ' + End + ' di ' + Totale + ' risultati';
      },

      ForwardGruppoPagineVisibile()
      {
        return this.FiltroCerca.length > this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina;
      },

      BackGruppoPagineVisibile()
      {
        return this.Paginazione.NrPagina > 1;
      },

      PagineVisibili()
      {
        let totalePagine = Math.ceil(this.FiltroCerca.length / this.Paginazione.NrRighePerPagina);
        let pagine = [];
        for (let i = 0; i < 5; i++) {
          let pagina = this.Paginazione.StartGruppoPagine + i;
          if (pagina <= totalePagine) pagine.push(pagina);
        }
        return pagine;
      }
    },

    watch:
    {
      'SchedaProdotto.Dati.PRODOTTO_COMPOSTO'(Valore)
      {
        if(!Valore && this.Tabs.ActiveTab == 'ProdottoComposto')
          this.Tabs.ActiveTab = 'Generale';

        if(Valore && this.Tabs.ActiveTab == 'LogProdotto')
          this.Tabs.ActiveTab = 'Generale';
      },

      SchedaProdotto :
      { 
          handler(NewValue,OldValue)
          {
            if(NewValue != OldValue && NewValue != undefined)
            {
                this.SchedaProdotto.DataTableProdottiMontaggio.AssignOnRowChange(() =>
                {
                  this.SchedaProdotto.Dati.ModificaTabellaProdottiMontaggio = true
                })
  
                this.SchedaProdotto.DataTableProdottiMontaggio.AssignOnRowDelete(() =>
                {
                  this.SchedaProdotto.Dati.ModificaTabellaProdottiMontaggio = true
                })
            } 
          },
          immediate : true
       }
    },

    methods: 
    {
      TastoPaginaVisibile(Offset)
      {
        return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighePerPagina < this.FiltroCerca.length
      },

      OnClickOrdinamento(Colonna)
      {
        var OrdinamentoDiscendente = false;
        if(Colonna.Campo == this.DataTable.OrdinatoPer.Campo)
           OrdinamentoDiscendente = !this.DataTable.OrdinatoPer.Discendente
        this.DataTable.OrdinaPer(Colonna.Campo,OrdinamentoDiscendente)
      },

      IsActivePage(Offset)
      {
        return this.Paginazione.NrPagina == this.Paginazione.StartGruppoPagine + Offset
      },

      OnClickChangePage(Offset)
      {
        this.Paginazione.NrPagina = this.Paginazione.StartGruppoPagine + Offset
      },

      PortaIndietroGruppoPagine()
      {
          if(this.BackGruppoPagineVisibile)
             if(--this.Paginazione.NrPagina < this.Paginazione.StartGruppoPagine)
                this.Paginazione.StartGruppoPagine--
      },

      PortaAvantiGruppoPagine()
      {
         if(this.ForwardGruppoPagineVisibile)
            if(++this.Paginazione.NrPagina > this.Paginazione.StartGruppoPagine + 4)
                this.Paginazione.StartGruppoPagine++
      },

      OnClickFirstPage()
      {
        this.Paginazione.NrPagina = 1
        this.Paginazione.StartGruppoPagine = 1
      },

      OnClickLastPage()
      { 
        this.Paginazione.NrPagina = Math.ceil(this.FiltroCerca.length / this.Paginazione.NrRighePerPagina)
        if (this.Paginazione.NrPagina >= 5)
          this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4
        else
          this.Paginazione.StartGruppoPagine = 1
      },

      CaricaSettori()
      {
        var Self = this
        SystemInformation.AdvQuery.GetSQL('Settori',{},
                                                    function(Results)
                                                    {
                                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Tutto");
                                                        if(ArrayInfo != undefined)
                                                        {
                                                          Self.ListaSettori = ArrayInfo
                                                        }
                                                        else SystemInformation.HandleError('Impossibile ottenere lista settori');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    })
      },

      EliminaProdottoSemplice()
      {
         if(this.SchedaProdotto.Dati.PRODOTTO_COMPOSTO == true && this.SchedaProdotto.DataTableProdottiMontaggio.Righe.length != 0)
         {
            this.PopupCancellaProdottiSemplici = true
            this.SchedaProdotto.DataTableProdottiMontaggio.Righe.forEach(function(Riga)
            {
              Riga.Eliminato = true
            })
         }
      },

      OnClickAnnullaCancellaProdottiSemplici()
      {
        this.PopupCancellaProdottiSemplici = false
      }
    },

    beforeMount() 
    {
      this.ActiveTab = 'Generale'
      this.CaricaSettori()
    },
   }
</script>