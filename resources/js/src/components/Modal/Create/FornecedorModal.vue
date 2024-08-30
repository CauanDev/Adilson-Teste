<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Fornecedor
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
                    <InputName v-model="name" placeholder="Digite o Nome do Fornecedor" />
                    <InputEmail v-model="email" placeholder="Digite o Email do Fornecedor" />
                    <button
                        :disabled="!isFormValid"
                        @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Fornecedor
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputEmail from "../../Inputs/InputEmail.vue";

export default {
    name: "FornecedorModal",
    props: {
        fornecedor: {
            type: Object,
            default: null
        }
    },
    components: { InputName, InputEmail },
    data() {
        return {
            name: '',
            email: ''
        };
    },
    computed: {
        isFormValid() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return this.name.trim() !== '' && !this.hasNumbers(this.name) && emailPattern.test(this.email);
        }
    },
    methods: {
        hasNumbers(str) {
            return /\d/.test(str);
        },
        submitForm() {
            this.$emit('addFornecedor', {
                name: this.name,
                email: this.email
            });
            this.$emit('close'); 
        }
    }
};
</script>
