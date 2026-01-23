<template>
 <div class="container">        
     <div class="registration-block" style="margin-top:10%">
         <div class="block block-transparent">
             <div class="head">
                 <div class="user">                           
                 </div>
             </div>             
             <div class="content controls npt">
                 <div class="form-row" style="margin-left:30px; font-size:15px; font-family:inherit;">
                  <p style="font:sans-serif">Benvenuto, questo è il tuo primo accesso, dovrai reimpostare la password.</p>
                  <p style="margin-bottom:10px">La nuova password deve contenere: </p>
                  <ul>
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
                   <li>Le due password devono essere uguali
                     <span v-if="CheckRipetiPassword" class="fa fa-check" style="color:#33ff33; font-size: 15px"></span>
                     <span v-if="Password != '' && !CheckRipetiPassword"  class="fa fa-times" style="color:#cb4d4d; font-size: 15px"></span>
                   </li>
                  </ul>
                  
                  <p style="margin-top:10px">Inserisci una nuova password: </p>
                  
                  
                 </div>
                 <div class="form-row">
                     <div class="col-md-12">
                         <div class="input-group">
                             <div class="input-group-addon">
                                 <span class="fa fa-key"></span>
                             </div>
                             <input type="password" autofocus class="form-control" v-model="VecchiaPassword" placeholder="Vecchia password o default password"/>
                         </div>
                     </div>
                     
                 </div>
                 <div class="form-row">
                     <div class="col-md-12" style="margin-top:10px">
                         <div class="input-group">
                             <div class="input-group-addon">
                                 <span class="fa fa-key"></span>
                             </div>
                             <input type="password" autofocus class="form-control" v-model="Password" placeholder="Password"/>
                         </div>
                     </div>
                     
                 </div>
                 <div class="form-row">
                 <div class="col-md-12" style="margin-top:10px">
                         <div class="input-group">
                             <div class="input-group-addon">
                                 <span class="fa fa-key"></span>
                             </div>
                             <input type="password" class="form-control" v-model="PasswordConferma" placeholder="Conferma password"/>
                         </div>
                     </div>
                    </div>
                 <div class="form-row">
                     <div class="col-md-12" style="margin-top:10px">
                        <a v-if="CheckPassword" class="btn btn-block btn-success" v-on:click="OnClickConferma">Conferma</a>
                     </div>
                 </div>   
             </div>
         </div>
     </div>
 </div>
</template>

<script>
import { SystemInformation} from '../SystemInformation.js'

export default 
{
    name: "VUEPrimoAccesso",
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
             Password          : '',
             PasswordConferma  : '',
             VecchiaPassword   : ''
     }
    },

    methods :
    {
        OnClickConferma()
        {
            if(this.Password == '' || this.PasswordConferma == '' || this.VecchiaPassword == '')
                alert('Inserire tutte le informazioni')
            else
            {
              var Self = this
              SystemInformation.AdvQuery.ChangePassword(this.VecchiaPassword ,this.Password,
                                              function()
                                              { 
                                                 Self.PrimoAccessoEffettuato()
                                                 SystemInformation.GoToFirstPage()
                                              },
                                              SystemInformation.HandleError);
            }
        },

        PrimoAccessoEffettuato()
        {
            var ObjQuery = { Operazioni: [] }
            var Self = this            
            ObjQuery.Operazioni.push({
                                        Query: "ModificaPrimoAccesso",
                                        Parametri: {
                                                    CHIAVE           : undefined
                                                    }
                                    });
            SystemInformation.AdvQuery.PostSQL('ModificaPrimoAccesso',ObjQuery,
                                                function()
                                                { 
                                                  Self.$router.go()
                                                },
                                                SystemInformation.HandleError)
        }
    },

    computed :
    {
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
            if(check5.test(this.Password))
                return false
            if(check5.test(this.PasswordConferma))
                return false
            return true
        },          
        CheckNumeri()
        {
            let check=/[0-9]/
            if(check.test(this.Password))
                return true
            return false
        },
        CheckSpeciali()
        {
            let check4=/[$-/:-?{-~!"^_}@`[\]]/
            if(check4.test(this.Password))
                return true
            return false
        },
        CheckMinuscole()
        {
            let check2=/[a-z]/
            if(check2.test(this.Password))
                return true
            return false
        },
        CheckMaiuscole()
        {
            let check3=/[A-Z]/
            if(check3.test(this.Password))
                return true
            return false 
        },
        CheckNumeroCaratteri()
        {
            if (this.Password.length >= 8)
                return true
            return false
        },
        CheckRipetiPassword()
        {
            if(this.Password != '')
            {
                if (this.Password == this.PasswordConferma)
                    return true
            }
            return false
        }       
    }
}

</script>