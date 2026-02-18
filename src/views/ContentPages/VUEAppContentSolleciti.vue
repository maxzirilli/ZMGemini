<template>

  <VUEModalCaricamentoDati v-if="PopupCaricamentoDati"
                         :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>

  <VUEModalInvioEmail v-if="PopupEmailVisibile"
                      :AttivazionePopup="PopupEmailVisibile" 
                      :OggettoEmail="OggettoEmail"
                      :ListaEmailCliente="ListaEmailCliente"
                      :EmailPEC="EmailPEC"
                      :VisualizzaLivelliSolleciti="true"
                      @onClickChiudiModal="AnnullaInvia()"
                      @onClickConfermaModal="ConfermaInvia()"
                      @onClickPrimoLivello='SelezionaPrimoLivello()'
                      @onClickSecondoLivello='SelezionaSecondoLivello()'
                      @onClickTerzoLivello='SelezionaTerzoLivello()'>
  </VUEModalInvioEmail>
   
  <VUEConfirm :Popup="PopupAnnullamento" :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Sei sicuro di voler eliminare la nota?'" @onClickConfermaPopup="ConfermaEliminazione" @onClickChiudiPopup="AnnullaAnnullamento">
  </VUEConfirm>

  <VUEConfirm :Popup="PopupEventi" :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Annullare le modifiche agli eventi?'" @onClickConfermaPopup="ConfermaAnnullamentoEventi" @onClickChiudiPopup="AnnullaAnnullamentoEventi">
  </VUEConfirm>

    <div @keydown="KeyDownF2($event)">
      <div class="ZMNuovaRigaScheda" style="float:left;width:100%">
        <div class="col-md-12">

          <div style="width:10%;float:left;font-size:16px;margin-top:5px"><label>Ragione sociale</label></div>
          <div style="float:left;width:15%;margin-left:-50px;margin-top:5px">
            <VUEInputClienti v-model ="FiltroRagioneSociale.Cliente" @onUpdate="newValue => FiltroRagioneSociale.Cliente = newValue" :ShowImage="true"/>
          </div>

          <div style="float:left;font-size:14px;padding-top: 5px;margin-left:-50px;width:7%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px">Seleziona:</label>
          </div>
          <div style="float:left;width:5%;">
            <select style="float:left;width:100%;margin-left:auto;margin-right:5px" class="form-control" v-model="FiltroClientiAttivi">
              <option value="Tutti">Tutti</option>
             <option value="Attivi">Attivi</option>
             <option value="NonAttivi">Inattivi</option>           
            </select>
          </div>

          <div style="float: left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px">Ordina per :</label>
          </div>
          <div style="float:left;width:11%;">
            <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="TipoOrdinamento">
             <option value="OrdinaRagioneSociale">Ragione Sociale</option>           
             <option value="OrdinaImporto">Importo</option>
            </select>
          </div>

          <div style="float: left;font-size:14px;padding-top: 5px;margin-left:-40px;width:7%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px">Clienti :</label>
          </div>
          <select style="float:left;width:13%;margin-left:-5px;margin-right:5px" class="form-control" v-model="FiltroClientiPassatiAdAvvocato">
             <option value="Tutti">Tutti</option>           
             <option value="PassatiAdAvvocato">Passati ad avvocato</option>
             <option value="NonPassatiAdAvvocato">Non passati ad avvocato</option>
          </select> 

          <div style="float: right; width: 15%">
            <a @click="Cerca()" style="width: 100%" class="btn btn-s-md btn-info">Cerca [F2]</a>
          </div>

        </div>    
      </div>
    </div>
  <div class="ZMCorpoSchedeDati" style="float:left;margin-top:5px">
      <div class="panel-default" v-for="(Fattura,Indice) in LsFatture" :key="Fattura.id"  style="cursor:pointer">
        <div v-if="Fattura.RagioneSociale != ''" @click="OnClickEspandiEventi(Indice)" style="padding-top:15px; height:50px; background-color: #68b6be;">
          <label style="width:25px;float:left;color:white; margin-top:3px; margin-left:1%" class="fa fa-envelope-o ZMIconButton" @click.stop="OnClickInviaMultipla(Fattura.IdCliente)"></label>
          <div style="font-weight: bold; background-color: #68b6be;color: white;float: left;width:7%; margin-left: 2%">N° ALLEGATI: {{ Fattura.ConteggioFatture }}</div>
          <div style="font-weight: bold; background-color: #68b6be;color: white;float: left; margin-left: 25%">{{ Fattura.RagioneSociale  }} - TOTALE DA PAGARE: {{FormattaImporto(Fattura.LsTotaleImporti[0].TotaleImporti)}}</div>
          <i v-if="!Fattura.Grafica.EventiEspansi" style="color:#59b9eb; font-size:15px; float:right; margin-right: 1%" class="fa fa-caret-square-o-down"></i>
          <i v-else style="color:#59b9eb; font-size:15px; float:right; margin-right: 1%" class="fa fa-caret-square-o-up"></i>
          <div style="font-weight: bold; background-color: #68b6be;color: white;float: right; margin-right: 2%">EVENTI</div>
          <div v-if="Fattura.DatiEventi.length > 0" style="font-weight: bold; background-color:#68b6be;color: white;float: right; margin-right:2px;margin-top:-5px">
            <img src="@/assets/images/AlertGenerico2.png">
          </div>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
          
        <div v-if="Fattura.Grafica.EventiEspansi" class="panel-collapse collapse in">
          <div style="background-color:rgb(230 230 230);cursor:pointer" class="panel-heading">
            <VUEDataTable :DataTable="ClasseDataTable.DataTableEventi" 
                          :NomeProgramma="'Gemini'" 
                          :PathLogo="require('../../assets/images/LogoGemini2.png')">
            </VUEDataTable>
            <button v-if="ShowConfermaEventi" type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:8%;margin-top:0.8%" @click="AnnullaEventi(Indice)" data-dismiss="modal">Annulla</button>
            <button v-if="ShowConfermaEventi" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:8%;margin-top:0.8%" @click="ConfermaEventi(Indice,IdCliente)" data-dismiss="modal">Conferma</button>
          </div>
          <div class="ZMSeparatoreFiltri">&nbsp;</div>
        </div>

        <div style="height:40px" class="panel-heading"  @click="OnClickEspandiPagina(Indice)">
            <div style="width:3%;float:left">
                <label style="width:25px;float:left;color:cadetblue; margin-right:2px;margin-left:4px" class="fa fa-envelope-o ZMIconButton" @click.stop="OnClickInviaSingola(Fattura.IdCliente,Fattura.Chiave,Fattura.Numero,GetTotaleImportiFattura(Fattura), Fattura.Data, Fattura.Cliente)"></label> 
            </div>
            <div v-if ="Fattura.Tipo == 'P'" style ="float:left;width:15px; margin-left: -30px; margin-right: 2px; margin-top: -5px; margin-bottom: auto" >
             <img src="@/assets/images/FatturaPregressa.png">
            </div>
            <div style="width:29%;float:left">
              <div style="width:60%;cursor:pointer;font-weight: initial;font-size:12px;float:left"></div>
              <label style="font-weight:bold;white-space: nowrap;">
                Cliente: 
                <span style="font-weight:initial;">
                  {{Fattura.Cliente}}
                </span>
              </label>
            </div>
            <div style="width:13%;float:left;">
              <div style="width:60%;cursor:pointer;font-weight: initial;font-size:12px;float:left"></div>
              <label style="font-weight:bold;white-space: nowrap;">
                  Fattura:
                  <span style="font-weight:initial">
                      {{Fattura.Numero}}
                  </span>
              </label>
            </div>
            <div style="width:10%;cursor:pointer;font-weight: initial;font-size:12px;float:left">
                <b>Pagato:&nbsp;</b>{{ FormattaImporto(Fattura.Pagato) }}
            </div>
            <div style="width:13%;cursor:pointer;font-weight: initial;font-size:12px;float:left">
                <b>Scaduto:&nbsp;</b> {{ FormattaImporto(Fattura.Scaduto) }} 
            </div>
            <div style="width:22%;cursor:pointer;font-weight: initial;font-size:12px;float:left">
              <b>Totale :</b> {{FormattaImporto(Fattura.Totale)}}
            </div>
            <div style="width:7%;cursor:pointer;font-weight: initial;font-size:12px;float:left">
              <b>Data:</b> {{ Fattura.Data }}
            </div>
            <div style="float:right;width:50px;">
              <i v-if="!Fattura.Grafica.FatturaEspansa" style="color:#59b9eb; font-size:15px; float:right;" class="fa fa-caret-square-o-down"></i>
              <i v-else style="color:#59b9eb; font-size:15px; float:right" class="fa fa-caret-square-o-up"></i>
            </div>
        </div>
        <div class="ZMSeparatoreFiltri">&nbsp;</div>
          <div v-if="Fattura.Grafica.FatturaEspansa" style="background-color:rgb(230 230 230);font-weight:bold;cursor:pointer;margin-top:3px" class="panel-heading" @click="OnClickEspandiRate(Indice)" >
            Rate
            <i v-if="!Fattura.Grafica.RateEspanse" style="color:#59b9eb; font-size:15px; float:right;" class="fa fa-caret-square-o-down" ></i>
            <i v-else style="color:#59b9eb; font-size:15px; float:right" class="fa fa-caret-square-o-up"></i>
             <div v-if="Fattura.Grafica.RateEspanse" class="panel-collapse collapse in">
               <div class="panel-body text-sm" style="background-color:white">
                  <table class="table table-striped b-t b-light" >
                    <thead>
                      <tr>
                        <th style="width:0.00001%;background:#b3dbff">Stato</th>
                        <th style="width:10%;background:#b3dbff">Data Scadenza</th>
                        <th style="width:10%;background:#b3dbff">Data Pagamento</th>
                        <th style="width:7%;background:#b3dbff">Importo</th>
                      </tr>
                    </thead>
                    <tbody v-for="Rata in Fattura.LsRate" :key="Rata.Chiave">
                        <i v-if="Rata.DataPagamento !== '-' || Rata.IdMovimento !== '-' " class="fa fa-circle" style="padding-top:2px;font-size:18px; color: green;margin-left: 20px;"></i>
                        <i v-else class="fa fa-circle" style="padding-top:2px;font-size:18px; color:red; margin-left: 20px"></i>
                        <i v-if="Rata.DataScadenza > new Date()" class="fa fa-circle" style="padding-top:2px;font-size:18px; color:yellow; margin-left: 20px"></i>
                        <th style="font-weight:initial">
                          {{ Rata.DataScadenza }} 
                         </th>
                        <th v-if="Rata.DataPagamento !== '-'" style="font-weight: initial;">
                          {{ Rata.DataPagamento }}
                        </th>
                        <th v-else>
                          {{Rata.DataMovimento}}
                        </th>
                        <th style="font-weight: initial;"> 
                          {{ FormattaImporto(Rata.Importo) }}
                        </th>
                    </tbody>
                  </table>
               </div>
              </div>
          </div>
        <div v-if="Fattura.Grafica.FatturaEspansa" style="background-color:rgb(230 230 230);font-weight:bold;cursor:pointer;margin-top:3px" class="panel-heading" @click="OnClickEspandiNote(Indice)">
              Note
            <i v-if="!Fattura.Grafica.NoteEspanse" style="color:#59b9eb; font-size:15px; float:right;" class="fa fa-caret-square-o-down"></i>
            <i v-else style="color:#59b9eb; font-size:15px; float:right" class="fa fa-caret-square-o-up"></i>
            <div v-if="Fattura.Grafica.NoteEspanse" class="panel-collapse collapse in">
              <div class="panel-body text-sm" style="background-color:white">
                <button class="btn btn-sm btn-success" style="float:left;" @click="AggiungiNota(Indice);">Aggiungi</button>
                  <table class="table table-striped b-t b-light">
                    <thead>
                      <tr>
                        <th style="width:10%;background:#b3dbff">Data</th>
                        <th colspan="2" style="width:60%;background:#b3dbff">Descrizione</th>
                      </tr>
                    </thead>
                    <tbody v-for="Nota in Fattura.LsNote" :key="Nota.Chiave">
                      <tr>
                        <td style="font-weight: initial;">
                          <input type="date" class="form-control" v-model="Nota.Data" @input="OnChangeDatiNota(Nota)">
                        </td>
                        <td style="font-weight: initial;">
                           <input type="text" class="form-control" v-model="Nota.Descrizione" @input="OnChangeDatiNota(Nota)">
                        </td>
                        <td>
                          <button v-if="!Nota.InModifica" class="btn btn-s-md btn-danger ;" style="float:right" @click="EliminaNota(Nota,Indice)">Elimina</button>
                          <button v-if="Nota.InModifica" class="btn btn-s-md btn-warning ;" style="float:right" @click="AnnullaModificheNota(Nota,Fattura)">Annulla</button>
                          <button v-if="Nota.InModifica" class="btn btn-s-md btn-success ;" style="float:right" @click="RegistraNota(Nota,Fattura.Chiave,Fattura.Tipo)">Conferma</button>
                       </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
           </div>
        </div>
    </div>
  </div>
</template>

<script>
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
import { TZDataTable, TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js';
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation.js';
import VUEModalInvioEmail from '@/views/SchedeDatabase/ComponentMultiScheda/VUEModalInvioEmail.vue';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js';
import { TZEconomicFunct } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js';
import { TSchedaFattura } from '../SchedeDatabase/VUESchedaFattura.vue';

class TSchedaEventoInsoluto
{
  constructor()
  {
    this.DataTableEventi = new TZDataTable('CHIAVE');
    this.DataTableEventi.ClearColumns();
    var Colonna = this.DataTableEventi.AddColumn('Data',
                                    TZDTableColumnType.typDate,
                                    'DATA',
                                    "15%")
    Colonna.DefaultValue = new Date()
    Colonna = this.DataTableEventi.AddColumn('Evento',
                                    TZDTableColumnType.typMemo,
                                    'DESCRIZIONE',
                                    "75%");
    Colonna.Necessario = true
  }

  AssignDati(ArrayDati)
  {
    this.DataTableEventi.AssignDati(ArrayDati)
  }
}

export default 
{
  props : ['Altezza'],
  components: { 
                VUEInputClienti,
                VUEDataTable,
                VUEModalInvioEmail,
                VUEConfirm,
                VUEModalCaricamentoDati,
              },
              
  data()
  {    
   return {
            AggiungiNote                    : false,
            ClasseDataTable                 : new TSchedaEventoInsoluto(),
            FiltroRagioneSociale            : { 
                                               Cliente : -1
                                              },
            CopiaParametri                  : {},
            LsFatture                       : [],
            LsEventi                        : [],
            LsTotaleImporti                 : [],
            PopupEmailVisibile              : false,
            ListaEmailCliente               : '',
            EmailPEC                        : '',
            LivelloSelezionato              : 'primo livello',
            PopupElimina                    : false,
            PopupInviaEmail                 : false,
            PopupAnnullamento               : false,
            PopupEventi                     : false,
            PopupCaricamentoDati            : false,
            ModificaTabelle                 : false,
            ShowConfermaEventi              : false,
            InvioEmailSingola               : false,
            InvioIdClientePremuto           : null,
            TipoOrdinamento                 : "OrdinaImporto",
            FiltroClienti                   : "Tutti",
            FiltroClientiPassatiAdAvvocato  : "NonPassatiAdAvvocato",
            FiltroClientiAttivi             : "Attivi",
            OggettoEmail                    :{
                                               CorpoEmail    : '',
                                               Cc            : '',
                                               Ccn           : '',
                                               Destinatario  : '',
                                               Oggetto       : '',
                                               NumeroFattura : [],
                                               Tipo          : '',
                                               Allegato      : []
                                             }, 
            LsChiavi                        : [],
            AllegatoMancante                : false,
            NomeProgramma                   : NOME_PROGRAMMA
          }                         
  },

  methods: 
  {        
    OnChangeDatiNota(Nota)
    {
      Nota.InModifica = true
    },

    FormattaImporto(Valore)
    {
      return TZEconomicFunct.EuropeanCurrencyFormat(Valore)
    },

    ConfermaEventi(Indice,IdCliente)
    {
      var Self = this;
      for(let i=0; i< Self.ClasseDataTable.DataTableEventi.Righe.length; i++)
      {
        var Parametri = {}
        Parametri.CHIAVE      = Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.CHIAVE == undefined ? -1 : Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.CHIAVE
        Parametri.DESCRIZIONE = Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.DESCRIZIONE.Valore
        Parametri.DATA        = Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.DATA.Valore
        Parametri.IdCliente   = IdCliente
        
          Parametri.ID_CLIENTE = Self.LsFatture[Indice].IdCliente
        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                  Query     : Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.CHIAVE == undefined ?  'InserisciEvento': "ModificaEvento",
                                  Parametri
                                })
        if(Self.ClasseDataTable.DataTableEventi.Righe[i].Eliminato)
        {
          ObjQuery.Operazioni.push({
                                    Query     : "EliminaEvento",
                                    Parametri : { CHIAVE : Self.ClasseDataTable.DataTableEventi.Righe[i].Dati.CHIAVE }
                                  })
        }
        SystemInformation.AdvQuery.PostSQL('Fatture',ObjQuery,
                                            function()
                                            {
                                              Self.Disponi()
                                              Self.ShowConfermaEventi = false
                                              Self.ModificaTabelle    = false
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            });
      }
    },

    AnnullaEventi(Indice)
    {
      this.PopupEventi = true
      this.LsEventi = this.LsFatture[Indice].DatiEventi
    },

    AnnullaAnnullamentoEventi()
    {
      this.PopupEventi = false
    },

    ConfermaAnnullamentoEventi()
    {
      this.ClasseDataTable.AssignDati([])
      this.ClasseDataTable.AssignDati(this.LsEventi)
      this.PopupEventi = false
      this.ShowConfermaEventi = false
    },

    OnClickEspandiPagina(Indice) 
    {
      if (event.target.tagName.toLowerCase() ==='onClick')
        {
        return;
        }
      for(var i = 0; i < this.LsFatture.length; i++)
      {
        if(i != Indice)
          this.LsFatture[i].Grafica.FatturaEspansa = false
        this.LsFatture[i].Grafica.EventiEspansi = false
      }
      this.LsFatture[Indice].Grafica.FatturaEspansa = !this.LsFatture[Indice].Grafica.FatturaEspansa

    },
    
    OnClickEspandiRate(Indice)
    {
      this.LsFatture[Indice].Grafica.RateEspanse = !this.LsFatture[Indice].Grafica.RateEspanse;
      this.LsFatture[Indice].LsRate.forEach((Rata) =>
      {
        if (Rata.DataPagamento !=null || Rata.IdMovimento != null)
          {
            Rata.RataPagata = true;
          }
      }) 
          
    },
    
    OnClickEspandiNote(Indice)
    {
      if (!(event.target.tagName.toLowerCase() === 'input' || event.target.tagName.toLowerCase() ==='button'))
          this.LsFatture[Indice].Grafica.NoteEspanse = !this.LsFatture[Indice].Grafica.NoteEspanse;
    },

    OnClickEspandiEventi(Indice)
    {
      if (!(event.target.tagName.toLowerCase() === 'input' || event.target.tagName.toLowerCase() ==='button'))
      {
        for(let i=0; i<this.LsFatture.length; i++)
        {
          this.LsFatture[i].Grafica.FatturaEspansa = false
          if(i != Indice)
            this.LsFatture[i].Grafica.EventiEspansi = false
        }

        this.LsFatture[Indice].Grafica.EventiEspansi = !this.LsFatture[Indice].Grafica.EventiEspansi;
        
        if(this.LsFatture[Indice].Grafica.EventiEspansi)
        {
          this.ClasseDataTable.AssignDati([])
          this.ClasseDataTable.AssignDati(this.LsFatture[Indice].DatiEventi)
        }

        this.ClasseDataTable.ModificaTabelle = false
        this.ShowConfermaEventi = false  
      }
    },
    
    KeyDownF2(event)
    {
        if(event.which == 113) 
          this.Cerca()
    },

    AggiungiNota(Indice)
    {
      let NuovaNota = { 
                        Chiave      : -1,
                        Data        : TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
                        Descrizione : '',
                        InModifica  : true
                      }

    this.AggiungiNote = !this.AggiungiNote
    this.LsFatture[Indice].LsNote.push(NuovaNota)
    },

    ConfermaInvia()
    {
      this.PopupEmailVisibile    = false
      var Self                   = this
      var ObjQuery               = { Operazioni : [] };
      var NumeriFatture          = ''
      var ChiaviPerStampaFattura = []
      var TipoFattura            = ''

      for(var j = 0; j < Self.LsFatture.length; j++)
      { 
        Self.LsFatture[j].Grafica.FatturaEspansa = false

        if(this.InvioEmailSingola)
        {
          if(Self.LsFatture[j].Chiave == Self.OggettoEmail.ChiaveFattura)
          {
            NumeriFatture = Self.LsFatture[j].Numero
            TipoFattura   = Self.LsFatture[j].Tipo
          }
        }
        else
        {
          if(Self.InvioIdClientePremuto == Self.LsFatture[j].IdCliente)
          {
            NumeriFatture     += Self.LsFatture[j].Numero + ' - '

            if(Self.LsFatture[j].Tipo == ('A'))
              ChiaviPerStampaFattura.push(Self.LsFatture[j].Chiave);
          }
        }
      }


      var Oggetto                   = {}
      Oggetto.Query                 = "InserisciEvento",
      Oggetto.Parametri             = {} 
      Oggetto.Parametri.CHIAVE      = -1
      
      if(this.InvioEmailSingola)
        Oggetto.Parametri.DESCRIZIONE =  'E\' stato inviato un sollecito per la fattura ' + NumeriFatture + ' di ' + Self.LivelloSelezionato + '.'
      else
      {
        Oggetto.Parametri.DESCRIZIONE =  'Sono stati inviati solleciti per le fatture ' + NumeriFatture + ' di ' + Self.LivelloSelezionato + '.'
      }

      Oggetto.Parametri.DATA        = TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date())
      
      Oggetto.Parametri.ID_CLIENTE         = this.InvioIdClientePremuto                    

      if(!this.InvioEmailSingola)
        NumeriFatture = NumeriFatture.slice(0,-3)



      if(this.InvioEmailSingola)
      { 
        
        if(TipoFattura == 'P')
        {
          let Parametri              = this.OggettoEmail

          Parametri.Chiave           = this.OggettoEmail.ChiaveFattura
          Parametri.InvioEmail       = true
          Parametri.IdCliente        = Oggetto.Parametri.ID_CLIENTE
          SystemInformation.AdvQuery.ExecuteExternalScript('InvioMailAccountSolleciti', Parametri,
                                                          function(Answer)
                                                          {
                                                            if(Answer.Risposta != undefined)
                                                            {
                                                              if(Answer.Risposta == 'MAIL_INVIATA')
                                                                alert('Invio mail effettuato con successo')
                                                              else 
                                                              {
                                                                if(Answer.ErroreMail != undefined)
                                                                  alert('Errore invio mail')
                                                              }
                                                              Self.OggettoEmail.CorpoEmail   = ''
                                                              Self.OggettoEmail.Cc           = ''
                                                              Self.OggettoEmail.Ccn          = ''
                                                              Self.OggettoEmail.Destinatario = ''
                                                              Self.OggettoEmail.Oggetto      = ''
                                                            } 
                                                          }, 
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                          })
          ObjQuery.Operazioni.push(Oggetto)
        }
        else
        {
          var Fattura    = new TSchedaFattura(SystemInformation.AdvQuery);
          Fattura.Chiave = this.OggettoEmail.ChiaveFattura

          let Parametri              = this.OggettoEmail
          Parametri.IdCliente        = Oggetto.Parametri.ID_CLIENTE

          Fattura.Invia(Parametri, 
                        function(Answer)
                        {         
                            if(Answer.Risposta == 'MAIL_INVIATA')
                              alert('Invio mail effettuato con successo')
                            else 
                            {
                              if(Answer.ErroreMail != undefined)
                                alert('Errore invio mail')
                            }
                        },
                        true)

          ObjQuery.Operazioni.push(Oggetto)
        } 
      }
      else
      {
        let Parametri        = this.OggettoEmail
        Parametri.Chiave     = [];
        Parametri.Tipo       = [];
        Parametri.InvioEmail = true

        for(var i = 0; i < this.LsFatture.length; i++)
        {
          if(this.InvioIdClientePremuto == this.LsFatture[i].IdCliente)
          { 
            Parametri.Chiave.push(this.LsFatture[i].Chiave)
            Parametri.Tipo.push(this.LsFatture[i].Tipo)
          } 
        }
        
        if(Parametri.Tipo.includes('P'))
        { 
            let Parametri              = this.OggettoEmail
            Parametri.Chiave           = [];
            Parametri.InvioEmail       = true
            Parametri.LsChiaviFattureA = ChiaviPerStampaFattura
            Parametri.IdCliente        = Oggetto.Parametri.ID_CLIENTE

            if(this.AllegatoMancante != false)
            {
              Parametri.Allegato         = []
              Parametri.LsChiaviFattureA = []
            }
            SystemInformation.AdvQuery.ExecuteExternalScript('InvioMailAccountSolleciti', Parametri,
                                                            function(Answer)
                                                            {  
                                                              if(Answer.Risposta != undefined)
                                                              {
                                                                if(Answer.Risposta == 'MAIL_INVIATA')
                                                                  alert('Invio mail effettuato con successo')
                                                                else 
                                                                {
                                                                  if(Answer.ErroreMail != undefined)
                                                                    alert('Errore invio mail')
                                                                }
                                                                Self.OggettoEmail.CorpoEmail   = ''
                                                                Self.OggettoEmail.Cc           = ''
                                                                Self.OggettoEmail.Ccn          = ''
                                                                Self.OggettoEmail.Destinatario = ''
                                                                Self.OggettoEmail.Oggetto      = ''
                                                              } 
                                                            }, 
                                                            function(HTTPError,SubHTTPError,Response)
                                                            {
                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            })
              ObjQuery.Operazioni.push(Oggetto)
        }
        else
        {
          Parametri.FromSolleciti    = -1
          Parametri.IdCliente        = Oggetto.Parametri.ID_CLIENTE
          SystemInformation.AdvQuery.ExecuteExternalScript('StampaFattura', Parametri,
                                                          function(Answer)
                                                          {  
                                                            if(Answer.Risposta != undefined)
                                                            {
                                                              if(Answer.Risposta == 'MAIL_INVIATA')
                                                                alert('Invio mail effettuato con successo')
                                                              else 
                                                              {
                                                                if(Answer.ErroreMail != undefined)
                                                                  alert('Errore invio mail')
                                                              }
                                                              Self.OggettoEmail.CorpoEmail   = ''
                                                              Self.OggettoEmail.Cc           = ''
                                                              Self.OggettoEmail.Ccn          = ''
                                                              Self.OggettoEmail.Destinatario = ''
                                                              Self.OggettoEmail.Oggetto      = ''
                                                            } 
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                          })
          
          ObjQuery.Operazioni.push(Oggetto)
        }
      }

      SystemInformation.AdvQuery.PostSQL('Fatture',ObjQuery,
                                        function()
                                        {
                                          for(var i = 0; i < Self.LsFatture.length; i++) 
                                          {
                                            if(Self.LsFatture[i].IdCliente == Self.InvioIdClientePremuto)
                                            {
                                              if(Self.InvioEmailSingola)
                                                  Self.LsFatture[i].DatiEventi.push({
                                                                                      DESCRIZIONE    : 'E\' stato inviato un sollecito per la fattura ' + NumeriFatture + ' di ' + Self.LivelloSelezionato + '.',
                                                                                      DATA           : TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
                                                                                      ID_CLIENTE     : Self.InvioIdClientePremuto,
                                                                                    })
                                              else Self.LsFatture[i].DatiEventi.push({
                                                                                      DESCRIZIONE    : 'Sono stati inviati solleciti per le fatture ' + NumeriFatture + ' di ' + Self.LivelloSelezionato + '.',
                                                                                      DATA           : TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
                                                                                      ID_CLIENTE     : Self.InvioIdClientePremuto,
                                                                                    })
                                            }
                                          }
                                          
                                          Self.LivelloSelezionato = 'primo livello';
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        });
    }, 

    AnnullaInvia()
    {
      this.EmailPEC                  = ''
      this.OggettoEmail.Destinatario = ''
      this.PopupEmailVisibile        = false
      this.LivelloSelezionato        = 'primo livello';
    },

    Disponi()
    {
      var Self = this 
      SystemInformation.AdvQuery.GetSQL('Fatture', this.CopiaParametri,
                                          function(Results)
                                          {
                                            let ArrayInfoEventi = SystemInformation.AdvQuery.FindResults(Results,"EventiInsoluti")
                                            for(var j = 0; j < Self.LsFatture.length; j++) 
                                              Self.LsFatture[j].DatiEventi = []

                                            ArrayInfoEventi.forEach(function(AEvento)
                                            {
                                              for(var i = 0; i < Self.LsFatture.length; i++)
                                              if(AEvento.ID_CLIENTE)
                                                if(Self.LsFatture[i].IdCliente == AEvento.ID_CLIENTE)
                                                  Self.LsFatture[i].DatiEventi.push(AEvento)
                                            })
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                            'ListaFattureNonPagate') 
    },

    GetTotaleImportiFattura(Fattura)
    {
      var Result = 0
      for(var i= 0; i < Fattura.LsRate.length; i++)
          Result += Fattura.LsRate[i].Importo          
      return Result
    },

    Cerca()
    {
      var Self = this
      this.PopupCaricamentoDati = true
      this.LsFatture = []
      function CompletaFattura(AFattura)
      {
        AFattura.Totale = 0;
        AFattura.Pagato = 0;
        AFattura.Scaduto = 0;
        AFattura.LsRate.forEach(function(ARata)
        {
          if(ARata.DataPagamento !== '-' || ARata.IdMovimento !== '-')
              AFattura.Pagato += ARata.Importo
          if(ARata.DataPagamento == '-')
              AFattura.Scaduto += ARata.Importo
          AFattura.Totale += ARata.Importo
        })
      }
      
      var Parametri = {}
      if(this.TipoOrdinamento == 'OrdinaRagioneSociale')
          Parametri.OrdinaRagioneSociale = true
      if(this.TipoOrdinamento == 'OrdinaImporto' || this.FiltroClienti == 'Tutti')
          Parametri.OrdinaImporto = true
      if(this.FiltroClientiAttivi != 'Tutti')
      {
        if(this.FiltroClientiAttivi == 'Attivi')
          Parametri.ClientiAttivi = 'T'
        else Parametri.ClientiAttivi = 'F'
      }

      if(this.FiltroClientiPassatiAdAvvocato == 'PassatiAdAvvocato')
        Parametri.PassataAdAvvocato = 'T'
      if(this.FiltroClientiPassatiAdAvvocato == 'NonPassatiAdAvvocato')
        Parametri.PassataAdAvvocato = 'F'
    
      if(this.FiltroClienti == 'Clienti')
          Parametri.Clienti = true
      if(this.FiltroRagioneSociale.Cliente != -1)
          Parametri.CHIAVE = this.FiltroRagioneSociale.Cliente
        
      this.CopiaParametri = Parametri
      SystemInformation.AdvQuery.GetSQL('Fatture',Parametri,
                                  function(Results)
                                  { 
                                    let ArrayRateNonPagate = SystemInformation.AdvQuery.FindResults(Results,("DettaglioFattureNonPagate"));
                                    
                                    if(ArrayRateNonPagate != undefined)
                                    { 
                                      Self.LsFatture = []
                                      var IdFattura = -1
                                      var ObjectFattura = null
                                      var UltimaChiaveCliente = -1
                                      var ObjTitolo = null;

                                      ArrayRateNonPagate.forEach(function(ARata)
                                      {
                                        if(IdFattura != ARata.CHIAVE_FATTURA)
                                        {
                                          if(IdFattura != -1)
                                            CompletaFattura(ObjectFattura); 
                                          
                                          ObjectFattura = {
                                                            Chiave                      : ARata.CHIAVE_FATTURA,
                                                            Numero                      : ARata.NUMERO_FATTURA,
                                                            IdCliente                   : ARata.ID_CLIENTE,
                                                            Anno                        : TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(ARata.DATA_FATTURA)),
                                                            Data                        : TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ARata.DATA_FATTURA)),
                                                            Cliente                     : ARata.RAGIONE_SOCIALE_DEL_CLIENTE,
                                                            Tipo                        : ARata.TIPO_FATTURA,
                                                            Grafica                     : {
                                                                                            FatturaEspansa : false,
                                                                                            RateEspanse    : false,
                                                                                            NoteEspanse    : false,
                                                                                            EventiEspansi  : false,
                                                                                          },
                                                            Importo                     : ARata.IMPORTO / 100,
                                                            LsRate                      : [],
                                                            LsNote                      : [],
                                                            LsTotaleImporti             : [],
                                                            ConteggioFatture            : 1,
                                                            DatiEventi                  : [],
                                                            Allegato                    : ARata.ALLEGATO
                                                          } 

                                            ObjectFattura.RagioneSociale =  UltimaChiaveCliente != ARata.ID_CLIENTE ?  ARata.RAGIONE_SOCIALE_DEL_CLIENTE : ''

                                          if(ObjectFattura.RagioneSociale != '')
                                            ObjTitolo = ObjectFattura
                                          else ObjTitolo.ConteggioFatture++
                                          
                                          UltimaChiaveCliente        =  ARata.ID_CLIENTE
                                          Self.LsFatture.push(ObjectFattura)
                                          IdFattura = ARata.CHIAVE_FATTURA
                                        }

                                        ObjectFattura.LsRate.push({
                                                                    Chiave        : ARata.CHIAVE_RATA,
                                                                    DataScadenza  : TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ARata.SCADENZA_RATA)),
                                                                    DataPagamento : (ARata.PAGAMENTO_RATA == null || ARata.PAGAMENTO_RATA == undefined) ? '-' : TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ARata.PAGAMENTO_RATA)),
                                                                    Importo       : ARata.IMPORTO / 100,
                                                                    IdMovimento   : (ARata.ID_MOVIMENTO == null || ARata.ID_MOVIMENTO == undefined) ?  '-' : ARata.ID_MOVIMENTO,
                                                                    DataMovimento : (ARata.DATA_MOVIMENTO == null || ARata.DATA_MOVIMENTO == undefined) ? '-' : TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(ARata.DATA_MOVIMENTO)),
                                                                    RataPagata    : ARata.PAGAMENTO_RATA != null && ARata.PAGAMENTO_RATA != undefined
                                                                  })
                                        
                                      })
                                      
                                      if(ObjectFattura != null)
                                          CompletaFattura(ObjectFattura);

                                      let ArrayInfoEventi = SystemInformation.AdvQuery.FindResults(Results,"EventiInsoluti")

                                      ArrayInfoEventi.forEach(function(AEvento)
                                      { 
                                        for(var i = 0; i < Self.LsFatture.length; i++) 
                                            if(Self.LsFatture[i].IdCliente == AEvento.ID_CLIENTE)
                                                Self.LsFatture[i].DatiEventi.push(AEvento)
                                      }) 

                                      let ArrayNoteFatture = SystemInformation.AdvQuery.FindResults(Results,"NoteFattureNonPagate")

                                      ArrayNoteFatture.forEach(function(ANota)
                                      {
                                        for(var i = 0; i < Self.LsFatture.length; i++)
                                          { 
                                            if(Self.LsFatture[i].Chiave == ANota.IDFATTURA_NOTA)
                                            {  
                                              Self.LsFatture[i].LsNote.push({
                                                                              Chiave      : ANota.CHIAVE_NOTA,
                                                                              IdFattura   : ANota.IDFATTURA_NOTA,
                                                                              Data        : ANota.DATA_NOTA,
                                                                              Descrizione : ANota.DESCRIZIONE_NOTA,
                                                                              InModifica  : false
                                                                            })
                                            }
                                          } 
                                      }) 
                                      
                                      let ArrayNoteFatturePregresse = SystemInformation.AdvQuery.FindResults(Results,"NoteFatturePregresseNonPagate")

                                      ArrayNoteFatturePregresse.forEach(function(ANota)
                                      {
                                        for(var i = 0; i < Self.LsFatture.length; i++)
                                        { 
                                          if(Self.LsFatture[i].Chiave == ANota.ID_FATTURA_INSOLUTA_PREGRESSA)
                                          { 
                                            Self.LsFatture[i].LsNote.push({
                                                                            Chiave      : ANota.CHIAVE_NOTA,
                                                                            IdFattura   : ANota.ID_FATTURA_INSOLUTA_PREGRESSA,
                                                                            Data        : ANota.DATA_NOTA,
                                                                            Descrizione : ANota.DESCRIZIONE_NOTA,
                                                                            InModifica  : false
                                                                          })
                                          }
                                        }   
                                      })
                                        
                                      let ArrayTotaleImportoFatture = SystemInformation.AdvQuery.FindResults(Results, "TotaleImportoFatture")

                                      ArrayTotaleImportoFatture.forEach(function(ATotaleImporto)
                                      { 
                                        for(var i = 0; i < Self.LsFatture.length; i++)
                                          {
                                            if(Self.LsFatture[i].IdCliente == ATotaleImporto.ID_CLIENTE)
                                              Self.LsFatture[i].LsTotaleImporti.push({
                                                                                      IdCliente    : ATotaleImporto.ID_CLIENTE,
                                                                                      TotaleImporti: ATotaleImporto.TOTALE_IMPORTO/100
                                                                                    })
                                          }
                                      })
                                      
                                        Self.LsFatture.sort(function(a,b)
                                                            {
                                                              if(a.LsTotaleImporti[0].TotaleImporti > b.LsTotaleImporti[0].TotaleImporti)
                                                                  return -1
                                                              if(a.LsTotaleImporti[0].TotaleImporti < b.LsTotaleImporti[0].TotaleImporti)
                                                                  return 1
                                                              return 0
                                                            })
                                      Self.PopupCaricamentoDati = false     
                                    }  
                                    else SystemInformation.HandleError('Impossibile ottenere il dettaglio della fattura');
                                  }, 
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                    Self.PopupCaricamentoDati = false
                                  },   
                                  'ListaFattureNonPagate') 
    },

    RegistraNota(Nota,IdFattura,Tipo)
    {
      var TipoFattura = Tipo
      var Parametri = {}
      Parametri.ChiaveNota  = Nota.Chiave
      Parametri.DATA        = Nota.Data
      Parametri.DESCRIZIONE = Nota.Descrizione
      if(TipoFattura == 'P')
          Parametri.ID_FATTURA_INSOLUTA_PREGRESSA = IdFattura
      else
          Parametri.ID_FATTURA = IdFattura
      var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                                Query     : Nota.Chiave == -1 ? "Inserisci" : "Modifica",
                                Parametri : Parametri
                                })
      SystemInformation.AdvQuery.PostSQL('NoteRate',ObjQuery,function(Response)
      {
          if(Nota.Chiave == -1)
              Nota.Chiave = Response.NewKey1;
          Nota.InModifica = false
      },
      function(HTTPError,SubHTTPError,Response)
      {
        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
      
    },

    AnnullaModificheNota(Nota,Fattura)
    {   
      if(Nota.Chiave != -1)
      {
        SystemInformation.AdvQuery.GetSQL('Fatture',{ Chiave :Nota.Chiave},
                                function(Results)
        {
          let ArrayNote = SystemInformation.AdvQuery.FindResults(Results,"ContenutoNota")
          Nota.Data        = ArrayNote[0].DATA
          Nota.Descrizione = ArrayNote[0].DESCRIZIONE
          Nota.InModifica  = false
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        },
        'SelectContenutoNota')
      }
      else Fattura.LsNote.splice(Fattura.LsNote.indexOf(Nota),1)
    },

    OnClickInviaMultipla(IdCliente)
    {
        var Self  = this
        Self.OggettoEmail.Destinatario   = ''
        Self.OggettoEmail.Oggetto        = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_PRIMO_SOLLECITO
        Self.OggettoEmail.Cc             = ''
        Self.OggettoEmail.Ccn            = ''
        Self.OggettoEmail.NumeroFattura  = []
        Self.OggettoEmail.Importo        = []
        Self.OggettoEmail.Data           = []
        Self.OggettoEmail.Totale         = []
        Self.OggettoEmail.Cliente        = []
        Self.OggettoEmail.Allegato       = []
        this.InvioEmailSingola           = false
        this.InvioIdClientePremuto       = IdCliente
        
        for(var j = 0; j < Self.LsFatture.length; j++)
        {
          Self.LsFatture[j].Grafica.FatturaEspansa = false
          Self.LsFatture[j].Grafica.EventiEspansi = false
        }
        
        for(var i= 0; i < Self.LsFatture.length ; i++)
        {
          if(IdCliente == Self.LsFatture[i].IdCliente)
          { 
            Self.OggettoEmail.NumeroFattura.push(Self.LsFatture[i].Numero)
            Self.OggettoEmail.Data.push(Self.LsFatture[i].Data)
            Self.OggettoEmail.Importo.push(Self.LsFatture[i].Importo)
            Self.OggettoEmail.Totale.push(Self.LsFatture[i].LsTotaleImporti[0].TotaleImporti)
            Self.OggettoEmail.Cliente.push(Self.LsFatture[i].Cliente)
            if(Self.LsFatture[i].Allegato != null)
              Self.OggettoEmail.Allegato.push(Self.LsFatture[i].Allegato)
            else Self.AllegatoMancante = true
          }
        }
        Self.OggettoEmail.CorpoEmail = SystemInformation.Configurazioni.Impostazioni.CONFIGURAZIONE_MAIL_SOLLECITI_PRIMO_LIVELLO.replace('[TOKEN_FATTURA]',Self.GetDescrizioneToken()) 
        
        var Parametri = {}
        Parametri.CHIAVE = IdCliente
        SystemInformation.AdvQuery.GetSQL('Fatture', Parametri,
        function(Results)
        {
          var ListaEmailCliente = []

            ListaEmailCliente = SystemInformation.AdvQuery.FindResults(Results,'ListaEmailClienti')

          var ArrayEmailPEC               = SystemInformation.AdvQuery.FindResults(Results, 'PECCliente')
          var EmailPEC                    = ''
          if(ArrayEmailPEC != undefined && ArrayEmailPEC.length != 0)
            EmailPEC                      = TSchedaGenerica.DisponiFromString( ArrayEmailPEC[0].PEC_CLIENTE )
          

          if(ListaEmailCliente != undefined)
          {
            Self.ListaEmailCliente        = ''

            for(i = 0; i < ListaEmailCliente.length; i++)
            {
              if(ListaEmailCliente[i].EMAIL_CLIENTE != undefined && ListaEmailCliente[i].EMAIL_CLIENTE != '')
              {
                Self.ListaEmailCliente += ListaEmailCliente[i].EMAIL_CLIENTE + ';' + ' '
                Self.OggettoEmail.Destinatario += ListaEmailCliente[i].EMAIL_CLIENTE + ';' + ' '
              }
            }
            if(EmailPEC != undefined && EmailPEC != '')
            {
              Self.EmailPEC                   = EmailPEC + ';' + ' '
              Self.OggettoEmail.Destinatario += EmailPEC + ';' + ' '
            }

            Self.LivelloSelezionato = 'primo livello';
            Self.PopupEmailVisibile = true
          }
          else SystemInformation.HandleError('Impossibile ottenere la casella di posta elettronica');
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        },
        'ListaEmailFatturaSolleciti')
    },

    GetDescrizioneToken()
    {
      var Result = ''
      for(var i= 0; i < this.OggettoEmail.NumeroFattura.length; i++)
        {
           Result += '\n'+'N° Fattura: '  + this.OggettoEmail.NumeroFattura[i] + '  ' +'Importo: '+  this.OggettoEmail.Importo[i] + '  ' +'Data: ' + this.OggettoEmail.Data[i] + '  '              
        }
        Result += '\n' + 'Totale: ' + this.OggettoEmail.Totale[0];            
          return Result 
    },

    OnClickInviaSingola(IdCliente, IdFattura, Numero, Importo, Data, Cliente)
    {   
        var Self  = this
        Self.OggettoEmail.Destinatario   = ''
        Self.OggettoEmail.Oggetto        = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_PRIMO_SOLLECITO
        Self.OggettoEmail.Cc             = ''
        Self.OggettoEmail.Ccn            = ''
        Self.OggettoEmail.ChiaveFattura  = IdFattura
        Self.OggettoEmail.NumeroFattura  = [Numero]
        Self.OggettoEmail.Importo        = [Importo]
        Self.OggettoEmail.Data           = [Data]
        Self.OggettoEmail.Totale         = [Importo]
        Self.OggettoEmail.Cliente        = [Cliente]
        Self.OggettoEmail.CorpoEmail     = SystemInformation.Configurazioni.Impostazioni.CONFIGURAZIONE_MAIL_SOLLECITI_PRIMO_LIVELLO.replace('[TOKEN_FATTURA]',Self.GetDescrizioneToken() )
        Self.OggettoEmail.Allegato       = []

        
        this.InvioEmailSingola          = true
        this.InvioIdClientePremuto      = -1

        for(var j = 0; j < Self.LsFatture.length; j++)
        {
          Self.LsFatture[j].Grafica.FatturaEspansa = false
          Self.LsFatture[j].Grafica.EventiEspansi  = false
        }
        
        for(var i= 0; i < Self.LsFatture.length ; i++)
        {
          if(Numero == Self.LsFatture[i].Numero)
          {
            if(Self.LsFatture[i].Allegato != null)
              Self.OggettoEmail.Allegato.push(Self.LsFatture[i].Allegato)
          }
        }

        var Parametri = {}
        Parametri.CHIAVE = IdCliente
        SystemInformation.AdvQuery.GetSQL('Fatture', Parametri, 
        function(Results)
        {
            var ListaEmailCliente = []

            ListaEmailCliente = SystemInformation.AdvQuery.FindResults(Results,'ListaEmailClienti')

            var ArrayEmailPEC               = SystemInformation.AdvQuery.FindResults(Results, 'PECCliente')
            var EmailPEC                    = ''
            if(ArrayEmailPEC != undefined && ArrayEmailPEC.length != 0)
              EmailPEC                      = TSchedaGenerica.DisponiFromString( ArrayEmailPEC[0].PEC_CLIENTE )
            
            if(ListaEmailCliente != undefined)
            {
              Self.ListaEmailCliente        = ''

              for(i = 0; i < ListaEmailCliente.length; i++)
              {
                if(ListaEmailCliente[i].EMAIL_CLIENTE != undefined && ListaEmailCliente[i].EMAIL_CLIENTE != '')
                {
                  Self.ListaEmailCliente += ListaEmailCliente[i].EMAIL_CLIENTE + ';' + ' '
                  Self.OggettoEmail.Destinatario += ListaEmailCliente[i].EMAIL_CLIENTE + ';' + ' '
                }
              }

              if(EmailPEC != undefined && EmailPEC != '')
              {
                Self.EmailPEC = EmailPEC + ';' + ' '
                Self.OggettoEmail.Destinatario += EmailPEC + ';' + ' '
              }

              Self.LivelloSelezionato = 'primo livello';
              Self.PopupEmailVisibile           = true
            }
            else SystemInformation.HandleError('Impossibile ottenere la casella di posta elettronica');
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        },
        'ListaEmailFatturaSolleciti')
        this.InvioIdClientePremuto      = IdCliente
    },

    SelezionaPrimoLivello()
    {
      var Self                        = this
      Self.LivelloSelezionato         = 'primo livello'
      Self.OggettoEmail.Oggetto       = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_PRIMO_SOLLECITO
      Self.OggettoEmail.CorpoEmail    = SystemInformation.Configurazioni.Impostazioni.CONFIGURAZIONE_MAIL_SOLLECITI_PRIMO_LIVELLO.replace('[TOKEN_FATTURA]',Self.GetDescrizioneToken())
    },

    SelezionaSecondoLivello()
    {
      var Self                        = this
      Self.LivelloSelezionato         = 'secondo livello'
      Self.OggettoEmail.Oggetto       = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_SECONDO_SOLLECITO
      Self.OggettoEmail.CorpoEmail    = SystemInformation.Configurazioni.Impostazioni.CONFIGURAZIONE_MAIL_SOLLECITI_SECONDO_LIVELLO.replace('[TOKEN_FATTURA]',Self.GetDescrizioneToken())
    },

    SelezionaTerzoLivello()
    {
      var Self                        = this
      Self.LivelloSelezionato         = 'terzo livello'
      Self.OggettoEmail.Oggetto       = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_TERZO_SOLLECITO
      Self.OggettoEmail.CorpoEmail    = SystemInformation.Configurazioni.Impostazioni.CONFIGURAZIONE_MAIL_SOLLECITI_TERZO_LIVELLO.replace('[TOKEN_FATTURA]',Self.GetDescrizioneToken())
    },

    EliminaNota(Nota,Indice)
    {
      this.NotaDaEliminare     = Nota
      this.PopupAnnullamento   = true
      this.IndiceFatture       = Indice
    },

    AnnullaAnnullamento()
    {
      this.PopupAnnullamento = false
    },

    ConfermaEliminazione()
    {  
      var Self = this
      var ObjQuery = { Operazioni : []}
      this.PopupAnnullamento = false
        if(Self.NotaDaEliminare.Chiave != -1)
        {
          ObjQuery.Operazioni.push({
                                    Query: "Elimina",
                                    Parametri : {
                                                  ChiaveNota : Self.NotaDaEliminare.Chiave
                                                }      
                                    })
                                    
          SystemInformation.AdvQuery.PostSQL('NoteRate',ObjQuery,function()
          {
            Self.LsFatture[Self.IndiceFatture].LsNote.splice(Self.LsFatture[Self.IndiceFatture].LsNote.indexOf(Self.NotaDaEliminare), 1)
          },
          function(HTTPError,SubHTTPError,Response)
          {
            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
          });
        }
        else Self.LsFatture[Self.IndiceFatture].LsNote.splice(Self.LsFatture[Self.IndiceFatture].LsNote.indexOf(Self.NotaDaEliminare), 1)
      
    },
  },

  watch :
  {
   'ClasseDataTable.DataTableEventi' :
    { 
          handler(NewValue,OldValue)
          {
             if(NewValue != OldValue && NewValue != undefined)
             {
                 this.ClasseDataTable.DataTableEventi.AssignOnRowChange(() =>
                 {
                   this.ClasseDataTable.ModificaTabelle = true
                   this.ShowConfermaEventi = true
                 })
 
                 this.ClasseDataTable.DataTableEventi.AssignOnRowDelete(() =>
                 {
                   this.ClasseDataTable.ModificaTabelle = true
                   this.ShowConfermaEventi = true
                 })
             } 
          },
          immediate : true
     },
  },

  mounted()
  {
    this.Cerca()
  },
}

</script>
<style>
</style>