<template>
    <NavBar />
    <router-view v-slot="{ Component, route }">
        <div :key="route.name">
            <Component :is="Component" />
        </div>
    </router-view>
</template>
<script>
  import NavBar from './src/components/NavBar/NavBar.vue';
  
  export default {
    name: 'App',
    components: {
      NavBar
    }
  };
</script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

body{
    font-family: 'Poppins', sans-serif;
    background-color: rgb(243 244 246);
}
button{
  border: 1px black solid;
}
@media screen and (max-width: 640px) {
  .mobile {
      flex-direction: column;
  }
}
</style>