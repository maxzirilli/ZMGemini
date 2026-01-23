<template>
    <div>
        <div class="ZMSeparatoreScheda" 
            style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#68b6be">
            Storico
        </div>
        <div>

            <div class="col-md-12" style="background-color: #d0e9ff;" v-for="SingoloLog in RighePagina" :key="SingoloLog.Chiave">
                <!-- <hr style="margin-bottom:5px;margin-top:5px"> -->  
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 15px; font:bold; margin-left:10px"> {{ SingoloLog.Dati.Data }}</b>
                </div>
                <div class="col-md-3">
                    Effettuato da {{ SingoloLog.Dati.Utente }}
                </div>
                <div class="col-md-6" style="font-size: 13px">
                    <p style="white-space: pre">{{ SingoloLog.Dati.Note }}</p>

               </div>
            </div>
        </div>
    </div>
    <div style="clear: both;padding-bottom: 1%;">&nbsp;</div>
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
              <li><a @click="PortaIndietroGruppoPagine()" ><i :class="{ ZMDisabled : !BackGruppoPagineVisibile }" class="fa fa-chevron-left"></i></a></li>
              <li :class="{ active : IsActivePage(0)}"><a @click="OnClickChangePage(0)">{{Paginazione.StartGruppoPagine}}</a></li>
              <li v-if="TastoPaginaVisibile(1)" :class="{ active : IsActivePage(1)}"><a @click="OnClickChangePage(1)">{{Paginazione.StartGruppoPagine + 1}}</a></li>
              <li v-if="TastoPaginaVisibile(2)" :class="{ active : IsActivePage(2)}"><a @click="OnClickChangePage(2)">{{Paginazione.StartGruppoPagine + 2}}</a></li>
              <li v-if="TastoPaginaVisibile(3)" :class="{ active : IsActivePage(3)}"><a @click="OnClickChangePage(3)">{{Paginazione.StartGruppoPagine + 3}}</a></li>
              <li v-if="TastoPaginaVisibile(4)" :class="{ active : IsActivePage(4)}"><a @click="OnClickChangePage(4)">{{Paginazione.StartGruppoPagine + 4}}</a></li>
              <li><a @click="PortaAvantiGruppoPagine()"><i :class="{ ZMDisabled : !ForwardGruppoPagineVisibile }" class="fa fa-chevron-right"></i></a></li>
            </ul>
          </div>
        </div>
      </footer>
</template>

<script>
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'

export class TSingoloLogDocumentiEconomici
{
   constructor(Chiave,Note,Data,IdDocumento,Utente)
   {
     this.Dati = {}
     this.Dati.Chiave      = Chiave;
     this.Dati.Note        = Note.trim();
     Data                  = new Date(Data)
     this.Dati.Data        = TZDateFunct.FormatDateTime('dd/mm/yyyy hh:nn:ss', Data)
     this.Dati.Utente      = Utente
     this.Dati.IdDocumento = IdDocumento
   }
}

export class TSchedaLogDocumentiEconomici
{
   constructor()
   {
      this.LsLogDocumentiEconomici = []
   }

   ClearLogDocumentiEconomici()
   {
    this.LsLogDocumentiEconomici= []
   }

   AssignDati(LsLogDocumentiEconomici,NomeCampoDocumento)
   {
      this.NomeCampoDocumento       = NomeCampoDocumento;
      this.LsLogDocumentiEconomici = []
      for(let i = 0; i < LsLogDocumentiEconomici.length; i++)
      {
          let SingoloLogDocumentiEconomici = new TSingoloLogDocumentiEconomici(LsLogDocumentiEconomici[i].CHIAVE, 
                                                                               LsLogDocumentiEconomici[i].NOTE,
                                                                               LsLogDocumentiEconomici[i].DATA,
                                                                               LsLogDocumentiEconomici[i][NomeCampoDocumento], 
                                                                               LsLogDocumentiEconomici[i].UTENTE_RAGIONE_SOCIALE? LsLogDocumentiEconomici[i].UTENTE_RAGIONE_SOCIALE : LsLogDocumentiEconomici[i].UTENTE_USERNAME)
          this.LsLogDocumentiEconomici.push(SingoloLogDocumentiEconomici)
      }
   }
}

export default {

  components : 
   {    
   },

  props : ['SchedaLogDocumentiEconomici'],
  data()
  {
    return {
            Paginazione : {
                               NrRighe   : 50,
                               NrPagina  : 1,
                               StartGruppoPagine : 1
                           },
            }
  },

    computed :
  {
    RighePagina :
    {
      get()
      {
        let Result = [];
        for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighe;
            i < this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;
            i++)
            Result.push(this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici[i]);
        return Result;
      }
    },
    ForwardGruppoPagineVisibile : 
    {
      get()
      {
        return this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length >= this.Paginazione.NrPagina * this.Paginazione.NrRighe;
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
        if(this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length < MaxRighe) MaxRighe = this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length;
        let StartRecord = this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length == 0 ? 0 : this.Paginazione.NrRighe * (this.Paginazione.NrPagina - 1) + 1;
        return "Visualizzati " +  StartRecord + ' - ' +
                MaxRighe + ' di ' +  this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length + ' risultati'
      }
    },    
  },

  methods: 
  {
    AggiungiRighe()
    {
        this.DataTable.AddRowsOnTop(this.NrRigheDaAggiungere);
    },
    TastoPaginaVisibile(Offset)
    {
      return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighe <= this.SchedaLogDocumentiEconomici.LsLogDocumentiEconomici.length
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
  },

}
</script>