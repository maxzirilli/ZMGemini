<template>
   <input type="text" 
          class="form-control" 
          :value="modelValue" 
          placeholder="Partita IVA" 
          @input="$emit('update:modelValue',$event.target.value)"/> 
</template>

<script>
 export default {
   data()
   {
      return {}
   },
   props : ['modelValue']
  
 }
</script>
