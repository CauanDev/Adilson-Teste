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
                                R${{ row.preco.replace('.',',') }}
                            </td>                            
                            <td class="px-2 py-2">
                                <ItemCounter :maxValue="row.quantidade"
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
    name: 'ProdutosNormalTable',
    components: { ItemCounter },
    props: {
        headers: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            produtos: [],
            counters: [] // Mantenha um objeto de contadores vazio
        };
    },
    methods: {
        async getProdutos() {
            try {
                const data = await http.post('/filter-produto',{"status":"Ativo"});
                this.produtos = data.data.produtos;
            } catch (error) {
                console.log(error);
            }
        },
        updateValue(name, preco, quantidade) {
            // Calcule o total
            const total = preco * quantidade;

            // Verifique se o produto já existe no array de contadores
            const index = this.counters.findIndex(item => item.name === name);
            if (index !== -1) {
                // Atualize o produto existente
                this.counters[index] = { name, quantidade, total };
            } else {
                // Adicione um novo produto
                this.counters.push({ name, quantidade, total });
            }
            // Emita o evento com o array atualizado
            this.$emit('update:counters', this.counters);
        }
    },
    mounted() {
        this.getProdutos();
    }
};
</script>