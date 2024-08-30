<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 ml-4 whitespace-nowrap">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group select-none">
                    <tr>
                        <!-- Cabeçalho para a coluna Nome -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('nome')">
                                <IconArrowDown v-if="sortColumn === 'nome' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nome
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Nome do Fornecedor -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('fornecedorNome')">
                                <IconArrowDown v-if="sortColumn === 'fornecedorNome' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nome Fornecedor
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Quantidade Produtos -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('quantidadeProdutos')">
                                <IconArrowDown v-if="sortColumn === 'quantidadeProdutos' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nº de Produtos
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Segmento -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('segmento')">
                                <IconArrowDown v-if="sortColumn === 'segmento' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Segmento
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Status -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('status')">
                                <IconArrowDown v-if="sortColumn === 'status' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Status
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Itera sobre a lista de itens ordenados -->
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class">
                        <td class="td-class text-center rounded-l-xl whitespace-nowrap" data-label="Nome">{{ item.nome }}</td>
                        <td class="td-class text-center" data-label="Nome Fornecedor">{{ item.fornecedorNome }}</td>
                        <td class="td-class text-center" data-label="Quantidade de Produtos">{{ item.produtos.length }}</td>
                        <td class="td-class text-center" data-label="Segmento">{{ item.segmento }}</td>
                        <td class="td-class text-center cursor-pointer" data-label="Status" @click="$emit('update', item)">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Ativo',
                                'bg-red-600 text-white whitespace-nowrap': item.status === 'Suspenso'
                            }"
                                class="rounded-md px-2 py-1 text-xs font-semibold uppercase antialiased border border-black">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center rounded-r-xl flex flex-col sm:flex-row gap-2 whitespace-nowrap" data-label="Ações">
                            <button type="button" @click="$emit('delete', item)"
                                class="border border-black focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm p-2">Apagar</button>
                            <button type="button" @click="$emit('details', item)"
                                class="border border-black text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-2">Ver Detalhes</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import IconArrowDown from '../../Svg/IconArrowDown.vue';

export default {
    name: "PedidoTable",
    emits: ['delete', 'details', 'update'],
    components: { IconArrowDown },
    props: {
        body: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'nome', // Define a coluna inicial para ordenação
            sortDirection: 'asc', // Define a direção inicial da ordenação
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                if (this.sortColumn === 'quantidadeProdutos') {
                    // Ordena valores numéricos com base na coluna 'quantidadeProdutos'
                    return (a.produtos.length - b.produtos.length) * modifier;
                } else {
                    // Ordena strings com base nas colunas 'nome', 'fornecedorNome', 'segmento' e 'status'
                    if (a[this.sortColumn] < b[this.sortColumn]) return -1 * modifier;
                    if (a[this.sortColumn] > b[this.sortColumn]) return 1 * modifier;
                    return 0;
                }
            });
        }
    },
    methods: {
        sortTable(column) {
            if (this.sortColumn === column) {
                // Alterna a direção da ordenação se a coluna já estiver selecionada
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                // Define a coluna e a direção de ordenação padrão se a coluna for diferente
                this.sortDirection = 'asc';
            }
            this.sortColumn = column;
        }
    }
};
</script>

<style scoped>
/* Estilos para evitar rolagem horizontal */
html,
body {
    overflow-x: hidden;
}

.tr-class {
    @apply flex flex-col sm:table-row;
}

.td-class {
    @apply px-4 py-3 bg-[#D3D3D3];
}

@media (max-width: 639px) {
    table {
        @apply w-full max-w-xs mx-auto;
        border-collapse: collapse;
    }

    .td-class {
        @apply text-gray-900;
        display: block;
        width: 100%;
        position: relative;
        padding-left: 50%;
        box-sizing: border-box;
        font-size: 0.875rem;
    }

    .td-class::before {
        content: attr(data-label);
        font-weight: bold;
        position: absolute;
        left: 0;
        width: 45%;
        padding-left: 10px;
        white-space: nowrap;
    }

    .tr-class {
        @apply flex flex-col mb-2;
    }

    .td-class span {
        @apply text-xs px-2 py-1;
    }

    .td-class[data-label="Ações"] {
        display: flex;
        text-align: center;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        justify-items: flex-end;
        padding-left: 0;
        padding-right: 0;
    }

    .td-class[data-label="Ações"] button {
        display: block;
        width: auto;
        margin-bottom: 0.5rem;
    }

    .td-class[data-label="Ações"] button:last-of-type {
        margin-bottom: 0;
    }

    .td-class {
        @apply rounded-none;
    }
}
</style>
