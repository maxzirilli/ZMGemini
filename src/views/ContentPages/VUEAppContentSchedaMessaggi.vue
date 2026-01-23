<template>
    <div class="container" style="width:95%">    
        <div class="col-md-12">
            
            <div class="content list">
                <div class="form row" v-for="Messaggio in ListaMessaggi" :Key="Messaggio.Chiave" style="margin-bottom:2%">
                        <div class="col-md-1">
                            <div class="list-info">
                                <img v-if="Messaggio.Immagine != ''" 
                                    :src="Messaggio.Immagine"
                                    class="img-circle img-thumbnail" 
                                    style="width:100%"/>
                                <img v-else src="@/assets/images/IconaPDF.png" 
                                    class="img-circle img-thumbnail" 
                                    style="width:100%"/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <a  class="list-text-name">{{Messaggio.Titolo}}</a>

                            <p>
                                {{Messaggio.Testo}}
                            </p>                       
                            
                        </div>

                        <div class="col-md-1" style="float:right;">
                            <label class="switch">
                                <input type="checkbox" v-model="Messaggio.Letto" @change="CambiaLetto(Messaggio)">
                                <span class="slider round"></span>
                            </label>                    
                        </div>
                    <div class="col-md-12" v-for="Allegato in Messaggio.Allegati" :key="Allegato.CHIAVE" style="background:#d0e9ff">
                        <div class="col-md-4" style="border-right: 2px solid #4cc0c1">
                                <span v-if="Allegato.Tipo == TIPO_FILE.FilePdf" style="margin-right:5px;">
                                    <img style="width:20px" src="@/assets/images/LogoPDF.png"></span>
                                <span v-if="Allegato.Tipo == TIPO_FILE.FileImmagine" style="margin-right:5px;" >
                                    <img style="width:20px" src="@/assets/images/LogoImg.png">
                                </span>
                                <span v-if="Allegato.Tipo == TIPO_FILE.FileAltro" style="margin-right:5px">
                                    <img style="width:20px" src="@/assets/images/LogoImgDef.png">
                                </span>
                            {{ Allegato.NomeFile }}
                        </div>
                        <div class="col-md-7" >
                            {{ Allegato.Descrizione }}
                        </div>
                        <div class="col-md-1">
                            <a class="fa fa-cloud-download fa-2x"  v-on:click="SalvaAllegato(Allegato)" style="margin-left:5%; float:right; cursor:pointer" title="Salva allegato"></a>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>  
</template>

<script>
import { SystemInformation, TIPO_FILE } from '@/SystemInformation.js'
import { saveAs } from 'file-saver';
import { TZStringConvFunct } from '../../../../../../../../Librerie\\VUE\\ZStringConvFunct.js'

export default {
    name : 'app',
    data() {
        return {
            ListaMessaggi : [],
            TIPO_FILE
        }
    },

    methods :
    {
        CambiaLetto(messaggio)
        {
            let ObjQuery = { Operazioni : [] }
            if (messaggio.Letto)
            {
                ObjQuery.Operazioni.push({Query : 'Insert',
                                          Parametri : {ID_MESSAGGIO : messaggio.Chiave}})
            }
            else
            {
                ObjQuery.Operazioni.push({Query : 'Delete',
                                          Parametri : {ID_MESSAGGIO : messaggio.Chiave}})
            }
            SystemInformation.AdvQuery.PostSQL('Utenti_Messaggi', ObjQuery,
                                                function(){}, function(HTTPError,SubHTTPError,Response){SystemInformation.HandleError(HTTPError,SubHTTPError,Response);})
        },

        CaricaListaMessaggi()
        {
            var Self = this;
            SystemInformation.AdvQuery.GetSQL('Utenti_Messaggi',{},
                                            function(Results)
                                            {
                                                let ArrayInfoMessaggi = SystemInformation.AdvQuery.FindResults(Results,"ListaMessaggi")
                                                let ArrayInfoAllegati = SystemInformation.AdvQuery.FindResults(Results,"ListaAllegati")
                                                let ArrayInfoLetti    = SystemInformation.AdvQuery.FindResults(Results,'ListaLetti')
                                                if (ArrayInfoMessaggi != undefined)
                                                {
                                                    for (let i = 0; i < ArrayInfoMessaggi.length; i++)
                                                    {
                                                        let ObjMessaggio = {Chiave   : ArrayInfoMessaggi[i].CHIAVE,
                                                                            Titolo   : ArrayInfoMessaggi[i].TITOLO,
                                                                            Immagine : ArrayInfoMessaggi[i].IMMAGINE,
                                                                            Testo    : ArrayInfoMessaggi[i].TESTO,
                                                                            Letto    : false,
                                                                            Allegati : []}
                                                        if (ArrayInfoLetti.findIndex(messaggio => messaggio.ID_MESSAGGIO == ObjMessaggio.Chiave) != -1)
                                                            ObjMessaggio.Letto = true
                                                        if (ArrayInfoAllegati != undefined)
                                                        {
                                                            for (let j = 0; j < ArrayInfoAllegati.length; j++)
                                                            {
                                                                if (ArrayInfoAllegati[j].ID_MESSAGGIO == ObjMessaggio.Chiave)
                                                                    ObjMessaggio.Allegati.push({
                                                                                                  Chiave : ArrayInfoAllegati[j].CHIAVE,
                                                                                                  Allegato : ArrayInfoAllegati[j].ALLEGATO,
                                                                                                  Tipo : ArrayInfoAllegati[j].TIPO,
                                                                                                  NomeFile : ArrayInfoAllegati[j].NOME_FILE,
                                                                                                  Descrizione : ArrayInfoAllegati[j].DESCRIZIONE
                                                                    })
                                                            }
                                                        }
                                                        Self.ListaMessaggi.push(ObjMessaggio)
                                                    }
                                                }
                                                else SystemInformation.HandleError('Errore nel modello messaggi'); 
                                                
                                            },
                                            SystemInformation.HandleError)
        },

        SalvaAllegato(allegato)
        {
            let Elementi = allegato.Allegato.split(',')
            saveAs(TZStringConvFunct.Base64AsBlob(Elementi[1]), allegato.NomeFile)
        },
    },    

    computed:
    {
        
    },

    beforeMount()
    {
      this.CaricaListaMessaggi() 
    }
}
</script>
