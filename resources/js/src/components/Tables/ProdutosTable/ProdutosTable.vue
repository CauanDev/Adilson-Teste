<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 ml-4 whitespace-nowrap">
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
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('totalCompras')">
                                <IconArrowDown v-if="sortColumn === 'totalCompras' && sortDirection === 'desc'"
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
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class ">
                        <td class="td-class text-center rounded-l-xl" data-label="Name">{{ item.name }}</td>
                        <td class="td-class text-center" data-label="Nº de Pedidos">{{ item.totalCompras }}</td>
                        <td class="td-class text-center" data-label="Estoque">{{ item.quantidade }}</td>
                        <td class="td-class text-center" data-label="Marca">{{ item.marca }}</td>
                        <td class="td-class text-center" data-label="Segmento">{{ item.segmento }}</td>
                        <td class="td-class text-center" data-label="Status" @click="this.$emit('update', item)">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Ativo',
                                'bg-red-600 text-white': item.status === 'Suspenso'
                            }"
                                class="rounded-md px-2 py-1 text-xs font-semibold uppercase antialiased cursor-pointer border border-black">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center rounded-r-xl flex flex-col sm:flex-row gap-2"
                            data-label="Ações">
                            <button type="button" @click="this.$emit('delete', item)"
                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm p-2">Apagar</button>
                            <button type="button" @click="this.$emit('details', item)"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-2">Ver
                                Detalhes</button>

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
            // Return a sorted copy of the body array
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                if (a[this.sortColumn] < b[this.sortColumn]) return -1 * modifier;
                if (a[this.sortColumn] > b[this.sortColumn]) return 1 * modifier;
                return 0;
            });
        }
    },
    methods: {
        sortTable(column) {
            if (this.sortColumn === column) {
                // Toggle sort direction if clicking the same column
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                // Set column to sort and default direction to 'asc'
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
