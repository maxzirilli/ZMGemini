<template>
  <VUEModal v-if="VisualizzaPopupListaAllegati" :Titolo="'Lista allegati'" :Altezza="'400px'" :Larghezza="'750px'"
            @onClickChiudiModal="VisualizzaPopupListaAllegati = false">
    <template v-slot:Body>
    <div class="form-row">

        <div class="col-md-12" v-if="ListaAllegati.length != 0">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>    
                      <th style="width:80%">Allegato</th>
                      <th style="width:20%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Allegato, index in ListaAllegati" :key="index" style="font-size:15px">
                      <td> {{ Allegato.DESCRIZIONE != null? Allegato.DESCRIZIONE : 'Allegato n. ' + (index + 1)}}</td>
                      <td>  
                        <button type="button" class="btn btn-info" style="font-weight:bold;" @click="VisualizzaAllegato(Allegato)" data-dismiss="modal">Visualizza</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </div>

        <div class="col-md-12" v-else>
          NON SONO PRESENTI ALLEGATI
        </div>

    </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="VisualizzaPopupListaAllegati = false" data-dismiss="modal">Chiudi</button>
    </template>
  </VUEModal>

  <VUEModal v-if="VisualizzaPopupErrore" :Titolo="'Errore durante l\'invio email'" :Altezza="'300px'" :Larghezza="'750px'"
            @onClickChiudiModal="OnClickChiudiPopupErrore" @onClickConfermaModal="OnClickChiudiPopupErrore">
    <template v-slot:Body>
    <div class="form-row">
      <br>
      <div class="col-md-12">                
        <div style="font-size:16px">{{ ErroreVisualizzato }}</div>
      </div>
    </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickChiudiPopupErrore" data-dismiss="modal">Chiudi</button>
    </template>
  </VUEModal>

  <VUEModal v-if="VisualizzaPopupCorpoEmail" :Titolo="'Corpo Email'" :Altezza="'800px'" :Larghezza="'1200px'"
            @onClickChiudiModal="OnClickChiudiPopupCorpoEmail">
          <template v-slot:Body>
            <div v-html="CorpoEmail"></div>
          </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickChiudiPopupCorpoEmail" data-dismiss="modal">Chiudi</button>
    </template>
  </VUEModal>

  <VUEModal v-if="VisualizzaPopupInoltro" :Titolo="'Inoltro email'" :Altezza="'550px'" :Larghezza="'60%'"
            @onClickChiudiModal="OnClickChiudiPopupInoltroEmail">
          <template v-slot:Body>

           <div style="float:left;width:100%; margin-bottom: 6px;font-size: 16px;">
             <text style="font-weight: bold">Inserire l'indirizzo da cui partirà l'email:</text>
           </div>
            <div class="col-md-6" style="float:left">  
              <div style="float:left;">
                <input  type ="radio" v-model="ListaMittenti.Tipo" value="Avvisi" style="transform: scale(1.5)">
               </div>
                <div style="float:left; margin-left: 7px; width:26%;margin-top:-2px">
                   <text style="font-weight: bold; font-size:20px">Avvisi</text>
               </div>
               <div style="float:left;">
                <input  type ="radio" v-model="ListaMittenti.Tipo" value="Fatture" style="transform: scale(1.5)">
               </div>
               <div style="float:left; margin-left: 7px; width:26%;margin-top:-2px">
                   <text style="font-weight: bold; font-size:20px">Fatture</text>
               </div>
               <div style="float:left;">
                <input  type ="radio" v-model="ListaMittenti.Tipo" value="Solleciti" style="transform: scale(1.5)">
               </div>
                <div style="float:left;margin-left: 7px;width:26%;margin-top:-2px">
                   <text style="font-weight: bold; font-size:20px">Solleciti</text>
               </div>

           </div>
           <div class="col-md-6" style="float:left" v-if="OggettoEmail.IdCliente != null || OggettoEmail.IdAmministratore != null"> 
            <button style="float:right;margin-left:10px;margin-top: -8px" @click="OnClickGuardaListaEventi(false)" class="btn btn-info">Guarda lista eventi</button>
            <div style="float:right;">
              <div style="float:left;">
                <input  type ="checkbox" style="transform: scale(1.5)" v-model="OggettoEmail.AllegaListaEventiSolleciti">
              </div>
              <div style="float:left; margin-left: 10px;">
                <text style="font-weight: bold; font-size:16px;">Allega Lista Eventi</text>
              </div>
            </div> 
           </div>

           
           <div style="float:left;width:100%; margin-top: 10px">
             <text style="font-weight: bold">Destinatario</text>
             <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
             <input type="text" style="text-transform: none!important;" placeholder="Destinatario" class="form-control" v-model="OggettoEmail.Destinatario"/>
           </div>
           <div style="float:left;width:100%">
            <text style="font-weight: bold">Cc</text>
            <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
            <input type="text" placeholder="Cc" style="text-transform: none!important;" class="form-control" v-model="OggettoEmail.Cc"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Ccn</text>
            <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
            <input type="text" style="text-transform: none!important;" placeholder="Ccn" class="form-control" v-model="OggettoEmail.Ccn"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Oggetto</text>
            <input type="text" placeholder="Oggetto" style="text-transform: none!important;" class="form-control" v-model="OggettoEmail.Oggetto"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Corpo email</text>
            <textarea type="text" style="text-transform:none!important;resize:none; height:200px" class="form-control" v-model="OggettoEmail.CorpoEmail"/>
          </div>
         </template>

         <template v-slot:Footer>
           <button class="btn btn-danger" @click="OnClickChiudiPopupInoltroEmail" style="float:right;margin-right:20px;width:20%">Annulla</button>
           <button v-if="OggettoEmail.CorpoEmail.trim() != '' && OggettoEmail.Oggetto.trim() != '' && OggettoEmail.Destinatario.trim() != ''"  class="btn btn-success" @click="OnClickInoltraEmail(Email)" style="float:right;margin-right:20px;width:20%">Conferma</button>
       </template>
  </VUEModal>

  <div class="row" style="padding:10px;border-top:1px;border: 1 px solid" @keydown="KeyDownF2($event)">
    <div class="col-md-12">
      <div style="float:left;font-size:14px;margin-top:5px;width:6%;text-align: right; padding-right: 15px;">
        <label style="margin-bottom: 0px;">Tipologia</label>
      </div>
      <div style="float:left;width:13%;">
        <select style="float:left;width:100%;" class="form-control" v-model="Filtro.TipologiaDocumento">
          <option value="Tutte">Tutte</option>                       
          <option value="Fatture">Fatture</option>   
          <option value="Preventivi">Preventivi</option>
          <option value="Conferme">Conferme</option>
          <option value="Note">Note di credito</option>
        </select>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      
      <div style="float: left;font-size:14px;padding-top: 5px;width:6%; padding-right: 15px;">
        <label>Email</label>
      </div>
      <div style="float:left;width:13%;">
        <input type="text" class="form-control" v-model="Filtro.Email"/>
      </div> 
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float: left;font-size:14px;padding-top: 5px;width:6%; padding-right: 15px;">
        <label>Corpo email</label>
      </div>
      <div style="float:left;width:13%;">
        <input type="text" class="form-control" v-model="Filtro.CorpoEmail"/>
      </div> 
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float: left;font-size:14px;padding-top: 5px;width:6%; padding-right: 15px;">
        <label>Invio eseguito</label>
      </div>
      <div style="float:left;width:8%;">
        <select style="float:left;width:100%;" class="form-control" v-model="Filtro.SuccessoInvio">
          <option :value="-1">Tutti</option>                       
          <option value="Successo">Successo</option>   
          <option value="Errore">Errore</option>
        </select>
      </div> 
      
      <div style="float:right;width:1%">&nbsp;</div>

      <button style="float:right" class="btn btn-s-md btn-info" @click="CaricaLogEmail">Cerca [F2]</button>

    </div>

    <div class="col-md-12" style="margin-top:10px">
        <div style="float:left;font-size:14px;margin-top:5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Dalla data</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="Filtro.DataDa" />
        </div>

        <div style="float:left;width:4%">&nbsp;</div>

        <div style="float: left;font-size:14px;padding-top: 5px;width:6%; padding-right: 15px;">
          <label>Alla data</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="Filtro.DataA" />
        </div>
    </div>

    <div class="col-md-12">
      <div class="row wrapper">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:100%;width:100%;">
            <table class="table table-striped b-t b-light">
              <thead>
                <tr>    
                  <th style="width:6%">Data</th>
                  <th style="width:6%">Tipo documento</th>
                  <th style="width:14%">Mittente</th>
                  <th style="width:14%">Destinatario</th>
                  <th style="width:13%">Cc</th>
                  <th style="width:13%">Ccn</th>
                  <th style="width:10%">Oggetto</th>
                  <th style="width:6%">Corpo email</th>
                  <th style="width:6%">Allegato</th>
                  <th style="width:6%">Inoltra Email</th>
                  <th style="width:6%">Inviata</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="ListaEmail.length == 0">
                  <td colspan="11">Non sono presenti email inviate</td>
                </tr>
                <tr v-for="Email in RighePagina" :key="Email.CHIAVE" style="font-size:15px">
                  <td> {{ Email.DATA_INSERIMENTO }} </td>
                  <td v-if="Email.TIPO_DOCUMENTO == TipoDocumenti.Fattura"> Fattura </td>
                  <td v-else-if="Email.TIPO_DOCUMENTO == TipoDocumenti.NotaDiCredito"> Nota di credito </td>
                  <td v-else-if="Email.TIPO_DOCUMENTO == TipoDocumenti.PreventivoConferma"> Conferma </td>
                  <td v-else-if="Email.TIPO_DOCUMENTO == TipoDocumenti.PreventivoMulti"> Preventivo </td>
                  <td v-else-if="Email.TIPO_DOCUMENTO == TipoDocumenti.Interno"> Interno </td>
                  <td v-else></td>
                  <td> {{ Email.MITTENTE }} </td>
                  <td> {{ Email.DESTINATARIO }} </td>
                  <td> {{ Email.CC }} </td>
                  <td> {{ Email.CCN }} </td>
                  <td> {{ Email.OGGETTO }} </td>
                  <td>
                    <button v-if="Email.CORPO_EMAIL != null && Email.CORPO_EMAIL != ''" style="float:left" class="btn btn-s-md btn-info" @click="OnClickCaricaCorpoEmail(Email)">Visualizza</button>
                 </td>
                  <td> 
                    <button  style="float:left" class="btn btn-s-md btn-info" @click="OnClickCaricaAllegato(Email)">Visualizza</button> 
                  </td>
                  <td> 
                    <button  style="float:left" class="btn btn-s-md btn-info" @click="OnClickApriPopupInoltroEmail(Email)">Inoltra</button> 
                  </td>
                  <td>
                    <label v-if="Email.ERRORE_INVIO_EMAIL == null || Email.ERRORE_INVIO_EMAIL == ''" style="background-color:#8ec165; color:white;text-align:center;height:30px;padding-top:4px;padding-left:15px;padding-right:15px">Successo</label> 
                    <button v-else style="float:left" class="btn btn-s-md btn-danger" @click="OnClickVisualizzaErrore(Email.ERRORE_INVIO_EMAIL)">Fallito</button> 
                    <!-- <label v-else-if="!Email.SUCCESSO" style="background-color:#fb6b5b; color:white;text-align:center;height:30px;padding-top:4px;padding-left:15px;padding-right:15px">Fallito</label>  -->
                  </td>
                </tr>
              </tbody>
            </table>
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
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import { SystemInformation, TIPOLOGIA_DOCUMENTI } from '@/SystemInformation';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js';
import VUEModal from '@/components/Slots/VUEModal.vue';

export default
{
  data()
  {
    return {
              ListaEmail               : [],
              TipoDocumenti            : TIPOLOGIA_DOCUMENTI,
              VisualizzaPopupErrore    : false,
              VisualizzaPopupCorpoEmail: false,
              VisualizzaPopupInoltro   : false,
              ErroreVisualizzato       : '',
              Filtro                   : {
                                          Email              : '',
                                          CorpoEmail         : '',
                                          TipologiaDocumento : 'Tutte',
                                          SuccessoInvio      : -1,
                                          DataDa: TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date(new Date().setMonth(new Date().getMonth() - 1))),
                                          DataA:  TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date())
                                         },
              Paginazione              : {
                                          NrRighe           : 10,
                                          NrPagina          : 1,
                                          StartGruppoPagine : 1
                                         },
              CorpoEmail               : '',
              OggettoEmail             : {
                                            CorpoEmail       : '',
                                            Cc               : '',
                                            Ccn              : '',
                                            Destinatario     : '',
                                            Oggetto          : '',
                                            Allegato         : [],
                                            IdCliente        : null,
                                            IdAmministratore : null,
                                            AllegaListaEventiSolleciti : false,
                                            DescrizioneListaEventi     : null,
                                            TitoloListaEventi          : null,
                                         },
              ListaAllegati             : [],
              ListaMittenti             : {
                                            Tipo: 'Avvisi'
                                          },
              VisualizzaPopupListaAllegati : false,
    }
  },
  components : {
                 VUEModal,
              },

  computed : 
  {
    RighePagina :
    {
      get()
      {
        let Result = [];
        for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighe;
            i < this.ListaEmail.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;
            i++)
            Result.push(this.ListaEmail[i]);
        return Result;
      }
    },
    ForwardGruppoPagineVisibile : 
    {
      get()
      {
        return this.ListaEmail.length > this.Paginazione.NrPagina * this.Paginazione.NrRighe;
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
        if(this.ListaEmail.length < MaxRighe) MaxRighe = this.ListaEmail.length;
        let StartRecord = this.ListaEmail.length == 0 ? 0 : this.Paginazione.NrRighe * (this.Paginazione.NrPagina - 1) + 1;
        return "Visualizzati " +  StartRecord + ' - ' +
                MaxRighe + ' di ' +  this.ListaEmail.length + ' risultati'
      }
    }, 
  },

  methods : 
  {
    TastoPaginaVisibile(Offset)
    {
      return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighe < this.ListaEmail.length
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
     this.Paginazione.NrPagina = Math.ceil(this.ListaEmail.length / this.Paginazione.NrRighe)
     if (this.Paginazione.NrPagina >= 5)
          this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
     else this.Paginazione.StartGruppoPagine =1;  
    },  
    
    OnClickInoltraEmail()
    {
      var Self = this
      this.VisualizzaPopupInoltro = false
      if(this.ListaMittenti.Tipo != '')
      {
        let Parametri          = this.OggettoEmail
        Parametri.InoltroEmail = -1

        let ExtraScriptDaChiamare = ''

        switch(this.ListaMittenti.Tipo )
        {
          case 'Fatture'   : ExtraScriptDaChiamare = 'InvioMailAccountFatture'
                             break
          case 'Avvisi'    : ExtraScriptDaChiamare = 'InvioMailAccountAvvisi'
                             break
          case 'Solleciti' : ExtraScriptDaChiamare = 'InvioMailAccountSolleciti'
                             break
        }

        if(this.OggettoEmail.AllegaListaEventiSolleciti)
        {
          this.GetAllegatoListaEventi(function()
          {
            SystemInformation.AdvQuery.ExecuteExternalScript(ExtraScriptDaChiamare, Parametri,
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
                                                              } 
                                                              Self.ClearOggettoEmail()
                                                              Self.CaricaLogEmail()
                                                            }, 
                                                            function(HTTPError,SubHTTPError,Response)
                                                            {
                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            })
          })
        }
        else
        {
          SystemInformation.AdvQuery.ExecuteExternalScript(ExtraScriptDaChiamare, Parametri,
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
                                                              } 
                                                              Self.ClearOggettoEmail()
                                                              Self.CaricaLogEmail()
                                                            }, 
                                                            function(HTTPError,SubHTTPError,Response)
                                                            {
                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            })
        }
      }
    },

    GetAllegatoListaEventi(OnSuccess)
    {
      let Self      = this
      let Query     = ''
      let Parametri = {}

      if(this.OggettoEmail.IdCliente != null)
      {
        Query = 'SelectListaEventiSollecitiCliente'
        Parametri.ID_CLIENTE = this.OggettoEmail.IdCliente
      }
      else
      {
        if(this.OggettoEmail.IdAmministratore != null)
        {
          Query = 'SelectListaEventiSollecitiAmministratore'
          Parametri.ID_AMMINISTRATORE = this.OggettoEmail.IdAmministratore
        }
      }

      SystemInformation.AdvQuery.GetSQL('LogEmail', Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Lista");
                                          if(ArrayInfo != undefined)
                                          {
                                            let Descrizione = []
                                            let Titolo      = 'Eventi solleciti'
                                            
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                              Descrizione.push('• ' + TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(ArrayInfo[i].DATA)) + ' - ' + ArrayInfo[i].DESCRIZIONE)
                                            
                                            Self.OggettoEmail.DescrizioneListaEventi = Descrizione
                                            Self.OggettoEmail.TitoloListaEventi      = Titolo

                                            if(OnSuccess != undefined)
                                              OnSuccess()
                                          }
                                          else SystemInformation.HandleError('Impossibile ottenere il log delle email');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        Query)
    },
   
    CaricaLogEmail()
    {
      var Self = this;
      let Parametri = {}
      if(this.Filtro.Email.trim() != '')
        Parametri.Email = '%' + this.Filtro.Email.trim() + '%'
      if(this.Filtro.CorpoEmail.trim() != '')
        Parametri.CorpoEmail = '%' + this.Filtro.CorpoEmail.trim() + '%'
      if(this.Filtro.SuccessoInvio != -1)
      {
        switch(this.Filtro.SuccessoInvio)
        {
          case 'Successo' : Parametri.Successo = -1
                            break
          case 'Errore'   : Parametri.Errore = -1
                            break
        }
      }
      if(this.Filtro.TipologiaDocumento != 'Tutte')
      {
        switch(this.Filtro.TipologiaDocumento)
        {
          case 'Fatture'    : Parametri.Fatture = -1
                              break
          case 'Preventivi' : Parametri.Preventivi = -1
                              break
          case 'Conferme'   : Parametri.Conferme = -1
                              break
          case 'Note'       : Parametri.Note = -1
                              break
        }
      }

      if(this.Filtro.DataDa)
        Parametri.DataDal = TZDateFunct.FormatDateTime('yyyy-mm-dd', TZDateFunct.DateFromHTMLInput(this.Filtro.DataDa));

      if(this.Filtro.DataA)
        Parametri.DataAl = TZDateFunct.FormatDateTime('yyyy-mm-dd', TZDateFunct.DateFromHTMLInput(this.Filtro.DataA));

      SystemInformation.AdvQuery.GetSQL('LogEmail', Parametri,
                                          function(Results)
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaLogEmail");
                                            if(ArrayInfo != undefined)
                                            {
                                              ArrayInfo.forEach(function(Email)
                                              {
                                                  if(Email.DATA_INSERIMENTO)
                                                    Email.DATA_INSERIMENTO = TZDateFunct.FormatDateTime('dd/mm/yyyy hh:nn',TZDateFunct.DateFromHTMLInput(Email.DATA_INSERIMENTO))
                                                  if(Email.DESTINATARIO != '')
                                                    Email.DESTINATARIO     = Email.DESTINATARIO.replaceAll(';', ';\n')
                                                    Email.MITTENTE     = TSchedaGenerica.DisponiFromString(Email.MITTENTE,true)
                                                  if(Email.CC != '')
                                                    Email.CC               = Email.CC.replaceAll(';', ';\n')
                                                  if(Email.CCN != '')
                                                    Email.CCN              = Email.CCN.replaceAll(';', ';\n')
                                                  if(Email.ERRORE_INVIO_EMAIL)
                                                    Email.ERRORE_INVIO_EMAIL = TSchedaGenerica.DisponiFromString(Email.ERRORE_INVIO_EMAIL,true)
                                                
                                              });
                                              Self.ListaEmail = ArrayInfo
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il log delle email');
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          })
    },

    OnClickCaricaAllegato(Email)
    {
      var Self = this
      
      SystemInformation.AdvQuery.GetSQL('LogEmail', { CHIAVE : Email.CHIAVE},
                                                    function(Result)
                                                    {  
                                                      let ArrayInfo = SystemInformation.AdvQuery.FindResults(Result,"GetAllegati");
                                                      if(ArrayInfo != undefined)
                                                      {
                                                        if(ArrayInfo.length == 1)
                                                        {
                                                          if(ArrayInfo[0].ALLEGATO != undefined)
                                                          {
                                                            let Parametri         = {}
                                                            let Posizione         = ArrayInfo[0].ALLEGATO.split(";");
                                                            Parametri.Posizione   = Posizione[1]
                                                            
                                                            SystemInformation.AdvQuery.GetAttachment(Posizione[1],
                                                                                                    function(Result)
                                                                                                    {
                                                                                                      var routeData = Self.$router.resolve({ name   : "IFrameStampe"});
                                                                                                      var AWindow = window.open(routeData.href, "_blank");
                                                                                                      AWindow.BodyPDF = (Posizione[0] + ';base64,' + Result);
                                                                                                    })
                                                          }
                                                        }
                                                        else
                                                        {
                                                          Self.ListaAllegati                = ArrayInfo
                                                          Self.VisualizzaPopupListaAllegati = true
                                                        }
                                                      }
                                                      else SystemInformation.HandleError('Allegati non presenti','','');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    },
                                                    'GetAllegato') 
    },

    VisualizzaAllegato(Allegato)
    {
      var Self = this

      let Parametri         = {}
      let Posizione         = Allegato.ALLEGATO.split(";");
      Parametri.Posizione   = Posizione[1]

      SystemInformation.AdvQuery.GetAttachment(Posizione[1],
                                              function(Result)
                                              {
                                                var routeData = Self.$router.resolve({ name   : "IFrameStampe"});

                                                var AWindow = window.open(routeData.href, "_blank");

                                                AWindow.BodyPDF = (Posizione[0] + ';base64,' + Result);
                                              })
    },

    OnClickCaricaCorpoEmail(Email)
    {
      this.CorpoEmail                = Email.CORPO_EMAIL
      this.VisualizzaPopupCorpoEmail = true
    },

    OnClickApriPopupInoltroEmail(Email)
    {
      var Self = this
     
      SystemInformation.AdvQuery.GetSQL('LogEmail', { CHIAVE : Email.CHIAVE},
                                                    function(Result)
                                                    {  
                                                      let ArrayInfo = SystemInformation.AdvQuery.FindResults(Result,"GetAllegati");
                                                      if(ArrayInfo != undefined)
                                                      {
                                                        for(let i= 0; i < ArrayInfo.length ;i++)
                                                        {
                                                          let Allegato = {
                                                                            POSIZIONE   : ArrayInfo[i].ALLEGATO,
                                                                            DESCRIZIONE : ArrayInfo[i].DESCRIZIONE
                                                                         }

                                                          Self.OggettoEmail.Allegato.push(Allegato)
                                                        }

                                                        Self.OggettoEmail.CorpoEmail       = Email.CORPO_EMAIL
                                                        var CorpoEmailPulito               = document.createElement("div")
                                                        CorpoEmailPulito.innerHTML         = Self.OggettoEmail.CorpoEmail
                                                        Self.OggettoEmail.CorpoEmail       = CorpoEmailPulito.textContent || CorpoEmailPulito.innerText
                                                        Self.OggettoEmail.CorpoEmail       = Self.OggettoEmail.CorpoEmail.trim(); 
                                                        Self.OggettoEmail.IdCliente        = Email.ID_CLIENTE
                                                        Self.OggettoEmail.IdAmministratore = Email.ID_AMMINISTRATORE
                                                        Self.OggettoEmail.AllegaListaEventiSolleciti = false
                                                        Self.VisualizzaPopupInoltro        = true
                                                      }
                                                      else SystemInformation.HandleError('Allegati non presenti','','');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    },
                                                    'GetAllegato') 
    },

    ClearOggettoEmail()
    {
      this.OggettoEmail.CorpoEmail          = ''
      this.OggettoEmail.Cc                  = ''
      this.OggettoEmail.Ccn                 = ''
      this.OggettoEmail.Destinatario        = ''
      this.OggettoEmail.Oggetto             = ''
      this.OggettoEmail.IdCliente           = null
      this.OggettoEmail.IdAmministratore    = null
      this.OggettoEmail.AllegaListaEventiSolleciti = false
      this.OggettoEmail.DescrizioneListaEventi     = null
      this.OggettoEmail.TitoloListaEventi   = null
    },

    OnClickChiudiPopupInoltroEmail()
    {
      this.ClearOggettoEmail()
      this.VisualizzaPopupInoltro = false
    },
    
    OnClickChiudiPopupCorpoEmail()
    {
      this.VisualizzaPopupCorpoEmail = false
    },

    OnClickVisualizzaErrore(Errore)
    {
      this.VisualizzaPopupErrore = true
      this.ErroreVisualizzato    = Errore
    },

    OnClickChiudiPopupErrore()
    {
      this.VisualizzaPopupErrore = false
      this.ErroreVisualizzato    = ''
    },

    KeyDownF2(event)
    {
      if(event.which == 113) 
        this.CaricaLogEmail();
    },

    OnClickGuardaListaEventi()
    {
      let Self      = this
      let Query     = ''
      let Parametri = {}

      if(this.OggettoEmail.IdCliente != null)
      {
        Query = 'SelectListaEventiSollecitiCliente'
        Parametri.ID_CLIENTE = this.OggettoEmail.IdCliente
      }
      else
      {
        if(this.OggettoEmail.IdAmministratore != null)
        {
          Query = 'SelectListaEventiSollecitiAmministratore'
          Parametri.ID_AMMINISTRATORE = this.OggettoEmail.IdAmministratore
        }
      }

      SystemInformation.AdvQuery.GetSQL('LogEmail', Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Lista");
                                          if(ArrayInfo != undefined)
                                          {
                                            let Descrizione = []
                                            let Titolo      = 'Eventi solleciti'
                                            
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                              Descrizione.push('• ' + TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(ArrayInfo[i].DATA)) + ' - ' + ArrayInfo[i].DESCRIZIONE)

                                            Self.StampaDocumentoGenerico(Descrizione, Titolo)
                                          }
                                          else SystemInformation.HandleError('Impossibile ottenere il log delle email');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        Query)
    },

    StampaDocumentoGenerico(Descrizione, Titolo)
    {
      let Self = this
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaDocumentoGenerico', { Descrizione : Descrizione, Titolo : Titolo },
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
  },

  beforeMount()
  {
    this.CaricaLogEmail()
  }
}
</script>

<style>
</style>