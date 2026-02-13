<template>
<VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>
  <div @keydown="KeyDownF2($event)">
    <div class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding:10px; border-top:1px; height: 95px; padding-bottom: 0;">
      <div class="col-md-11">
        <div style="float:left;width:1%"></div>
        <!-- <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickStampaFiliali">Stampa</button> -->
        <div style="float:left;font-size:14px;padding-top: 5px;margin-right:5px">
          <label>Prodotto</label>
        </div>
        <div style="float:left;width:13%;margin-right:20px">
          <VUEInputProdotti v-model="NomeProdotto"/>
        </div> 

          <div style="float: left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 15px;">
              <label>Magazzino</label>
            </div>
            <div style="float:left;width:11%">
              <select class="form-control" v-model="NomeMagazzino">
                  <option selected :value="-1">-</option>
                  <option v-for="(Magazzino) in ListaMagazzini" :key="Magazzino.CHIAVE" :value="Magazzino.CHIAVE">
                      {{ Magazzino.DESCRIZIONE }}
                  </option>
              </select>
            </div> 
      </div>
      <div class="col-md-1" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">
        <button @click="OnClickCaricaListaProdotti()" class="btn btn-s-md btn-info" style="margin-right: 5px;">[F2] Cerca</button>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12" style="padding:20px">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                   <template v-for="Magazzino in LsMagazzini" :key="Magazzino.Chiave">
                      <thead>
                        <tr>
                          <th style="position: sticky; top: 0;font-size:16px" colspan="6">{{ Magazzino.DESCRIZIONE_MAGAZZINO }}</th>
                        </tr>
                        <tr>
                          <th>Prodotto</th>
                          <th>Quantità in magazzino</th>
                        </tr>
                      </thead>
                      <tbody>
  <tr v-if="!Magazzino.Prodotti || Magazzino.Prodotti.length === 0">
    <td colspan="2" style="font-style: italic; color: #999;">
      Nessun prodotto in questo magazzino
    </td>
  </tr>
  <tr v-for="Prodotto in Magazzino.Prodotti" :key="Prodotto.CHIAVE">
    <td>{{ Prodotto.DESCRIZIONE_PRODOTTO }}</td>
    <td>{{ Prodotto.QUANTITA_MAGAZZINO }}</td>
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
import { SystemInformation } from '@/SystemInformation.js';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
import VUEInputProdotti from '@/components/InputComponents/VUEInputProdotti.vue';

export default 
{
 components : 
 {
  VUEModalCaricamentoDati,
  VUEInputProdotti
 },
 data()
 {
   return { 
            NomeProdotto               : '',
            PopupAttesaCalcolo         : false,
            LsMagazzini                : [],
            LsMagazziniTotali          : [],
            Paginazione              : {
                                          NrRighePerPagina  : 30,
                                          NrPagina          : 1,
                                          StartGruppoPagine : 1,
                                          NrMagazzini         : 0
                                       },
            ListaMagazzini              : SystemInformation.Configurazioni.Magazzini,
            NomeMagazzino               : -1

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
        if(this.Paginazione.NrMagazzini < MaxRighe) MaxRighe = this.Paginazione.NrMagazzini;
        let StartRecord = this.Paginazione.NrMagazzini == 0 ? 0 : this.Paginazione.NrRighePerPagina * (this.Paginazione.NrPagina - 1) + 1;
        return "Visualizzati " +  StartRecord + ' - ' +
                MaxRighe + ' di ' +  this.Paginazione.NrMagazzini + ' risultati'
      }
    },
    ForwardGruppoPagineVisibile : 
    {
      get()
      {
        return this.Paginazione.NrMagazzini >= this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina;
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
      this.Paginazione.NrPagina = Math.ceil(this.Paginazione.NrMagazzini / this.Paginazione.NrRighePerPagina)
      if(this.Paginazione.NrPagina >= 5)
        this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
      else this.Paginazione.StartGruppoPagine = 1;  
      this.GestionePaginazione()

    },  
    TastoPaginaVisibile(Offset)
    {
      return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighePerPagina <= this.Paginazione.NrMagazzini
    },

    GetParametri()
    {
      var Parametri = {
                        Limite : this.Paginazione.NrRighePerPagina,
                        Offset : (this.Paginazione.NrPagina -1) * this.Paginazione.NrRighePerPagina
                      }
      if(this.NomeProdotto != '')
         Parametri.NomeProdotto = '%' + this.NomeProdotto.toUpperCase() + '%';

      if(this.NomeMagazzino != -1)
         Parametri.NomeMagazzino = this.NomeMagazzino;

      return Parametri
    },

    OnClickCaricaListaProdotti()
    {
      var Self = this
      Self.LsMagazzini = []
      Self.LsMagazziniTotali = []
    
      SystemInformation.AdvQuery.GetSQL('Magazzini', this.GetParametri(),
        function(Results)
        {
          var ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "LsMagazziniProdotti")
    
          if(!ArrayInfo || ArrayInfo.length == 0)
          {
            Self.GestionePaginazione()
            SystemInformation.HandleError('Impossibile ottenere lista dei magazzini')
            return
          }
    
          var LastChiave = null
          var LastMagazzino = null
    
          ArrayInfo.forEach(function(Riga)
          {
            // Nuovo magazzino se cambia la chiave
            if(Riga.CHIAVE != LastChiave)
            {
              LastMagazzino = 
              {
                Chiave: Riga.CHIAVE,
                DESCRIZIONE_MAGAZZINO: Riga.DESCRIZIONE_MAGAZZINO,
                Prodotti: []
              }
              Self.LsMagazziniTotali.push(LastMagazzino)
              LastChiave = Riga.CHIAVE
            }
    
            // Aggiungo il prodotto solo se presente
            if(Riga.CHIAVE_PRODOTTO != null)
            {
              LastMagazzino.Prodotti.push({
                                            CHIAVE               : Riga.CHIAVE_PRODOTTO,
                                            DESCRIZIONE_PRODOTTO : Riga.DESCRIZIONE_PRODOTTO,
                                            QUANTITA_MAGAZZINO   : (Riga.QUANTITA_MAGAZZINO ?? 0) / 100})
            }
          })
    
          Self.GestionePaginazione()
        },
        function(HTTPError, SubHTTPError, Response)
        {
          SystemInformation.HandleError(HTTPError, SubHTTPError, Response)
        },
        'LsMagazziniProdotti'
      )
    },

    GestionePaginazione()
    {
      this.Paginazione.NrMagazzini = this.LsMagazziniTotali.length

      this.LsMagazzini = []

      for(let i = (this.Paginazione.NrPagina -1) * this.Paginazione.NrRighePerPagina; i < this.LsMagazziniTotali.length; i++)
        if(this.LsMagazzini.length == this.Paginazione.NrRighePerPagina)
          break;
        else this.LsMagazzini.push(this.LsMagazziniTotali[i])
    },

    // ListaLogFiliali(ChiaveFiliale)
    // {
    //   this.LsLogFiliali = true
    //   var Self = this
    //   SystemInformation.AdvQuery.GetSQL('Clienti',{ CHIAVE_FILIALE : ChiaveFiliale },
    //                                          function(Results)
    //                                          {
    //                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "LogFilialiAttive");
    //                                           if (ArrayInfo != undefined) 
    //                                           {
    //                                              Self.ElencoLogFiliali = ArrayInfo
    //                                              Self.ElencoLogFiliali.forEach(function(Filiale)
    //                                                         {
    //                                                           Filiale.DATA_ORA = new Date(Filiale.DATA_ORA)
    //                                                           Filiale.DATA_ORA = TZDateFunct.FormatDateTime('dd-mm-yyyy hh:nn',Filiale.DATA_ORA)
    //                                                         })
    //                                           }
    //                                           else SystemInformation.HandleError('Impossibile ottenere lista dei log');
    //                                          },
    //                                          function (HTTPError, SubHTTPError, Response) 
    //                                          {
    //                                           SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
    //                                          },
    //                                           'LogFiliali');
    // },

    // OnClickChiudiLista()
    // {
    //    this.LsLogFiliali = false
    // },

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
        this.OnClickCaricaListaProdotti();
    },


    // OnClickEsportaFilialiExcel()
    // {
    //     this.PopupAttesaCalcolo = true
    //     var Self = this
    //     SystemInformation.AdvQuery.ExecuteExternalScript('EsportazioneFilialiClientiExcel',
    //     this.GetParametri(),
    //     function(Results)
    //     {
    //       let ArrayInfo = Results.ListaFiliali
    //       if(ArrayInfo != undefined)
    //       {
    //         const wb = XLSX.utils.book_new();
    //         let ArrayContenitore = []
    //         let Row = 
    //           [
    //             { v: "Regione", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
    //             { v: "Provincia", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
    //             { v: "Comune", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Indirizzo", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Nr. civico", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "CAP", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Filiale", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Cliente", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Mese visita", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Estintori", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Porte", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Luci", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Evacuatori", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "EVAC", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Fumi", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Sprinkler", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Gruppi press.", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Imp. Idr.", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //             { v: "Idranti", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //           ];
    //         ArrayContenitore.push(Row)

    //         let RowDivisore = 
    //         [
    //           { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
    //         ];

    //         let LastRegione   = -1
    //         // let LastProvincia = -1
    //         // let LastComune    = -1
    //         let TotaleAttrezzature = { 
    //                                     Estintori  : 0,
    //                                     Porte      : 0,
    //                                     Luci       : 0,
    //                                     Evacuatori : 0,
    //                                     EVAC       : 0,
    //                                     Fumi       : 0,
    //                                     Sprinkler  : 0,
    //                                     Gruppi     : 0,
    //                                     ImpIdr     : 0,
    //                                     Idranti    : 0,
    //                                     Totale     :0,
    //                                     AggiungiAttrezzature(NumEst, NumPorte, NumLuci, NumEvacuatori, NumEVAC, NumFumi, NumSprinkler, NumGruppi, NumImpIdr, NumIdranti)
    //                                     {
    //                                       this.Estintori  += NumEst
    //                                       this.Porte      += NumPorte
    //                                       this.Luci       += NumLuci
    //                                       this.Evacuatori += NumEvacuatori
    //                                       this.EVAC       += NumEVAC
    //                                       this.Fumi       += NumFumi
    //                                       this.Sprinkler  += NumSprinkler
    //                                       this.Gruppi     += NumGruppi
    //                                       this.ImpIdr     += NumImpIdr
    //                                       this.Idranti    += NumIdranti
    //                                     }
    //                                  }
    //         ArrayInfo.forEach(function(ARecord)
    //         {
    //           // if(LastRegione != ARecord.REGIONE)
    //           // {
    //             if(LastRegione != -1 && LastRegione != ARecord.REGIONE)
    //             {
    //               ArrayContenitore.push(RowDivisore)
    //               ArrayContenitore.push(RowDivisore)
    //               // ArrayContenitore.push(RowDivisore)
    //             }
    //             let RowRegione = 
    //             [
    //               { v: ARecord.REGIONE, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.PROVINCIA, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.COMUNE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.INDIRIZZO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.NR_CIVICO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.CAP, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.NOME, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.RAGIONE_SOCIALE_CLIENTE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.MESE_VISITA != null? TZDateFunct.GetMesiInItaliano()[ARecord.MESE_VISITA - 1] : "NON PRESENTE", t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
    //               { v: ARecord.NumeroEstintori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroPorte, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroLuci, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroEvacuatori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroEVAC, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroFumi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroSprinkler, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroGruppi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroImpIdr, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //               { v: ARecord.NumeroIdranti, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //             ];
    //             ArrayContenitore.push(RowRegione)
    //             LastRegione   = ARecord.REGIONE

    //             TotaleAttrezzature.AggiungiAttrezzature(ARecord.NumeroEstintori,
    //                                                     ARecord.NumeroPorte,
    //                                                     ARecord.NumeroLuci,
    //                                                     ARecord.NumeroEvacuatori,
    //                                                     ARecord.NumeroEVAC,
    //                                                     ARecord.NumeroFumi,
    //                                                     ARecord.NumeroSprinkler,
    //                                                     ARecord.NumeroGruppi,
    //                                                     ARecord.NumeroImpIdr,
    //                                                     ARecord.NumeroIdranti)
    //         });
    //         ArrayContenitore.push([
    //                                 { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
    //                              ])

    //         ArrayContenitore.push ([
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10 }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: "Totale:", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
    //                                 { v: TotaleAttrezzature.Estintori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Porte, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Luci, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Evacuatori, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.EVAC, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Fumi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Sprinkler, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Gruppi, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.ImpIdr, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                                 { v: TotaleAttrezzature.Idranti, t: "s", s: {name: 'Liberation Sans', sz: 10  }},
    //                               ])

    //         // STEP 3: Create worksheet with rows; Add worksheet to workbook
    //         const ws = XLSX.utils.aoa_to_sheet(ArrayContenitore);
    //         XLSX.utils.book_append_sheet(wb, ws, "readme demo");

    //         var wscols = [
    //                         {wch:12},
    //                         {wch:12},
    //                         {wch:25},
    //                         {wch:30},
    //                         {wch:12},
    //                         {wch:12},
    //                         {wch:35},
    //                         {wch:35},
    //                         {wch:12}
    //                       ];
                          
    //         ws['!cols'] = wscols;

    //         // STEP 4: Write Excel file to browser
    //         XLSX.writeFile(wb, "EsportazioneClienti.xlsx");
    //         Self.PopupAttesaCalcolo = false

    //       }
    //       else SystemInformation.HandleError('Impossibile ottenere la lista dei clienti da esportare');
    //     },
    //     function(HTTPError,SubHTTPError,Response)
    //     {
    //       SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
    //       Self.PopupAttesaCalcolo = false
    //     })
    // },

 },


 beforeMount()
 {
    this.OnClickCaricaListaProdotti()
 }
}
</script>


