<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center mt-2">
        <div class="flex justify-center gap-2">
            <input v-model="name" @input="validateName('name')" maxlength="25"
                class="pl-2 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Digite o Produto" />
                <input v-model="segmento" maxlength="25"
                class="pl-2 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Digite o Segmento" />
            <input v-model="marca" @input="validateName('marca')" maxlength="25"
                class="pl-2 h-10 w-[28%] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                placeholder="Digite a Marca" />
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
import http from "../../services/http.js"
import InputNumber from "../Inputs/InputNumber.vue"
export default {
    name: "ProdutoFilter",
    components: { InputNumber },
    data() {
        return {
            ordem:'all',
            name: '',
            marca: '',
            quantidadeMin: '',
            quantidadeMax: '',
            status: 'all',
            dataMinima: '',
            dataMaxima: '',
            fornecedor: [],
            segmento:'',
            selectedFornecedor: 'all'
        };
    },
    computed: {
        isFormValid() {
            return this.name.trim() !== '' ||
                this.marca.trim() !== '' ||
                this.status !== 'all' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                this.selectedFornecedor !== 'all'||
                this.quantidadeMax!==''||
                this.quantidadeMin!=='' ||
                this.ordem!==''
        }
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
        applyFilter() {
            const filter = {}
            if (this.name.trim()) filter.name = this.name.trim()
            if (this.marca.trim()) filter.marca = this.marca.trim()
            if (this.quantidadeMin) filter.quantidadeMin = this.quantidadeMin
            if (this.quantidadeMax) filter.quantidadeMax = this.quantidadeMax
            if (this.status !== 'all') filter.status = this.status
            if (this.dataMinima) filter.dataMinima = this.dataMinima
            if (this.dataMaxima) filter.dataMaxima = this.dataMaxima
            if (this.selectedFornecedor !== 'all') filter.fornecedor_id = this.selectedFornecedor
            if (this.ordem !=='all') filter.ordem = this.ordem
            if (this.segmento !== '') filter.segmento = this.segmento
            this.$emit('applyFilter', filter,'produtos')
        },
        async getFornecedor() {
            try {
                const data = await http.get('/fornecedores')
                this.fornecedor = data.data.fornecedores
            }
            catch (error) {
                console.log(error)
            }
        }
    },
    created() {
        this.getFornecedor()
    }
};
</script>
