<template>
  <div>
  <VUEHeader/>
  <section id="content" class="m-t-lg wrapper-md animated fadeInUp bg-dark" @keydown.enter = "OnClickLogin">       
    <div class="container aside-xxl">      
      <section class="panel panel-default bg-white m-t-lg">
        <header class="panel-heading text-center">
          <strong>Sign in</strong>
        </header>
         <VUEModal v-if="RecuperoPsw" :Titolo="'Recupero Password'" :Altezza="'200px'" :Larghezza="'800px'"
                   @onClickChiudiModal="AnnullaRecupero">
           <template v-slot:Body>
              <div class="col-md-12">                
                  <p>Inserendo il nome utente nella riga sottostante, verrà inviata una nuova password all'account email collegato</p>
                  <br>
                  <input class="form-control" style="width:40%" type="text" v-model="NomeUtente">
              </div>
           </template>
           <template v-slot:Footer>
            <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="AnnullaRecupero" data-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:18%" @click="ConfermaRecupero" data-dismiss="modal">Invia</button>
           </template>
         </VUEModal> 
        <div class="panel-body wrapper-lg">
          <!-- <div class="form-group">
            <label class="control-label">Partita IVA</label>
            <input type="text" placeholder="Partita IVA" style="height:40px;text-transform: none!important" v-model="PartitaIVA" class="form-control ZMFormRegister">
          </div> -->
          <div class="form-group">
            <label class="control-label">Utente</label>
            <input type="text" placeholder="UserName" style="height:40px;text-transform: none!important" v-model="UserName" class="form-control ZMFormRegister">
          </div>
          <div class="form-group">
            <label class="control-label">Password</label>
            <input type="password" placeholder="Password" style="height:40px;text-transform: none!important" v-model="Password" class="form-control ZMFormRegister">
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" v-model="Ricordami"> Ricordati di me
            </label>
          </div>
          <a class="pull-right m-t-xs" style="cursor:pointer" @click="RecuperoPsw = true"><small>Password dimenticata?</small></a>
          <button class="btn btn-primary" @click="OnClickLogin">Entra</button>
          <div class="line line-dashed"></div>
          <p class="text-muted text-center"><small>Do not have an account?</small></p>
          <a href="#/Registrati" class="btn btn-default btn-block">Create an account</a>
        </div>
      </section>
    </div>
  </section>
 </div>
</template>

<script>
import VUEHeader from '@/components/FrameComponentsMultiPurpose/VUEHeader.vue';
import { LOCALSTORAGE, SystemInformation} from '../SystemInformation.js'
import VUEModal from '../components/Slots/VUEModal.vue';

export default 
{
    name: "VUELogin",
    data() 
    {
     return { 
               UserName    : '',
               Password    : '',
              //  PartitaIVA  : '',
               Ricordami   : true,
               RecuperoPsw : false,
               NomeUtente  : ''
            };
    },
    components: 
    {
       'VUEHeader' : VUEHeader ,
        VUEModal
    },
    methods: 
    {
       OnClickLogin()
       {
          var Self = this;
          SystemInformation.AdvQuery.Login(this.UserName,
                                           this.Password,
                                           this.Ricordami,
                                           function(TokenRememberMe)
                                           {
                                             if(TokenRememberMe == undefined)
                                             {
                                                localStorage.removeItem(LOCALSTORAGE.TokenRememberMe);
                                                // localStorage.removeItem(LOCALSTORAGE.TokenPartitaIVA);
                                             }
                                             else 
                                             {
                                               localStorage.setItem(LOCALSTORAGE.TokenRememberMe, TokenRememberMe);
                                              //  localStorage.setItem(LOCALSTORAGE.TokenPartitaIVA, Self.PartitaIVA);
                                             }
                                             SystemInformation.GetUserInformation(function()
                                             {
                                              // if(SystemInformation.UserInformation.Ruolo != RUOLI.Tecnico)
                                              // {
                                                if(SystemInformation.UserInformation.PrimoAccesso != 'F')
                                                  Self.$router.push('/PrimoAccesso')
                                                else
                                                {
                                                    Self.$router.push('/appMainWindow/Dashboard')
                                                }
                                              // }
                                              // else 
                                              // {
                                              //   alert('Non hai i requisiti per eseguire l\'accesso');
                                              // }
                                             },
                                             function(HTTPError,SubHTTPError)
                                             {
                                               SystemInformation.HandleError(HTTPError,SubHTTPError);
                                             });
                                           },
                                           function(HTTPError,SubHTTPError)
                                           {
                                             SystemInformation.HandleError(HTTPError,SubHTTPError);
                                           });
                                          //  this.PartitaIVA);
       },

       AnnullaRecupero()
       {
         this.RecuperoPsw = false
         this.NomeUtente = ''
       },

       ConfermaRecupero()
       {
         this.RecoverPassword(this.AnnullaRecupero)
       },

       RecoverPassword(OnSuccess)
       {
           let Parametri = {NomeUtente : this.NomeUtente}
           SystemInformation.AdvQuery.ExecuteExternalScript('ExtraSendMailRecuperoPsw',Parametri,function(Answer) 
           {
               if(Answer.Risposta == 'MAIL_INVIATA')
               {
                   OnSuccess();
                   alert('Invio mail effettuato con successo')
               }
               else 
               {
                   if(Answer.ErroreMail != undefined)
                      alert('Errore invio mail')
                   if (Answer.MessaggioUtente)
                      alert("L'account selezionato non esiste o non ha alcuna mail collegata")                   
               }
           },
           function(ErrorMessage,DescrErrore)
           {
              alert(ErrorMessage + '[' + DescrErrore + ']')
           });
       },
    },
    beforeMount()
    {
      if(SystemInformation.AdvQuery.CurrentUserLogged)
          if (this.$router.currentRoute.value.fullPath == "/Login")
            SystemInformation.GoToFirstPage()
    }
};
</script>