<template>
    <div class="max-h-[250px] overflow-y-auto">
        <div class="flex items-center justify-center sm:ml-0 ml-4  p-2">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group select-none">
                    <tr>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('name')">
                                <IconArrowDown v-if="sortColumn === 'name' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nome
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('total_vendas')">
                                <IconArrowDown v-if="sortColumn === 'total_vendas' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nº de Pedidos 
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('quantidade')">
                                <IconArrowDown v-if="sortColumn === 'quantidade' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Estoque
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('marca')">
                                <IconArrowDown v-if="sortColumn === 'marca' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Marca
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('segmento')">
                                <IconArrowDown v-if="sortColumn === 'segmento' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Segmento
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('quantidade')">
                                <IconArrowDown v-if="sortColumn === 'quantidade' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Média<br>Compra
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class ">
                        <td class="td-class text-center rounded-l-xl" data-label="Name">{{ item.name }}</td>
                        <td class="td-class text-center" data-label="Nº de Pedidos">{{ item.total_vendas }}</td>
                        <td class="td-class text-center" data-label="Estoque">{{ item.quantidade }}</td>
                        <td class="td-class text-center" data-label="Marca">{{ item.marca }}</td>
                        <td class="td-class text-center" data-label="Segmento">{{ item.segmento }}</td>
                        <td class="td-class text-center rounded-r-xl" data-label="Segmento"> {{ item.tempo_medio_dias.toFixed(2) }}</td>
                       
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import IconArrowDown from '../../Svg/IconArrowDown.vue';

export default {
    name: "FornecedorTable",
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
            sortColumn: 'name', // Default column for sorting
            sortDirection: 'asc', // Default sorting direction
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                let aValue = a[this.sortColumn];
                let bValue = b[this.sortColumn];
                
                // Convert to numbers if the value is numeric
                if (!isNaN(aValue) && !isNaN(bValue)) {
                    aValue = Number(aValue);
                    bValue = Number(bValue);
                }

                if (aValue < bValue) return -1 * modifier;
                if (aValue > bValue) return 1 * modifier;
                return 0;
            });
        }
    },
    methods: {
        sortTable(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
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
