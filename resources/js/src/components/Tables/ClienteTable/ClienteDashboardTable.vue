<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 whitespace-nowrap max-h-[240px] mb-2 overflow-y-auto">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group">
                    <tr class="select-none">
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('nome')">
                                <IconArrowDown v-if="sortColumn === 'nome' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nome
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('sexo')">
                                <IconArrowDown v-if="sortColumn === 'sexo' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Sexo
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer"
                                @click="sortTable('pedidos_count')">
                                <IconArrowDown v-if="sortColumn === 'pedidos_count' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nº de Pedidos
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class whitespace-nowrap">
                        <td class="td-class text-center rounded-l-xl" data-label="Nome">{{ item.nome }}</td>
                        <td class="td-class text-center" data-label="Sexo">{{ item.sexo }}</td>
                        <td class="td-class text-center rounded-r-xl" data-label="Pedidos">{{ item.pedidos_count }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import IconArrowDown from '../../Svg/IconArrowDown.vue';

export default {
    name: "FuncionarioTable",
    components: { IconArrowDown },
    props: {
        body: {
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'nome', // Ordenação inicial por nome
            sortDirection: 'asc', // Direção inicial ascendente
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;

                if (this.sortColumn === 'pedidos_count') {
                    // Ordenação numérica
                    return (parseFloat(a[this.sortColumn]) - parseFloat(b[this.sortColumn])) * modifier;
                } else {
                    // Ordenação alfabética
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
                // Alterna a direção se a coluna for a mesma
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                // Define a direção ascendente ao trocar de coluna
                this.sortDirection = 'asc';
            }
            // Define a coluna atual para ordenar
            this.sortColumn = column;
        }
    },
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
