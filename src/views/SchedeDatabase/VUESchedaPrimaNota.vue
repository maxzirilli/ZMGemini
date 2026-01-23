<template>
    <div @keydown="KeyDownF2($event)">
      <div class="row" style="padding:10px;border-top:1px;border: 1 px solid">
        <div class="col-md-12">
        <div style="float:left;width:1%">&nbsp;</div>
        <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickStampaPrimaNota">Stampa</button>
        <button style="float:right; margin-right: 15px" @click="OnClickCaricaDatiPrimaNota" class="btn btn-s-md btn-info">[F2] Cerca</button>
        
          <div style="float:right;width:15%;margin-right:20px">
            <input type="date" class="form-control" v-model="AllaData"/>
          </div>
          <div style="float:right;font-size:14px;padding-top: 5px;">
            <label>al &nbsp;&nbsp;&nbsp;</label>
          </div>
          <div style="float:right;width:15%;margin-right:10px">
            <input type="date" class="form-control" v-model="DallaData"/>
          </div>
          <div style="float:right;font-size:14px;padding-top: 5px;">
            <label>Dal &nbsp;&nbsp;&nbsp;</label>
          </div>
          <div style="float:right;width:15%;margin-right:20px">
            <VUEInputContoCorrente v-model="IdContoCasse" @change="OnChangeContoCassa"/>
          </div>
          <div style="float:right;font-size:14px;padding-top: 5px;">
            <label>Conto/cassa &nbsp;&nbsp;</label>
          </div>
          <div style="float:right;width:3%;margin-right:10px">
            <input type="checkbox" class="form-control" style="height:20px" v-model="OrdineDecrescente" @change="OnChangeOrdineDecrescente"/>
          </div>
          <div style="float:right;font-size:14px;padding-top: 5px;">
            <label>Ordine decrescente</label>
          </div>  
        </div>
        <div class="col-md-12">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>    
                      <th style="width:8%">&nbsp;</th>
                      <th style="width:8%">&nbsp;</th>
                      <th style="width:6%">&nbsp;</th>
                      <th style="width:50%">&nbsp;</th>
                      <template v-for="ContoCorrente in ListaContiCorrenteCasse" :key="ContoCorrente.CHIAVE">
                        <th v-if="ContoCorrente.IsContoCorrente != 'T'" :colspan="ContoCorrente.MostraSaldo ? '3' : '2'">
                          Cassa<br/>{{ ContoCorrente.Descrizione }} 
                          <i v-if="VisibilitaColonnaSaldiPrimaNota" class="ZMIconButton fa fa-arrow-circle-down text-info" style="float: right" @click="ContoCorrente.MostraSaldo = !ContoCorrente.MostraSaldo"></i> 
                        </th>
                        <th v-if="ContoCorrente.IsContoCorrente == 'T'" :colspan="ContoCorrente.MostraSaldo ? '3' : '2'">
                          {{ ContoCorrente.Descrizione }}<br/>{{ ContoCorrente.NrConto }}
                          <i v-if="VisibilitaColonnaSaldiPrimaNota" class="ZMIconButton fa fa-arrow-circle-down text-info" style="float: right" @click="ContoCorrente.MostraSaldo = !ContoCorrente.MostraSaldo"></i> 
                        </th>
                      </template>
                    </tr>
                    <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th>Saldo inizio periodo: </th>
                      <template v-for="Conto in ListaContiCorrenteCasse" :key="Conto.CHIAVE">      
                        <th style="font-size:14px;text-align:right;" :colspan="Conto.MostraSaldo ? '3' : '2'">&nbsp;{{ FormattaCifre(Conto.Saldo / 100) }}</th>
                      </template>
                    </tr>
                    <tr>
                      <th>Data pag.</th>
                      <th>Data doc.</th>
                      <th>Codice Clien. / Forn.</th>
                      <th>Descrizione</th>
                      <template v-for="Elemento in ListaContiCorrenteCasse" :key="Elemento.CHIAVE">
                          <th>ENTRATE</th>
                          <th>USCITE</th>
                          <th v-if="Elemento.MostraSaldo">SALDO</th>
                      </template>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Movimento in MovimentiPagina" :key="Movimento.CHIAVE" style="font-size:15px">
                      <td> {{ Movimento.StringData }} </td>
                      <td> {{ Movimento.DataDocumento }} </td>
                      <td> {{ Movimento.Codice }} </td>
                      <td style="white-space: pre-wrap"> {{ GetDescrizioneMovimento(Movimento.Descrizione, Movimento.IdTipologiaMovimento)}} {{Movimento.Note != '' && Movimento.Note != null? ' - ' + Movimento.Note : ''}}</td>
                      <template v-for="ContoCorrente in ListaContiCorrenteCasse" :key="ContoCorrente.Chiave">
                          <td style="text-align:right;border-left:1px #68b6be solid">{{GetImportoPrimaNota(Movimento,ContoCorrente,true)}}</td>
                          <td style="color:red;text-align:right;">{{GetImportoPrimaNota(Movimento,ContoCorrente,false)}}</td>
                          <td v-if="ContoCorrente.MostraSaldo" style="text-align:right;" :style="{ color : !Movimento.SaldoPositivo ? 'red' : ''}" >{{ GetImportoSaldo(Movimento,ContoCorrente) }}</td>
                      </template>
                    </tr>
                  </tbody>
                  <thead>
                    <tr>    
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>Totali: </th>
                      <template v-for="ContoCorrente in ListaContiCorrenteCasse" :key="ContoCorrente.CHIAVE">
                        <th style="text-align:right;border-left:1px #68b6be solid">&nbsp; {{GetTotaleEntrateUscite(ContoCorrente,true)}}</th>
                        <th style="color:red;text-align:right;">&nbsp; {{GetTotaleEntrateUscite(ContoCorrente,false)}}</th>
                        <th v-if="ContoCorrente.MostraSaldo" style="color:red;text-align:right;">&nbsp;</th>
                      </template>
                    </tr>
                     <tr>    
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>&nbsp;</th>
                      <th>Saldo fine periodo: </th>
                      <template v-for="ContoCorrente in ListaContiCorrenteCasse" :key="ContoCorrente.CHIAVE">
                        <th style="font-size:14px;text-align:right;" :colspan="ContoCorrente.MostraSaldo ? '3' : '2'">&nbsp;{{FormattaCifre(ContoCorrente.TotaleTmp / 100)}}</th>
                      </template>
                    </tr>
                  </thead>
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
import { SystemInformation } from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEInputContoCorrente from '@/components/InputComponents/VUEInputContoCorrente.vue'

export default 
{
   components : 
   {
      VUEInputContoCorrente,
   },
   data()
   {
     return { 
              DallaData                : '',
              AllaData                 : '',
              SaldoTemp                : {},
              OrdineDecrescente        : false,
              MostraSaldo              : false,
              IdContoCasse             : -1,
              ListaContiCorrenteCasse  : [],
              ListaMovimenti           : [],
              VisibilitaColonnaSaldiPrimaNota : SystemInformation.AccessRights.VisibilitaColonnaSaldiPrimaNota(),
              Paginazione              : {
                                            NrMovimenti       : 15,
                                            NrPagina          : 1,
                                            StartGruppoPagine : 1
                                         },
            }
   },

   computed:
   {
      MovimentiPagina :
      {
        get()
        {
          let Result = [];
          for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrMovimenti;
              i < this.ListaMovimenti.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrMovimenti;
              i++)
              Result.push(this.ListaMovimenti[i]);
          return Result;
        }
      },

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
          var MaxRighe = this.Paginazione.NrMovimenti * (this.Paginazione.NrPagina);
          if(this.ListaMovimenti.length < MaxRighe) MaxRighe = this.ListaMovimenti.length;
          let StartRecord = this.ListaMovimenti.length == 0 ? 0 : this.Paginazione.NrMovimenti * (this.Paginazione.NrPagina - 1) + 1;
          return "Visualizzati " +  StartRecord + ' - ' +
                  MaxRighe + ' di ' +  this.ListaMovimenti.length + ' risultati'
        }
      },
      ForwardGruppoPagineVisibile : 
      {
        get()
        {
          return this.ListaMovimenti.length >= this.Paginazione.NrPagina * this.Paginazione.NrMovimenti;
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
      },
      PortaIndietroGruppoPagine()
      {
          if(this.BackGruppoPagineVisibile)
             if(--this.Paginazione.NrPagina < this.Paginazione.StartGruppoPagine)
                this.Paginazione.StartGruppoPagine--;
      },
      PortaAvantiGruppoPagine()
      {
         if(this.ForwardGruppoPagineVisibile)
            if(++this.Paginazione.NrPagina > this.Paginazione.StartGruppoPagine + 4)
                this.Paginazione.StartGruppoPagine++;
      },
      OnClickFirstPage()
      {
        this.Paginazione.NrPagina = 1
        this.Paginazione.StartGruppoPagine = 1;
      },
      OnClickLastPage()
      { 
        this.Paginazione.NrPagina = Math.ceil(this.ListaMovimenti.length / this.Paginazione.NrMovimenti)
        if(this.Paginazione.NrPagina >= 5)
          this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
        else this.Paginazione.StartGruppoPagine = 1;  
      },  
      TastoPaginaVisibile(Offset)
      {
        return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrMovimenti <= this.ListaMovimenti.length
      },


      GetImportoPrimaNota(Movimento,ContoCorrente,IsEntrata)
      {
        if(Movimento.ContoCorrente == ContoCorrente.Chiave)
           if((IsEntrata && Movimento.Importo > 0) || (!IsEntrata && Movimento.Importo < 0))
               return this.FormattaCifre(Movimento.Importo / 100)
        return ''
      },

      GetImportoSaldo(Movimento,ContoCorrente)
      {
        let Result  = ''

        if(Movimento.ContoCorrente == ContoCorrente.Chiave)
           Result = this.FormattaCifre(Movimento.CurrentSaldo)

        return Result
      },

      GetTotaleEntrateUscite(ContoCorrente,IsEntrata)
      {
        var Totale = 0
        for(var i = 0; i < this.ListaMovimenti.length; i++)
        {
          var Movimento = this.ListaMovimenti[i]
          if(Movimento.ContoCorrente == ContoCorrente.Chiave)
          {
            if(IsEntrata && Movimento.Importo > 0)
               Totale += Number(Movimento.Importo)
            if(!IsEntrata && Movimento.Importo < 0)
               Totale += Number(Movimento.Importo)
          }
        
        }
        return this.FormattaCifre(Totale / 100)

      },

      GetDescrizioneMovimento(Descrizione, ChiaveTipologia)
      {
        var Result = Descrizione

        //da php \n arriva come \\n
        if(Result.includes('\\n'))
          Result = Result.replaceAll('\\n', '\n')
        
        if(ChiaveTipologia != '' && ChiaveTipologia != null)
          for(let i = 0; i < SystemInformation.Configurazioni.CatMovimenti.length; i++)
          {
            if(SystemInformation.Configurazioni.CatMovimenti[i].CHIAVE == ChiaveTipologia)
            {
              if(Result.trim() != '')
                 Result += ' - '
              Result += SystemInformation.Configurazioni.CatMovimenti[i].DESCRIZIONE
              break;
            }
          }

        return Result;
      },

      OnChangeOrdineDecrescente()
      {
        var Self = this
        this.ListaMovimenti.sort(function(a,b)
                                {
                                  if(!Self.OrdineDecrescente)
                                  {
                                    if(a.Data > b.Data)
                                      return 1
                                    if(a.Data < b.Data)
                                      return -1
                                    return 0
                                  }
                                  else
                                  {
                                    if(a.Data < b.Data)
                                      return 1
                                    if(a.Data > b.Data)
                                      return -1
                                    return 0
                                  }
                                })
      },

      OnChangeContoCassa()
      {
        this.OnClickCaricaDatiPrimaNota()
      },

      OnClickCaricaDatiPrimaNota()
      {
        var Self = this
        var Parametri = { 
                          Dal               : this.DallaData, 
                          Al                : this.AllaData, 
                          OrdineDecrescente : this.OrdineDecrescente,
                          IdContoCasse      : this.IdContoCasse != -1? this.IdContoCasse : null 
                        }
        SystemInformation.AdvQuery.ExecuteExternalScript('PrimaNota', Parametri,
                                                        function(Results)
                                                        {
                                                          if(Results.LsMovimenti != undefined && Results.LsContiCorrenti != undefined )
                                                          {
                                                            Self.ListaContiCorrenteCasse  = Results.LsContiCorrenti
                                                            Self.ListaMovimenti           = Results.LsMovimenti
                                                            Self.SaldoTemp                = {} 

                                                            Self.ListaContiCorrenteCasse.forEach(function(Elemento)
                                                            {
                                                              Elemento.MostraSaldo = false
                                                              if(!Self.SaldoTemp[Elemento.Chiave])
                                                              {
                                                                Self.SaldoTemp[Elemento.Chiave] = Elemento.Saldo / 100
                                                              }
                                                            })

                                                            Self.ListaMovimenti.forEach(function(Elemento)
                                                            {
                                                              Elemento.Data = new Date(Elemento.Data)
                                                              Elemento.StringData = TZDateFunct.FormatDateTime('dd-mm-yyyy',Elemento.Data)
                                                              if(Elemento.DataDocumento != '')
                                                              {
                                                                Elemento.DataDocumento= new Date(Elemento.DataDocumento)
                                                                Elemento.DataDocumento = TZDateFunct.FormatDateTime('dd-mm-yyyy',Elemento.DataDocumento)
                                                              }
                                                            })
                                                            Self.OnChangeOrdineDecrescente()

                                                            Self.ListaMovimenti.forEach(function(Elemento)
                                                            {
                                                              Self.SaldoTemp[Elemento.ContoCorrente] = Self.SaldoTemp[Elemento.ContoCorrente] + (Elemento.Importo / 100)
                                                              Elemento.CurrentSaldo = Self.SaldoTemp[Elemento.ContoCorrente]
                                                              Elemento.SaldoPositivo = Elemento.CurrentSaldo > 0 ? true : false
                                                            })

                                                          }
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        }) 
      },

      OnClickStampaPrimaNota()
      {
        var Self = this
        var Parametri = { 
                          Dal               : this.DallaData, 
                          Al                : this.AllaData, 
                          OrdineDecrescente : this.OrdineDecrescente,
                          IdContoCasse      : this.IdContoCasse != -1? this.IdContoCasse : null 
                        }
        SystemInformation.AdvQuery.ExecuteExternalScript('StampaPrimaNota', Parametri,
                                                      function(Result)
                                                      {  
                                                        if(Result.PDF != undefined)
                                                        {
                                                          var routeData = Self.$router.resolve({
                                                                        name   : "IFrameStampe"
                                                          });
                                                          var AWindow = window.open(routeData.href, "_blank");
                                                          AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                        }
                                                        else SystemInformation.HandleError('Documento non presente','','');
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      }) 
      },

      KeyDownF2(event)
      {
        if(event.which == 113) 
          this.OnClickCaricaDatiPrimaNota();
      },

      FormattaCifre(Importo, InsertEuro = true) 
      {
        let parts = Importo.toFixed(2).split('.'); 
        // Aggiungiamo il punto come separatore delle migliaia
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    
        return (InsertEuro ? '€ ' : '') + parts[0] + ',' + parts[1];
      }

   },


   beforeMount()
   {
      let Data = new Date()
      this.DallaData = TZDateFunct.DateInHTMLInputFormat(new Date(Data.getFullYear(), 0, 1)),
      this.AllaData  = TZDateFunct.DateInHTMLInputFormat(new Date()),
      this.OnClickCaricaDatiPrimaNota()
   }
}
</script>