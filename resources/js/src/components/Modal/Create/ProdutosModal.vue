<template>
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 flex items-center justify-center z-50 overflow-hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Adicionar Compra
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
                    <InputName v-model="name" @input="filterProducts" placeholder="Digite o Nome" />
                    <ComprasProdutos :body="filteredProducts" @productSelected="productSelected" />
                    <InputNumber v-model="quantidade" placeholder="Digite a Quantidade" />
                    <div v-if="!disabled" class="flex flex-col gap-2">
                        <InputSalario v-model="preco" placeholder="Digite o Valor de Custo" />
                        <InputSalario v-model="valor" placeholder="Digite o Preço" />
                    </div>

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
                        Fazer Compra
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import http from "./../../../../src/services/http.js";
import InputName from "./../../../../src/components/Inputs/InputName.vue";
import InputNumber from "./../../../../src/components/Inputs/InputNumber.vue";
import InputSalario from "../../Inputs/InputSalario.vue";
import ComprasProdutos from "../../Tables/Compras/ComprasProdutos.vue";

export default {
    name: "ProdutosModal",
    components: { InputName, InputNumber, InputSalario, ComprasProdutos },
    props: {
        allCompras: {
            type: Array
        }
    },
    data() {
        return {
            marcas: [],
            name: '',
            marca: '',
            valor: '',
            preco: '',
            quantidade: '',
            marcaID: '',
            produtos: [],
            produto_id: null,
            filteredProducts: [],
            choosedProduct: {},
            disabled: false
        };
    },
    computed: {
        isFormValid() {
            return this.name.trim() && this.quantidade && this.preco.trim() && this.valor.trim() && this.marcaID;
        },
        isPrecoInvalido() {
            if (!this.preco || !this.valor) return false;
            const precoFloat = parseFloat(this.preco.replace(/^R\$\s?/, '').replace(',', '.').trim());
            const valorFloat = parseFloat(this.valor.replace(/^R\$\s?/, '').replace(',', '.').trim());
            return precoFloat >= valorFloat;
        }
    },
    methods: {
        async getMarcas() {
            try {
                const data = await http.post('/filter-marca', { status: "Ativo" });
                this.marcas = data.data.marcas.map(marca => ({
                    name: marca.nome,
                    id: marca.id
                }));
            } catch (error) {
                console.error(error);
            }
        },
        async getProdutos() {
            try {
                const data = await http.get('/produtos');
                this.produtos = data.data.produtos;
            } catch (error) {
                console.error(error);
            }
        },
        filterProducts() {
            const searchTerm = this.name.trim().toLowerCase();
            if (searchTerm) {
                this.filteredProducts = this.produtos.filter(produto =>
                    produto.name.toLowerCase().includes(searchTerm)
                );
            } else {
                this.filteredProducts = [];
            }

            // Limpar campos se nenhum produto for encontrado
            if (this.filteredProducts.length === 0) {
                // alert("Vou apagar 1")

                this.preco = '';
                this.valor = '';
                this.choosedProduct = null;
                this.disabled = false;
            } else if (this.filteredProducts.length === 1) {
                this.productSelected(this.filteredProducts[0]);
            }
        },
        async filterProductsByMarca() {
            const searchTerm = this.name.trim().toLowerCase();
            if (searchTerm) {
                const filtered = this.produtos.filter(produto =>
                    produto.name.toLowerCase().includes(searchTerm) &&
                    (!this.marcaID || produto.marca_id === this.marcaID)
                );
                this.filteredProducts = filtered;
            } else {
                this.filteredProducts = [];
            }
            if (this.filteredProducts.length === 0) {
                // alert("Vou apagar")

                if (this.quantidade == '') {
                    this.preco = '';
                    this.valor = '';
                }


                this.choosedProduct = null;
                this.disabled = false;
            }
            else {
                this.disabled = true
                this.productSelected(this.filteredProducts);
            }
        },
        productSelected(product) {
            const produto = this.allCompras.find(produto => produto.produto_id === product.id);
            if (produto) {
                this.marcaID = produto.produto.marca_id;
                this.preco = produto.valor;
                this.valor = produto.produto.preco;
                this.produto_id = produto.produto_id;
                this.choosedProduct = produto;
                this.disabled = true;
            }
        },
        submitForm() {
            const body = {
                preco: this.preco.replace(/^R\$\s?/, '').replace(',', '.').trim(),
                valor: this.valor.replace(/^R\$\s?/, '').replace(',', '.').trim(),
                name: this.name,
                marcaID: this.marcaID,
                quantidade: this.quantidade,
                ...(this.choosedProduct ? { produtoID: this.produto_id } : {})
            };


            this.$emit('addProduto', body);
            this.$emit('close');
        }
    },
    watch: {
        marcaID: 'filterProductsByMarca'
    },
    created() {
        this.getMarcas();
        this.getProdutos();
    }
};
</script>
