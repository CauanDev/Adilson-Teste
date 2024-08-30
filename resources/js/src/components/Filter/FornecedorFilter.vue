<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center">
        <div class="flex justify-center gap-2">
            <input v-model="nome" @input="validateName('nome')" maxlength="25"
                class="pl-2 mt-6 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Digite o Nome" />
                <div class="text-center">
                    <h1>Status</h1>
                    <select v-model="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="Suspenso">Suspenso</option>
                        <option value="Ativo">Ativo</option>
                        <option value="all">Todas as Opções</option>
    
                    </select>
                </div>
        </div>

        <div class="flex gap-2 text-center text-xs">
            <div>
                <h1>Data Minima</h1>
                <input v-model="dataMinima" type="date"
                class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
            </div>
           <div>
            <div> Data Maxima</div>
            <input v-model="dataMaxima" type="date"
            class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
           </div>
           
        </div>
        <div class="flex">
            <button type="button" @click="this.$emit('openModalFornecedor')"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Adicionar
                Fornecedor</button>
            <button type="button" :disabled="!isFormValid" @click="applyFilter"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Aplicar
                Filtro <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                </svg>
            </button>
        </div>

    </div>
</template>

<script>
export default {
    name: "FornecedorFilter",
    data() {
        return {
            nome: '',
            produto: '',
            status: 'all',
            ordenar: 'all',
            dataMinima: '',
            dataMaxima: '',
        };
    },
    computed: {
        isFormValid() {
            return this.nome.trim() !== '' ||
                this.produto.trim() !== '' ||
                this.status !== 'all' ||
                this.ordenar !== 'all' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '';
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
            const filter = {}
            if (this.nome.trim()) filter.nome = this.nome.trim()
            if (this.status !== 'all') filter.status = this.status
            if (this.dataMinima) filter.dataMinima = this.dataMinima
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima
            this.$emit('applyFilter', filter)
        }
    }
};
</script>
