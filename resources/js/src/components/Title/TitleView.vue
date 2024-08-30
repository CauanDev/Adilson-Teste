<template>
<div class="flex justify-center">
    <h1 class="mb-2 mt-2 text-4xl font-extrabold leading-none tracking-tight text-black md:text-5xl lg:text-6xl ">{{this.title}}</h1>
</div>

</template>
<script>
export default{
    name:"TitleView",
    props:{
        title:{
            required: true
        }
    }
}
</script>