<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap">
        <!-- Linha 1 -->
        <div class="flex gap-2 mobile">
            <div class="flex justify-center gap-2">
                <input v-model="name" maxlength="25"
                    class="pl-2 mt-4 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Nome do Cliente" />
                    
                <input v-model="nameFuncionario" maxlength="25"
                    class="pl-2 mt-4 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Nome do Funcionario" />
            </div>
        </div>

        <!-- Linha 2 -->
        <div class="flex justify-center items-center gap-2 w-[50%] mobile">
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

        <!-- Linha 3 -->
        <div class="flex gap-2 mobile">
            <div class="flex gap-2">
                <input v-model="valorMin" maxlength="6"
                    class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Valor Mínimo" />

                <input v-model="valorMax" maxlength="6"
                    class="pl-2 h-10 w-[140px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Valor Máximo" />
            </div>
        </div>

        <!-- Linha 4 -->
        <div class="flex justify-center gap-2">
            <div class="w-[160px] text-center">
                <h1 class="text-xs">Selecione o Status</h1>
                <select v-model="status"
                    class="bg-gray-50 w-[160px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    <option value="">Todas as Opções</option>
                    <option value="Não Processado">Não Processado</option>
                    <option value="Entregue">Entregue</option>
                </select>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex gap-0.5">
            <button type="button" @click="openModalFuncionario"
                class="focus:outline-none w-[180px] text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 me-2 text-[12px]">
                Adicionar Pedido
            </button>

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
    name: "PedidosFilter",
    data() {
        return {
            name: '',
            status: '',
            nameFuncionario:'',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: ''
        };
    },
    computed: {
        isFormValid() {
            const valorMin = Number(this.valorMin);
            const valorMax = Number(this.valorMax);

            // Verifica se valorMin é maior que valorMax
            const valorRangeValid = !this.valorMin || !this.valorMax || valorMin <= valorMax;

            // O formulário é válido se pelo menos um campo de filtro é preenchido
            return (this.name.trim() !== '' ||
                this.nameFuncionario.trim() !== ''||
                this.status !== '' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                (this.valorMin.trim() !== '' || this.valorMax.trim() !== '')) &&
                valorRangeValid;
        }
    },
    methods: {
    applyFilter() {
        const filters = {
            name: this.name.trim() || undefined,
            nameFuncionario: this.nameFuncionario.trim() || undefined,
            status: this.status || undefined,
            dataMinima: this.dataMinima || undefined,
            dataMaxima: this.dataMaxima || undefined,
            valorMin: this.valorMin.trim() || undefined,
            valorMax: this.valorMax.trim() || undefined
        };

        // Remove propriedades com valores undefined
        const filteredFilters = Object.fromEntries(
            Object.entries(filters).filter(([_, value]) => value !== undefined)
        );

        this.$emit('applyFilter', filteredFilters,'pedidos');
    },
    openModalFuncionario() {
        this.$emit('openModalFuncionario');
    }
}
};
</script>
