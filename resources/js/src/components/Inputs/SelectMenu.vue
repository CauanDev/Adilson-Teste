<template>
    <div class="flex flex-col justify-center items-center">
      <label class="block mb-1 text-sm text-slate-800">
        Selecione a Categoria
      </label>
  
      <div class="relative w-full md:w-1/2 lg:w-1/4">
        <select
          v-model="selectedCategory"
          @change="emitCategory"
          class="w-full h-12 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
          <option value="none">Selecione uma Categoria</option>
          <option value="funcionarios">Funcionarios</option>
          <option value="clientes">Clientes</option>
          <option value="produtos">Produtos</option>
          <option value="pedidos">Pedidos</option>
        </select>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2"
          stroke="currentColor" class="h-5 w-5 ml-1 absolute top-3.5 right-2.5 text-slate-700">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
        </svg>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "SelectMenu",
    data() {
      return {
        selectedCategory: 'none'
      };
    },
    methods: {
      emitCategory() {
        this.$emit('categoryChanged', this.selectedCategory);
      }
    }
  };
  </script>
  