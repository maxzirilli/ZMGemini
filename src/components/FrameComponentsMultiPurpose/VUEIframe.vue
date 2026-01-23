<template>
    <iframe :src="BodyPDF" :height="HeightPDF" style="border:none; overflow:hidden;  width:100%"></iframe>
</template>

<script>


export default 
{
    name:'IframeStampe',
    data() 
    {
      return {
               BodyPDF : '',
               HeightPDF : '100px'
             }
    },

    methods :
    {
        SetDimensions() 
        {
            this.HeightPDF = window.innerHeight;
        }
    },

    mounted() 
    {
       window.addEventListener('resize', this.SetDimensions);
    },
    unmounted() 
    {
       window.removeEventListener('resize', this.SetDimensions);
    },
    
    beforeMount()
    {
       this.BodyPDF = window.BodyPDF;
       this.SetDimensions();
    }
}

    
</script>