<template>
   <input type="text" 
          class="form-control" 
          :value="modelValue" 
          placeholder="CAP" 
          @input="$emit('update:modelValue',$event.target.value)"
          maxlength="5"/> 
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
