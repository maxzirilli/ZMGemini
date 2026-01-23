<template>
  <header id="header" class="navbar navbar-fixed-top bg-white box-shadow b-b b-light" style="min-height: 50px;">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
      <!-- Logo -->
      <div class="navbar-header" style="margin-left: -25%;">
        <a href="#" class="navbar-brand" style="display: inline-flex; align-items: center;">
          <img src="../../assets/images/LogoGemini.png" style="height: 40px; margin-left: 10px;">
          <span class="logo-gemini">GEMINI</span>
        </a>
      </div>

      <!-- Menu desktop -->
      <ul class="nav navbar-nav navbar-right hidden-xs" style="display: flex; align-items: center;">
        <li v-for="AMenu in Menu" :key="AMenu.Caption"><a :href="AMenu.Link">{{ AMenu.Caption }}</a></li>
        <li>
          <div class="m-t-sm">
            <a class="btn btn-link btn-sm" style="font-size: 22px; font-weight: bold; margin-left: 25px;margin-bottom:5%;" @click="OnClickLogin">{{ LoginMenu }}</a>
          </div>
        </li>
      </ul>

      <!-- Menu Mobile Icon -->
      <button class="btn btn-link visible-xs" style="position: fixed" @click="OnClickApriMenu">
        <i class="fa fa-bars"></i>
      </button>
    </div>

    <!-- Menu mobile a tendina -->
    <div v-if="OpenMenuATendina" class="visible-xs bg-white b-t p">
      <ul class="nav navbar-nav">
        <li v-for="AMenu in Menu" :key="AMenu.Caption"><a :href="AMenu.Link">{{ AMenu.Caption }}</a></li>
        <li>
          <a class="btn btn-link btn-block" @click="OnClickLogin">{{ LoginMenu }}</a>
        </li>
      </ul>
    </div>
  </header>
</template>



<script>
import { SystemInformation } from '@/SystemInformation';

export default {
  name: "VUEHeader",
  data() 
  {
    return {
      SystemInformation: SystemInformation,
      OpenMenuATendina: false,
      Menu: [
      ]
    };
  },
  components: {},
  computed:
   {
    LoginMenu:
    {
      get() 
      {
        if (SystemInformation.AdvQuery != undefined)
          return (SystemInformation.AdvQuery.CurrentUserLogged ? "ENTRA" : "LOGIN");
        else return ('-');
      }
    },
  },
  methods: 
  {
    OnClickLogin()
    {
      if (SystemInformation.AdvQuery.CurrentUserLogged)
        this.$router.push('/appMainWindow/Dashboard');
      else this.$router.push('/login');
    },

    OnClickApriMenu() 
    {
      this.OpenMenuATendina = !this.OpenMenuATendina
    },
  }
  
};
</script>
