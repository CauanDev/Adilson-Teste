<template>
    <div class="flex flex-col gap-2 w-full justify-center items-center whitespace-nowrap">
        <div class="flex gap-2 mobile">
            <div class="flex justify-center gap-2">
                <!-- Campo Nome -->
                <input v-model="name" @input="validateName('name')" maxlength="25"
                    class="pl-2 mt-4 w-[160px] h-10 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                    placeholder="Digite o Produto" />

                <!-- Campo Marca -->
                <div class="text-center w-[160px]">
                    <h1 class="text-xs">Escolher a Marca</h1>
                    <select v-model="selectedMarca"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Nenhuma Opção</option>
                        <option v-for="marca in marcas" :key="marca" :value="marca">{{ marca }}</option>
                    </select>
                </div>

                <!-- Campo Segmento -->
                <div class="text-center w-[160px]">
                    <h1 class="text-xs">Escolher o Segmento</h1>
                    <select v-model="selectedSegmento"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">Nenhuma Opção</option>
                        <option v-for="segmento in segmentos" :key="segmento" :value="segmento">{{ segmento }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-2 mobile">
            <div class="flex flex-col gap-2">
                <div class="flex gap-2 ">
                    <input v-model="quantidadeMin" @input="validateNumericInput('quantidadeMin')" maxlength="6"
                        class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                        placeholder="Quantidade Mínima" />

                    <input v-model="quantidadeMax" @input="validateNumericInput('quantidadeMax')" maxlength="6"
                        class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                        placeholder="Quantidade Máxima" />
                    <input v-model="fornecedor" @input="validateName('fornecedor')" maxlength="25"
                        class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                        placeholder="Nome do Fornecedor" />

                </div>
                <div class="flex gap-2">
                    <div>
                        <div class="flex gap-2">
                            <input v-model="valorMin" @input="validateNumericInput('valorMin')" maxlength="7"
                                class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                                placeholder="Valor Mínimo" />

                            <input v-model="valorMax" @input="validateNumericInput('valorMax')" maxlength="7"
                                class="pl-2 h-10 w-[160px] py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                                placeholder="Valor Máximo" />
                        </div>
                        <div class="flex gap-2 text-center text-xs">
                            <div>
                                <h1>Data Mínima</h1>
                                <input v-model="dataMinima" type="date"
                                    class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                            </div>
                            <div>
                                <div>Data Máxima</div>
                                <input v-model="dataMaxima" type="date"
                                    class="pr-3 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-0.5 justify-center items-center">
                        <button type="button" @click="applyFilter" :disabled="!isFormValid"
                            class="w-[160px] h-[35px] text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Aplicar Filtro
                            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

<script>
import http from "../../services/http.js"

export default {
    name: "FuncionarioFilter",
    data() {
        return {
            name: '',
            status: '',
            dataMinima: '',
            dataMaxima: '',
            valorMin: '',
            valorMax: '',
            quantidadeMin: '',
            quantidadeMax: '',
            selectedMarca: '',
            selectedSegmento: '',
            marcas: [],
            segmentos: [],
            fornecedor: ''
        };
    },
    computed: {
        isFormValid() {
            const valorMin = Number(this.valorMin);
            const valorMax = Number(this.valorMax);
            const quantidadeMin = Number(this.quantidadeMin);
            const quantidadeMax = Number(this.quantidadeMax);

            const valorRangeValid = !this.valorMin || !this.valorMax || valorMin <= valorMax;
            const quantidadeRangeValid = !this.quantidadeMin || !this.quantidadeMax || quantidadeMin <= quantidadeMax;
            const dataRangeValid = !this.dataMinima || !this.dataMaxima || new Date(this.dataMinima) <= new Date(this.dataMaxima);

            return (this.name.trim() !== '' ||
                this.status !== '' ||
                this.dataMinima !== '' ||
                this.dataMaxima !== '' ||
                this.selectedMarca !== '' ||
                this.selectedSegmento !== '' ||
                this.fornecedor.trim() !== '' ||  // Incluindo a validação do fornecedor
                (this.valorMin || this.valorMax) ||
                (this.quantidadeMin || this.quantidadeMax)) &&
                valorRangeValid &&
                quantidadeRangeValid &&
                dataRangeValid;
        }
    },

    methods: {
        validateName(field) {
            if (field === 'name' && this.name.trim() === '') {
                this.name = '';
            }
        },
        validateNumericInput(field) {
            // Remove caracteres não numéricos
            this[field] = this[field].replace(/[^0-9]/g, '');
        },
        validateName(field) {
            if (this[field].trim() === '') {
                this[field] = '';
            }
        },

        applyFilter() {
            this.$emit('applyFilter', {
                name: this.name.trim() || null,
                status: this.status || null,
                dataMinima: this.dataMinima || null,
                dataMaxima: this.dataMaxima || null,
                valorMin: this.valorMin || null,
                valorMax: this.valorMax || null,
                quantidadeMin: this.quantidadeMin || null,
                quantidadeMax: this.quantidadeMax || null,
                marca: this.selectedMarca || null,
                segmento: this.selectedSegmento || null,
                fornecedor: this.fornecedor.trim()
            });
        },
        async getMarca() {
            try {
                const { data } = await http.get('/marcas');
                this.marcas = data.marcas.map((marca) => marca.nome);
                this.segmentos = [...new Set(data.marcas.map((marca) => marca.segmento))]; // Assumindo que segmentos estão no mesmo endpoint
            } catch (error) {
                console.error("Erro ao buscar marcas e segmentos", error);
            }
        }
    },
    created() {
        this.getMarca();
    }
}
</script>
