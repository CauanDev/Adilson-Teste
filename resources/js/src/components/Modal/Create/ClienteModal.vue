<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Cliente
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
                    <InputName v-model="name" placeholder="Digite o Nome" />
                    <div class="text-center w-[75%]">
                        <h1 class="text-xs">Escolher o Sexo</h1>
                        <div class="flex justify-around mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="sexo" value="Masculino" class="mr-2">
                                Masculino
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="sexo" value="Feminino" class="mr-2">
                                Feminino
                            </label>
                        </div>
                    </div>
                    <div class="text-center w-[75%]">
                        <h1 class="text-xs">Escolher o Tipo de Cliente</h1>
                        <div class="flex justify-around mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="tipo" value="Vip" class="mr-2">
                                Vip
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="tipo" value="Normal" class="mr-2">
                                Normal
                            </label>
                        </div>
                    </div>
                    
                    <InputAge v-model="idade" placeholder="Digite a Data de Nascimento" />
                    <InputEmail v-model="email" placeholder="Digite o email" />
                    <button :disabled="!isFormValid" @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import http from "./../../../../src/services/http.js";
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputAge from "./../../../../src/components/Inputs/InputAge.vue";
import InputEmail from "../../Inputs/InputEmail.vue";

export default {
    name: "ProdutosModal",
    components: { InputName, InputAge, InputEmail },
    data() {
        return {
            name: '',
            sexo: '',
            tipo:"",
            idade: '',
            email: '',
        };
    },
    computed: {
        isFormValid() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return this.name && this.sexo && emailPattern.test(this.email);
        },
    },
    methods: {
        submitForm() {
            // Função para formatar a data no formato dd/mm/yyyy
            const formatDate = (value) => {
                if (value.length === 8) {
                    const day = value.slice(0, 2);
                    const month = value.slice(2, 4);
                    const year = value.slice(4, 8);
                    return `${day}/${month}/${year}`;
                }
                return value;
            };
            console.log(formatDate(this.idade))
            this.$emit('addCliente', {
                nome: this.name,
                sexo: this.sexo,
                data_nasc: formatDate(this.idade), // Formata a data aqui
                email: this.email,
                tipo:this.tipo
            });
            this.$emit('close');
        }
    },

};
</script>
