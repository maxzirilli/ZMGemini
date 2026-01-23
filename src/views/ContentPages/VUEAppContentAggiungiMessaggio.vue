<template>
    <div class="container">

        <div class="row"> 
            <div style="width:95%">               
                <div class="block">
                    <div class="content">
                                    <div class="form-row">
                                        <br>
                                        <div class="col-md-12">                
                                            <div class="col-md-3">
                                                <div v-if="NuovoMessaggio.Immagine == ''">
                                                    <img style="width:80%" src="@/assets/images/IconaPDF.png" class="img-circle">
                                                </div>
                                                <div v-else>
                                                    <img style="width:80%" :src="NuovoMessaggio.Immagine" class="img-circle">
                                                </div>
                                                <br>
                                                <div>
                                                    <label class="btn" type="button" style="cursor:pointer;width:80%;border-color:#d9d9d9">Inserisci nuova immagine         
                                                        <input style="display:none;" type="file" id="inputImmagineUtente" @change="LoadImmagineFromFile(elemento)" accept="image/*">
                                                    </label>                     
                                                </div>
                                                <div style="margin-top:4%">
                                                    <label class="btn" type="button" style="cursor:pointer;width:80%;border-color:#d9d9d9">Inserisci allegato         
                                                        <input style="display:none;" type="file" id="allegatoCaricato" @change="CaricaAllegato(elemento)" accept="all/*">
                                                    </label>                     
                                                </div>
                                            </div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-8" style="padding-right:0px">
                                                <div>Titolo: <input class="form-control" style="width:100%" type="text" v-model="NuovoMessaggio.Titolo"></div>
                                                <br>
                                                <div>Testo: <textarea class="form-control" style="width:100%; resize:none; height: 200px;" v-model="NuovoMessaggio.Testo"></textarea></div>
                                                
                                            </div>
                                        </div>
                                            <div v-if="NuovoMessaggio.Allegati.length > 0" class="col-md-12" style="margin-top: 2%">Allegati:
                                                <table cellpadding="0" cellspacing="0" width="100%" style="margin-top: 1%" class="table table-bordered sortable">
                                                    <tbody>
                                                        <tr v-for="Allegato, index in NuovoMessaggio.Allegati" :key="index">                        
                                                            <td style="width:30%">
                                                                    <span v-if="Allegato.TIPO == TIPO_FILE.FilePdf" style="margin-right:5px;">
                                                                        <img style="width:20px" src="@/assets/images/LogoPDF.png"></span>
                                                                    <span v-if="Allegato.TIPO == TIPO_FILE.FileImmagine" style="margin-right:5px;" >
                                                                        <img style="width:20px" src="@/assets/images/LogoImg.png">
                                                                    </span>
                                                                    <span v-if="Allegato.TIPO == TIPO_FILE.FileAltro" style="margin-right:5px">
                                                                        <img style="width:20px" src="@/assets/images/LogoImgDef.png">
                                                                    </span>
                                                                    {{Allegato.NOME_FILE}}
                                                            </td>
                                                            <td style="width:55%" @mousedown="ModificaDescrizione(index)">
                                                                <p v-if="!descrizioneallegato[index]">
                                                                    {{ Allegato.DESCRIZIONE }}
                                                                </p>
                                                                <input v-else type="text" style="width:100%" v-model="Allegato.DESCRIZIONE" ref="input"  @blur="ConfermaDescrizione(index)" @keydown.enter="ConfermaDescrizione(index)" >
                                                            </td>
                                                            <td style="width:15%; vertical-align: middle;">
                                                                <a class="fa fa-cloud-download fa-2x"  v-on:click="SalvaAllegato(Allegato)" style="margin-left:5% ; cursor:pointer" title="Salva allegato"></a>
                                                                <a class="fa fa-pencil fa-2x" v-on:click="ModificaDescrizione(index)"  style="margin-right:20%;margin-left:20% ; cursor:pointer" title="Aggiungi o modifica la descrizione allegato"></a>                                                         
                                                                <a class="fa fa-trash fa-2x" v-on:click="EliminaAllegato(index)" style="cursor:pointer; margin-right: 5%;" title="Rimuovi allegato"></a>
                                                            </td>                                                    
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>

                                <div class="col-md-12" style="margin-top:2%">
                                    <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:18%" @click="Annulla">Annulla</button>
                                    <button type="button" v-bind:disabled="NuovoMessaggio.Titolo == '' || NuovoMessaggio.Testo == ''" class="btn btn-success" style="float:right;font-weight:bold;width:18%" @click="OnClickAggiungiMessaggio">Conferma</button>
                                </div>
                       
                    </div>
                </div>          
            </div>               
        </div>
    </div>           
</template>


<script>
import {SystemInformation, TIPO_FILE} from '@/SystemInformation.js'
import { TZFileFunct } from '../../../../../../../../Librerie/VUE/ZFileFunct.js'
import { TZImageFunct } from '../../../../../../../../Librerie\\VUE\\ZImageFunct.js'
import { saveAs } from 'file-saver';
import { TZStringConvFunct } from '../../../../../../../../Librerie\\VUE\\ZStringConvFunct.js'

export default {
    name:'app',
    emits:['OnClickApriPopup', 'OnClickConfermaModifiche'],
    components: {
    },
    data()
    {
       return {
                NuovoMessaggio      : {Chiave: undefined, Titolo: '', Testo: '', Immagine: '', Allegati: []},
                TIPO_FILE,
                AllegatiInizio      : [],
                descrizioneallegato : []
              }
    },
    methods:
    {
        OnClickAggiungiMessaggio()
        {
            var ObjQuery = {Operazioni : []}
            if (this.$route.params.pagina == 'nuovo')
            {
                ObjQuery.Operazioni.push({Query : 'InsertMessaggio',
                                        Parametri : {
                                                        Titolo   : this.NuovoMessaggio.Titolo,
                                                        Testo    : this.NuovoMessaggio.Testo,
                                                        Immagine : this.NuovoMessaggio.Immagine
                                                    }})

                for (let i = 0; i < this.NuovoMessaggio.Allegati.length; i++)
                {
                    ObjQuery.Operazioni.push({Query: 'InsertAllegato',
                                            Parametri: {
                                                            Descrizione : this.NuovoMessaggio.Allegati[i].DESCRIZIONE,
                                                            Nome_File   : this.NuovoMessaggio.Allegati[i].NOME_FILE,
                                                            Tipo        : this.NuovoMessaggio.Allegati[i].TIPO,
                                                            Allegato    : this.NuovoMessaggio.Allegati[i].ALLEGATO 
                                            },
                                            ResetKeys : [2]})
                }
            }
            else
            {                
                for (let i = 0; i < this.NuovoMessaggio.Allegati.length; i++)
                {
                    if (this.AllegatiInizio.findIndex(allegato => allegato == this.NuovoMessaggio.Allegati[i].CHIAVE) == -1)
                    {
                        ObjQuery.Operazioni.push({Query: 'InsertAllegato2',
                                                Parametri: {
                                                                Messaggio   : this.$route.params.pagina,
                                                                Descrizione : this.NuovoMessaggio.Allegati[i].DESCRIZIONE,
                                                                Nome_File   : this.NuovoMessaggio.Allegati[i].NOME_FILE,
                                                                Tipo        : this.NuovoMessaggio.Allegati[i].TIPO,
                                                                Allegato    : this.NuovoMessaggio.Allegati[i].ALLEGATO 
                                                },
                                                ResetKeys : [2]})
                    }
                    else
                    {
                        let index = this.AllegatiInizio.findIndex(allegato => allegato == this.NuovoMessaggio.Allegati[i].CHIAVE)
                        ObjQuery.Operazioni.push({Query: 'EditAllegato',
                                                Parametri: {
                                                                Allegato    : this.AllegatiInizio[index],
                                                                Descrizione : this.NuovoMessaggio.Allegati[i].DESCRIZIONE
                                                }})
                    }
                    for (let j = 0; j < this.AllegatiInizio.length; j++)
                    {
                        if (this.NuovoMessaggio.Allegati.findIndex(allegato => allegato.CHIAVE == this.AllegatiInizio[j]) == -1)
                        {
                            ObjQuery.Operazioni.push({Query: 'DeleteAllegato',
                                                    Parametri: {
                                                                    Allegato : this.AllegatiInizio[j]
                                                    }})
                        }
                    }
                }
                ObjQuery.Operazioni.push({Query : 'EditMessaggio',
                                        Parametri : {
                                                        Chiave   : this.$route.params.pagina,
                                                        Titolo   : this.NuovoMessaggio.Titolo,
                                                        Testo    : this.NuovoMessaggio.Testo,
                                                        Immagine : this.NuovoMessaggio.Immagine
                                                    }})
            }
            var Self = this
            SystemInformation.AdvQuery.PostSQL('Admin_Messaggi',ObjQuery,
                                                                function()
                                                                {
                                                                    Self.$emit('OnClickConfermaModifiche')
                                                                    Self.$router.push('/appMainWindow/GestioneMessaggi')
                                                                },
                                                                function(HTTPError,SubHTTPError,Response)
                                                                {
                                                                SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                });                
            
        },

        Annulla()
        {
            this.$emit('OnClickApriPopup')
        },

        CaricaAllegato()
        {
            var Self    = this;
            var Reader  = new FileReader();
            var File    = document.getElementById('allegatoCaricato');
            Reader.readAsDataURL(File.files[0]);

            Reader.onload = function (e) 
            {
                var Path = File.value
                let ObjAllegato = {}
                ObjAllegato.NOME_FILE = TZFileFunct.ExtractFileName(Path)
                ObjAllegato.ALLEGATO  = e.target.result
                ObjAllegato.TIPO      = TIPO_FILE.FileAltro
                if(e.target.result.includes('image'))
                {
                    
                ObjAllegato.TIPO = TIPO_FILE.FileImmagine
                }
                if(e.target.result.includes('application/pdf'))
                ObjAllegato.TIPO = TIPO_FILE.FilePdf
                ObjAllegato.DESCRIZIONE = ''
                Self.NuovoMessaggio.Allegati.push(ObjAllegato)
                Self.descrizioneallegato.push(false)
            }          
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
                    Self.NuovoMessaggio.Immagine = Img.src 
                }
                Img.src = e.target.result
            }
        },

        EliminaAllegato(index)
        {
            this.NuovoMessaggio.Allegati.splice(index, 1)
            this.descrizioneallegato.splice(index, 1)
        },

        SalvaAllegato(allegato)
        {
            let Elementi = allegato.ALLEGATO.split(',')
            saveAs(TZStringConvFunct.Base64AsBlob(Elementi[1]), allegato.NOME_FILE)
        },

        ModificaDescrizione(index)
        {
            this.descrizioneallegato[index] = true
        },

        ConfermaDescrizione(index)
        {
            this.descrizioneallegato[index] = false
        }
    },
    computed :
    {
        
    },
    beforeMount()
    {
        if (this.$route.params.pagina != 'nuovo')
        {
            let Self = this
            SystemInformation.AdvQuery.GetSQL('Admin_Messaggi', {Chiave: this.$route.params.pagina}, 
            function(Results)
            {
                let ArrayInfoMessaggio = SystemInformation.AdvQuery.FindResults(Results,"Dettaglio");
                let ArrayInfoAllegati = SystemInformation.AdvQuery.FindResults(Results,"DettaglioAllegati")
                if (ArrayInfoMessaggio != undefined)
                {
                    Self.NuovoMessaggio.Chiave = ArrayInfoMessaggio[0].CHIAVE
                    Self.NuovoMessaggio.Titolo = ArrayInfoMessaggio[0].TITOLO
                    Self.NuovoMessaggio.Testo  = ArrayInfoMessaggio[0].TESTO
                    Self.NuovoMessaggio.Immagine = ArrayInfoMessaggio[0].IMMAGINE
                    for (let i = 0; i < ArrayInfoAllegati.length; i++)
                    {
                        Self.NuovoMessaggio.Allegati.push(ArrayInfoAllegati[i])
                        Self.AllegatiInizio.push(ArrayInfoAllegati[i].CHIAVE)
                        Self.descrizioneallegato.push(false)
                    }
                }
                else SystemInformation.HandleError('Errore nel modello messaggi')
            }, SystemInformation.HandleError, 'SelectDettaglio')
        }  

    }
}
</script>