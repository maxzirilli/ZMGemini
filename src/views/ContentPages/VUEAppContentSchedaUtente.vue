<template>
    <div class="container">      
        <div>  
    
            <div class="col-md-9">
        
                <div>
            
                    <div class="header">
                    </div>
                        <div>
                            <div class="col-md-3">Username: *</div>
                            <div class="col-md-9">
                                <input type    = "text" 
                                       class   = "form-control"
                                       style="text-transform: none!important"
                                       placeholder = "Inserire qui il nome utente"
                                       v-model = "DatiProfilo.USERNAME"
                                />
                            </div>
                        </div>
                        <br>
                        <br>
                        <div>
                            <div class="col-md-3">Ragione Sociale: *</div>
                            <div class="col-md-9">
                                <input type    = "text" 
                                       class   = "form-control"
                                       style="text-transform: none!important"
                                       v-model = "DatiProfilo.RAGIONE_SOCIALE"
                                       placeholder = "Inserire qui la ragione sociale"
                                />
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="form-row">
                            <div class="col-md-3">Email: </div>
                            <div class="col-md-9">
                                <input type    = "text" 
                                       class   = "form-control"
                                       style="text-transform: none!important"
                                       v-model = "DatiProfilo.EMAIL"
                                       placeholder = "Inserire qui l'email (potrà essere modificata in seguito dall'utente)"
                                />
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="form-row">
                            <div class="col-md-3">Telefono: </div>
                            <div class="col-md-9">
                                <input type    = "text" 
                                       class   = "form-control"
                                       v-model = "DatiProfilo.TELEFONO"
                                       placeholder = "Inserire qui il numero di telefono (potrà essere modificato in seguito dall'utente)"
                                />
                            </div>
                        </div>
                        <br>
                        <br>                
                        <div class="form-row">
                            <div class="col-md-3">Ruolo: *</div>
                            <div class="col-md-3">
                                <select class="form-control" v-model="DatiProfilo.ROLE">
                                    <option v-if="RuoloUtente == 'A'" :value="'A'">Amministratore</option>
                                    <option v-for="Ruolo in ObjRuoli" :Key="Ruolo.Id" :value="Ruolo.Id">{{Ruolo.Descrizione}}</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <br>                    
                        
                        <div class="form-row">
                            <br>
                            <div class="col-md-3">Immagine</div>
                            <div class="col-md-9">                                                       
                                <div class="col-md-2">  
                                    <div href="javascript:;" v-if="DatiProfilo.IMMAGINE != ''">
                                        <img style="height:50px" :src="DatiProfilo.IMMAGINE">
                                    </div>
                                    <div v-else>
                                        <img style="height:50px" src="@/assets/images/avatar_default.jpg">
                                    </div>
                                </div>
                                <div class="col-md-3">    
                                    <label class="btn btn-info" type="button" style="cursor:pointer; margin-left:-10px"> 
                                     Carica foto            
                                         <input style="display:none;" type="file" id="inputImmagineUtente" @change="LoadImmagineFromFile(elemento)" accept="image/*">
                                     </label>
                                </div>
                            </div>
                        </div> 
                        
                        <div class="form-row">
                            <br>
                            <div class="col-md-3">
                                <span>* Questi campi sono obbligatori</span>
                            </div>
                            
                            <div class="col-md-7">
                                
                                <div class="input-group" style="float:right">
                                    <button v-bind:disabled="CheckDati" type="button" class="btn btn-success" @click="OnClickConferma" style="width:140%;margin-top:10px; float:right">Conferma</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <button type="button" class="btn btn-danger" @click="OnClickAnnulla" style="width:150%;margin-top:10px;">Annulla</button>
                                </div>
                            </div>
                        </div>  
                </div>    
            </div>       
        </div>
    </div>
</template>
    
    
    
    
    
<script>
    import { SystemInformation,RUOLI } from '@/SystemInformation.js'
    import { TZImageFunct } from '../../../../../../../../Librerie/VUE/ZImageFunct.js'
    
    export default {
        name:'app',
        emits: ['OnClickApriPopup', 'OnClickConfermaModifiche'],
        data()
        {
           return {
                     DatiProfilo     : {},
                     Ruoli           : RUOLI,
                     Modello         : '',
                     ObjRuoli        : [{Id: 'S', Descrizione: 'SuperUtente'},
                                        {Id: 'D', Descrizione: 'Dipendente'}],
                     RuoloUtente     : SystemInformation.UserInformation.Ruolo,
                  }
        },
        methods :
        {
          OnClickAnnulla()
          {
            this.$emit('OnClickApriPopup')
          },
    
          OnClickConferma()
          {
            var ObjQuery = {Operazioni : []}
            var Self = this
            ObjQuery.Operazioni.push(
                            {
                            Query : this.$route.params.pagina == 'nuovo' ? 'Insert' : 'Update',
                            Parametri : {
                                            CHIAVE          : this.$route.params.pagina == 'nuovo' ? undefined : this.$route.params.pagina,
                                            USERNAME        : this.DatiProfilo.USERNAME,
                                            RAGIONE_SOCIALE : this.DatiProfilo.RAGIONE_SOCIALE,
                                            EMAIL           : this.DatiProfilo.EMAIL,
                                            IMMAGINE        : this.DatiProfilo.IMMAGINE,
                                            TELEFONO        : this.DatiProfilo.TELEFONO,  
                                            ROLE            : this.DatiProfilo.ROLE                          
                            }
                        })
            SystemInformation.AdvQuery.PostSQL(this.Modello, ObjQuery, function(){
                                                                                        Self.$emit('OnClickConfermaModifiche')
                                                                                        Self.$router.push(('/appMainWindow/GestioneUtenti'))
                                                                                    }, SystemInformation.HandleError)
          },
    
          EliminaLogo()     
          {         
            var Self = this;
            Self.DatiProfilo.IMMAGINE = '';
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
                  TZImageFunct.ResizeImage(Img,300,300,TypeImg) 
                  Self.DatiProfilo.IMMAGINE = Img.src 
               }
               Img.src = e.target.result
            }
           }
        },
    
        computed :
        {
          CheckDati()
          {
            if (this.DatiProfilo.USERNAME != '' &&
                this.DatiProfilo.ROLE != '' && 
                this.DatiProfilo.RAGIONE_SOCIALE != '')
                return false
            return true
          }
        },
        beforeMount()
        {
            switch (SystemInformation.UserInformation.Ruolo)
            {
                    case this.Ruoli.Amministratore : this.Modello = 'Admin_GestioneUtenti'
                    break
                    case this.Ruoli.SuperUtente    : this.Modello = 'SuperUtente_GestioneUtenti'
                    break
                    default                        : this.Modello = ''
            }
            var Self = this;
            if (this.$route.params.pagina != 'nuovo')
            {
                SystemInformation.AdvQuery.GetSQL(this.Modello,{CHIAVE : this.$route.params.pagina},
                function(Results)
                {
                    let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Dettaglio")
                    if(ArrayInfo != undefined)
                    {
                        Self.DatiProfilo = ArrayInfo[0]
                    }                  
                    else SystemInformation.HandleError('Errore nel modello utenti')
                },
                SystemInformation.HandleError, 'SelectDettaglio');   
            }
            else this.DatiProfilo = {
                                        CHIAVE : undefined,
                                        IMMAGINE : '',
                                        RAGIONE_SOCIALE : '',
                                        ROLE : 'D',
                                        USERNAME : '',
                                        EMAIL : '',
                                        TELEFONO : '' 
                                    }                                             
        },       
        
    }
</script>   