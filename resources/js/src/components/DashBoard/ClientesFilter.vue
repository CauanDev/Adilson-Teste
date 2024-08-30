<template>
    <div class="flex flex-col gap-2 w-full items-center whitespace-nowrap">
        <div class="flex gap-2 mobile">
            <div class="flex justify-center gap-2 ">
                <!-- Campo Nome -->
                <input v-model="name" @input="validateName('name')" maxlength="25"
                    class="pl-2 mt-4 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Digite o Nome" />

                <!-- Campo Sexo -->
                <div class="text-center w-[160px]">
                    <h1 class="text-xs">Escolher o Sexo</h1>
                    <select v-model="sex"
                        class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="all">Nenhuma Opção</option>
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
                        class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Todas as Opções</option>
                        <option value="Suspenso">Suspenso</option>
                        <option value="Ativo">Ativo</option>
                    </select>
                </div>

                <div class="w-[160px] text-center">
                    <h1 class="text-xs">Selecione o Tipo</h1>
                    <select v-model="tipo"
                        class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Todas as Opções</option>
                        <option value="Normal">Normal</option>
                        <option value="Vip">VIP</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-center items-center gap-2 w-[50%] mobile">
            <div class="flex gap-2 mt-4">
                <!-- Idade Mínima -->
                <input v-model="idadeMin" @input="validateAge('idadeMin')" maxlength="3"
                    class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Idade Mínima" />

                <!-- Idade Máxima -->
                <input v-model="idadeMax" @input="validateAge('idadeMax')" maxlength="3"
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
            <div class="flex gap-2 w-full items-center justify-center ">
                <button type="button" :disabled="!isFormValid" @click="applyFilter"
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
            sex: 'all',
            tipo: '' // Adicionado tipo aqui
        };
    },
    computed: {
        isFormValid() {
            const idadeMin = Number(this.idadeMin);
            const idadeMax = Number(this.idadeMax);

            // Verifica se idadeMin é maior que idadeMax
            const idadeRangeValid = !this.idadeMin || !this.idadeMax || idadeMin <= idadeMax;

            // O formulário é válido se:
            // - Pelo menos um campo de filtro é preenchido
            // - A faixa de idade é válida (ou não é preenchida)
            // - A faixa de salário é válida (ou não é preenchida)
            return (this.name.trim() !== '' ||
                this.sex !== 'all' ||
                this.status !== '' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                this.ordem !== '' ||
                this.idadeMin || this.idadeMax ||
                this.tipo) &&
                idadeRangeValid;
        }
    },
    methods: {
        validateName(field) {
            if (field === 'name') {
                this.name = this.name.replace(/[0-9]/g, ''); // Remove números
                if (this.name.trim() === '') {
                    this.name = '';
                }
            }
        },
        validateAge(field) {
            const value = Number(this[field]);
            if (isNaN(value)) {
                this[field] = '';
            } else if (value > 120) {
                this[field] = '120';
            } else {
                this[field] = value.toString().replace(/[^0-9]/g, '');
            }
        },
        applyFilter() {
            const filters = {};

            if (this.name.trim()) filters.nome = this.name.trim();
            if (this.status) filters.status = this.status;
            if (this.dataMinima) filters.dataMinima = this.dataMinima;
            if (this.dataMaxima) filters.dataMaxima = this.dataMaxima;
            if (this.ordem) filters.ordem = this.ordem;
            if (this.idadeMin && this.idadeMin !== '0') filters.idadeMin = this.idadeMin;
            if (this.idadeMax && this.idadeMax !== '0') filters.idadeMax = this.idadeMax;
            if (this.sex !== 'all') filters.sexo = this.sex;
            if (this.tipo) filters.tipo = this.tipo; // Adicionado filtro tipo

            // Somente emitir se houver pelo menos um filtro
            if (Object.keys(filters).length > 0) {
                this.$emit('applyFilter', filters, 'clientes');
            }
        },
    }
}
</script>

<style scoped>
.mobile {
    flex-direction: row;
}

@media screen and (max-width: 640px) {
    .mobile {
        flex-direction: column;
    }
}
</style>
