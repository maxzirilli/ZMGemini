<template>
  <div>
    <div style="display: flex; align-items: center; width: 50%; background-color: #d0e9ff;; margin-top: 1%;">
      <div style="display: flex; align-items: center; width: 30%;">
        <label style="font-weight: bold;">Dal</label>
        <input type="date" class="form-control" v-model="DataDal">
      </div>
      <div style="width: 5%">&nbsp;</div>
      <div style="display: flex; align-items: center; width: 30%;">
        <label style="font-weight: bold;">Al</label>
        <input type="date" class="form-control" v-model="DataAl">
      </div>
    </div>
    <div class="ZMSeparatoreScheda" style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px; margin-top: 1%; background-color:#68b6be">
          Storico
    </div>
    <div>
      <div class="col-md-12" style="background-color: #d0e9ff;" v-for="SingoloLog in RigheFiltratePerData" :key="SingoloLog.Chiave">
        <hr v-if="SingoloLog.Dati.Quantita > 0" style="margin-bottom:15px;margin-top:10px">
          <div class="col-md-3">
            <b style="color:#2596be; font-size: 15px; font:bold; margin-left:10px"> {{ SingoloLog.Dati.Data }}</b>
          </div>
          <div class="col-md-4" style="font-size: 13px">
            <p style="white-space: pre">{{ SingoloLog.Dati.Descrizione }}</p>
          </div>
          <div v-if="SingoloLog.Dati.Quantita > 0" class="col-md-4" style="font-size: 13px; padding-left: 20%;">
            <p style="white-space: pre">+{{ SingoloLog.Dati.Quantita }}</p>
          </div>
          <div v-else class="col-md-4" style="font-size: 13px; padding-left: 20%;">
            <p style="white-space: pre">{{ SingoloLog.Dati.Quantita }}</p>
          </div>
      </div>
    </div>
  </div>
  <div style="clear: both;padding-bottom: 2%;">&nbsp;</div>
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
</template>

<script>
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'

export class TSingoloLogProdotti
{
   constructor(Chiave,Descrizione,Data,Quantita)
   {
     this.Dati = {}
     this.Dati.Chiave      = Chiave;
     this.Dati.Descrizione = Descrizione != undefined ? Descrizione.trim() : '';
     Data                  = new Date(Data)
     this.Dati.Data        = TZDateFunct.FormatDateTime('dd/mm/yyyy hh:nn:ss', Data)
     this.Dati.Quantita    = (Quantita / 100) 
   }
}

export class TSchedaLogProdotti
{
   constructor()
   {
      this.LsLogProdotti = []
   }

   ClearLogProdotti()
   {
    this.LsLogProdotti= []
   }

   AssignDati(LsLogProdotti)
   {
      this.LsLogProdotti = []
      for(let i = 0; i < LsLogProdotti.length; i++)
      {
          let SingoloLogProdotti = new TSingoloLogProdotti(LsLogProdotti[i].CHIAVE, 
                                                          LsLogProdotti[i].DESCRIZIONE,
                                                          LsLogProdotti[i].DATA,
                                                          LsLogProdotti[i].QUANTITA)
          this.LsLogProdotti.push(SingoloLogProdotti)
      }
   }
}

export default {

  components : 
   {    
   },

  props : ['SchedaLogProdotti'],
  data()
  {
    return {
              Paginazione : {
                                NrRighe   : 10,
                                NrPagina  : 1,
                                StartGruppoPagine : 1
                            },
              DataDal     : TZDateFunct.FormatDateTime('yyyy-mm-dd',TZDateFunct.DecreaseDate(new Date(), 5)),
              DataAl      : TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
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
            i < this.SchedaLogProdotti.LsLogProdotti.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;
            i++)
            Result.push(this.SchedaLogProdotti.LsLogProdotti[i]);
        return Result;
      }
    },

    RigheFiltratePerData()
    {
      if (!this.DataDal && !this.DataAl) 
          return this.RighePagina

      return this.RighePagina.filter((SingoloLog) => 
              {
                var Data = SingoloLog.Dati.Data
                var DataArray = Data.split(" ")[0].split("/")
                var DataOk = `${DataArray[2]}-${DataArray[1]}-${DataArray[0]}`


                if (this.DataDal && DataOk < this.DataDal) 
                    return false
                if (this.DataAl && DataOk > this.DataAl) 
                    return false

                return true
              })
    },

    ForwardGruppoPagineVisibile : 
    {
      get()
      {
        return this.SchedaLogProdotti.LsLogProdotti.length >= this.Paginazione.NrPagina * this.Paginazione.NrRighe;
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
        if(this.SchedaLogProdotti.LsLogProdotti.length < MaxRighe) MaxRighe = this.SchedaLogProdotti.LsLogProdotti.length;
        let StartRecord = this.SchedaLogProdotti.LsLogProdotti.length == 0 ? 0 : this.Paginazione.NrRighe * (this.Paginazione.NrPagina - 1) + 1;
        return "Visualizzati " +  StartRecord + ' - ' +
                MaxRighe + ' di ' +  this.SchedaLogProdotti.LsLogProdotti.length + ' risultati'
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
      return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighe <= this.SchedaLogProdotti.LsLogProdotti.length
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
      this.Paginazione.NrPagina = Math.ceil(this.SchedaLogProdotti.LsLogProdotti.length / this.Paginazione.NrRighe)
      if(this.Paginazione.NrPagina >= 5)
        this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
      else this.Paginazione.StartGruppoPagine = 1;  
    },  
  },

}
</script>