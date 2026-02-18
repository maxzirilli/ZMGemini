<template>
    <div>
      <VUEConfirm :Popup="PopupContentAmm" 
                  :PathLogo="require('../../assets/images/LogoGemini2.png')"
                  :Programma="NomeProgramma"
                  :Richiesta="'Vuoi inviare ' + (ListaDocumentiSelezionati.length != 1? 'i documenti' : 'il documento') + ' allo SdI?'" @onClickConfermaPopup="ConfermaInvioFatt" @onClickChiudiPopup="AnnullaInvioFatt">
      </VUEConfirm>

      <VUEConfirm :Popup="PopupContentAmmPagina" :PathLogo="require('../../assets/images/LogoGemini2.png')"
                  :Programma="NomeProgramma"
                  :Richiesta="'Vuoi inviare ' + (ListaDocumentiSelezionati.length != 1? 'i documenti' : 'il documento') + ' di questa pagina allo SdI?'" @onClickConfermaPopup="ConfermaInvioFattPagina" @onClickChiudiPopup="AnnullaInvioFattPagina">
      </VUEConfirm>

      <VUEModal v-if="ConfermaDocumentiInviati" :PathLogo="require('../../assets/images/LogoGemini2.png')"
                                                :Programma="NomeProgramma"
                                                :Titolo="'Comunicazione'" 
                                                :Altezza="'130px'" :Larghezza="'400px'"
                   @onClickChiudiModal="ConfermaDocumentiInviati = false" @onClickConfermaModal="ConfermaDocumentiInviati = false">
        <template v-slot:Body>
        <div class="form-row">
          <br>
          <div class="col-md-12">                
            <div style="font-size:16px">Le copie di cortesia sono state inviate.</div>
          </div>
        </div> 
        </template>
        <template v-slot:Footer>
          <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="ConfermaDocumentiInviati = false" data-dismiss="modal">Chiudi</button>
        </template>
      </VUEModal>

      <div class="row" style="padding: 10px; border-top: 1px;">
        <div class="col-md-12">
          <div style="float:left;width:1%">&nbsp;</div>
          <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickSelezionaTuttiDocumenti">Seleziona tutti</button>
          <div style="float:left;width:1%">&nbsp;</div>
          <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickDeselezionaTuttiDocumenti">Deseleziona tutti</button>
          <div style="float:left;width:1%">&nbsp;</div>
          <button style="float:left" class="btn btn-s-md btn-info" @click="OnClickInvertiSelezionati">Inverti</button>
          
          <button style="float:right;width:20%" class="btn btn-s-md btn-info" :disabled="ListaDocumentiSelezionati.length == 0 || AttivazioneCreaZipXML" @click="OnClickCambioStatoDaInviare('TuttiSelezionati')">Invia tutti i selezionati</button>
          <!-- Rimosso @click dopo AttivazioneInvioASdi" -->
          <div style="float:right;width:1%">&nbsp;</div>
          <button style="float:right;width:20%" class="btn btn-s-md btn-info" :disabled="ListaDocumentiSelezionati.length == 0 || AttivazioneCreaZipXML" @click="OnClickCambioStatoDaInviare('SelezionatiPagina')">Invia i selezionati della pagina</button>
          <!-- Rimosso @click dopo AttivazioneInvioASdi" -->
          <div style="float:left;width:1%;">&nbsp;</div>
        </div>

        <div class="col-md-12">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:100%;width:100%;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>    
                      <th style="width:3%">&nbsp;</th>
                      <th>Numero</th>
                      <th>Data</th>
                      <th>Importo</th>
                      <th>Cliente intestatario</th>
                      <th style="width:10%">&nbsp;</th>
                      <th style="width:10%">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Documento in RighePagina" :key="Documento.CHIAVE" style="font-size:15px">
                      <td><input type="checkbox"  value="true" v-model="Documento.Selezionato" style="height:20px;width:20px"/></td>
                      <td v-if="Documento.IS_NOTA_DI_CREDITO == 'T'"> Nota di credito nr. {{ Documento.NUMERO }} </td>
                      <td v-else-if="Documento.IS_AUTOFATTURA == 'T'"> Autofattura nr. {{ Documento.NUMERO }} </td>
                      <td v-else-if="Documento.DA_BANCO == 'T'"> Fattura da banco nr. B{{ Documento.NUMERO }} </td>
                      <td v-else> Fattura nr. {{ Documento.NUMERO }} </td>
                      <td> {{ Documento.DATA }} </td>
                      <td> {{ Documento.IMPORTO }} </td>
                      <td> {{ Documento.RAGIONE_SOCIALE }} </td>
                      <td style="text-align: center;">
                          <button class="btn btn-s-md btn-info" @click="OnClickStampaDocumento(Documento)"
                            :style="{ visibility: Documento.IS_AUTOFATTURA == 'T' ? 'hidden' : 'visible' }">
                            Stampa</button>
                      </td>
                      <td style="text-align: center;"><button class="btn btn-s-md btn-info" @click="OnClickGeneraXML(Documento)">Genera XML</button></td>
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
                        <li :class="{ active : IsActivePage(0)}"><a @click="OnClickChangePage(0)">{{Paginazione.StartGruppoPagine }}</a></li>
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
      <VUEModalCaricamentoDati v-if="InvioFatture"
                               :OggettoModalCaricamentoDati="OggettoModalCaricamentoDati"
                               :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>
      <!-- <div v-if="InvioFatture" style="border:1px">
       <div class="modal-backdrop in" style=";z-index:99998"></div>
        <div class="modal-dialog" data-focus style=";z-index:99999;position:fixed;width: 30%;top:20%;left:0;right:0;bottom:0">
          <div class="modal-content" style="background:#d0e9ff;height: 30%; min-height:250px">
            <div style="background-color:#68b6be" class="modal-header">
                <h4 class="modal-title" style="color:#d0e9ff;" id="myModalLabel">
                <img src="@/assets/images/LogoArtax2.png" style="height:20px;float:left" class="m-r-sm">
                Artax - Invio fatture allo SdI 
                </h4>
            </div>
            <div class="modal-body" style="height:120%">
            Invio fatture....
            </div>
          </div>
        </div>
      </div> -->
    </div>
</template>

<script>
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { saveAs } from 'file-saver';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import { TSchedaFattura } from '../SchedeDatabase/VUESchedaFattura.vue';
import { TSchedaNotaDiCredito } from '../SchedeDatabase/VUESchedaNotaDiCredito.vue';
import VUEModalCaricamentoDati, { TOggettoModalCaricamentoDati } from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';

export default 
{
  data()
  {
    return { 
            ListaDocumenti              : [],
            InvioFatture                : false,
            PopupContentAmm             : false,
            PopupContentAmmPagina       : false,
            AttivazioneCreaZipXML       : false,
            ControlloInvioFatture       : false,
            ConfermaDocumentiInviati    : false,
            OggettoModalCaricamentoDati : new TOggettoModalCaricamentoDati(),
            NomeProgramma               : NOME_PROGRAMMA,
            Paginazione                 : {
                                            NrRighe           : 30,
                                            NrPagina          : 1,
                                            StartGruppoPagine : 1
                                          },
          }
  },
components : { VUEConfirm, VUEModalCaricamentoDati, VUEModal},
methods :
{
  CaricaFattureDaEsportare()
  {
    var Self = this
    SystemInformation.AdvQuery.GetSQL('EsportaFattureManualmente',{},
                                      function(Results)
                                      {
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FattureInCompilazione");
                                        if(ArrayInfo != undefined)
                                        {     
                                          ArrayInfo.forEach(function(Documento)
                                          {
                                            Documento.DATA    = new Date(Documento.DATA)
                                            Documento.DATA    = TZDateFunct.FormatDateTime('dd/mm/yyyy',Documento.DATA) 
                                            Documento.IMPORTO = (Documento.IMPORTO / 10000).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'})
                                            Documento.Selezionato = true
                                          })
                                          Self.ListaDocumenti = ArrayInfo;
                                        }
                                        Self.InvioFatture = false;
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        Self.InvioFatture = false;
                                      })
  },

  FStampaFattura(Documento)
  {
    var Fattura = new TSchedaFattura(SystemInformation.AdvQuery)
    Fattura.SetKey(Documento.CHIAVE)
    Fattura.Stampa((BodyPDF) =>
                   {
                     if(BodyPDF != undefined)
                     {
                       var routeData = this.$router.resolve({ name   : "IFrameStampe" });
                       var AWindow = window.open(routeData.href, "_blank");
                       AWindow.BodyPDF = BodyPDF;
                     }
                   })
  },

  FStampaNotaDiCredito(Documento)
  {
    var Nota = new TSchedaNotaDiCredito(SystemInformation.AdvQuery)
    Nota.SetKey(Documento.CHIAVE)
    Nota.Stampa((BodyPDF) =>
                   {
                     if(BodyPDF != undefined)
                     {
                       var routeData = this.$router.resolve({ name   : "IFrameStampe" });
                       var AWindow = window.open(routeData.href, "_blank");
                       AWindow.BodyPDF = BodyPDF;
                     }
                   })
  },

  OnClickStampaDocumento(Documento)
  {
    if(Documento.IS_NOTA_DI_CREDITO == 'T')
    {
       this.FStampaNotaDiCredito(Documento);
       return;
    }
    if(Documento.IS_AUTOFATTURA == 'T')
       return;
    this.FStampaFattura(Documento)
  },

  OnClickCambioStatoDaInviare(FileSelezionati)
  { 
    var ObjQuery              = { Operazioni : [] };
    let ListaDocs;
    let LsChiaviFatture       = [];
    let LsChiaviAutofatture   = [];
    let LsChiaviNoteDiCredito = [];

    if(FileSelezionati === 'TuttiSelezionati')
    {
      ListaDocs = this.ListaDocumentiSelezionati;
    }
    else if(FileSelezionati === 'SelezionatiPagina')
    {
      ListaDocs = this.ListaDocumentiSelezionatiPagina;
    }
  
    ListaDocs.forEach(function(doc)
          {
            if(doc.IS_NOTA_DI_CREDITO === 'T') 
            {
              LsChiaviNoteDiCredito.push(doc.ChiaveNota);
            }
            else if(doc.IS_AUTOFATTURA === 'T')
            {
              LsChiaviAutofatture.push(doc.ChiaveAutofattura);
            } 
            else 
            {
              LsChiaviFatture.push(doc.ChiaveFattura);
            }
          })
    
    if (LsChiaviFatture.length > 0) 
    {
        ObjQuery.Operazioni.push({
                               Query     : "UpdateFattureDaZippare",
                               Parametri : {
                                             ChiaviFatture : LsChiaviFatture
                                           }
                             });
    }
    
    if (LsChiaviAutofatture.length > 0)
    {
        ObjQuery.Operazioni.push({
                                   Query     : "UpdateAutofattureDaZippare",
                                   Parametri : {
                                                 ChiaviAutofatture : LsChiaviAutofatture
                                               }
                                 });
    }

    if (LsChiaviNoteDiCredito.length > 0)
    {
        ObjQuery.Operazioni.push({
                                   Query     : "UpdateNoteDiCreditoDaZippare",
                                   Parametri : {
                                                 ChiaviNoteDiCredito : LsChiaviNoteDiCredito
                                               }
                                 });
    }
    
    this.InvioFatture = true;
    SystemInformation.AdvQuery.PostSQL('EsportaFattureManualmente',ObjQuery,
                           () => 
                           {
                            this.CaricaFattureDaEsportare();
                           },
                           (HTTPError,SubHTTPError,Response) =>
                           {
                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                            this.InvioFatture = false;
                           }
                         )
  },

  OnClickGeneraXML(Documento)
  {
      SystemInformation.AdvQuery.ExecuteExternalScript('GeneraXML',Documento,
      (Answer) =>
      {
         let ABlob = new Blob([Answer.XMLBody], 
         {
             type: "application/xml;charset=utf-8"
         });
         saveAs(ABlob,'XML' + Documento.CHIAVE + '.xml')
      },
      function(ErrorMessage,DescrErrore)
      {
         alert(ErrorMessage + '[' + DescrErrore + ']')
      });
  },

  ConfermaInvioFatt()
  {
    if(!this.ControlloInvioFatture)
    {
      this.ControlloInvioFatture = true
      var Self                   = this                            
      this.InvioFatture          = true
      this.PopupContentAmm       = false
      const NUM_MIN_FATT = 3
      var Spezzoni = [];
      var i = 0;
      this.CalcoloBarraDiProgressione(Spezzoni, i)
      var Vettore = [];

      this.ListaDocumentiSelezionati.forEach(function(Documento)
      {
        if(i == 0)
        {
          Spezzoni.push(Vettore); 
        }

        Vettore.push(Documento)
        i++;
    
        if(i >= NUM_MIN_FATT)
        {
          Vettore = [];
          i = 0;
        }
      })
      
      i = 0;
      var QualcheMailInviata = false
      var InvioProssimiDocumenti = function()
      {  
        if(i < Spezzoni.length)
         {
           SystemInformation.AdvQuery.ExecuteExternalScript('InviaFatturaASdI', Spezzoni[i],
                                                      function(Result)
                                                      {  
                                                        i++;
                                                        if(Result.Risposta == 'MAIL_INVIATA')
                                                           QualcheMailInviata = true
                                                        
                                                        setTimeout(InvioProssimiDocumenti,400);
                                                        Self.CalcoloBarraDiProgressione(Spezzoni,i)
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        alert(HTTPError)
                                                        Self.InvioFatture          = false
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        Self.ControlloInvioFatture = false
                                                        Self.CaricaFattureDaEsportare()
                                                      })  
         }
         else
         {
            Self.InvioFatture          = false
            Self.CaricaFattureDaEsportare()
            Self.ControlloInvioFatture = false
            //Self.AttivazioneInvioASdi  = false
            
            if(QualcheMailInviata)
               setTimeout(function() { Self.ConfermaDocumentiInviati = true }, 200)
         }
      }
      InvioProssimiDocumenti();
    }
  },

  ConfermaInvioFattPagina()
  {
    if(!this.ControlloInvioFatture)
    {
      this.ControlloInvioFatture = true
      var Self                   = this                            
      this.InvioFatture          = true
      this.PopupContentAmmPagina = false
      const NUM_MIN_FATT = 3
      var Spezzoni = [];
      var i = 0;
      this.CalcoloBarraDiProgressione(Spezzoni, i)
      var Vettore = [];

      this.ListaDocumentiSelezionatiPagina.forEach(function(Documento)
      {
        if(i == 0)
        {
          Spezzoni.push(Vettore); 
        }

        Vettore.push(Documento)
        i++;
    
        if(i >= NUM_MIN_FATT)
        {
          Vettore = [];
          i = 0;
        }
      })
      
      i = 0;
      var QualcheMailInviata = false
      var InvioProssimiDocumenti = function()
      {  
        if(i < Spezzoni.length)
         {
           SystemInformation.AdvQuery.ExecuteExternalScript('InviaFatturaASdI', Spezzoni[i],
                                                      function(Result)
                                                      {  
                                                        i++;
                                                        if(Result.Risposta == 'MAIL_INVIATA')
                                                           QualcheMailInviata = true
                                                        
                                                        setTimeout(InvioProssimiDocumenti,400);
                                                        Self.CalcoloBarraDiProgressione(Spezzoni,i)
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        alert(HTTPError)
                                                        Self.InvioFatture          = false
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        Self.ControlloInvioFatture = false
                                                        Self.CaricaFattureDaEsportare()
                                                      })  
         }
         else
         {
            Self.InvioFatture          = false
            Self.CaricaFattureDaEsportare()
            Self.ControlloInvioFatture = false
            Self.AttivazioneInvioASdi  = false
            
            if(QualcheMailInviata)
               setTimeout(function() { Self.ConfermaDocumentiInviati = true }, 200)
         }
      }

      InvioProssimiDocumenti();
    }
  },

  CalcoloBarraDiProgressione(Spezzoni, i)
  {
    if(Spezzoni.length != 0)
    {
      let PercentualePerIndex = 100 / Spezzoni.length
      this.OggettoModalCaricamentoDati.Width       = PercentualePerIndex * i
      this.OggettoModalCaricamentoDati.DatoPassato = true
    }
    else 
    {
      this.OggettoModalCaricamentoDati.Width       = 0.01
      this.OggettoModalCaricamentoDati.DatoPassato = true
    }
  },

  AnnullaInvioFatt()
  {
    this.PopupContentAmm      = false
    //this.AttivazioneInvioASdi = false
  },

  AnnullaInvioFattPagina()
  {
    this.PopupContentAmmPagina = false
    //this.AttivazioneInvioASdi  = false
  },

  OnClickDeselezionaTuttiDocumenti()
  {
    this.ListaDocumenti.forEach(function(Documento)
    {
      Documento.Selezionato = false
    })
  },

  OnClickSelezionaTuttiDocumenti()
  {
    this.ListaDocumenti.forEach(function(Documento)
    {
      Documento.Selezionato = true
    })
  },

  OnClickInvertiSelezionati()
  {
    if(this.SingolaPagina != true)
    {
      this.ListaDocumenti.forEach(function(Documento)
      {
        if(Documento.Selezionato)
          Documento.Selezionato = false
        else Documento.Selezionato = true
      })
    }
    else
    {
      for(var i = 0; i < this.RighePagina.length ; i++)
      {
        if(this.RighePagina[i].Selezionato)
          this.RighePagina[i].Selezionato = false
        else this.RighePagina[i].Selezionato = true
      }
    }
  },

  //OnClickZipXMLFattureSelezionate()
  //{
  //  if(!this.AttivazioneCreaZipXML)
  //  {
  //    this.AttivazioneCreaZipXML = true
  //   this.PopupContentAmm      = true
  //  }
  //},

  //OnClickZipXMLFattureSelezionatePagina()
  //{
  //  if(!this.AttivazioneCreaZipXML)
  //  {
  //    this.AttivazioneCreaZipXML  = true
  //    this.PopupContentAmmPagina = true
  //  }
  //},

  TastoPaginaVisibile(Offset)
  {
    return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighe < this.ListaDocumenti.length
  },

  IsActivePage(Offset)
  {
    return this.Paginazione.NrPagina == this.Paginazione.StartGruppoPagine + Offset;
  },

  OnClickChangePage(Offset)
  {
    this.SingolaPagina = false
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
    this.Paginazione.NrPagina = Math.ceil(this.ListaDocumenti.length / this.Paginazione.NrRighe)
    if (this.Paginazione.NrPagina >= 5)
        this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4;
    else this.Paginazione.StartGruppoPagine = 1;    
  },
},

computed:
{
  ListaDocumentiSelezionati:
  {
    get()
    {
      var Result = []
      this.ListaDocumenti.forEach(function(Documento)
      {
        if(!Documento.Selezionato) return;
        
        let ExtraInfo = 
        {
          Anno    : Documento.ANNO,
          Numero  : Documento.NUMERO,
          Cliente : Documento.RAGIONE_SOCIALE
        }

        if(Documento.Selezionato && Documento.IS_NOTA_DI_CREDITO == 'T')
          Result.push({...ExtraInfo, ChiaveNota : Documento.CHIAVE, IS_NOTA_DI_CREDITO : 'T', IS_AUTOFATTURA : 'F'})
        else 
        {
          if(Documento.Selezionato && Documento.IS_AUTOFATTURA == 'T')
            Result.push({...ExtraInfo, ChiaveAutofattura : Documento.CHIAVE, IS_NOTA_DI_CREDITO : 'F', IS_AUTOFATTURA : 'T'})
          else 
          {
            if(Documento.Selezionato && Documento.IS_AUTOFATTURA == 'F' && Documento.IS_NOTA_DI_CREDITO == 'F')
              Result.push({...ExtraInfo, ChiaveFattura : Documento.CHIAVE, DaBanco : Documento.DA_BANCO, IS_NOTA_DI_CREDITO : 'F', IS_AUTOFATTURA : 'F'})     
          }
        } 
      })
      return Result
    }
  },

  ListaDocumentiSelezionatiPagina:
  {
    get()
    {
      var Result = []
      this.RighePagina.forEach(function(Documento)
      {
        if(!Documento.Selezionato) return;
        
        let ExtraInfo = 
        {
          Anno    : Documento.ANNO,
          Numero  : Documento.NUMERO,
          Cliente : Documento.RAGIONE_SOCIALE
        }

        if(Documento.Selezionato && Documento.IS_NOTA_DI_CREDITO == 'T')
          Result.push({...ExtraInfo, ChiaveNota : Documento.CHIAVE, IS_NOTA_DI_CREDITO : 'T', IS_AUTOFATTURA : 'F'})
        else 
        {
          if(Documento.Selezionato && Documento.IS_AUTOFATTURA == 'T')
            Result.push({...ExtraInfo, ChiaveAutofattura : Documento.CHIAVE, IS_NOTA_DI_CREDITO : 'F', IS_AUTOFATTURA : 'T'})
          else 
          {
            if(Documento.Selezionato && Documento.IS_AUTOFATTURA == 'F' && Documento.IS_NOTA_DI_CREDITO == 'F')
              Result.push({...ExtraInfo, ChiaveFattura : Documento.CHIAVE, DaBanco : Documento.DA_BANCO, IS_NOTA_DI_CREDITO : 'F', IS_AUTOFATTURA : 'F'})     
          }
        } 
      })
      return Result
    }
  },

  RighePagina :
  {
    get()
    {
      let Result = [];
      for(let i = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighe;
          i < this.ListaDocumenti.length && i < (this.Paginazione.NrPagina) * this.Paginazione.NrRighe;
          i++)
          Result.push(this.ListaDocumenti[i]);
      return Result;
    }
  },

  ForwardGruppoPagineVisibile : 
  {
    get()
    {
      return this.ListaDocumenti.length > this.Paginazione.NrPagina * this.Paginazione.NrRighe;
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
      if(this.ListaDocumenti.length < MaxRighe) MaxRighe = this.ListaDocumenti.length;
      let StartRecord = this.ListaDocumenti.length == 0 ? 0 : this.Paginazione.NrRighe * (this.Paginazione.NrPagina - 1) + 1;
      return "Visualizzati " +  StartRecord + ' - ' +
              MaxRighe + ' di ' +  this.ListaDocumenti.length + ' risultati'
    }
  }, 
},

  beforeMount()
  {
     this.CaricaFattureDaEsportare()
  }
}
</script>