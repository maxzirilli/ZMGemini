<template>
   <input type="text" 
          class="form-control" 
          :value="modelValue" 
          placeholder="Codice fiscale" 
          style="text-transform: uppercase!important;"
          @input="ReturnUpperCase($event)"
          @change="ReturnUpperCase"/> 
</template>

<script>
 export default {
   data()
   {
      return {}
   },
   props : ['modelValue'],

   methods :
   {
      ReturnUpperCase($event)
      {
         $event.target.value = $event.target.value.toUpperCase()
         this.$emit('update:modelValue',$event.target.value)
      }
   }
  
 }
</script>
