<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap ">
        <div class="flex gap-2 mobile">
            <div class="flex justify-center gap-2 ">
                <!-- Campo Nome -->
                <input v-model="name" @input="validateName('name')" maxlength="25"
                    class="pl-2 mt-4 w-[160px] h-10 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Digite o Nome" />

                <!-- Campo Sexo -->
                <div class="text-center w-[160px]">
                    <h1 class="text-xs">Escolher o Sexo</h1>
                    <select v-model="sex"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Nenhuma Opção</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 text-center">
                <!-- Status -->
                <div class="w-[160px] text-center">
                    <h1 class="text-xs">Selecione o Status</h1>
                    <select v-model="status"
                        class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Todas as Opções</option>
                        <option value="Suspenso">Suspenso</option>
                        <option value="Ativo">Ativo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-center items-center gap-2 w-[50%] mobile">
            <div class="flex gap-2 mt-4">
                <!-- Idade Mínima -->
                <input v-model="idadeMin" @input="validateQuantity('idadeMin')" maxlength="3"
                    class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Idade Mínima" />

                <!-- Idade Máxima -->
                <input v-model="idadeMax" @input="validateQuantity('idadeMax')" maxlength="3"
                    class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Idade Máxima" />
            </div>
            <div class="gap-2 flex text-center">
                <div>
                    <h1 class="text-xs">Digite a Data Mínima</h1>
                    <input v-model="dataMinima" type="date"
                        class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                </div>

                <div>
                    <h1 class="text-xs">Digite a Data Máxima</h1>
                    <input v-model="dataMaxima" type="date"
                        class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                </div>
            </div>

        </div>

        <div class="flex gap-2 mobile">
            <div class="flex gap-2 ">

                <input v-model="salarioMin" @input="validateQuantity('salarioMin')" maxlength="6"
                    class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Salário Mínimo" />

                <input v-model="salarioMax" @input="validateQuantity('salarioMax')" maxlength="6"
                    class="pl-2 h-10 w-[140px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Salário Máximo" />
            </div>
            <div class="flex gap-0.5">
                <button type="button" @click="this.$emit('openModalFuncionario')"
                    class="focus:outline-none w-[180px] text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3  me-2 text-[12px]">
                    Adicionar Funcionario
                </button>

                <button type="button" @click="applyFilter"
                    class="w-[160px] text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Aplicar Filtro
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FuncionarioFilter",
    data() {
        return {
            ordem: '',
            name: '',
            status: '',
            dataMinima: '',
            dataMaxima: '',
            idadeMin: '',
            idadeMax: '',
            sex: '',
            salarioMin: '',
            salarioMax: ''
        };
    },
    computed: {
        isFormValid() {
            const idadeMin = Number(this.idadeMin);
            const idadeMax = Number(this.idadeMax);
            const salarioMin = Number(this.salarioMin);
            const salarioMax = Number(this.salarioMax);

            // Verifica se idadeMin é maior que idadeMax
            const idadeRangeValid = !this.idadeMin || !this.idadeMax || idadeMin <= idadeMax;

            // Verifica se salarioMin é maior que salarioMax
            const salarioRangeValid = !this.salarioMin || !this.salarioMax || salarioMin <= salarioMax;

            // O formulário é válido se:
            // - Pelo menos um campo de filtro é preenchido
            // - A faixa de idade é válida (ou não é preenchida)
            // - A faixa de salário é válida (ou não é preenchida)
            return (this.name.trim() !== '' ||
                this.status !== '' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                this.ordem !== '' ||
                this.sex !== '' ||
                (this.idadeMin || this.idadeMax) ||
                (this.salarioMin || this.salarioMax)) &&
                idadeRangeValid &&
                salarioRangeValid;
        }
    },
    methods: {
        validateName(field) {
            if (field === 'name' && this.name.trim() === '') {
                this.name = '';
            }
        },
        validateQuantity(field) {
            if ((field === 'idadeMin' && !this.idadeMin) ||
                (field === 'idadeMax' && !this.idadeMax) ||
                (field === 'salarioMin' && !this.salarioMin) ||
                (field === 'salarioMax' && !this.salarioMax)) {
                this[field] = '';
            }
        },
        applyFilter() {
            this.$emit('applyFilter', {
                name: this.name.trim() || null,
                status: this.status || null,
                dataMinima: this.dataMinima || null,
                dataMaxima: this.dataMaxima || null,
                ordem: this.ordem || null,
                idadeMin: this.idadeMin || null,
                idadeMax: this.idadeMax || null,
                salarioMinimo: this.salarioMin || null,
                salarioMaximo: this.salarioMax || null,
                sexo: this.sex || null
            });
        }
    }
};
</script>
