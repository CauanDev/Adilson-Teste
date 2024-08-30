<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center">
        <div class="flex gap-2 flex-col">
            <div class="flex justify-center gap-2 ">
                <!-- Campo Nome -->
                <input v-model="nameMarca" @input="validateName('nameMarca')" maxlength="25"
                    class="pl-2 mt-4 h-10 w-[170px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Nome da Marca" />

                <input v-model="nameFornecedor" @input="validateName('nameFornecedor')" maxlength="25"
                    class="pl-2 mt-4 h-10 w-[170px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Nome do Fornecedor" />
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
            <div class="w-[160px] text-center">
                <h1 class="text-xs">Selecione o Status</h1>
                <select v-model="status"
                    class="bg-gray-50 w-[170px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="Suspenso">Suspenso</option>
                    <option value="Ativo">Ativo</option>
                    <option value="all">Todas as Opções</option>
                </select>
            </div>
        </div>

        <div class="flex h-[45px]">
            <!-- Botão Adicionar Produto -->
            <button type="button" @click="this.$emit('openModalMarca')"
                class="focus:outline-none w-[160px] text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Adicionar Marca
            </button>

            <!-- Botão Aplicar Filtro -->
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
</template>

<script>
export default {
    name: "ClientesFilter",
    data() {
        return {
            nameFornecedor: '',
            nameMarca: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
        };
    },
    computed: {
        isFormValid() {
            return (this.nameFornecedor.trim() !== '' ||
                this.nameMarca.trim() !== '' ||
                this.status !== 'all' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '');
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
        applyFilter() {
            const filter = {};
            if (this.nameMarca.trim()) filter.nameMarca = this.nameMarca.trim();
            if (this.nameFornecedor.trim()) filter.nameFornecedor = this.nameFornecedor.trim();
            if (this.status) filter.status = this.status;
            if (this.dataMinima) filter.dataMinima = this.dataMinima;
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima;
            this.$emit('applyFilter', filter);
        }
    }
};
</script>
