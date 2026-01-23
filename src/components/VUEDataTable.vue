/* 
*************************************
NON SOSTITUIRE CON LE LIBRERIE PERCHE' CI SONO LE DIVERSE CATEGORIE PER OGNI ROW
*************************************
*/

<template>
   <div>
     <section class="panel panel-default">
      <header v-if="DataTable.Configurazione.Titolo" class="panel-heading">
        {{DataTable.Configurazione.Titolo}}
      </header>
       <div v-if="!RicercaDisabilitata">
        <div class="row wrapper">
          <div class="col-sm-9 m-b-xs" v-if="!TastoDisabilitato" >
              <button class="btn btn-sm btn-success" style="float:left;" @click="AggiungiRighe();">Aggiungi</button>
              <slot name="Buttons"></slot>
              <div style="float:left">
                <input type="number" min="1" style="width: 60px; height: 30px" v-model="NrRigheDaAggiungere">
              </div>   
              <div v-if="RowType == 'FilialiClienti'">
              <div style="float: left;font-size:14px;width:10%;text-align: right; padding-right: 10px;padding-top: 5px;">
              <label style="margin-bottom: 0px;">Stato filiali</label>
              </div>
              <div style="float:left;width:11%;">
               <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FiltroFiliali">
                 <option value="Tutte">Tutte</option>
                 <option value="Attive">Attive</option>           
                 <option value="Disattive">Disattive</option>           
               </select>
              </div>
              </div>
          </div>   
          <div class="col-sm-9 m-b-xs" v-else></div>
          <div class="col-sm-3">
              <input type="text" class="input-sm form-control" placeholder="Cerca..." v-model="Filtro">
          </div>
          
        </div>
       </div>
        
      <!-- Tabella standard-->
      <div v-if="RowType == undefined" class="table-responsive">
        <table class="table table-striped b-t b-light">
          <thead>
            <tr>
              <template v-for="Colonna in DataTable.Configurazione.Colonne" :key="Colonna.Id">
                <th v-if="!Colonna.Nascosta"
                    :style="{ width : Colonna.Larghezza,
                              'text-align' : Colonna.GetTitleAlignment() }" 
                    :class="{ 'th-sortable' : Colonna.Ordinamento }">
                    {{Colonna.Titolo + (Colonna.Ordinamento ? "&nbsp;&nbsp;" : "")}}
                    <span v-if="Colonna.Ordinamento" class="th-sort" @click="OnClickOrdinamento(Colonna)">
                      <i v-if="DataTable.OrdinatoPer.Campo == Colonna.Campo" 
                        class="fa" 
                        :class="{ 'fa-sort-up'   : DataTable.OrdinatoPer.Discendente,
                                  'fa-sort-down' : !DataTable.OrdinatoPer.Discendente  
                                }"
                        style="box-sizing: border-box;">
                      </i>
                      <i class="fa fa-sort" style="box-sizing: border-box;"></i>
                    </span>
                </th>
              </template>
              <th :style="{ width : LarghezzaColonneRestanti}"></th>
              <th :style="{ width : LarghezzaColonneRestanti}"></th>
            </tr>
          </thead>
          <tbody>
             <template v-for="(Riga,NdxRiga) in RighePagina" :key="Riga.Dati[DataTable.Configurazione.CampoChiave]">
                <tr :class="{DivRighePari : NdxRiga % 2 == 0, DivRigheDispari : NdxRiga % 2 != 0}">
                   <VUEDataRow :Riga="Riga" :DataTable="DataTable" @onClick="EventoOnClick"/>
                </tr>
                <tr v-if="Riga.RowErrors().length != 0" :class="{DivRighePari : NdxRiga % 2 == 0, DivRigheDispari : NdxRiga % 2 != 0}">
                   <td :colspan="DataTable.Configurazione.Colonne.length + 2">
                     <label v-for="(Error,NdxError) in Riga.RowErrors()" :Key="NdxError" style="margin-bottom: 0px; margin-top:0px;" class="ZMFormLabelError">{{Error}}</label>
                   </td>
                </tr>
             </template>   
          </tbody>
        </table>
      </div>
      <!-- Filiali clienti -->
      <div v-if="RowType == 'FilialiClienti'" class="table-responsive">
        <div class="DivRighe" v-for="Riga in FilialiFiltrate" :key="Riga.Dati[DataTable.Configurazione.CampoChiave]">
            <VUEDataRowFilialiClienti :Riga="Riga" :DataTable="DataTable" :SchedaCliente="SchedaCliente"/>
        </div>
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
</template>

<script>
import VUEDataRow from './DataRows/VUEDataRow.vue';
import VUEDataRowFilialiClienti from './DataRows/VUEDataRowFilialiClienti.vue'

export default 
{
    name: "VUEDataTable",
    emits: ['onChange','onClick'],
    data() 
    {
        return {
             Paginazione : {
                               NrRighe   : 10,
                               NrPagina  : 1,
                               StartGruppoPagine : 1
                           },
             NrRigheDaAggiungere : 1,
             Filtro              : '',
             OrdinamentoInCorso  : false,
             FiltroFiliali       : "Attive",
        };
    },
    props: ["DataTable","RowType","TastoDisabilitato","SchedaCliente","NumeroRighePerPagina","RicercaDisabilitata"],
    watch:
    {
      DataTable :
      {
         handler()
         {
           
           this.DataTable.CheckDoppioni();
           this.$emit('onChange');
         },
         deep : true
      }
    },
    methods: 
    {
      AggiungiRighe()
      {
         this.DataTable.AddRowsOnTop(this.NrRigheDaAggiungere);
      },
      TastoPaginaVisibile(Offset)
      {
        return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighe < this.RigheFiltrate.length
      },
      OnClickOrdinamento(Colonna)
      {
        var OrdinamentoDiscendente = false;
        if(Colonna.Campo == this.DataTable.OrdinatoPer.Campo)
           OrdinamentoDiscendente = !this.DataTable.OrdinatoPer.Discendente;
        this.DataTable.OrdinaPer(Colonna.Campo,OrdinamentoDiscendente);
      },
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
        this.Paginazione.NrPagina = Math.ceil(this.RigheFiltrate.length / this.Paginazione.NrRighe)
        if (this.Paginazione.NrPagina >= 5)
              this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
        else this.Paginazione.StartGruppoPagine =1;  
      },

      EventoOnClick(Parametro)
      {
        this.$emit('onClick', Parametro)  
      },
    },
    components: 
    { 
      VUEDataRow, 
      VUEDataRowFilialiClienti, 
    },
    computed:
    {
      RigheFiltrate :
      {
        get()
        {
          var Self = this;
          var Filtro = this.Filtro.toUpperCase().trim();
          var ListaRighe = this.DataTable.Righe.filter(function(Riga)
          {
            if(Riga.Eliminato)
               return false;
            if(Filtro == '') 
               return true;
            for(let i = 0; i < Self.DataTable.Configurazione.Colonne.length;i++)
            {
                let Colonna = Self.DataTable.Configurazione.Colonne[i];
                if(Riga.GetText(Colonna.Campo).toUpperCase().indexOf(Filtro) !== -1)
                   return true;
            }
            return false;
          });
          
          return ListaRighe;
        }
      },
      RighePagina :
      {
        get()
        {
          let Result = [];
          for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighe;
              i < this.RigheFiltrate.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;
              i++)
              Result.push(this.RigheFiltrate[i]);
          return Result;
        }
      },
      ForwardGruppoPagineVisibile : 
      {
        get()
        {
          return this.RigheFiltrate.length > this.Paginazione.NrPagina * this.Paginazione.NrRighe;
        }
      },
      LarghezzaColonneRestanti :
      {
          get()
          {
            var Larghezza = 0;
            if(this.DataTable.LarghezzaColonneRestantiInPixels == 0)  
            {
              this.DataTable.Configurazione.Colonne.forEach(function(AColonna)
              {
                Larghezza += parseInt(AColonna.Larghezza);
              })
              return parseFloat((100 - Larghezza) / 2,2)  + '%';
            }
            else return this.DataTable.LarghezzaColonneRestantiInPixels + 'px';
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
          var MaxRighe = this.Paginazione.NrRighe * (this.Paginazione.NrPagina);
          if(this.RigheFiltrate.length < MaxRighe) MaxRighe = this.RigheFiltrate.length;
          let StartRecord = this.RigheFiltrate.length == 0 ? 0 : this.Paginazione.NrRighe * (this.Paginazione.NrPagina - 1) + 1;
          return "Visualizzati " +  StartRecord + ' - ' +
                  MaxRighe + ' di ' +  this.RigheFiltrate.length + ' risultati'
        }
      },

      FilialiFiltrate :
      {
        get()
        {
          let Result = [];
          var FiltroFiliali = this.FiltroFiliali;
          let ListaFiltrata = this.RigheFiltrate;
      
          if(FiltroFiliali == 'Attive')
          {
             ListaFiltrata = ListaFiltrata.filter(function(Riga)
             {
               return Riga.Dati.FILIALE_DISATTIVATA.Valore == false;
             });
          }
      
          if(FiltroFiliali == 'Disattive')
          {
             ListaFiltrata = ListaFiltrata.filter(function(Riga)
             {
               return Riga.Dati.FILIALE_DISATTIVATA.Valore == true;
             });
          }
      
          for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighe;i < ListaFiltrata.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;i++)
          {
              Result.push(ListaFiltrata[i]);
          }
      
          return Result;
        }
      },

    },

    beforeMount()
    {
      if(this.NumeroRighePerPagina != undefined)
        this.Paginazione.NrRighe = this.NumeroRighePerPagina
    },
}
</script>
<style>
 .DivRighePari td{
    clear: both;
    padding-left: 10px;
    padding-right: 10px;
    background-color: #d0e9ff !important;
 }
 
 .DivRigheDispari  td {
    clear: both;
    padding-left: 10px;
    padding-right: 10px;
    background-color: #b3dbff !important;
 }
 
  .DivRighe:nth-child(odd) {
    background-color: #d0e9ff;
 }

 .DivRighe:nth-child(even) {
    background-color: #b3dbff;
 }
</style>