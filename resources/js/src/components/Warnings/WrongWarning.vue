<template>
    <div class="w-full h-full absolute bg-blue"></div>
    <div class="absolute z-50 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg border-2 border-slate-300">
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-black-200 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal" @click="this.$emit('close')">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only" >Close modal</span>
                </button>       
                <div class="p-4 md:p-5 flex flex-col text-center items-center">
                    <svg fill="#000000" class="w-10 mb-2" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    viewBox="0 0 208.891 208.891" xml:space="preserve">
               <path d="M0,170l65.555-65.555L0,38.891L38.891,0l65.555,65.555L170,0l38.891,38.891l-65.555,65.555L208.891,170L170,208.891
                   l-65.555-65.555l-65.555,65.555L0,170z"/>
               </svg>                    
               <h3 class="text-2xl font-bold text-black ">{{title}}</h3>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
export default {
    name: 'WrongWarning',
    emits: ['close'],
    props:{
        title:String
    }
    }
</script>