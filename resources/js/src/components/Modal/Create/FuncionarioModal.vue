<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Criar Novo Funcionário
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
                    <div class="text-center">
                        <h1 class="text-xs">Escolher o Sexo</h1>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" value="Masculino" v-model="newValue.sexo" class="form-radio text-blue-600" />
                                <span class="ml-2">Masculino</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" value="Feminino" v-model="newValue.sexo" class="form-radio text-blue-600" />
                                <span class="ml-2">Feminino</span>
                            </label>
                        </div>
                    </div>
                    <InputAge v-model="newValue.idade" placeholder="Digite a Data de Nascimento" />
                    <InputEmail v-model="newValue.email" placeholder="Digite o Email" />
                    <InputSalario v-model="newValue.salario" placeholder="Digite o Salário"/>
                    <button :disabled="!isFormValid" @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Funcionário
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputAge from "./../../../../src/components/Inputs/InputAge.vue";
import InputEmail from "../../Inputs/InputEmail.vue";
import InputSalario from "../../Inputs/InputSalario.vue";

export default {
    name: "FuncionarioModal",
    components: { InputName, InputAge, InputEmail, InputSalario },
    data() {
        return {
            newValue: {
                name: '',
                sexo: '',
                idade: '',
                email: '',
                salario: '',
            }
        };
    },
    computed: {
        isFormValid() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return this.newValue.name && this.newValue.sexo && emailPattern.test(this.newValue.email) && this.newValue.idade;
        }
    },
    methods: {
        submitForm() {
            if (this.isFormValid) {
                const salarioNumero = parseFloat(this.newValue.salario.replace('R$', '').replace(',', '.')).toFixed(2);
                const formatDate = (value) => {
                if (value.length === 8) {
                    const day = value.slice(0, 2);
                    const month = value.slice(2, 4);
                    const year = value.slice(4, 8);
                    return `${day}/${month}/${year}`;
                }
                return value;
            };
                this.$emit('addFuncionario', {
                    nome: this.newValue.name,
                    sexo: this.newValue.sexo,
                    data_nasc: formatDate(this.newValue.idade),
                    email: this.newValue.email,
                    salario: salarioNumero
                });
                this.$emit('close');
            }
        }
    },
    watch: {
        'newValue.salario'(newValue) {
            if (!newValue.startsWith('R$')) {
                this.newValue.salario = 'R$' + newValue;
            }
        }
    }
};
</script>
