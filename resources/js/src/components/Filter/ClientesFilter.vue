<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center">
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
                <!-- Data Mínima -->
                <div>
                    <h1 class="text-xs">Digite a Data Mínima</h1>
                    <input v-model="dataMinima" type="date"
                        class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                </div>

                <!-- Data Máxima -->
                <div>
                    <h1 class="text-xs">Digite a Data Máxima</h1>
                    <input v-model="dataMaxima" type="date"
                        class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
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

            <!-- Status -->
            <div class="w-[160px] text-center">
                <h1 class="text-xs">Selecione o Status</h1>
                <select v-model="status"
                    class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="Suspenso">Suspenso</option>
                    <option value="Ativo">Ativo</option>
                    <option value="all">Todas as Opções</option>
                </select>
            </div>
            <div class="w-[160px] text-center whitespace-nowrap">
                <h1 class="text-xs">Selecione o Tipo de Cliente</h1>
                <select v-model="tipo"
                class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                <option value="Vip">Vip</option>
                    <option value="Normal">Normal</option>
                    <option value="all">Todas as Opções</option>
                </select>
            </div>

            <!-- Ordenar Por -->
            <div class="text-center w-[160px]">
                <h1 class="text-xs">Ordenar por</h1>
                <select v-model="ordem"
                    class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="all">Nenhuma Opção</option>
                    <option value="maisVendas">Mais Compras</option>
                    <option value="menosVendas">Menos Compras</option>
                </select>
            </div>
        </div>

        <div class="flex h-[45px]">
            <!-- Botão Adicionar Produto -->
            <button type="button" @click="this.$emit('openModalCliente')"
                class="focus:outline-none w-[160px] text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Adicionar Cliente
            </button>

            <!-- Botão Aplicar Filtro -->
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
</template>

<script>
export default {
    name: "ClientesFilter",
    data() {
        return {
            ordem: 'all',
            name: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            idadeMin: '',
            idadeMax: '',
            sex:'all',
            tipo:'all'
        };
    },
    computed: {
        isFormValid() {
            const idadeMin = Number(this.idadeMin);
            const idadeMax = Number(this.idadeMax);

            // Verifica se idadeMin é maior que idadeMax
            const idadeRangeValid = !this.idadeMin || !this.idadeMax || idadeMin <= idadeMax;

            return (this.name.trim() !== '' ||
                this.status !== 'all' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                this.ordem !== '' ||
                (this.idadeMin && this.idadeMax)) &&
                idadeRangeValid;
        }
    },
    methods: {
        validateName(field) {
            this[field] = this[field].replace(/\d/g, '');
            this.validateLength(field);
        },
        validateLength(field) {
            if (this[field].length > 25) this[field] = this[field].slice(0, 25);
        },
        validateQuantity(field) {
            if (isNaN(this[field])) {
                this[field] = '';
            } else {
                this[field] = this[field].replace(/[^0-9]/g, '');
                const maxValues = { idadeMin: 120, idadeMax: 120 };
                const max = maxValues[field];
                if (max && Number(this[field]) > max) this[field] = max.toString();
            }
        },
        applyFilter() {
            const filter = {};
            if (this.name.trim()) filter.nome = this.name.trim();
            if (this.status !== 'all') filter.status = this.status;
            if (this.ordem !== 'all') filter.ordem = this.ordem;
            if (this.sex !== 'all') filter.sex = this.sex;
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            if (this.idadeMin) filter.idadeMin = this.idadeMin;
            if (this.idadeMax) filter.idadeMax = this.idadeMax;
            if (this.tipo) filter.tipo = this.tipo
            this.$emit('applyFilter', filter);
        }
    }
};
</script>
