<template>
<section class="vbox" style="height:400px">
  <header class="header bg-white b-b clearfix" v-if="!VisualizzaAllegato && (ReadOnly != 'true')">
    <div class="row m-t-sm">
      <div class="col-sm-8 m-b-xs" >
      <button type="button" 
          class="btn btn-success" 
          style="width:150px;"
          @click="OnClickNuovoAllegato()">
          Nuovo
  </button>
  </div>
    </div>
  </header>


  <section class="scrollable wrapper w-f" v-if="!VisualizzaAllegato">
    <section class="panel panel-default">
      <div class="table-responsive">
        <table class="table table-striped m-b-none">
          <thead>
            <tr>
              <th width="50%" class="th-sortable" data-toggle="class">Nome</th>
              <th width="10%">Azioni</th>
            </tr>
          </thead>
          <tbody  v-if="(LsAllegatiVisibili.length > 0)">
            <tr v-for="Allegato in LsAllegatiVisibili" :key="Allegato.Dati.Chiave">
              <td>{{Allegato.Dati.Descrizione}}</td>
              <td>
                <a @click="OnClickVisualizzaAllegato(Allegato)" data-toggle="class" style="margin-right:20px;font-size:17px; cursor:pointer" title="Apri allegato"><i class="fa fa-paperclip"></i></a>
                <a @click="OnClickDownloadAllegato(Allegato)" data-toggle="class" style="margin-right:20px;font-size:17px; cursor:pointer" title="Apri allegato"><i class="fa fa-download"></i></a>
                <a v-if="(ReadOnly != 'true')" @click="OnClickEliminaAllegato(Allegato)" data-toggle="class" style="margin-right:20px;font-size:17px;color:#fb6b5b; cursor:pointer" title="Elimina allegato"><i class="fa fa-trash-o"></i></a>
              </td>
            </tr>
          </tbody>
          <tbody v-else><tr><td colspan="2" style="font-size:20px">Non sono presenti allegati</td></tr></tbody>
        </table>
      </div>
    </section>
  </section>

  <div v-if="VisualizzaAllegato">
    <button @click="OnCloseFrame" style="float:right;" class="btn btn-default"><span class="fa fa-times icon-muted fa-fw" style="font-size:20px; color:#68b6be"></span></button>
    <button v-if="IframeAllegato.DaRegistrare" @click="OnClickAccettaAllegato" style="float:right;margin-right:5px" class="btn btn-default"><span class="fa fa-check icon-muted fa-fw" style="font-size:20px; color:#68b6be"></span></button>
    <div v-if="(ReadOnly != 'true')" class="col-md-2" style="margin-right: 20px">
      <br>
      <br>
      <button type="button" 
          class="btn btn-info" 
          style="width:150px;"
          @click="CaricaAllegato">
          Carica allegato
      </button>
      <input id="fileLoadDocument" 
          accept="all"
          class="form-control" 
          height=1px
          type="file" 
          style="display: none"
          @change="AllegatoLoaded" />
      <label v-if="IframeAllegato.Allegato == ''" style="" class="ZMFormLabelError">Campo obbligatorio</label>
    </div>
    <div class="col-md-6">
      <br>
      <a>Nome: </a>    
      <input class="form-control" style="width:80%" type="text" :readonly="ReadOnly == 'true'" v-model="IframeAllegato.Dati.Descrizione" @input="OnChangeValoriIframe(IframeAllegato)">
      
      <label v-if="IframeAllegato.Dati.Descrizione == ''" class="ZMFormLabelError">Campo obbligatorio</label>

    </div>


      <div class="ZMNuovaRigaScheda">
        <br>
      <iframe style="background:white;" 
                title="Allegato" 
                id="prova"
                :src="IframeAllegato.Allegato" 
                height="500px" 
                width=100%
                @change="OnChangeValoriIframe(IframeAllegato)">
                <!-- @load="OnLoadIframeAllegato" -->
          </iframe>
      </div>
  </div>
</section>
</template>

<script>

import { TAdvQueryOggettoBlob, TAdvQueryErrors, OPERAZIONE_BLOB } from '../../../../../../../Librerie/VUE/ZAdvQuery'
import { TSchedaGenerica } from '../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { TZStringConvFunct } from '../../../../../../../Librerie/VUE/ZStringConvFunct.js'
import { saveAs } from 'file-saver'

export class TSingoloAllegato
{
   constructor(Chiave, Descrizione, Allegato, DaRegistrare, IdDocumento, InfoPosizione)
   {
     this.Dati = {}
     this.Dati.Chiave       = Chiave;
     this.Dati.Descrizione  = Descrizione;
     this.Allegato          = Allegato;
     this.InfoPosizione     = InfoPosizione
     this.Dati.DaEliminare  = false;
     this.DaRegistrare      = DaRegistrare;
     this.Dati.IdDocumento  = IdDocumento
     this.Dati.Contatore    = 0
     this.Snapshot          = JSON.stringify(this.Dati)  

   }

   PrepareParameterObject(Operazioni, NomeCampoDocumento, LsFileDaEliminare)
   {
      if(this.DaRegistrare || this.Dati.DaEliminare)
      {
        if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare)
        {
          let AllegatoBase64 = this.Allegato.split("base64,");
          Operazioni.push({
                              Query     : 'InserisciAllegato',
                              Parametri : { 
                                            CHIAVE               : undefined,
                                            ALLEGATO             : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Inserimento,
                                                                                   TSchedaGenerica.PrepareForRecordString(AllegatoBase64[1], true),
                                                                                   AllegatoBase64[0],
                                                                                   '',
                                                                                   NomeCampoDocumento),
                                            DESCRIZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.Descrizione),
                                            [NomeCampoDocumento] : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdDocumento)
                                          },
                              ResetKeys   : [2]
          })
        }
        else
        {
            let AllegatoBase64 = this.Allegato.split("base64,");
            let Posizione = this.InfoPosizione.split(";");

            if(this.Modificato() && !this.Dati.DaEliminare) 
            {
                Operazioni.push({
                                    Query     : 'ModificaAllegato',
                                    Parametri : { 
                                                  CHIAVE      : this.Dati.Chiave,
                                                  DESCRIZIONE : TSchedaGenerica.PrepareForRecordString(this.Dati.Descrizione),
                                                  ALLEGATO    : new TAdvQueryOggettoBlob(OPERAZIONE_BLOB.Update,
                                                                                TSchedaGenerica.PrepareForRecordString(AllegatoBase64[1], true),
                                                                                AllegatoBase64[0],
                                                                                Posizione[1],
                                                                                NomeCampoDocumento),
                                                }
                })
            }
            else 
            {
              if(this.Dati.DaEliminare)
              {
                 Operazioni.push({
                                    Query     : 'EliminaAllegato',
                                    Parametri : { CHIAVE   : this.Dati.Chiave }
                 })
                 LsFileDaEliminare.push(Posizione[1])
              }
            }
        }
      }
    }

   AllDataOk()
   {
     var Result = this.Dati.Descrizione != ''
     if(Result)
     {
        if(!this.Dati.DaEliminare && this.DaRegistrare)
          Result = this.Allegato != '';
     }
     return Result
   }

   Modificato()
   {
    return this.Snapshot != JSON.stringify(this.Dati)
   }
}

export class TSchedaAllegati
{
   constructor()
   {
      this.LsAllegati        = []
      this.LsFileDaEliminare = []
   }

   PassAdvQuery(AdvQuery)
   {
      this.AdvQuery = AdvQuery
   }

   Modificato()
   {
      for(var i = 0; i < this.LsAllegati.length; i++)
         if(this.LsAllegati[i].Modificato())
            return true
      return false;
   }


   AllDataOk()
   {
    for(var i = 0; i < this.LsAllegati.length; i++)
        if(!this.LsAllegati[i].AllDataOk())
          return false
    return true 
   }

   PrepareQueryParameters(Operazioni, NomeCampoDocumento)
   {
     for(var i = 0; i < this.LsAllegati.length; i++)
        if(this.LsAllegati[i].Dati.DaEliminare || this.LsAllegati[i].DaRegistrare || this.LsAllegati[i].Modificato())
            this.LsAllegati[i].PrepareParameterObject(Operazioni, NomeCampoDocumento, this.LsFileDaEliminare)
     return Operazioni
   }

   DeleteFileDaEliminare()
   {
      if(this.LsFileDaEliminare != [])
      {
        var Self      = this
        this.AdvQuery.DeleteAttachment(this.LsFileDaEliminare,
                                        function()
                                        {
                                          Self.LsFileDaEliminare = []
                                        },
                                        function () { console.error(Self.LsFileDaEliminare) })
      }
   }

   GetAttachment(Path, OnSuccess)
   {
      var Self = this
      this.AdvQuery.GetAttachment(Path,
                                  function(Result)
                                  {
                                    if (Result != undefined)
                                    {
                                      if(Result != null)
                                      {
                                        if(OnSuccess != undefined)
                                          OnSuccess(Result)
                                      }
                                      else Self.HandleError('Allegato non presente'); 
                                    }
                                  },
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    Self.HandleError(HTTPError,SubHTTPError,Response);
                                  })
   
   }

   HandleError(Riga1,Riga2,Response)
   {
     if(Response == TAdvQueryErrors.HTTP_ERROR_NOT_LOGGED)
       this.Router.push("/login");
     else alert(Riga1 + "\n" + Riga2);
   }

   SetIdDocumento(IdDocumento)
   {
      this.IdDocumento = IdDocumento
   }

   AssignDati(LsAllegati)
   {
      this.LsAllegati = []
      this.VisualizzaAllegato = false;
      for(var i = 0; i < LsAllegati.length; i++)
         {
          var SingoloAllegato = new TSingoloAllegato(LsAllegati[i].CHIAVE,
                                                     LsAllegati[i].DESCRIZIONE,
                                                     '',
                                                     false,
                                                     this.IdDocumento,
                                                     LsAllegati[i].ALLEGATO)
          this.LsAllegati.push(SingoloAllegato)
         }
      this.Snapshot = JSON.stringify(this.LsAllegati);
   }

   ClearAllegati()
   {
    this.LsAllegati = []
   }
}

export default {

 emits: ['onChange'],
 data()
 {
    return {
              VisualizzaAllegato   : false,
              IframeAllegato       : {},
              IndiceChiaveAllegati : -1,
              NuovoAllegato        : false
    }
 },
 props : ['SchedaAllegati', 'NomeCampoModello','ReadOnly'],
 computed :
 {
      LsAllegatiVisibili :
      {
         get()
         {
            var Result = []
            this.SchedaAllegati.LsAllegati.forEach(function(AnAllegato)
            {
               if(!AnAllegato.Dati.DaEliminare)
                 Result.push(AnAllegato)
            })
            return Result;
         }
      },

      CurrentSchedaAllegati : 
      {
        get()
        {
          return this.SchedaAllegati;
        }
      },

      IsIframeChanged:
      {
        get()
        {
          if(this.IframeAllegato.DaRegistrare)
            return true
          else return false
        }
      }
  
 },

 watch:
  {
    CurrentSchedaAllegati:
    {
      handler()
      {
        this.$emit('onChange') 
      },
      deep: true
    }
  },

 methods:
 {
    OnChangeValoriIframe(IframeAllegato)
    {
      IframeAllegato.DaRegistrare = true;
      this.$emit('onChange')
    },

    OnClickEliminaAllegato(Allegato)
    {
      Allegato.Dati.DaEliminare = true
      this.$emit('onChange')
    }, 

    OnClickNuovoAllegato()
    {
      this.VisualizzaAllegato = true
      this.IframeAllegato = new TSingoloAllegato(-1,'','',true,this.CurrentSchedaAllegati.IdDocumento, '')
      this.NuovoAllegato = true;
    },

    OnCloseFrame()
    {
      this.VisualizzaAllegato = false;
      this.IframeAllegato     = {}
    },

    OnClickAccettaAllegato()
    {
      if(this.NuovoAllegato)
      {
        this.CurrentSchedaAllegati.LsAllegati.push(this.IframeAllegato) 
        this.$emit('onChange')
      }
      this.IframeAllegato     = {}
      this.NuovoAllegato      = false
      this.VisualizzaAllegato = false;
    },

    OnClickVisualizzaAllegato(Allegato)
    {
      this.IframeAllegato = Allegato; 
      if(Allegato.Dati.Chiave > 0 && Allegato.Allegato == '')
      {
        var Self = this;
        let Parametri         = {}
        let Posizione         = Allegato.InfoPosizione.split(";");
        Parametri.Posizione   = Posizione[1]

        if(Allegato.Dati.Chiave != undefined)
          this.SchedaAllegati.GetAttachment(Posizione[1],
                                            function(Result)
                                            {
                                                Self.IframeAllegato.Allegato = Posizione[0] + ';base64,' + Result
                                                Self.VisualizzaAllegato      = true
                                                Self.NuovoAllegato           = false
                                            })
      }
      else this.VisualizzaAllegato = true
    },

    // OnLoadIframeAllegato()
    // {
    //   alert(document.getElementById('prova'));
    //   document.getElementById('prova').contentWindow.onerror = function (event) 
    //   {
    //     console.error('Errore durante il caricamento del file:', event.message);
    //   }
    // },

    OnClickDownloadAllegato(Allegato)
    {
      if(Allegato.Dati.Chiave > 0)
      {
        let Parametri         = {}
        let Posizione         = Allegato.InfoPosizione.split(";");
        Parametri.Posizione   = Posizione[1]

        if(Allegato.Dati.Chiave != undefined)
          this.SchedaAllegati.GetAttachment(Posizione[1],
                                            function(Result)
                                            {
                                              saveAs(TZStringConvFunct.Base64AsBlob(Result), Allegato.Dati.Descrizione + Allegato.InfoPosizione.slice(-4))
                                            })
      }
    },

    AllegatoLoaded()
    {
      var Self = this;
      var Reader  = new FileReader();
      var File = document.getElementById('fileLoadDocument');
      Reader.onload = function (e) 
      {
        Self.IframeAllegato.Allegato = e.target.result
        Self.IframeAllegato.DaRegistrare = true;
        Self.IframeAllegato.Dati.Contatore++;
        Self.$emit('onChange')
      }
      Reader.readAsDataURL(File.files[0]);
    },

     CaricaAllegato()
     {
       document.getElementById('fileLoadDocument').click(); 
     }
 },
  
}
</script>