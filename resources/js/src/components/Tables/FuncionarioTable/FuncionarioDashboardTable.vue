<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 whitespace-nowrap max-h-[240px] mb-2 overflow-y-auto">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead >
                    <tr class="select-none">
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('nome')">
                                <IconArrowDown class="mr-2" />
                                Nome
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('sexo')">
                                <IconArrowDown class="mr-2" />
                                Sexo
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('salario')">
                                <IconArrowDown class="mr-2" />
                                Salário
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('pedidos_count')">
                                <IconArrowDown class="mr-2" />
                                Nº de Pedidos
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class">
                        <td class="td-class text-center rounded-l-xl" data-label="Name">{{ item.nome }}</td>
                        <td class="td-class text-center" data-label="Sexo">{{ item.sexo }}</td>
                        <td class="td-class text-center" data-label="Salario">R${{ item.salario }}</td>
                        <td class="td-class text-center rounded-r-xl" data-label="Quantidade">{{ item.pedidos_count }}</td>
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
    emits: ['delete', 'details', 'update'],
    components: { IconArrowDown },
    props: {
        body: {
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'nome', // Inicialmente ordenado por nome
            sortDirection: 'asc',
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                if (this.sortColumn === 'pedidos_count') {
                    return (a[this.sortColumn] - b[this.sortColumn]) * modifier;
                } else if (this.sortColumn === 'salario') {
                    return (parseFloat(a[this.sortColumn]) - parseFloat(b[this.sortColumn])) * modifier;
                } else if (this.sortColumn === 'nome') {
                    return (a[this.sortColumn].localeCompare(b[this.sortColumn])) * modifier;
                } else {
                    // Assumindo que "sexo" é uma string e deve ser ordenado alfabeticamente
                    return (a[this.sortColumn].localeCompare(b[this.sortColumn])) * modifier;
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
