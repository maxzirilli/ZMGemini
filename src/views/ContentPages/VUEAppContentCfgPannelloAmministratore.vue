<template>
  <VUEConfirm :Popup="PopupCfgAmministratore" :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>
  <div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
      <a v-if="CanRecord()" class="btn btn-s-md btn-success" style="margin-right:20px" @click="Registra()">Conferma</a>
      <a class="btn btn-s-md btn-danger" @click="PopupCfgAmministratore = true">Annulla</a>
  </div>
  <div class="ZMCorpoSchedeDati">
    <header class="panel-heading bg-light" >
        <ul class="nav nav-tabs nav-justified">
          <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
              :class="{ active : ATab.Id == Tabs.ActiveTab }">
              <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}</a>
          </li>
         
        </ul>
    </header>

    <div v-if="Tabs.ActiveTab == 'MySond'">
      <div class="col-md-4"></div>
      <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
       <div v-if="!Dati.InvioManuale">
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Codice azienda</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.CodAzienda" placeholder="Codice azienda"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Username</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.Username" placeholder="Username"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Password</label>
            <input style="text-transform: none!important" type="password" class="form-control" v-model="Dati.Password" placeholder="Password"/>
        </div>
       </div>
        <div style="float:left;width:100%;">
          <div style="float: left;padding-top:5px;padding-bottom:5px">
            <input type="checkbox" v-model="Dati.InvioManuale" @change="OnInvioManualeChange">
          </div>
          <div style="float: left;padding-top:5px;padding-bottom:5px;margin-left:6px;margin-right: 10px">
            <label style="font-weight: bold;margin-bottom: 0px;margin-top: 0px;font-size:14px;">Gestione manuale invio fatture.</label>
          </div>
        </div>
      </div>
      <div class="col-md-4"></div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>

    <div v-if="Tabs.ActiveTab == 'ModelloEMail'">
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div class="ZMSeparatoreScheda" 
                style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#2596be">
                Configurazioni email dopo invio Sdi
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
          <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailDopoSdi.OggettoEmail" placeholder="Oggetto email"/>
          </div>
          <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Corpo email</label>
              <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.MailDopoSdi.CorpoEmail" placeholder="Corpo email"/>
          </div>
        </div>  
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div class="ZMSeparatoreScheda" 
                style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#2596be">
                Configurazioni email solleciti
        </div>
        <div style="float:left;width:100%">
          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.OggettoEmailPrimoSollecito" placeholder="Oggetto email"/>
            </div>
            <label style="font-size:14px;font-weight: bold;">Avviso primo livello</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.PrimoLivello" placeholder = "Primo Livello"/>
            <label v-if="!Dati.ConfigurazioneMailSolleciti.PrimoLivello.includes('[TOKEN_FATTURA]')" class="ZMFormLabelError">Questo campo deve includere la stringa [TOKEN_FATTURA]</label>
          </div>

          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.OggettoEmailSecondoSollecito" placeholder="Oggetto email"/>
             </div>
            <label style="font-size:14px;font-weight: bold;">Avviso secondo livello</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.SecondoLivello" placeholder = "Secondo Livello"></textarea>
            <label v-if="!Dati.ConfigurazioneMailSolleciti.SecondoLivello.includes('[TOKEN_FATTURA]')" class="ZMFormLabelError">Questo campo deve includere la stringa [TOKEN_FATTURA]</label>
          </div>

          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.OggettoEmailTerzoSollecito" placeholder="Oggetto email"/>
            </div>
            <label style="font-size:14px;font-weight: bold;">Avviso terzo livello</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioneMailSolleciti.TerzoLivello" placeholder = "Terzo Livello"/>
            <label v-if="!Dati.ConfigurazioneMailSolleciti.TerzoLivello.includes('[TOKEN_FATTURA]')" class="ZMFormLabelError">Questo campo deve includere la stringa [TOKEN_FATTURA]</label>
          </div>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div class="ZMSeparatoreScheda" 
                style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#2596be">
                Configurazioni email preventivi
        </div>
        <div style="float:left;width:100%">
          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivo" placeholder="Oggetto email preventivo"/>
            </div>
            <label style="font-size:14px;font-weight: bold;">Corpo Email</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivo" placeholder = "Corpo email Preventivo"/>
          </div>

          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email preventivi da anomalie</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivoDaAnomalie" placeholder="Oggetto email preventivi da anomalie"/>
             </div>
            <label style="font-size:14px;font-weight: bold;">Corpo Email preventivi da anomalie</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie" placeholder = "Corpo Email preventivi da anomalie"></textarea>
            <label v-if="!Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie.includes('[TOKEN_INDIRIZZO_FILIALE]')" class="ZMFormLabelError">Questo campo deve includere la stringa [TOKEN_INDIRIZZO_FILIALE]</label>
          </div>

        </div>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>
       <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div class="ZMSeparatoreScheda" 
                style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#2596be">
                Configurazioni invio email fatture dall'albero
        </div>
        <div style="float:left;width:100%">
          <div class="col-md-4"></div>
          <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
            <div style="float:left;width:100%">
              <label style="font-size:14px;font-weight:bold;">Oggetto email fatture</label>
              <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.ConfigurazioniMailFatture.OggettoEmailFatture" placeholder="Oggetto email fatture"/>
             </div>
            <label style="font-size:14px;font-weight: bold;">Corpo email fatture</label>
            <textarea style="resize:none;text-transform: none!important; height:250px" type="text" class="form-control" v-model="Dati.ConfigurazioniMailFatture.CorpoEmailFatture" placeholder = "Corpo Email fatture"></textarea>
          </div>
        </div>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>
       <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>


    <div  v-if="Tabs.ActiveTab == 'ConfigEMailAvvisi'">
      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Host</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.Host" placeholder="Host"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Port</label>
            <input min="0" step="1" type="text" class="form-control" v-model="Dati.MailAvvisi.Port" placeholder="Port"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Secure</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.Secure" placeholder="Secure"/>
        </div>
        <div style="float:left;width:100%;">
          <div style="float: left;padding-top:5px;padding-bottom:5px">
            <input type="checkbox" v-model="Dati.MailAvvisi.Auth">
          </div>
          <div style="float: left;padding-top:5px;padding-bottom:5px;margin-left:6px;margin-right: 10px">
            <label style="font-weight: bold;margin-bottom: 0px;margin-top: 0px;font-size:14px;">Auth</label>
          </div>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Account</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.User" placeholder="User"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Password</label>
            <input type="password" class="form-control" v-model="Dati.MailAvvisi.PasswordSmtp" placeholder="Password"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from mail</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.FromMail" placeholder="SMTP from mail"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from name</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.FromName" placeholder="SMTP from name"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">IMAP configuration avvisi</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailAvvisi.ImapAvvisi" placeholder="Imap solleciti"/>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>
    <!--ADD-->
    <div  v-if="Tabs.ActiveTab == 'ConfigEmailSolleciti'">
      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Host</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.Host" placeholder="Host"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Port</label>
            <input min="0" step="1" type="text" class="form-control" v-model="Dati.MailSolleciti.Port" placeholder="Port"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Secure</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.Secure" placeholder="Secure"/>
        </div>
        <div style="float:left;width:100%;">
          <div style="float: left;padding-top:5px;padding-bottom:5px">
            <input type="checkbox" v-model="Dati.MailSolleciti.Auth">
          </div>
          <div style="float: left;padding-top:5px;padding-bottom:5px;margin-left:6px;margin-right: 10px">
            <label style="font-weight: bold;margin-bottom: 0px;margin-top: 0px;font-size:14px;">Auth</label>
          </div>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Account</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.User" placeholder="User"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Password</label>
            <input type="password" class="form-control" v-model="Dati.MailSolleciti.PasswordSmtp" placeholder="Password"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from mail</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.FromMail" placeholder="SMTP from mail"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from name</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.FromName" placeholder="SMTP from name"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">IMAP configuration solleciti</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailSolleciti.ImapSolleciti" placeholder="Imap avvisi"/>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>
    <!--ADD-->


    <div  v-if="Tabs.ActiveTab == 'ConfigEMailFatture'">
      <div class="col-md-12" style="padding-top:1%;padding-bottom:1%">
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Host</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.Host" placeholder="Host"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Port</label>
            <input min="0" step="1" type="text" class="form-control" v-model="Dati.MailFatture.Port" placeholder="Port"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Secure</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.Secure" placeholder="Secure"/>
        </div>
        <div style="float:left;width:100%;">
          <div style="float: left;padding-top:5px;padding-bottom:5px">
            <input type="checkbox" v-model="Dati.MailFatture.Auth">
          </div>
          <div style="float: left;padding-top:5px;padding-bottom:5px;margin-left:6px;margin-right: 10px">
            <label style="font-weight: bold;margin-bottom: 0px;margin-top: 0px;font-size:14px;">Auth</label>
          </div>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Account</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.User" placeholder="User"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">Password</label>
            <input type="password" class="form-control" v-model="Dati.MailFatture.PasswordSmtp" placeholder="Password"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from mail</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.FromMail" placeholder="SMTP from mail"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">SMTP from name</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.FromName" placeholder="SMTP from name"/>
        </div>
        <div style="float:left;width:100%">
            <label style="font-weight: bold;">IMAP configuration fatture</label>
            <input style="text-transform: none!important" type="text" class="form-control" v-model="Dati.MailFatture.ImapFatture" placeholder="Imap fatture"/>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>


    <div v-if="Tabs.ActiveTab == 'ConfigurazioniProgramma'">   
      <div class="col-md-4"></div>
      <div class="col-md-4" style="padding-top:1%;padding-bottom:10px">
        <div style="float:left;width:100%">
            <label style="font-size:14px;">Stile carattere
               <select style="float:left;width:100%;" class="form-control" v-model="Dati.ConfigurazioniStileCarattere">
                  <option :value="OpzioniStileGrafico.NonModificare">-</option>
                  <option :value="OpzioniStileGrafico.TuttoMaiuscolo">Tutto maiuscolo</option>           
                  <option :value="OpzioniStileGrafico.TuttoMinuscolo">Tutto minuscolo</option>           
               </select>
            </label>
        </div>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-4"></div>
      <div class="col-md-4" style="padding-top:1%; padding-bottom:10px;">
        <div style="float:left; width:100%; display: flex; align-items: center; gap: 10px;">
          <label style="font-size:14px; margin: 0;">Generazione preventivo su ogni collaudo</label>
          <input style="margin-bottom:3px" type="checkbox" v-model="Dati.GenerazionePreventivoCollaudo"/>
        </div>
      </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-4"></div>
      <div class="col-md-4" style="padding-top:1%;padding-bottom:1%">
      </div>
      
      

        <div class="ZMSeparatoreFiltri">&nbsp;</div>
        
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4" style="padding-top:1%;padding-bottom:10px">
          <div style="float:left;width:100%">
            <label style="font-size:14px;">Magazzino di default
              <select style="float:left;width:100%;" class="form-control" v-model="Dati.ID_MAGAZZINO">
                <option :value="0">-</option>
                <option v-for="Magazzino in ListaMagazzini" :key="Magazzino.CHIAVE" :value="Magazzino.CHIAVE">
                  {{ Magazzino.DESCRIZIONE }}
                </option>
              </select>
            </label>
          </div>
        </div>

      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <div class="col-md-12"></div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
    </div>
  </div>
</template>

<script>
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { TSchedaGenericaCharCase } from '@/../../../../../../Librerie/VUE/ZSchedaGenerica.js'

// import { TZImageFunct } from '@/../../../../../../Librerie/VUE/ZImageFunct.js'

export default 
{
  props : ['Altezza','NomeModello'],
  components : { VUEConfirm},

  data()
  {
     return { 
              Tabs  :     {
                              ActiveTab : 'MySond',
                              Tabs      : [
                                            {
                                              Caption : 'Canale fatturazione elettronica',
                                              Id      : 'MySond'
                                            },
                                            {
                                              Caption : 'Configurazioni email avvisi',
                                              Id      : 'ConfigEMailAvvisi'
                                            },
                                            {
                                              Caption : 'Configurazioni email solleciti',
                                              Id      : 'ConfigEmailSolleciti'
                                            },
                                            {
                                              Caption : 'Configurazioni email fatture',
                                              Id      : 'ConfigEMailFatture'
                                            },
                                            {
                                              Caption : 'Configurazioni programma',
                                              Id      : 'ConfigurazioniProgramma'
                                            },
                                            {
                                              Caption : 'Modello email',
                                              Id      : 'ModelloEMail'
                                            }

                                          ]
                          },
              Dati :      {
                              Username                              : '',
                              Password                              : '',
                              CodAzienda                            : '',
                              InvioManuale                          : '', 
                                  MailAvvisi              : {           
                                                              Host                    : '',
                                                              Port                    : '',
                                                              Secure                  : '',
                                                              Auth                    : '',
                                                              User                    : '',
                                                              PasswordSmtp            : '',
                                                              FromMail                : '',
                                                              FromName                : '',
                                                              ConfigurazioniSolleciti : '',
                                                              ImapAvvisi              : '',
                                                            },
                                  MailFatture             : {           
                                                              Host                    : '',
                                                              Port                    : '',
                                                              Secure                  : '',
                                                              Auth                    : '',
                                                              User                    : '',
                                                              PasswordSmtp            : '',
                                                              FromMail                : '',
                                                              FromName                : '',
                                                              ImapFatture             : '',
                                                            },
                                 MailSolleciti            : {           
                                                              Host                    : '',
                                                              Port                    : '',
                                                              Secure                  : '',
                                                              Auth                    : '',
                                                              User                    : '',
                                                              PasswordSmtp            : '',
                                                              FromMail                : '',
                                                              FromName                : '',
                                                              ImapSolleciti           : '',
                                                            },
                                  MailDopoSdi             : {
                                                              OggettoEmail            : '',
                                                              CorpoEmail              : '',
                                                            },
                              ConfigurazioneMailSolleciti : {
                                                             PrimoLivello                 : '' ,
                                                             SecondoLivello               : '',
                                                             TerzoLivello                 : '',
                                                             OggettoEmailPrimoSollecito   : '',
                                                             OggettoEmailSecondoSollecito : '',
                                                             OggettoEmailTerzoSollecito   : '',
                                                            },
                              ConfigurazioniMailPreventivi: {
                                                             OggettoEmailPreventivo          : '',
                                                             OggettoEmailPreventivoDaAnomalie: '',
                                                             CorpoEmailPreventivo            : '',
                                                             CorpoEmailPreventivoDaAnomalie  : '',
                                                            },
                              ConfigurazioniMailFatture:    {
                                                             OggettoEmailFatture  : '',
                                                             CorpoEmailFatture    : '',
                                                            },
                              ConfigurazioniStileCarattere : TSchedaGenericaCharCase.NonModificare,
                              GenerazionePreventivoCollaudo : true,

                              ID_MAGAZZINO                          : 0,
                          },
              Snapshot    : '',

              PopupCfgAmministratore          : false,
              OpzioniStileGrafico             : TSchedaGenericaCharCase,
              ListaMagazzini                  : SystemInformation.Configurazioni.Magazzini,
              NomeProgramma                   : NOME_PROGRAMMA
     }
  },

  computed :
  {
    ModificheDaApplicare : 
    {
      get()
      {
        if(this.Snapshot != '')
          return this.Snapshot != JSON.stringify(this.Dati)
        return false
      }
    },
  
  },

  methods:
  {
    Registra()
    {
      var Self = this;
      var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                               Query     : "Update", 
                               Parametri : {
                                               USERNAME_MY_SOND          : TSchedaGenerica.PrepareForRecordString(this.Dati.Username, true),
                                               PASSWORD_MY_SOND          : TSchedaGenerica.PrepareForRecordString(this.Dati.Password, true),
                                               COD_AZIENDA_MY_SOND       : TSchedaGenerica.PrepareForRecordString(this.Dati.CodAzienda, true),
                                               SMTP_HOST_FATTURE         : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.Host, true),
                                               SMTP_PORT_FATTURE         : TSchedaGenerica.PrepareForRecordInteger(this.Dati.MailFatture.Port),
                                               SMTP_SECURE_FATTURE       : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.Secure, true),
                                               SMTP_AUTH_FATTURE         : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.MailFatture.Auth),
                                               SMTP_USER_FATTURE         : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.User, true),
                                               SMTP_PASSWORD_FATTURE     : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.PasswordSmtp, true),
                                               SMTP_FROM_MAIL_FATTURE    : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.FromMail, true), 
                                               SMTP_FROM_NAME_FATTURE    : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.FromName, true),
                                               SMTP_HOST_AVVISI          : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.Host, true),
                                               SMTP_PORT_AVVISI          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.MailAvvisi.Port),
                                               SMTP_SECURE_AVVISI        : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.Secure, true),
                                               SMTP_AUTH_AVVISI          : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.MailAvvisi.Auth),
                                               SMTP_USER_AVVISI          : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.User, true),
                                               SMTP_PASSWORD_AVVISI      : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.PasswordSmtp, true),
                                               SMTP_FROM_MAIL_AVVISI     : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.FromMail, true), 
                                               SMTP_FROM_NAME_AVVISI     : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.FromName, true),
                                               IMAP_AVVISI               : TSchedaGenerica.PrepareForRecordString(this.Dati.MailAvvisi.ImapAvvisi,true),
                                               IMAP_FATTURE              : TSchedaGenerica.PrepareForRecordString(this.Dati.MailFatture.ImapFatture,true),
                                               SMTP_HOST_SOLLECITI         : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.Host, true),
                                               SMTP_PORT_SOLLECITI         : TSchedaGenerica.PrepareForRecordInteger(this.Dati.MailSolleciti.Port),
                                               SMTP_SECURE_SOLLECITI       : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.Secure, true),
                                               SMTP_AUTH_SOLLECITI         : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.MailSolleciti.Auth),
                                               SMTP_USER_SOLLECITI         : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.User, true),
                                               SMTP_PASSWORD_SOLLECITI     : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.PasswordSmtp, true),
                                               SMTP_FROM_MAIL_SOLLECITI    : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.FromMail, true), 
                                               SMTP_FROM_NAME_SOLLECITI    : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.FromName, true),
                                               IMAP_SOLLECITI              : TSchedaGenerica.PrepareForRecordString(this.Dati.MailSolleciti.ImapSolleciti,true)
                                           }
                             });
      ObjQuery.Operazioni.push({
                                  Query     : "UpdateImpostazioniProgramma",
                                  Parametri : {
                                                  STILE_STRINGHE                                : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniStileCarattere),
                                                  OGGETTO_EMAIL_DOPO_SDI                        : TSchedaGenerica.PrepareForRecordString(Self.Dati.MailDopoSdi.OggettoEmail, true),
                                                  CORPO_EMAIL_DOPO_SDI                          : TSchedaGenerica.PrepareForRecordString(Self.Dati.MailDopoSdi.CorpoEmail, true),
                                                  CONFIGURAZIONE_MAIL_SOLLECITI_PRIMO_LIVELLO   : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.PrimoLivello, true),
                                                  CONFIGURAZIONE_MAIL_SOLLECITI_SECONDO_LIVELLO : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.SecondoLivello, true),
                                                  CONFIGURAZIONE_MAIL_SOLLECITI_TERZO_LIVELLO   : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.TerzoLivello, true),
                                                  OGGETTO_EMAIL_PRIMO_SOLLECITO                 : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailPrimoSollecito, true),
                                                  OGGETTO_EMAIL_SECONDO_SOLLECITO               : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailSecondoSollecito, true),
                                                  OGGETTO_EMAIL_TERZO_SOLLECITO                 : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailTerzoSollecito, true),
                                                  OGGETTO_EMAIL_PREVENTIVI                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivo,true),
                                                  CORPO_EMAIL_PREVENTIVI                        : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivo,true),
                                                  OGGETTO_EMAIL_PREVENTIVI_DA_ANOMALIE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivoDaAnomalie,true),
                                                  CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE            : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie,true),
                                                  OGGETTO_EMAIL_FATTURE                         : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailFatture.OggettoEmailFatture,true),
                                                  CORPO_EMAIL_FATTURE                           : TSchedaGenerica.PrepareForRecordString(Self.Dati.ConfigurazioniMailFatture.CorpoEmailFatture,true),
                                                  GENERAZIONE_PREVENTIVO_COLLAUDO               : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.GenerazionePreventivoCollaudo),
                                                  MAGAZZINO                                     : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.ID_MAGAZZINO),
                                                  ESPORTAZIONE_MANUALE_FATTURE                  : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.InvioManuale)
                                              } 
                             }); 


      SystemInformation.AdvQuery.PostSQL('ConfigurazioniAmministratore', ObjQuery, function()
      {
          SystemInformation.GetConfigurazioni(function()
          {
            ObjQuery = {};
            // Self.Annulla();
            Self.CaricaDatiMySond();
          })
      },
      function(HTTPError,SubHTTPError,Response)
      {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
    },

    CanRecord()
    {
      return(
              this.Dati.ConfigurazioneMailSolleciti.PrimoLivello.includes('[TOKEN_FATTURA]') &&
              this.Dati.ConfigurazioneMailSolleciti.SecondoLivello.includes('[TOKEN_FATTURA]') &&
              this.Dati.ConfigurazioneMailSolleciti.TerzoLivello.includes('[TOKEN_FATTURA]') &&
              this.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie.includes('[TOKEN_INDIRIZZO_FILIALE]') &&
              this.Dati.ConfigurazioneMailSolleciti.PrimoLivello.trim() != '' &&
              this.Dati.ConfigurazioneMailSolleciti.PrimoLivello.trim() != '' &&
              this.Dati.ConfigurazioneMailSolleciti.PrimoLivello.trim() != '' &&
              this.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivo.trim() != '' &&
              this.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivo.trim() != '' &&
              this.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivoDaAnomalie.trim() != '' &&
              this.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie.trim() != '' &&
              this.Dati.ConfigurazioniMailFatture.OggettoEmailFatture.trim() != '' &&
              this.Dati.ConfigurazioniMailFatture.CorpoEmailFatture.trim() != '' 
            );
    },

    ConfermaElimina()
    {
       this.CaricaDatiMySond()
       this.AnnullaElimina()
    },

    AnnullaElimina()
    {
       this.PopupCfgAmministratore = false
       this.CaricaDatiMySond()
    },

    CaricaDatiMySond()
    {
      var Self = this;
      SystemInformation.AdvQuery.GetSQL("ConfigurazioniAmministratore",{},
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Configurazioni");
                                          let ArrayInfoImpostazioni = SystemInformation.AdvQuery.FindResults(Results,"ImpostazioniProgramma");
                                          if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                          {
                                            Self.Dati.Username                  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].USERNAME_MY_SOND, true)                          
                                            Self.Dati.Password                  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PASSWORD_MY_SOND, true)                              
                                            Self.Dati.CodAzienda                = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_AZIENDA_MY_SOND, true) 
                                            Self.Dati.MailAvvisi.Host           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_HOST_AVVISI, true)
                                            Self.Dati.MailAvvisi.Port           = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SMTP_PORT_AVVISI)
                                            Self.Dati.MailAvvisi.Secure         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_SECURE_AVVISI, true)
                                            Self.Dati.MailAvvisi.Auth           = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].SMTP_AUTH_AVVISI)
                                            Self.Dati.MailAvvisi.User           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_USER_AVVISI, true)
                                            Self.Dati.MailAvvisi.PasswordSmtp   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_PASSWORD_AVVISI, true)
                                            Self.Dati.MailAvvisi.FromMail       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_MAIL_AVVISI, true)
                                            Self.Dati.MailAvvisi.FromName       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_NAME_AVVISI, true)
                                            Self.Dati.MailFatture.Host          = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_HOST_FATTURE, true)
                                            Self.Dati.MailFatture.Port          = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SMTP_PORT_FATTURE)
                                            Self.Dati.MailFatture.Secure        = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_SECURE_FATTURE, true)
                                            Self.Dati.MailFatture.Auth          = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].SMTP_AUTH_FATTURE)
                                            Self.Dati.MailFatture.User          = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_USER_FATTURE, true)
                                            Self.Dati.MailFatture.PasswordSmtp  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_PASSWORD_FATTURE, true)
                                            Self.Dati.MailFatture.FromMail      = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_MAIL_FATTURE, true)
                                            Self.Dati.MailFatture.FromName      = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_NAME_FATTURE, true)
                                            Self.Dati.MailAvvisi.ImapAvvisi     = TSchedaGenerica.DisponiFromString(ArrayInfo[0].IMAP_AVVISI,true)
                                            Self.Dati.MailFatture.ImapFatture   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].IMAP_FATTURE,true)

                                            Self.Dati.MailSolleciti.Host           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_HOST_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.Port           = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SMTP_PORT_SOLLECITI)
                                            Self.Dati.MailSolleciti.Secure         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_SECURE_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.Auth           = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].SMTP_AUTH_SOLLECITI)
                                            Self.Dati.MailSolleciti.User           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_USER_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.PasswordSmtp   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_PASSWORD_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.FromMail       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_MAIL_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.FromName       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].SMTP_FROM_NAME_SOLLECITI, true)
                                            Self.Dati.MailSolleciti.ImapSolleciti  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].IMAP_SOLLECITI,true)
                                          }

                                          if(ArrayInfoImpostazioni != undefined)
                                          {
                                            Self.Dati.ConfigurazioniStileCarattere                                  = ArrayInfoImpostazioni[0].STILE_STRINGHE  
                                            Self.Dati.MailDopoSdi.OggettoEmail                                      = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_DOPO_SDI, true)
                                            Self.Dati.MailDopoSdi.CorpoEmail                                        = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CORPO_EMAIL_DOPO_SDI, true)
                                            Self.Dati.ConfigurazioneMailSolleciti.PrimoLivello                      = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CONFIGURAZIONE_MAIL_SOLLECITI_PRIMO_LIVELLO, true) 
                                            Self.Dati.ConfigurazioneMailSolleciti.SecondoLivello                    = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CONFIGURAZIONE_MAIL_SOLLECITI_SECONDO_LIVELLO, true)
                                            Self.Dati.ConfigurazioneMailSolleciti.TerzoLivello                      = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CONFIGURAZIONE_MAIL_SOLLECITI_TERZO_LIVELLO, true)
                                            Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailPrimoSollecito        = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_PRIMO_SOLLECITO,true)
                                            Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailSecondoSollecito      = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_SECONDO_SOLLECITO ,true)         
                                            Self.Dati.ConfigurazioneMailSolleciti.OggettoEmailTerzoSollecito        = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_TERZO_SOLLECITO, true)
                                            Self.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivo           = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_PREVENTIVI,true)
                                            Self.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivo             = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CORPO_EMAIL_PREVENTIVI,true)
                                            Self.Dati.ConfigurazioniMailPreventivi.OggettoEmailPreventivoDaAnomalie = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_PREVENTIVI_DA_ANOMALIE,true)
                                            Self.Dati.ConfigurazioniMailPreventivi.CorpoEmailPreventivoDaAnomalie   = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CORPO_EMAIL_PREVENTIVI_DA_ANOMALIE,true)
                                            Self.Dati.ConfigurazioniMailFatture.OggettoEmailFatture                 = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].OGGETTO_EMAIL_FATTURE,true)
                                            Self.Dati.ConfigurazioniMailFatture.CorpoEmailFatture                   = TSchedaGenerica.DisponiFromString(ArrayInfoImpostazioni[0].CORPO_EMAIL_FATTURE,true)
                                            Self.Dati.GenerazionePreventivoCollaudo                                 = TSchedaGenerica.DisponiFromBoolean(ArrayInfoImpostazioni[0].GENERAZIONE_PREVENTIVO_COLLAUDO)
                                            Self.Dati.ID_MAGAZZINO                                                  = TSchedaGenerica.DisponiFromInteger(ArrayInfoImpostazioni[0].MAGAZZINO)
                                            Self.Dati.InvioManuale                                                  = TSchedaGenerica.DisponiFromBoolean(ArrayInfoImpostazioni[0].ESPORTAZIONE_MANUALE_FATTURE) 
                                          }
                                          Self.Snapshot = JSON.stringify(Self.Dati);  
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        });
    },

    // Annulla()
    // {
    //    var Self = this;
    //    SystemInformation.AdvQuery.GetSQL('Testi',{},
    //                                      function(Results)
    //                                      {
    //                                        ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"TuttiTesti");
                                           
    //                                        Self.Snapshot = JSON.stringify(Self.Dati)                                        
    //                                      },
    //                                      function(HTTPError,SubHTTPError,Response)
    //                                      {
    //                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
    //                                      });
    // },

    OnInvioManualeChange()
    {
     if(this.Dati.InvioManuale)
     {
      this.Dati.CodAzienda = '';
      this.Dati.Username   = '';
      this.Dati.Password   = '';
     }
    }
  },

  beforeMount() 
  {
    this.ActiveTab = 'MySond'
    this.CaricaDatiMySond();
    // this.Annulla();
  },
}
</script>