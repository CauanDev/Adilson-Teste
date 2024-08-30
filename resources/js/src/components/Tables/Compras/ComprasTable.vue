<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 ml-4 whitespace-nowrap">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group">
                    <tr class="select-none">
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('produto.name')">
                                <IconArrowDown class="mr-2" />
                                Nome do Produto
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('quantidade')">
                                <IconArrowDown class="mr-2" />
                                Nº de Produtos
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('total')">
                                <IconArrowDown class="mr-2" />
                                Valor Total
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('produto.marca.nome')">
                                <IconArrowDown class="mr-2" />
                                Marca
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('produto.marca.segmento')">
                                <IconArrowDown class="mr-2" />
                                Segmento
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class ">
                        <td class="td-class text-center rounded-l-xl" data-label="Nome do Produto">
                            {{ item.produto ? item.produto.name : "Produto Apagado" }}
                        </td>
                        <td class="td-class text-center" data-label="Nº de Produtos">{{ item.quantidade }}</td>
                        <td class="td-class text-center" data-label="Valor Total">R$ {{ item.total }}</td>
                        <td class="td-class text-center" data-label="Marca">
                            {{ item.produto ? item.produto.marca.nome : "Produto Apagado" }}
                        </td>
                        <td class="td-class text-center" data-label="Segmento">
                            {{ item.produto ? item.produto.marca.segmento : "Produto Apagado" }}
                        </td>
                        <td class="td-class text-center rounded-r-xl flex flex-col sm:flex-row gap-2"
                            data-label="Ações">
                            <button type="button" @click="$emit('details', item)"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-2">
                                Ver Detalhes
                            </button>
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
    name: "ComprasTable",
    emits: ['delete', 'details', 'update'],
    components: { IconArrowDown },
    props: {
        body: {
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'produto.name',
            sortDirection: 'asc',
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;

                // Função para navegar através de propriedades aninhadas
                const getValue = (item, column) => {
                    return column.split('.').reduce((acc, key) => acc && acc[key], item);
                };

                const valueA = getValue(a, this.sortColumn);
                const valueB = getValue(b, this.sortColumn);

                if (typeof valueA === 'string' && typeof valueB === 'string') {
                    return modifier * valueA.localeCompare(valueB);
                } else if (typeof valueA === 'number' && typeof valueB === 'number') {
                    return modifier * (valueA - valueB);
                } else {
                    return modifier * (valueA < valueB ? -1 : 1);
                }
            });
        }
    },
    methods: {
        sortTable(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortDirection = 'asc';
            }
            this.sortColumn = column;
        }
    }
};
</script>



<style scoped>
/* Ensure no horizontal scrolling */
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
        /* smaller font size */
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
        /* smaller padding */
    }

    /* Mobile-specific styles for Ações column */
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
        /* Space between buttons */
    }

    .td-class[data-label="Ações"] button:last-of-type {
        margin-bottom: 0;
        /* Remove bottom margin for the last button */
    }

    .td-class {
        @apply rounded-none;
    }
}
</style>
