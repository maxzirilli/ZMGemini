<template>
  <header class="bg-dark dk header navbar navbar-fixed-top-xs">
    <div class="navbar-header aside-md">
      <a class="btn btn-link visible-xs">
        <i class="fa fa-bars"></i>
      </a>
      <a href="#" class="navbar-brand" data-toggle="fullscreen">
        <img src="../../assets/images/LogoGemini.png" class="m-r-sm" style="font-size:24px;margin-right:3px">&nbsp;&nbsp;GEMINI
      </a>
      <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
        <i class="fa fa-cog"></i>
      </a>
    </div>
    <a class="navbar-brand" data-toggle="fullscreen" v-if="DeveloperMode">
        <span class="m-r-sm" style="font-size:24px;margin-right:3px;color:red">&nbsp;&nbsp;DEBUG MODE ATTIVA</span>
    </a>

    <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">
      <li class="hidden-xs" v-if="UserInformation.Ruolo != 'A' && $route.params.pagina != 'SchedaMessaggi' && MessaggiCount > 0">
        <a  @click="GoToMessaggi" @load="ConteggiomessaggiNonLetti">
          <i class="fa fa-bell"></i>
          <span v-if="Notifiche > 0" class="badge badge-sm up bg-danger m-l-n-sm count">{{Notifiche}}</span>
        </a>
      </li>
      
      <li class="dropdown open"  style="z-index:9999;">
          <a style="cursor:pointer" @click="OnClick" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            <span class="thumb-sm avatar pull-left">
              <img v-if="UserInformation.Immagine != '' && UserInformation.Immagine != null" :src="UserInformation.Immagine" style="max-width:30px; max-height:30px; min-width:30px; min-height:30px">
              <img v-else src="@/assets/images/avatar_default.jpg">
            </span>
            {{ UserInformation.RagioneSociale }} <b class="fa" :class="{ 'fa-sort-up' : aperto, 'fa-sort-down' : !aperto }" ></b>
          </a>
            

          <ul class="dropdown-menu" v-if="aperto">
            <li>
              <a style="cursor:pointer" @click="OnClickLogout">Logout</a>
            </li>
            <li>
              <a style="cursor:pointer" @click="OnClickPopupProfilo">Modifica Profilo</a>
            </li>
            <li>
              <a style="cursor:pointer" @click="OnClickPopupPassword">Modifica Password</a>
            </li>
            <li v-if="UserInformation.Ruolo == Ruoli.Amministratore || UserInformation.Ruolo == Ruoli.SuperUtente" class="divider"></li>
            <li v-if="UserInformation.Ruolo == Ruoli.Amministratore || UserInformation.Ruolo == Ruoli.SuperUtente">
              <a style="cursor:pointer" @click="OnClickPopupDCPM">Scarica i tuoi dati</a>
            </li>
            <li class="divider"></li>
            <li>
              <a @click="OnClick">Chiudi</a>
            </li>
          </ul>
        </li>
    </ul>      
  </header>

  <body>

       <VUEModal v-if="profilomodifica" :Titolo="'Modifica profilo'" :Altezza="'400px'" :Larghezza="'1000px'"
                   @onClickChiudiModal="AnnullaModificaProfilo" @onClickConfermaModal="ConfermaModificaProfilo">
           <template v-slot:Body>
            <div class="form-row">
              <br>
              <div class="col-md-12">                
                  <div class="col-md-3">  
                      <div href="javascript:;" v-if="UserInformationEdit.Immagine != ''">
                        <img style="height:200px; max-height:200px; max-width:200px; min-height:200px; min-width:200px" :src="UserInformationEdit.Immagine" class="img-circle">
                      </div>
                      <div href="javascript:;" v-else>
                        <img style="height:200px" src="@/assets/images/avatar_default.jpg" class="img-circle">
                      </div>
                      <br>
                      <div>
                        <label class="btn" type="button" style="cursor:pointer;width:100%;background-color:white; width:200px">Inserisci nuova immagine         
                            <input style="display:none;" type="file" id="inputImmagineUtente" @change="LoadImmagineFromFile(elemento)" accept="image/*">
                        </label>                     
                      </div>
                  </div>
                  <div class="col-md-1"></div>
                  <div class="col-md-8">
                      <div>Ragione sociale: <input class="form-control" style="width:100%" type="text" v-model="UserInformationEdit.RagioneSociale"></div>
                      <br>
                      <div>Email: <input class="form-control" style="width:100%" type="text" v-model="UserInformationEdit.EMail"></div>
                  </div>
              </div>
            </div> 
           </template>
         </VUEModal> 
         
       <VUEModal v-if="passwordmodifica" :Titolo="'Modifica password'" :Altezza="'400px'" :Larghezza="'1000px'"
                   @onClickChiudiModal="AnnullaModificaPassword">
           <template v-slot:Body>
            <div class="form-row" style="font-size:14px">
              <ul>La nuova password deve contenere:
                <li>Almeno otto caratteri (non sono ammessi spazi);
                  <span v-if="CheckNumeroCaratteri && CheckSpazi" class="fa fa-check" style="color:#33ff33; font-size: 15px"></span>
                  <span v-else class="fa fa-times" style="color:#cb4d4d; font-size: 15px"></span>
                </li>
                <li>Una lettera maiuscola ed una minuscola;
                  <span v-if="CheckMinuscole && CheckMaiuscole" class="fa fa-check" style="color:#33ff33; font-size: 15px"></span>
                  <span v-else class="fa fa-times" style="color:#cb4d4d; font-size: 15px"></span>                
                </li>
                <li>Uno dei seguenti caratteri: $-/:-?{-~!"^_}@`[];
                  <span v-if="CheckSpeciali" class="fa fa-check" style="color:#33ff33; font-size: 15px"></span>
                  <span v-else class="fa fa-times" style="color:#cb4d4d; font-size: 15px"></span>
                </li>
                <li>Un numero
                  <span v-if="CheckNumeri" class="fa fa-check" style="color:#33ff33; font-size: 15px"></span>
                  <span v-else class="fa fa-times" style="color:#cb4d4d; font-size: 15px"></span>
                </li>
              </ul>
              <br>
              <div class="col-md-12">               
                <div>Vecchia Password: <input type="password" class="form-control" style="width:100%" v-model="Password.Vecchia"></div>
                <br>
                <div>Nuova Password: <input type="password" class="form-control" style="width:100%" v-model="Password.Nuova"></div>
                <br>
                <div>Ripeti Password: <input type="password" class="form-control" style="width:100%" v-model="Password.Ripeti"></div>
              </div>
              <br>
              <ul v-if="!CheckRipetiPassword && Password.Nuova != ''">Le password inserite non coincidono</ul>
            </div>
           </template>
           <template v-slot:Footer>
            <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="AnnullaModificaPassword" data-dismiss="modal">Annulla</button>
            <button v-if="CheckPassword" type="button" class="btn btn-success" style="float:right;font-weight:bold;width:18%" @click="ConfermaModificaPassword" data-dismiss="modal">Conferma</button>
           </template>
         </VUEModal>

         <VUEModal v-if="ScaricaDatiDCPM" :Titolo="'Scarica dati personali'" :Altezza="'250px'" :Larghezza="'600px'"
                   @onClickChiudiModal="ScaricaDatiDCPM = false, BottoneRichiestaDisabilitato = false">
           <template v-slot:Body>
                <VUEConfirm :Popup="PopupConfermaRichiestaDati" 
                            :Richiesta="'Attenzione!\nSe confermi, non potrai più scaricare i dati richiesti in precedenza.\nSei sicuro di voler procedere?'" 
                            @onClickConfermaPopup="OnClickRichiestaDownloadDati" 
                            @onClickChiudiPopup="OnClickAnnullaRichiestaDati">
                </VUEConfirm>

                <button style="float:left;margin-right:5px;margin-left:5px;" @click="OnClickConfermaRichiestaDati()" class="btn btn-info" :disabled="BottoneRichiestaDisabilitato">Richiedi Dati</button>

                <div class="col-md-12">
                  <div class="row wrapper">
                    <section class="panel panel-default" style="background-color:white;">
                      <div class="table-responsive" style="max-height:100%;width:100%;">
                        <table class="table table-striped b-t b-light">
                          <thead>
                            <tr>    
                              <th style="width:20%">Data Richiesta</th>
                              <th style="width:20%">Stato</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-if="ListaStatusGDPR.length == 0">
                              <td colspan="9">Nessuna richiesta di dati presente</td>
                            </tr>
                            <tr v-for="Dati in ListaStatusGDPR" :key="Dati.DATA_RICHIESTA" style="font-size:15px">
                              <td> {{ Dati.DATA_RICHIESTA }} </td>
                              <td v-if="Dati.STATO_DOWNLOAD == StatoDownloadGDPR.Pronto">
                                <button @click="OnClickScaricaZip(Dati)" class="btn btn-success" style="min-width: 100px">Scarica file zip</button>
                              </td>
                              <td v-else> In elaborazione </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </section>
                  </div>
                </div>
              <!-- </ul>
            </div> -->
           </template>
           <template v-slot:Footer>
            <button type="button" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="ScaricaDatiDCPM = false, BottoneRichiestaDisabilitato = false" data-dismiss="modal">Chiudi</button>
           </template>
         </VUEModal>

  </body>
</template>

<script>
import { SystemInformation,
         RUOLI,
         STATO_DOWNLOAD_GDPR } from '@/SystemInformation';
import { TZImageFunct } from '../../../../../../../..\\Librerie\\VUE\\ZImageFunct.js'
import VUEModal from '@/components/Slots/VUEModal.vue';
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js'
import { saveAs } from 'file-saver'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEConfirm from '@/components/VUEConfirm.vue';

export default
{
   name: "VUEAppHeader",
   components: { VUEModal,
                 VUEConfirm},
   data()
   {
      return {
              UserInformation             : SystemInformation.UserInformation,
              aperto                      : false,
              profilomodifica             : false,
              passwordmodifica            : false,
              ScaricaDatiDCPM             : false,
              BottoneRichiestaDisabilitato: false,
              PresenzaFileGiàScaricato    : false,
              PopupConfermaRichiestaDati  : false,
              Ruoli                       : RUOLI,
              StatoDownloadGDPR           : STATO_DOWNLOAD_GDPR,
              ListaStatusGDPR             : [],
              UserInformationEdit         : {},
              Password                    : {},
              Notifiche                   : 0,
              MessaggiCount               : 0,
              DeveloperMode               : SystemInformation.DeveloperMode,
              
      };
   },
   beforeMount()
   {
      this.UserInformationEdit = {
                                    RagioneSociale : this.UserInformation.RagioneSociale,
                                    EMail          : this.UserInformation.EMail,
                                    Immagine       : this.UserInformation.Immagine,
                                    Ruolo          : this.UserInformation.Ruolo
        }
        if (this.UserInformationEdit.Immagine == undefined)
        {
          this.UserInformationEdit.Immagine = ''
        }
        this.Password            = {
                                      Nuova          : '',
                                      Ripeti         : '',
                                      Vecchia        : ''
        }
        this.ConteggiomessaggiNonLetti()
   },
   methods :
   {
      ConteggiomessaggiNonLetti()
      {
        
        if (SystemInformation.UserInformation.Ruolo != 'A')
        {
          let Self = this
          SystemInformation.AdvQuery.GetSQL('Utenti_Messaggi', {},
          function(Results)
          {
            let Messaggi = SystemInformation.AdvQuery.FindResults(Results,"MessaggiCount")
            let MessaggiLetti = SystemInformation.AdvQuery.FindResults(Results,'MessaggiLettiCount')
            Self.MessaggiCount = Messaggi[0].COUNT
            Self.Notifiche = Self.MessaggiCount - MessaggiLetti[0].COUNT_LETTI
          }, SystemInformation.HandleError, 'SelectNotifiche')
        }
      },

      OnClick()
      {
        this.aperto = !this.aperto
      },
      OnClickPopupProfilo()
      {
        this.profilomodifica = true
        this.aperto = false 
      },
      ConfermaModificaProfilo()
      {
        var ObjQuery = {Operazioni : []}
        var Self = this
        ObjQuery.Operazioni.push(
                        {
                          Query : 'ModificaProfilo',
                          Parametri : {
                                          RagioneSociale : this.UserInformationEdit.RagioneSociale,
                                          EMail          : this.UserInformationEdit.EMail,
                                          Immagine       : this.UserInformationEdit.Immagine,
                                          UserName       : this.UserInformation.UserName                                 
                          }
                       })
        SystemInformation.AdvQuery.PostSQL('ModificaProfilo', ObjQuery, function(){
                                                                                    Self.profilomodifica = false
                                                                                    Self.UserInformation = Self.UserInformationEdit
                                                                                  }, SystemInformation.HandleError)
      },
      AnnullaModificaProfilo()
      {
        this.profilomodifica = false
        this.UserInformationEdit.EMail = this.UserInformation.EMail
      },

      OnClickPopupPassword()
      {
        this.passwordmodifica = true
        this.aperto = false
      },

      OnClickLogout()
      {
        var Self = this;
        SystemInformation.AdvQuery.Logout(
        function() 
        {
            localStorage.clear();
            Self.$router.push('/')
            Self.$router.go()
        },
        function(Riga1,Riga2)
        {
          console.error(Riga1 + "\n" + Riga2);
        });
      },

      OnClickPopupDCPM()
      {
        const Self = this;
        SystemInformation.AdvQuery.GetSQL('EsportaDatiPerGDPR', { CHIAVE_CLIENTE : this.IdCliente }, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "StatusDownload");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.ListaStatusGDPR = ArrayInfo;
                                              if(Self.ListaStatusGDPR[0] != undefined)
                                              {
                                                if(Self.ListaStatusGDPR[0].STATO_DOWNLOAD == 'F' )
                                                  Self.BottoneRichiestaDisabilitato = true
                                                else Self.PresenzaFileGiàScaricato = true
                                              }
                                              else Self.PresenzaFileGiàScaricato = false

                                              Self.ScaricaDatiDCPM = true
                                              Self.aperto          = false
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista Status GDPR');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'SelectSQL');
      },

      OnClickScaricaZip(Dati)
      {
        var Parametri = { NomeFIle : 'Download' + Dati.DATA_RICHIESTA }
        SystemInformation.AdvQuery.ExecuteExternalScript('GetZipDatiPersonali',Parametri,function(JSONAnswer)
                                        {
                                          saveAs(TZStringConvFunct.Base64AsBlob(JSONAnswer.FileZip), "Datipersonali_"+ Dati.DATA_RICHIESTA+ ".zip")
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        })
      },

      OnClickConfermaRichiestaDati()
      {
        if(this.PresenzaFileGiàScaricato)
        {
          this.PopupConfermaRichiestaDati = true
        }
        else this.OnClickRichiestaDownloadDati()
      },

      OnClickAnnullaRichiestaDati()
      {
        this.PopupConfermaRichiestaDati = false
        this.ScaricaDatiDCPM = true
      },

      OnClickRichiestaDownloadDati()
      {
        var Self = this;
        this.PopupConfermaRichiestaDati = false

        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                    Query     : "Delete",
                                    Parametri : {}
                                })
        var Data = new Date()
        Data = TZDateFunct.FormatDateTime('yyyy-mm-dd', Data)
        ObjQuery.Operazioni.push({
                                    Query     : "NewRequest",
                                    Parametri : { DATA : Data }
                                })

        SystemInformation.AdvQuery.PostSQL('EsportaDatiPerGDPR',ObjQuery,function()
        {
            Self.OnClickPopupDCPM()
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        });
      },

      LoadImmagineFromFile()
      {
        var Self    = this;
        var Reader  = new FileReader()
        var ImgFile = document.getElementById('inputImmagineUtente')
        
        var TypeImg = ImgFile.files[0].type
        Reader.readAsDataURL(ImgFile.files[0])

        Reader.onload = function (e) 
        {
           var Img = new Image()
           Img.onload = function () 
           {
              Img.onload = undefined;
              TZImageFunct.ResizeImage(Img,200,300,TypeImg) 
              Self.UserInformationEdit.Immagine = Img.src 
           }
           Img.src = e.target.result
        }        
      },
      AnnullaModificaPassword()
      {
            this.Password.Nuova     = ''
            this.Password.Ripeti    = ''
            this.Password.Vecchia   = ''
            this.passwordmodifica = false
      },

      ConfermaModificaPassword()
      {
          var Self = this
          SystemInformation.AdvQuery.ChangePassword(this.Password.Vecchia,this.Password.Nuova,
                                          function()
                                          { 
                                              Self.passwordmodifica = false
                                          },
                                          SystemInformation.HandleError);
      },

      GoToMessaggi()
      {
        this.$router.push('/appMainWindow/SchedaMessaggi')
      },

    },
    computed :
    {
        CurrentRouter:
        {
          get()
          {
              return this.$route.fullPath 
          }
        },

        CheckPassword()
        {
            if(this.CheckSpazi)
            {
                if( this.CheckNumeroCaratteri && this.CheckRipetiPassword &&
                    this.CheckNumeri && this.CheckMinuscole
                    && this.CheckMaiuscole && this.CheckSpeciali)
                    return true
            }
            return false            
        },
        CheckSpazi()
        {
            let check5=/\s/
            if(check5.test(this.Password.Nuova))
                return false
            return true
        },          
        CheckNumeri()
        {
            let check=/[0-9]/
            if(check.test(this.Password.Nuova))
                return true
            return false
        },
        CheckSpeciali()
        {
            let check4=/[$-/:-?{-~!"^_}@`[\]]/
            if(check4.test(this.Password.Nuova))
                return true
            return false
        },
        CheckMinuscole()
        {
            let check2=/[a-z]/
            if(check2.test(this.Password.Nuova))
                return true
            return false
        },
        CheckMaiuscole()
        {
            let check3=/[A-Z]/
            if(check3.test(this.Password.Nuova))
                return true
            return false 
        },
        CheckNumeroCaratteri()
        {
            if (this.Password.Nuova.length >= 8)
                return true
            return false
        },
        CheckRipetiPassword()
        {
            if(this.Password.Nuova != '')
            {
                if (this.Password.Nuova == this.Password.Ripeti)
                    return true
            }
            return false
        },
    },

    watch:
    {
     CurrentRouter:
     {
        handler(NewValue,OldValue)
        {
          if(OldValue == '/appMainWindow/SchedaMessaggi')
            this.ConteggiomessaggiNonLetti()
        }
     }
    },
           
}

</script>