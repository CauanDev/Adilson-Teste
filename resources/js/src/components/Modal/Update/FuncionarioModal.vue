<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Atualizar {{ funcionario.name }}
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
                    <InputName v-model="newValue.name" placeholder="Digite o Nome do Funcionario" />
                    <div class="text-center">
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
                    </div>
                    <InputAge v-model="newValue.idade" placeholder="Digite a Idade do Funcionario" />
                    <InputEmail v-model="newValue.email" placeholder="Digite o Email do Funcionario" />
                    <InputSalario v-model="newValue.salario" placeholder="Digite o Salário do Funcionario" />
                    <button :disabled="!isFormValid" @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Atualizar Funcionario
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
import { format } from 'date-fns';

export default {
    name: "FuncionarioModal",
    components: { InputName, InputAge, InputEmail, InputSalario },
    props: {
        funcionario: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            newValue: {
                name: this.funcionario.name || '',
                sexo: this.funcionario.sexo || '',
                idade: this.funcionario.data_nasc|| '',
                email: this.funcionario.email || '',
                salario: this.funcionario.salario || '',
            }
        };
    },
    computed: {
        hasChanges() {

            const normalize = (value) => value ? value.trim().toLowerCase() : '';

            const nameChanged = normalize(this.newValue.name) !== normalize(this.funcionario.name);
            const sexoChanged = normalize(this.newValue.sexo) !== normalize(this.funcionario.sexo);
            const idadeChanged = normalize(this.newValue.idade) !== normalize(this.funcionario.idade);
            const emailChanged = normalize(this.newValue.email) !== normalize(this.funcionario.email);
            const salarioChanged = normalize(this.newValue.salario) !== normalize(this.funcionario.salario);

            return nameChanged || sexoChanged || idadeChanged || emailChanged || salarioChanged;
        },
        isFormValid() {

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return this.newValue.name && this.newValue.sexo && emailPattern.test(this.newValue.email) && this.newValue.idade;
        }

    },
    methods: {
        submitForm() {
            if (this.isFormValid && this.hasChanges) {
                const salarioNumero = parseFloat(this.newValue.salario.replace('R$', '').replace(',', '.')).toFixed(2);

                this.$emit('updateFuncionario', {
                    nome: this.newValue.name,
                    sexo: this.newValue.sexo,
                    data_nasc: this.newValue.idade,
                    email: this.newValue.email,
                    salario: salarioNumero,
                    id: this.funcionario.id
                });
                this.$emit('close');
            }
        }
    },
    watch: {
        funcionario: {
            handler(newFuncionario) {
                this.newValue = {
                    name: newFuncionario.name || '',
                    sexo: newFuncionario.sexo || '',
                    idade: newFuncionario.idade || '',
                    email: newFuncionario.email || '',
                    salario: newFuncionario.salario || '',
                };
            },
            deep: true
        },
        'newValue.salario'(newValue) {
            if (!newValue.startsWith('R$')) {
                this.newValue.salario = 'R$' + newValue;
            }
        }
    }
};
</script>
