<template>
  <div>
  <VUEHeader/>
  <section id="content" class="m-t-lg wrapper-md animated fadeInUp bg-dark" @keydown.enter = "OnClickLogin">       
    <div class="container aside-xxl">      
      <section class="panel panel-default bg-white m-t-lg">
        <header class="panel-heading text-center">
          <strong>Sign in</strong>
        </header>
         <VUEModal v-if="RecuperoPsw" :PathLogo="require('@/assets/images/LogoGemini2.png')"
                                      :Programma="NomeProgramma"
                                      :Titolo="'Recupero Password'" :Altezza="'200px'" :Larghezza="'800px'"
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
          <div class="form-group">
            <label class="control-label">Utente</label>
            <input type="text" placeholder="UserName" style="height:40px;text-transform: none!important" v-model="UserName" class="form-control ZMFormRegister">
          </div>
          <div class="form-group">
            <label class="control-label">Password</label>
             <div class="input-group">
              <input :type="PasswordVisibile ? 'text' : 'password'" placeholder="Password" style="height:40px;text-transform: none!important" v-model="Password" class="form-control ZMFormRegister">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default" style="height:40px"
                        :title="PasswordVisibile ? 'Nascondi password' : 'Mostra password'"
                        :aria-label="PasswordVisibile ? 'Nascondi password' : 'Mostra password'"
                        :aria-pressed="PasswordVisibile"
                        @keydown.enter.stop
                        @click="PasswordVisibile = !PasswordVisibile">
                  <i class="fa" :class="PasswordVisibile ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
              </span>
            </div>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" v-model="Ricordami"> Ricordati di me
            </label>
          </div>
          <a class="pull-right m-t-xs" style="cursor:pointer" @click="RecuperoPsw = true"><small>Password dimenticata?</small></a>
          <button :disabled="OnTryLogin" class="btn btn-primary" @click="OnClickLogin">Entra</button>
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
import { LOCALSTORAGE, SystemInformation, NOME_PROGRAMMA} from '../SystemInformation.js'
import VUEModal from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';

export default 
{
    name: "VUELogin",
    data() 
    {
     return { 
               UserName         : '',
               Password         : '',
               Ricordami        : true,
               OnTryLogin       : false,
               RecuperoPsw      : false,
               NomeUtente       : '',
               NomeProgramma    : NOME_PROGRAMMA,
               PasswordVisibile : false,
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
          if(this.OnTryLogin)
            return;

          var Self = this;
          this.OnTryLogin = true;
          SystemInformation.AdvQuery.Login(this.UserName,
                                           this.Password,
                                           this.Ricordami,
                                           function(TokenRememberMe)
                                           {
                                             if(SystemInformation.DeveloperMode)
                                             {
                                               if(TokenRememberMe == undefined)
                                               {
                                                 localStorage.removeItem(LOCALSTORAGE.TokenRememberMe);
                                               }
                                               else
                                               {
                                                 localStorage.setItem(LOCALSTORAGE.TokenRememberMe, TokenRememberMe);
                                               }
                                             }
                                             SystemInformation.GetUserInformation(function()
                                             {
                                                if(SystemInformation.UserInformation.PrimoAccesso != 'F')
                                                  Self.$router.push('/PrimoAccesso')
                                                else
                                                {
                                                    Self.$router.push('/appMainWindow/Dashboard')
                                                }
                                               Self.OnTryLogin = false;
                                             },
                                             function(HTTPError,SubHTTPError)
                                             {
                                               Self.OnTryLogin = false;
                                               SystemInformation.HandleError(HTTPError,SubHTTPError);
                                             });
                                           },
                                           function(HTTPError,SubHTTPError)
                                           {
                                             Self.OnTryLogin = false;
                                             SystemInformation.HandleError(HTTPError,SubHTTPError);
                                           },
                                           undefined,
                                           !SystemInformation.DeveloperMode);
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
