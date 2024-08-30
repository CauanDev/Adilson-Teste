<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 ml-4">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group select-none">
                    <tr>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('name')">
                                <IconArrowDown v-if="sortColumn === 'name' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Nome
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('marca')">
                                <IconArrowDown v-if="sortColumn === 'marca' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Marca
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('fornecedorNome')">
                                <IconArrowDown v-if="sortColumn === 'fornecedorNome' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Fornecedor
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('quantidade')">
                                <IconArrowDown v-if="sortColumn === 'quantidade' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Quantidade
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('preco')">
                                <IconArrowDown v-if="sortColumn === 'preco' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Preço
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('status')">
                                <IconArrowDown v-if="sortColumn === 'status' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Status
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('tempoMedio')">
                                <IconArrowDown v-if="sortColumn === 'tempoMedio' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Tempo Médio
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('criadoEm')">
                                <IconArrowDown v-if="sortColumn === 'criadoEm' && sortDirection === 'desc'" class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Criado em
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class whitespace-nowrap">
                        <td class="td-class text-center rounded-l-xl" data-label="Nome">{{ item.name }}</td>
                        <td class="td-class text-center" data-label="Marca">{{ item.marca }}</td>
                        <td class="td-class text-center" data-label="Fornecedor">{{ item.fornecedorNome }}</td>
                        <td class="td-class text-center" data-label="Quantidade">{{ item.quantidade }}</td>
                        <td class="td-class text-center" data-label="Preço">R$ {{ item.preco }}</td>
                        <td class="td-class text-center" data-label="Status">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Ativo',
                                'bg-red-600 text-white': item.status === 'Suspenso'
                            }" class="px-2 py-1 text-xs rounded-md font-semibold uppercase antialiased">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center" data-label="Tempo Médio">{{ (item.tempoMedioDias) }}</td>
                        <td class="td-class text-center" data-label="Criado em">{{ formatDate(item.created_at) }}</td>
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
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'name',
            sortDirection: 'asc',
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                if (this.sortColumn === 'preco') {
                    return (parseFloat(a[this.sortColumn]) - parseFloat(b[this.sortColumn])) * modifier;
                } else if (this.sortColumn === 'tempoMedio' || this.sortColumn === 'criadoEm') {
                    // Convert dates to timestamps for comparison
                    return (new Date(a[this.sortColumn]) - new Date(b[this.sortColumn])) * modifier;
                } else {
                    if (typeof a[this.sortColumn] === 'string') {
                        return (a[this.sortColumn].localeCompare(b[this.sortColumn])) * modifier;
                    } else {
                        return (a[this.sortColumn] - b[this.sortColumn]) * modifier;
                    }
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
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    }
}
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
