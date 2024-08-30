<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Atualizar {{ cliente.name }}
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
                    <InputName v-model="newValue.name" placeholder="Digite o Nome" />
                    <div class="text-center flex flex-col gap-2">
                        <h1 class="text-xs">Escolher o Sexo</h1>
                        <div class="flex gap-10">
                            <div class="flex items-center mt-2">
                                <input id="masculino" type="radio" value="Masculino" v-model="newValue.sexo"
                                    class="mr-2 border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <label for="masculino" class="text-sm text-gray-900">Masculino</label>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="feminino" type="radio" value="Feminino" v-model="newValue.sexo"
                                    class="mr-2 border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <label for="feminino" class="text-sm text-gray-900">Feminino</label>
                            </div>
                        </div>
                        <div class="text-center">
                            <h1 class="text-xs">Escolher o Tipo de Cliente</h1>
                            <div class="flex justify-around mt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" v-model="newValue.tipo" value="Vip" class="mr-2">
                                    Vip
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" v-model="newValue.tipo" value="Normal" class="mr-2">
                                    Normal
                                </label>
                            </div>
                        </div>
                    </div>
                    <InputAge v-model="newValue.idade" placeholder="Digite a Data de Nascimento" />
                    <InputEmail v-model="newValue.email" placeholder="Digite o Email" />
                    <button :disabled="!isFormValid" @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Atualizar Cliente
                    </button>
                    <router-link :to="{ path: '/pedidos'}" class="w-full">
                    <button
                        class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Adicionar Compra
                    </button>
                </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputAge from "./../../../../src/components/Inputs/InputAge.vue";
import InputEmail from "../../Inputs/InputEmail.vue";

export default {
    name: "ProdutosModal",
    components: { InputName, InputAge, InputEmail },
    props: {
        cliente: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            newValue: {
                name: this.cliente.name || '',
                sexo: (this.cliente.sexo.trim() === "Masculino") ? "Masculino" : "Feminino",
                idade: this.cliente.idade || '',
                email: this.cliente.email || '',
                tipo: this.cliente.tipo
            }
        };
    },
    computed: {
        isFormValid() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return this.newValue.name && this.newValue.sexo && emailPattern.test(this.newValue.email) && this.newValue.idade && this.newValue.tipo;
        },
        hasChanges() {
            const normalize = (value) => {
                if (typeof value !== 'string') return '';
                return value.trim().toLowerCase();
            };

            return normalize(this.newValue.name) !== normalize(this.cliente.name) ||
                normalize(this.newValue.sexo) !== normalize(this.cliente.sexo) ||
                normalize(this.newValue.idade) !== normalize(this.cliente.idade) ||
                normalize(this.newValue.email) !== normalize(this.cliente.email)||
                normalize(this.newValue.tipo) !== normalize(this.cliente.tipo);
        }
    },
    methods: {
        submitForm() {
            if (this.isFormValid && this.hasChanges) {
                this.$emit('updateCliente', {
                    nome: this.newValue.name,
                    sexo: this.newValue.sexo,
                    idade: this.newValue.idade,
                    email: this.newValue.email,
                    id: this.cliente.id,
                    tipo: this.newValue.tipo
                });
                this.$emit('close');
            }
        }
    },
    watch: {
        cliente: {
            handler(newCliente) {
                this.newValue = {
                    name: newCliente.name || '',
                    sexo: newCliente.sexo, // Ensure default value
                    idade: newCliente.idade || '',
                    email: newCliente.email || '',
                    tipo: newCliente.tipo
                };
            },
            deep: true
        }
    }
};
</script>
