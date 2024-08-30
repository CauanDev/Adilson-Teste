<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center">

        <div class="flex justify-center gap-2">
            <select v-model="selectedMarca"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                <option value="all">Todas as Marcas</option>
                <option v-for="item in marcas" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <select v-model="selectedSegmento"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                <option value="all">Todos os Segmentos</option>
                <option v-for="item in uniqueSegmentos" :key="item">{{ item }}</option>
            </select>
        </div>
        <div class="flex justify-center gap-2">
            <input v-model="name" @input="validateName('name')" maxlength="25"
                class="pl-2 h-10 mt-4 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Nome do Produto" />
                <div class="text-center">
                    <h1 class="text-xs">Selecione o Status</h1>
                    <select v-model="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="Suspenso">Suspenso</option>
                        <option value="Ativo">Ativo</option>
                        <option value="all">Todas as Opções</option>
                    </select>
                </div>
        </div>
        <div class="flex justify-center gap-2">
            <input v-model="quantidadeMin" @input="validateQuantity('quantidadeMin')" maxlength="7"
                class="pl-2 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Quantidade Mínima" />
            <input v-model="quantidadeMax" @input="validateQuantity('quantidadeMax')" maxlength="7"
                class="pl-2 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Quantidade Máxima" />
        </div>
        <div class="flex gap-2 text-center">
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
        <div class="flex">
            <button type="button" @click="this.$emit('openModalProduto')"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Adicionar
                Produto</button>
            <button type="button" @click="applyFilter"
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
import http from "../../services/http.js"
import InputNumber from "../Inputs/InputNumber.vue"
export default {
    name: "ProdutoFilter",
    components: { InputNumber },
    data() {
        return {
            name: '',
            selectedMarca: 'all',
            selectedSegmento: 'all',
            quantidadeMin: '',
            quantidadeMax: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            fornecedor: [],
            selectedFornecedor: 'all',
            marcas: []
        };
    },
    computed: {
        uniqueSegmentos() {
            const segmentos = this.marcas.map(marca => marca.segmento);
            return [...new Set(segmentos)];
        },
        // isFormValid() {
        //     return this.name.trim() !== '' ||
        //         this.selectedMarca !== 'all' ||
        //         this.selectedSegmento !== 'all' ||
        //         this.status !== 'all' ||
        //         this.dataMinima !== '' ||
        //         this.dataMaxima !== '' ||
        //         this.selectedFornecedor !== 'all' ||
        //         this.quantidadeMax !== '' ||
        //         this.quantidadeMin !== '' 
        // }
    },
    methods: {
        validateName(field) {
            this[field] = this[field].replace(/\d/g, '');
            this.validateLength(field);
        },
        validateLength(field) {
            if (this[field].length > 19) this[field] = this[field].slice(0, 19);
        },
        validateQuantity(field) {
            if (isNaN(this[field])) {
                this[field] = '';
            } else {
                this[field] = this[field].replace(/[^0-9]/g, '');
            }
        },
        async getMarcas() {
            try {
                const data = await http.post('/filter-marca', { "status": "Ativo" });
                this.marcas = data.data.marcas.map(marca => ({
                    name: marca.nome,
                    id: marca.id,
                    segmento: marca.segmento
                }));
            } catch (error) {
                console.log(error);
            }
        },
        applyFilter() {
            const filter = {}
            if (this.name.trim()) filter.name = this.name.trim()
            if (this.selectedMarca !== 'all') filter.marca_id = this.selectedMarca
            if (this.selectedSegmento !== 'all') filter.segmento = this.selectedSegmento
            if (this.quantidadeMin) filter.quantidadeMin = this.quantidadeMin
            if (this.quantidadeMax) filter.quantidadeMax = this.quantidadeMax
            if (this.status !== 'all') filter.status = this.status
            if (this.dataMinima) filter.dataMinima = this.dataMinima
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima
            if (this.selectedFornecedor !== 'all') filter.fornecedor_id = this.selectedFornecedor
            this.$emit('applyFilter', filter)
        }
    },
    created() {
        this.getMarcas()
    }
};
</script>
