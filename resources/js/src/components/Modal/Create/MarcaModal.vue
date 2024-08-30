<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Nova Marca
                    </h3>
                    <button @click="$emit('close')"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                        data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 flex flex-col items-center gap-2">
                    <InputName v-model="name" placeholder="Digite o Nome da Marca" /> 
                    <InputName v-model="segmento" placeholder="Digite o Segmento" />                  
                    <div class="text-center w-[75%]" v-if="fornecedores.length >0">
                        <h1 class="text-xs">Escolher o Fornecedor</h1>
                        <select 
                            v-model="selectedFornecedor"
                            :disabled="this.fornecedor"
                            class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                            <option value="none">Selecione um Fornecedor</option>
                            <option v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id">
                                {{ fornecedor.name }}
                            </option>
                        </select>
                    </div>
                    <div v-else class="text-center">
                        <router-link to="/fornecedor">
                            <p class="text-gray-500">Nenhum Fornecedor Disponível</p>
                            <button type="button" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Criar Fornecedor</button>
                        </router-link>
                    </div>
                    <button
                    v-if="fornecedores.length >0"
                        :disabled="!isFormValid"
                        @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Marca
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputName from "./../../../../src/components/Inputs/InputName.vue"
import http from "../../../services/http.js"

export default {
    name: "MarcaModal",
    props: {
        fornecedor: {
            type: Object,
            default: null
        }
    },
    components: { InputName },
    data() {
        return {
            name: '',
            segmento: '',
            fornecedores: [],
            selectedFornecedor: this.fornecedor? this.fornecedor.id : 'none'
        };
    },
    computed: {
        isFormValid() {
            return this.name.trim() !== '' && 
                   !this.hasNumbers(this.name) && 
                   this.segmento.trim() !== '' &&
                   this.selectedFornecedor !== "none";
        }
    },
    methods: {
        hasNumbers(str) {
            return /\d/.test(str);
        },
        submitForm() {
            this.$emit('addMarca', {
                nome: this.name,
                segmento: this.segmento,
                fornecedor_id: this.selectedFornecedor
            });
            this.$emit('close'); 
        },
        async getFornecedores() {
            try {
                const response = await http.post('/filter-fornecedor',{"status":"Ativo"});
                this.fornecedores = response.data.fornecedores;
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.getFornecedores();        
    }
};
</script>