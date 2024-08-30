<template>
    <div class="flex justify-center max-h-[200px] overflow-y-auto w-full sm:w-[100%]">
        <div class="w-full">
            <div class="relative">
                <table class="w-full text-sm text-left rtl:text-right text-black">
                    <thead class="text-xs uppercase bg-gray-300 text-center text-black sticky top-0 z-10">
                        <tr>
                            <th v-for="(header, index) in headers" :key="index"
                                class="px-2 py-2 border-b border-gray-400 whitespace-nowrap">
                                {{ header }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="(row, rowIndex) in produtos" :key="rowIndex"
                            class="border-b border-gray-400 text-center">
                            <td class="px-2 py-2">
                                {{ row.name }}
                            </td>
                            <td class="px-2 py-2">
                                {{ row.quantidade }}
                            </td>
                            <td class="px-2 py-2">
                                <ItemCounter :maxValue="row.quantidade" :value="row.predefinido"
                                    @update:value="updateValue(row.name, row.preco, $event)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import http from "../../../services/http.js";
import ItemCounter from "../../Counter/ItemCounter.vue";

export default {
    name: 'ProdutosPredefinidosTable',
    components: { ItemCounter },
    props: {
        headers: {
            type: Array,
            required: true
        },
        initialProdutos: {
            type: [Array, String], // Aceita tanto array quanto string
            required: true
        }
    },
    data() {
        return {
            produtos: [],
            counters: []
        };
    },
    methods: {
        async getProdutos() {
            try {
                const data = await http.get('/produtos');
                // Verifica e processa initialProdutos
                let initialProdutosArray = this.initialProdutos;

                // Se initialProdutos for uma string, tenta parsear como JSON
                if (typeof initialProdutosArray === 'string') {
                    try {
                        initialProdutosArray = JSON.parse(initialProdutosArray);
                    } catch (error) {
                        console.error('Erro ao parsear initialProdutos como JSON:', error);
                        initialProdutosArray = []; // Define como array vazio se houver erro
                    }
                }

                // Certifica-se de que initialProdutosArray é um array
                if (!Array.isArray(initialProdutosArray)) {
                    console.error('initialProdutos não é um array:', initialProdutosArray);
                    initialProdutosArray = [];
                }

                // Processa produtos
                this.produtos = data.data.produtos.map(produto => {
                    const predefinido = initialProdutosArray.find(item => item.name === produto.name);
                    if (predefinido) {
                        return {
                            ...produto,
                            predefinido: predefinido.quantidade,
                            preco: produto.preco
                        };
                    } else {
                        return null;
                    }
                }).filter(produto => produto !== null);

                this.counters = this.produtos.map(produto => ({
                    name: produto.name,
                    quantidade: produto.predefinido,
                    total: produto.preco * produto.predefinido
                }));

                this.$emit('update:counters', this.counters);

            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
            }
        },
        updateValue(name, preco, quantidade) {
            const total = preco * quantidade;
            const index = this.counters.findIndex(item => item.name === name);
            if (index !== -1) {
                this.counters[index] = { name, quantidade, total };
            } else {
                this.counters.push({ name, quantidade, total });
            }
            this.$emit('update:counters', this.counters);
        }
    },
    mounted() {
        this.getProdutos();
    }
};
</script>
