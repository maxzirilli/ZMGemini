<template>
<VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>

<VUEModal v-if="LsLogFiliali" :PathLogo="require('../../assets/images/LogoGemini2.png')"
                               :Programma="NomeProgramma"
                               :Titolo="'Lista Log Filiale'" 
                               :Altezza="'250px'" 
                               :Larghezza="'900px'"
                               @onClickChiudiModal="OnClickChiudiLista">
 <template v-slot:Body>
  <div class="row wrapper">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
            <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
              <thead>
                <tr>
                  <th style="width:22%;position: sticky; top: 0;text-align:center;">Data e ora</th>
                  <th style="width:43%;position: sticky; top: 0;text-align:center;">Stato Filiale</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="LogFiliale in ElencoLogFiliali" :Key="LogFiliale.CHIAVE">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ LogFiliale.DATA_ORA }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ LogFiliale.FILIALE_DISATTIVATA == 'T' ? 'Disattivata' : 'Attivata' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>  
 </template>
 <template v-slot:Footer>
      <button class="btn btn-danger"  @click="OnClickChiudiLista"  style="float:right;margin-right:20px; width:20%">Annulla</button>  
 </template>
</VUEModal>
  <div @keydown="KeyDownF2($event)">
    <div class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding:10px; border-top:1px; height: 95px; padding-bottom: 0;">
      <div class="col-md-11">
        <div style="float:left;width:1%"></div>
        <!-- <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickStampaFiliali">Stampa</button> -->
         
        <div style="float:left;font-size:14px;padding-top: 5px;margin-right:7px">
          <label>Rag. soc.</label>
        </div>
        <div style="float:left; width:13%;margin-right:20px;">
          <VUEInputClienti v-model="IdCliente" 
                           :TakeAllClienti = true
                           @onChangeInputCliente="OnChangeIdCliente"
                           @onUpdate="newValue => IdCliente = newValue"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;margin-right:5px">
          <label>Nome</label>
        </div>
        <div style="float:left;width:13%;margin-right:20px">
          <input type="text" class="form-control" v-model="NomeFiliale"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;margin-right:28px">
          <label>Indirizzo </label>
        </div>
        <div style="float:left;width:13%;margin-right:20px">
          <input type="text" class="form-control" v-model="Indirizzo"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;">
          <label>Comune </label>
        </div>
        <div style="float:left;width:13%;margin-right:10px;margin-left: 20px;">
          <input type="text" class="form-control" v-model="Comune"/>
        </div>
      </div>

      <div class="col-md-1" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">
        <img src="@/assets/images/MenuExcel.png" title="Esporta su excel la lista delle filiali" style="margin-top:-8px;cursor:pointer;float:right;" @click="OnClickEsportaFilialiExcel">
        <button @click="OnClickCaricaListaFiliali()" class="btn btn-s-md btn-info" style="margin-right: 5px;">[F2] Cerca</button>
      </div>

      <div class="col-md-11">
        <div style="float:left;font-size:14px;padding-top: 13px;margin-right:5px">
          <label>Provincia </label>
        </div>
        <div style="float:left;width:13%;margin-right:20px;padding-top: 10px;">
          <VUEInputProvince v-model="Provincia" style="cursor:default"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 13px;margin-right:18px">
          <label>Regione </label>
        </div>
        <div style="float:left;width:13%;margin-right:20px;padding-top: 10px;">
          <VUEInputRegioni v-model="Regione" style="cursor:default"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 13px;">
          <label>Stato cliente </label>
        </div>
        <div style="float:left;width:13%;margin-right:20px;padding-top: 10px;">
          <select style="float:left;width:100%;" class="form-control" v-model ="StatoCliente">
            <option value="Tutti">Tutti</option>
            <option value="Attivi">Attivi</option>           
            <option value="NonAttivi">Non attivi</option>           
          </select>
        </div> 
        <div style="float:left;font-size:14px;padding-top: 13px;margin-right:15px">
          <label>Mese visita&nbsp;</label>
        </div>
        <div style="float:left;width:13%;margin-right:20px;padding-top: 10px;">
          <VUEInputMese v-model="MeseVisita" :NessunaSelezione="true" style="cursor:default"/>
        </div>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12" style="padding:20px">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                   <template v-for="Cliente in LsClienti" :key="Cliente.Chiave">
                      <thead>
                        <tr>
                          <th style="position: sticky; top: 0;font-size:16px" colspan="6">{{ Cliente.RAGIONE_SOCIALE }}</th>
                          <th style="position: sticky; top: 0;font-size:16px" colspan="1">Totale: {{ Cliente.Filiali.length }}</th>
                        </tr>
                        <tr>
                          <th>Nome Filiale</th>
                          <th>Provincia</th>
                          <th>Comune</th>
                          <th>Regione</th>
                          <th>Indirizzo</th>
                          <th>Cap</th>
                          <th>Stato Filiale</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="Filiale in Cliente.Filiali" :key="Filiale.CHIAVE">                       
                          <td> {{ Filiale.NOME }} </td>
                          <td> {{ Filiale.NOME_PROVINCIA}} </td>
                          <td> {{ Filiale.COMUNE}} </td>
                          <td> {{ Filiale.NOME_REGIONE}} </td>
                          <td> {{ Filiale.INDIRIZZO}} </td>
                          <td> {{ Filiale.CAP}} </td>
                          <td> {{ Filiale.FILIALE_DISATTIVATA == 'T' ? 'Disattivata' : 'Attivata'  }}
                          <div style="float:left;width:1%">&nbsp;</div>
                         <div style="padding-top:4px;float: right; width: 4%; text-align: center;">  
                         <a v-if = "Filiale.ATTIVAZIONI_PRESENTI != 0"
                          class="fa fa-list"
                          style="cursor:pointer;color:black;font-size:15px;margin-right:5px;"
                          @click="ListaLogFiliali(Filiale.CHIAVE)">
                          </a>
                         </div>
                         </td>
                        </tr>
                      </tbody>
                   </template>
                </table>
              </div>
            <footer class="panel-footer">
              <div class="row">
                <div class="col-sm-4 hidden-xs">
                </div>
                <div class="col-sm-4 text-center">
                  <small class="text-muted inline m-t-sm m-b-sm">
                    {{TestoPaginazione}}
                  </small>
                </div>
                <div class="col-sm-4 text-right text-center-xs">                
                  <ul class="pagination pagination-sm m-t-none m-b-none">
                    <li><a @click="OnClickFirstPage()"><i class="fa fa-backward"></i></a></li>
                    <li><a @click="PortaIndietroGruppoPagine()" ><i :class="{ ZMDisabled : !BackGruppoPagineVisibile }" class="fa fa-chevron-left"></i></a></li>
                    <li :class="{ active : IsActivePage(0)}"><a @click="OnClickChangePage(0)">{{Paginazione.StartGruppoPagine}}</a></li>
                    <li v-if="TastoPaginaVisibile(1)" :class="{ active : IsActivePage(1)}"><a @click="OnClickChangePage(1)">{{Paginazione.StartGruppoPagine + 1}}</a></li>
                    <li v-if="TastoPaginaVisibile(2)" :class="{ active : IsActivePage(2)}"><a @click="OnClickChangePage(2)">{{Paginazione.StartGruppoPagine + 2}}</a></li>
                    <li v-if="TastoPaginaVisibile(3)" :class="{ active : IsActivePage(3)}"><a @click="OnClickChangePage(3)">{{Paginazione.StartGruppoPagine + 3}}</a></li>
                    <li v-if="TastoPaginaVisibile(4)" :class="{ active : IsActivePage(4)}"><a @click="OnClickChangePage(4)">{{Paginazione.StartGruppoPagine + 4}}</a></li>
                    <li><a @click="PortaAvantiGruppoPagine()"><i :class="{ ZMDisabled : !ForwardGruppoPagineVisibile }" class="fa fa-chevron-right"></i></a></li>
                    <li><a @click="OnClickLastPage()"><i class="fa fa-forward"></i></a></li>
                  </ul>
                </div>
              </div>
            </footer>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation.js';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputRegioni from '@/components/InputComponents/VUEInputRegioni.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputMese from '@/components/InputComponents/VUEInputMese.vue';
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js';
import XLSX from 'xlsx-js-style/dist/xlsx.min.js';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';

export default 
{
 components : 
 {
  VUEModal,
  VUEInputProvince,
  VUEInputRegioni,
  VUEInputClienti,
  VUEInputMese,
  VUEModalCaricamentoDati
 },
 data()
 {
   return { 
            NomeFiliale              : '',
            Indirizzo                : '',
            Comune                   : '',
            Provincia                : -1,
            Regione                  : -1,
            IdCliente                : -1,
            MeseVisita               : -1,
            StatoCliente             : 'Tutti',
            LsLogFiliali             : false,
            PopupAttesaCalcolo       : false,
            LsClienti                : [],
            LsClientiTotali          : [],
            ElencoLogFiliali         : [],
            NomeProgramma            : NOME_PROGRAMMA,
            Paginazione              : {
                                          NrRighePerPagina  : 30,
                                          NrPagina          : 1,
                                          StartGruppoPagine : 1,
                                          NrFiliali         : 0
                                       }
          }
 },

 computed:
 {
    BackGruppoPagineVisibile :
    {
      get()
      {
        return this.Paginazione.NrPagina > 1;
      }
    },
    TestoPaginazione :
    {
      get()
      {
        var MaxRighe = this.Paginazione.NrRighePerPagina * (this.Paginazione.NrPagina);
        if(this.Paginazione.NrFiliali < MaxRighe) MaxRighe = this.Paginazione.NrFiliali;
        let StartRecord = this.Paginazione.NrFiliali == 0 ? 0 : this.Paginazione.NrRighePerPagina * (this.Paginazione.NrPagina - 1) + 1;
        return "Visualizzati " +  StartRecord + ' - ' +
                MaxRighe + ' di ' +  this.Paginazione.NrFiliali + ' risultati'
      }
    },
    ForwardGruppoPagineVisibile : 
    {
      get()
      {
        return this.Paginazione.NrFiliali >= this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina;
      }
    },
 },

 methods :
 {
    IsActivePage(Offset)
    {
      return this.Paginazione.NrPagina == this.Paginazione.StartGruppoPagine + Offset;
      
    },
    OnClickChangePage(Offset)
    {
      this.Paginazione.NrPagina = this.Paginazione.StartGruppoPagine + Offset;
      this.GestionePaginazione();
    },
    PortaIndietroGruppoPagine()
    {
        if(this.BackGruppoPagineVisibile)
           if(--this.Paginazione.NrPagina < this.Paginazione.StartGruppoPagine)
              this.Paginazione.StartGruppoPagine--;
        this.GestionePaginazione();
    },
    PortaAvantiGruppoPagine()
    {
       if(this.ForwardGruppoPagineVisibile)
          if(++this.Paginazione.NrPagina > this.Paginazione.StartGruppoPagine + 4)
              this.Paginazione.StartGruppoPagine++;
       this.GestionePaginazione(); 
    },
    OnClickFirstPage()
    {
      this.Paginazione.NrPagina = 1
      this.Paginazione.StartGruppoPagine = 1;
      this.GestionePaginazione()

    },
    OnClickLastPage()
    { 
      this.Paginazione.NrPagina = Math.ceil(this.Paginazione.NrFiliali / this.Paginazione.NrRighePerPagina)
      if(this.Paginazione.NrPagina >= 5)
        this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
      else this.Paginazione.StartGruppoPagine = 1;  
      this.GestionePaginazione()

    },  
    TastoPaginaVisibile(Offset)
    {
      return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighePerPagina <= this.Paginazione.NrFiliali
    },

    GetParametri()
    {
      var Parametri = {
                        Limite : this.Paginazione.NrRighePerPagina,
                        Offset : (this.Paginazione.NrPagina -1) * this.Paginazione.NrRighePerPagina
                      }
      if(this.NomeFiliale != '')
         Parametri.NomeFiliale = '%' + this.NomeFiliale.toUpperCase() + '%';
      if(this.Indirizzo != '')
         Parametri.Indirizzo = '%' + this.Indirizzo.toUpperCase() + '%';
      if(this.Comune != '')
         Parametri.Comune = '%' + this.Comune.toUpperCase() + '%';
      if(this.Provincia != -1)
         Parametri.Provincia = this.Provincia;  
      if(this.Regione != -1)
         Parametri.Regione = this.Regione;
      if(this.IdCliente != -1)
         Parametri.IdCliente = this.IdCliente;
      if(this.StatoCliente != 'Tutti')
         Parametri.StatoCliente = this.StatoCliente;
      if(this.StatoCliente == 'Attivi')
         Parametri.StatoCliente= 'T';
      if(this.StatoCliente == 'NonAttivi')
         Parametri.StatoCliente = 'F';
      if(this.MeseVisita != -1)
         Parametri.MeseVisita = this.MeseVisita;

      return Parametri
    },

    OnClickCaricaListaFiliali()
    {
      var Self = this
      this.LsClienti = []
                                
      SystemInformation.AdvQuery.GetSQL('Filiali', this.GetParametri(),
                                            function(Results)
                                             {
                                               let ArrayInfoLsClienti = SystemInformation.AdvQuery.FindResults(Results, "LsClientiFiliali");

                                               if(ArrayInfoLsClienti != undefined) 
                                               { 
                                                 Self.LsClientiTotali = []

                                                 let LastChiave   = -1;
                                                 let LastFiliale  = -1;
                                                 let LastCliente  = null;
                                                 ArrayInfoLsClienti.forEach(function(Riga)
                                                 {
                                                   if(Riga.CLIENTE != LastChiave)
                                                   {
                                                      LastCliente = {}
                                                      LastCliente.Chiave = Riga.CLIENTE
                                                      LastCliente.RAGIONE_SOCIALE = Riga.RAGIONE_SOCIALE
                                                      LastCliente.Filiali = []
                                                      LastChiave = Riga.CLIENTE
                                                      Self.LsClientiTotali.push(LastCliente)
                                                   }
                                                   if(Riga.CHIAVE != LastFiliale)
                                                   {
                                                      LastCliente.Filiali.push({
                                                                                CHIAVE               :  Riga.CHIAVE,
                                                                                NOME                 :  Riga.NOME,
                                                                                NOME_PROVINCIA       :  Riga.NOME_PROVINCIA,
                                                                                COMUNE               :  Riga.COMUNE,
                                                                                NOME_REGIONE         :  Riga.NOME_REGIONE,
                                                                                INDIRIZZO            :  Riga.INDIRIZZO,
                                                                                CAP                  :  Riga.CAP,
                                                                                FILIALE_DISATTIVATA  :  Riga.FILIALE_DISATTIVATA,
                                                                                ATTIVAZIONI_PRESENTI :  Riga.ATTIVAZIONI_PRESENTI
                                                                              })
                                                      LastFiliale = Riga.CHIAVE
                                                   }
                                                 })

                                                 Self.GestionePaginazione()
                                               }
                                               else SystemInformation.HandleError('Impossibile ottenere lista dei clienti');
                                             },
                                             function (HTTPError, SubHTTPError, Response) 
                                             {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                             },
                                              'LsClientiFiliali'); 
    },

    GestionePaginazione()
    {
      this.Paginazione.NrFiliali = this.LsClientiTotali.length

      this.LsClienti = []

      for(let i = (this.Paginazione.NrPagina -1) * this.Paginazione.NrRighePerPagina; i < this.LsClientiTotali.length; i++)
        if(this.LsClienti.length == this.Paginazione.NrRighePerPagina)
          break;
        else this.LsClienti.push(this.LsClientiTotali[i])
    },

    ListaLogFiliali(ChiaveFiliale)
    {
      this.LsLogFiliali = true
      var Self = this
      SystemInformation.AdvQuery.GetSQL('Clienti',{ CHIAVE_FILIALE : ChiaveFiliale },
                                             function(Results)
                                             {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "LogFilialiAttive");
                                              if (ArrayInfo != undefined) 
                                              {
                                                 Self.ElencoLogFiliali = ArrayInfo
                                                 Self.ElencoLogFiliali.forEach(function(Filiale)
                                                            {
                                                              Filiale.DATA_ORA = new Date(Filiale.DATA_ORA)
                                                              Filiale.DATA_ORA = TZDateFunct.FormatDateTime('dd-mm-yyyy hh:nn',Filiale.DATA_ORA)
                                                            })
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista dei log');
                                             },
                                             function (HTTPError, SubHTTPError, Response) 
                                             {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                             },
                                              'LogFiliali');
    },

    OnClickChiudiLista()
    {
       this.LsLogFiliali = false
    },

    // OnClickStampaFiliali()
    // {
    //   var Self = this
    //   var Parametri = { 
    //                     Dal               : this.DallaData, 
    //                     Al                : this.AllaData, 
    //                     OrdineDecrescente : this.OrdineDecrescente,
    //                     IdContoCasse      : this.IdContoCasse != -1? this.IdContoCasse : null 
    //                   }
    //   SystemInformation.AdvQuery.ExecuteExternalScript('StampaPrimaNota', Parametri,
    //                                                 function(Result)
    //                                                 {  
    //                                                   if(Result.PDF != undefined)
    //                                                   {
    //                                                     var routeData = Self.$router.resolve({
    //                                                                   name   : "IFrameStampe"
    //                                                     });
    //                                                     var AWindow = window.open(routeData.href, "_blank");
    //                                                     AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
    //                                                   }
    //                                                   else SystemInformation.HandleError('Documento non presente','','');
    //                                                 },
    //                                                 function(HTTPError,SubHTTPError,Response)
    //                                                 {
    //                                                   SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
    //                                                 }) 
    // },

    KeyDownF2(event)
    {
      if(event.which == 113) 
        this.OnClickCaricaListaFiliali();
    },

    OnChangeIdCliente()
    {
      // this.IdContratto = -1
    },

    OnClickEsportaFilialiExcel()
    {
        this.PopupAttesaCalcolo = true
        var Self = this
        SystemInformation.AdvQuery.ExecuteExternalScript('EsportazioneFilialiClientiExcel',
        this.GetParametri(),
        function(Results)
        {
          let ArrayInfo = Results.ListaFiliali
          if(ArrayInfo != undefined)
          {
            const wb = XLSX.utils.book_new();
            let ArrayContenitore = []
            let Row = 
              [
                { v: "Regione", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                { v: "Provincia", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                { v: "Comune", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Indirizzo", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Nr. civico", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "CAP", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Filiale", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Cliente", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Mese visita", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Estintori", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Porte", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Luci", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Evacuatori", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "EVAC", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Fumi", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Sprinkler", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Gruppi press.", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Imp. Idr.", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Idranti", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
              ];
            ArrayContenitore.push(Row)

            let RowDivisore = 
            [
              { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
            ];

            let LastRegione   = -1
            // let LastProvincia = -1
            // let LastComune    = -1
            let TotaleAttrezzature = { 
                                        Estintori  : 0,
                                        Porte      : 0,
                                        Luci       : 0,
                                        Evacuatori : 0,
                                        EVAC       : 0,
                                        Fumi       : 0,
                                        Sprinkler  : 0,
                                        Gruppi     : 0,
                                        ImpIdr     : 0,
                                        Idranti    : 0,
                                        Totale     :0,
                                        AggiungiAttrezzature(NumEst, NumPorte, NumLuci, NumEvacuatori, NumEVAC, NumFumi, NumSprinkler, NumGruppi, NumImpIdr, NumIdranti)
                                        {
                                          this.Estintori  += NumEst
                                          this.Porte      += NumPorte
                                          this.Luci       += NumLuci
                                          this.Evacuatori += NumEvacuatori
                                          this.EVAC       += NumEVAC
                                          this.Fumi       += NumFumi
                                          this.Sprinkler  += NumSprinkler
                                          this.Gruppi     += NumGruppi
                                          this.ImpIdr     += NumImpIdr
                                          this.Idranti    += NumIdranti
                                        }
                                     }
            ArrayInfo.forEach(function(ARecord)
            {
              // if(LastRegione != ARecord.REGIONE)
              // {
                if(LastRegione != -1 && LastRegione != ARecord.REGIONE)
                {
                  ArrayContenitore.push(RowDivisore)
                  ArrayContenitore.push(RowDivisore)
                  // ArrayContenitore.push(RowDivisore)
                }
                let RowRegione = 
                [
                  { v: ARecord.REGIONE, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.PROVINCIA, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.COMUNE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.INDIRIZZO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.NR_CIVICO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.CAP, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.NOME, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.RAGIONE_SOCIALE_CLIENTE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.MESE_VISITA != null? TZDateFunct.GetMesiInItaliano()[ARecord.MESE_VISITA - 1] : "NON PRESENTE", t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.NumeroEstintori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroPorte, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroLuci, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroEvacuatori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroEVAC, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroFumi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroSprinkler, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroGruppi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroImpIdr, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                  { v: ARecord.NumeroIdranti, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                ];
                ArrayContenitore.push(RowRegione)
                LastRegione   = ARecord.REGIONE

                TotaleAttrezzature.AggiungiAttrezzature(ARecord.NumeroEstintori,
                                                        ARecord.NumeroPorte,
                                                        ARecord.NumeroLuci,
                                                        ARecord.NumeroEvacuatori,
                                                        ARecord.NumeroEVAC,
                                                        ARecord.NumeroFumi,
                                                        ARecord.NumeroSprinkler,
                                                        ARecord.NumeroGruppi,
                                                        ARecord.NumeroImpIdr,
                                                        ARecord.NumeroIdranti)
            });
            ArrayContenitore.push([
                                    { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
                                 ])

            ArrayContenitore.push ([
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10 }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: "Totale:", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                    { v: TotaleAttrezzature.Estintori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Porte, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Luci, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Evacuatori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.EVAC, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Fumi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Sprinkler, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Gruppi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.ImpIdr, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: TotaleAttrezzature.Idranti, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                  ])

            // STEP 3: Create worksheet with rows; Add worksheet to workbook
            const ws = XLSX.utils.aoa_to_sheet(ArrayContenitore);
            XLSX.utils.book_append_sheet(wb, ws, "readme demo");

            var wscols = [
                            {wch:12},
                            {wch:12},
                            {wch:25},
                            {wch:30},
                            {wch:12},
                            {wch:12},
                            {wch:35},
                            {wch:35},
                            {wch:12}
                          ];
                          
            ws['!cols'] = wscols;

            // STEP 4: Write Excel file to browser
            XLSX.writeFile(wb, "EsportazioneClienti.xlsx");
            Self.PopupAttesaCalcolo = false

          }
          else SystemInformation.HandleError('Impossibile ottenere la lista dei clienti da esportare');
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
          Self.PopupAttesaCalcolo = false
        })
    },

 },


 beforeMount()
 {
    this.OnClickCaricaListaFiliali()
 }
}
</script>


