<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Alterar {{ this.fornecedor.name }}
                    </h3>
                    <button @click="this.$emit('close')"
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
                    <InputName v-model="inputName.name" placeholder="Digite o Novo Nome" />
                    <InputEmail v-model="inputName.email" placeholder="Digite o Novo Email" />
                    <NormalTable :headers="['Nome', 'Segmento', 'Quantidade', 'Status']" :body="body"
                        v-if="body.length > 0" />
                    <p class="text-gray-500" v-else>Nenhuma Marca Relacionada</p>
                    <button
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                        :disabled="!hasChanges" @click="this.$emit('saveChanges', inputName)">Salvar Alterações
                    </button>
                    <router-link to="/produtos"
                        class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-block">
                        Adicionar Produto
                    </router-link>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import http from "./../../../../src/services/http.js";
import NormalTable from "./../../../../src/components/Tables/NormalTable/NormalTable.vue";
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputEmail from "../../Inputs/InputEmail.vue";

export default {
    name: "FornecedorModal",
    components: { NormalTable, InputName, InputEmail },
    props: {
        fornecedor: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            body: [],
            inputName: {},
            originalData: {},
            disabled: false
        };
    },
    computed: {
        hasChanges() {
            return JSON.stringify(this.inputName) !== JSON.stringify(this.originalData);
        }
    },
    methods: {
        getDetails() {
            this.body = this.fornecedor.marcas.map(item => {
                return {
                    "name": item.nome,
                    "segmento": item.segmento,
                    "quantidade": item.produtos.length,
                    "status": item.status
                };
            });
        }
    },
    mounted() {
        this.getDetails();
        console.log(this.fornecedor)
        this.inputName = { name: this.fornecedor.name, email: this.fornecedor.email, id: this.fornecedor.id };
        this.originalData = { ...this.inputName }; // Armazena os dados originais
    }
};
</script>
