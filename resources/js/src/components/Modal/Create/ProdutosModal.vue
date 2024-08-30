<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Produto
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
                    <InputName v-model="name" placeholder="Digite o Nome" />
                    <InputNumber v-model="quantidade" placeholder="Digite a Quantidade" />
                    <InputSalario v-model="preco" placeholder="Digite o Valor de Custo" />
                    <InputSalario v-model="valor" placeholder="Digite o Preço" />

                    <!-- Mensagem de erro se o valor de custo for maior ou igual ao preço -->
                    <div v-if="preco && valor && parseFloat(preco.replace(/^R\$\s?/, '').replace(',', '.').trim()) >= parseFloat(valor.replace(/^R\$\s?/, '').replace(',', '.').trim())"
                        class="text-red-500 text-sm mt-2">
                        O valor de custo deve ser menor que o preço.
                    </div>
                    <label class="block text-sm font-medium text-gray-900">Selecione a Marca</label>

                    <div v-if="marcas.length > 0">
                        <select
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                            v-model="marcaID">
                            <option value="" disabled selected>Selecione uma marca</option>
                            <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                {{ marca.name }}
                            </option>
                        </select>
                    </div>
                    <div v-else class="text-center">
                        <router-link to="/marcas">
                            <p class="text-gray-500">Nenhuma Marca Disponível</p>
                            <button type="button"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Criar
                                Marca</button>
                        </router-link>
                    </div>


                    <!-- Adiciona a condição para mostrar o botão somente se houver marcas e a validação for bem-sucedida -->
                    <button v-if="marcas.length > 0" :disabled="isFormValid && isPrecoInvalido" @click="submitForm"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Criar Produto
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import http from "./../../../../src/services/http.js"
import InputName from "./../../../../src/components/Inputs/InputName.vue"
import InputNumber from "./../../../../src/components/Inputs/InputNumber.vue"
import InputSalario from "../../Inputs/InputSalario.vue";

export default {
    name: "ProdutosModal",
    components: { InputName, InputNumber, InputSalario },
    data() {
        return {
            marcas: [],
            name: '',
            marca: '',
            valor: '',
            preco: '',
            quantidade: '',
            marcaID: ''
        };
    },
    computed: {
        isFormValid() {
            // Verifica se todos os campos necessários estão preenchidos e se a marcaID está selecionada
            return this.name.trim() && this.quantidade && this.preco.trim() && this.valor.trim() && this.marcaID;
        },
        isPrecoInvalido() {
            // Verifica se o valor de custo é maior ou igual ao preço
            if (!this.preco || !this.valor) return false;
            const precoFloat = parseFloat(this.preco.replace(/^R\$\s?/, '').replace(',', '.').trim());
            const valorFloat = parseFloat(this.valor.replace(/^R\$\s?/, '').replace(',', '.').trim());
            return precoFloat >= valorFloat;
        }
    },
    methods: {
        async getMarcas() {
            try {
                const data = await http.post('/filter-marca', { "status": "Ativo" });
                this.marcas = data.data.marcas.map(marca => ({
                    name: marca.nome,
                    id: marca.id
                }));
            } catch (error) {
                console.log(error);
            }
        },
        submitForm() {
            this.$emit('addProduto', {
                name: this.name,
                marca: this.marca,
                preco: this.preco.replace(/^R\$\s?/, '').replace(',', '.').trim(),
                valor:this.valor.replace(/^R\$\s?/, '').replace(',', '.').trim(),
                quantidade: this.quantidade,
                marcaID: this.marcaID
            });
            this.$emit('close');
        }
    },
    created() {
        this.getMarcas();
    }
};
</script>
