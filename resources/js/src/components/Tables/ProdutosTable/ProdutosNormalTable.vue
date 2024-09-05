<template>
    <div class="flex justify-center max-h-[200px] overflow-y-auto w-full sm:w-[100%]" v-if="allProdutos.length > 0">
        <div class="w-full flex flex-col items-center gap-2 mt-2">
            <InputName small placeholder="Nome do Produto" @input="applyFilter" v-model="nameProduto" />

            <div class="relative">
                <table class="w-full text-sm text-left rtl:text-right text-black">
                    <thead class="text-xs uppercase bg-gray-300 text-center text-black sticky top-0 z-10">
                        <tr>
                            <th v-for="(header, index) in headers" :key="index"
                                class="px-1 py-2 border-b border-gray-400 whitespace-nowrap">
                                {{ header }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="(row, rowIndex) in produtos" :key="rowIndex"
                            class="border-b border-gray-400 text-center">
                            <td class="px-1 py-2 text-xs">
                                {{ row.name }}
                            </td>
                            <td class=" py-2">
                                R${{ row.preco.replace('.', ',') }}
                            </td>
                            <td>{{row.quantidade}}</td>
                            <td class=" py-2">
                                <ItemCounter 
                                    :maxValue="row.quantidade"
                                    :value="getCounterValue(row.name)"
                                    @update:value="updateValue(row.name, row.preco, $event)" 
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div v-else class="text-center">
        <router-link to="/compras">
            <p class="text-gray-500 text-xs mb-0.5">Nenhum Produto Disponível</p>
            <button type="button"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Criar
                Produto</button>
        </router-link>
    </div>
</template>

<script>
import http from "../../../services/http.js";
import ItemCounter from "../../Counter/ItemCounter.vue";
import InputName from "../../Inputs/InputName.vue";

export default {
    name: 'ProdutosNormalTable',
    components: { ItemCounter, InputName },
    props: {
        headers: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            allProdutos: [],
            nameProduto: '',
            produtos: [],
            counters: []
        };
    },
    methods: {
        async getProdutos() {
            try {
                const data = await http.post('/filter-produto', { "status": "Ativo" });
                this.allProdutos = data.data.produtos;
                this.produtos = data.data.produtos;
            } catch (error) {
                console.log(error);
            }
        },
        applyFilter() {
            const filteredProdutos = this.allProdutos.filter(produto => {
                let isValid = true;
                if (this.nameProduto !== '') {
                    if (!produto.name.toLowerCase().includes(this.nameProduto.toLowerCase())) isValid = false;
                }
                return isValid;
            });
            this.produtos = filteredProdutos;
        },
        updateValue(name, preco, quantidade) {
            const index = this.counters.findIndex(item => item.name === name);

            if (index !== -1) {
                if (quantidade > 0) {
                    this.counters[index].quantidade = quantidade;
                    this.counters[index].total = preco * quantidade;
                } else {
                    this.counters.splice(index, 1);
                }
            } else if (quantidade > 0) {
                this.counters.push({ name, quantidade, total: preco * quantidade });
            }

            // Emitir o valor total atualizado
            this.emitTotal();
        },
        getCounterValue(name) {
            const counter = this.counters.find(counter => counter.name === name);
            return counter ? counter.quantidade : 0;
        },
        emitTotal() {
            const total = this.counters.reduce((sum, counter) => sum + counter.total, 0);
            console.log(total)
            this.$emit('update:counters', this.counters);
        }
    },
    mounted() {
        this.getProdutos();
    }
};
</script>
