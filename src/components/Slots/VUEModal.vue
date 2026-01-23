<template>
<div>
 <div v-if="!NoBackdropModal" class="modal-backdrop in" style="z-index:99998"></div>
  <div class="modal-dialog" :style="{'width' : LarghezzaModal}" 
                             data-focus 
                             style="z-index:99999;position:fixed;top:10%;bottom:0;right:0;left:0">   
   <div class="modal-content" style="background:#68b6be;" ref="Modal" @mousedown="OnMouseDownDatiTask">
    <div class="modal-header" style="background-color:#68b6be; max-height:50px">
     <h4 class="modal-title" style="color:#d0e9ff;" id="myModalLabel">
      <img src="@/assets/images/LogoGemini.png" style="height:20px;float:left" class="m-r-sm">
       Gemini- {{ TitoloModal }}
       <button style="float:right;margin-top:-5px" @click="OnClickChiudiModal" class="btn btn-sm btn-icon btn-info">
           <i class="fa fa-times"></i>
       </button>
     </h4>
    </div>
    <div class="modal-body" :style="{'height' : AltezzaModal}" style="background:#d0e9ff; overflow-y:auto" id="ModalBody">
      <slot name="Body"></slot>
    </div>
    <div class="modal-footer" style="background-color:#68b6be;max-height:70px" id="ModalFooter">
     <slot name="Footer">
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="OnClickChiudiModal" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="OnClickConfermaModal" data-dismiss="modal">Conferma</button>
     </slot>
    </div>
   </div>
  </div>
</div>        
</template>

<script>
export default 
{
    name: "VUEModal",
    emits: ['onClickChiudiModal', 'onClickConfermaModal'],
    
    data() 
    {
        return {
                  positions  : {
                                  clientX: undefined,
                                  clientY: undefined,
                                  movementX: 0,
                                  movementY: 0
                               },
               };
    },
    props: ["Titolo", "Altezza", "Larghezza", "NoBackdrop", "AllowMovement"],

    computed:
    {
        TitoloModal:
        {
            get()
            {
               return this.Titolo
            }
        },
        AltezzaModal:
        {
            get()
            {
               return this.Altezza
            }
        },
        LarghezzaModal:
        {
            get()
            {
               return this.Larghezza
            }
        },
        NoBackdropModal:
        {
            get()
            {
               if(this.NoBackdrop == undefined)
                  return false
               else return this.NoBackdrop
            }
        },
    },

    methods:
    {
      OnClickChiudiModal()
      {
        this.$emit('onClickChiudiModal')
      },

      OnClickConfermaModal()
      {
        this.$emit('onClickConfermaModal')
      },

      OnMouseDownDatiTask: function (event) 
      {            
        if(this.AllowMovement)
        {
          this.positions.clientX = event.clientX
          this.positions.clientY = event.clientY
          document.onmousemove = this.OnMouseMoveDatiTask
          document.onmouseup = this.OnMouseUpDatiTask
        }
      },
      OnMouseMoveDatiTask: function (event) 
      {
        this.positions.movementX = this.positions.clientX - event.clientX
        this.positions.movementY = this.positions.clientY - event.clientY
        this.positions.clientX = event.clientX
        this.positions.clientY = event.clientY
        // set the element's new position:
        this.$refs.Modal.style.top = (this.$refs.Modal.offsetTop - this.positions.movementY) + 'px'
        this.$refs.Modal.style.left = (this.$refs.Modal.offsetLeft - this.positions.movementX) + 'px'
      },
      OnMouseUpDatiTask() 
      {
        document.onmouseup = null
        document.onmousemove = null
      },
    },

    beforeMount()
    {
      
    }
}
</script>