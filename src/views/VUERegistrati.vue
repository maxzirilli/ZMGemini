<template>
  <div>
  <VUEHeader/>
    <section id="content" class="m-t-lg wrapper-md animated fadeInDown bg-dark"  :style="{'min-height' : AltezzaSfondo + 'px'}">
      <div class="container aside-xxl">
        <section class="panel panel-default m-t-lg bg-white">
          <header class="panel-heading text-center">
            <strong>Contattaci</strong>
          </header>
          <div class="panel-body wrapper-lg" style="padding-top:0px">
            <div class="m-t-sm" style="padding-left:10px;padding-right:5px;padding-bottom:10px;text-align:-webkit-center;font-size:15px;text-align: center;">
              <br/>
              <br/>
               Contattaci in modo da organizzare un incontro dal vivo o una call se siamo troppo distanti
              <br/><br/>
               Durante l'incontro possiamo valutare le Vostre necessità e vedere se è necessario aggiungere qualche opzione al programma per venire incontro alle vostre esigenze.
              <br/><br/>
               Di seguito trovi tutti i nostri contatti
              <br/><br/>
                <a href="https://t.me/+393474526664"><img src="img/Telegram.png"></a>
                <a href="tel:+393474526664"><img src="img/Telefono.png"></a>
                <a href="https://wa.me/+393474526664"><img src="img/Whatsapp.png"></a>
              <br/>
            </div>
            <div class="line line-dashed"></div>
            <p class="text-muted text-center"><small>Possiedi già un account?</small></p>
            <a href="#/login" class="btn btn-default btn-block">Login</a>
          </div>
        </section>
      </div>
    </section>
 </div>
</template>

<script>
import VUEHeader from '../components/FrameComponentsMultiPurpose/VUEHeader.vue';
// import { TZGenericFunct } from '../../../../../../../Librerie/VUE/ZGenericFunct.js'
import { SystemInformation } from '@/SystemInformation';

export default 
{
    name: "VUERegistrati",
    setup() 
    {
      const login = function()
      {
        event('login', { method: 'Google' })
      }
      return {
        login
      }
    },
    
    data() 
    {
     return { 
              Generalita        : {
                                      Nome     : '',
                                      Cognome  : '',
                                      Telefono : '',
                                      Email    : ''
                                   },
              AltezzaSfondo     : screen.height
            };
    },
    components: 
    {
       'VUEHeader'  : VUEHeader
    },
    methods: 
    {
        CanSend()
        {
          return this.Generalita.Nome.trim()     != '' &&
                this.Generalita.Cognome.trim()  != '' &&
                this.Generalita.Telefono.trim() != '' &&               
                this.Generalita.Email.trim()    != ''               
        },

        OnClickInviaEmailIscrizione()
        {
          if(this.CanSend())
          {
            let Parametri = this.Generalita
            let Self = this
            SystemInformation.AdvQuery.ExecuteExternalScript('RegistrazioneProgettoInvioMail',Parametri,function(Answer) 
            {
                if(Answer.Risposta == 'MAIL_INVIATA')
                {
                    alert('Invio mail effettuato con successo')
                    Self.Generalita  = {
                                          Nome     : '',
                                          Cognome  : '',
                                          Telefono : '',
                                          Email    : ''
                                      }
                    Self.OpenMenuATendina = false
                }
                else 
                {
                    if(Answer.ErroreMail != undefined)
                      alert('Errore invio mail')
                }
            },
            function(ErrorMessage,DescrErrore)
            {
              alert(ErrorMessage + '[' + DescrErrore + ']')
            });
          }
          else alert('Inserisci tutti i parametri')
        }
    },

    computed:
    {
      //  Mobile : 
      //  {
      //     get() 
      //     {
      //       return TZGenericFunct.IsClientMobile()
      //     }
      //  }
    },
};
</script>