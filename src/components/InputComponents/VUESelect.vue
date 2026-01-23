<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option v-for="SelectOption in Elements" 
            :Key="SelectOption.Id"
            :value="SelectOption.Id"
            :selected="SelectOption.Id == modelValue">
            {{SelectOption.Descrizione}}
     </option>
  </select>
</template>

<script>
 export default 
 {
   data()
   {
      return {
             }
   },
   props : ['modelValue','Elements']
 }
</script>
