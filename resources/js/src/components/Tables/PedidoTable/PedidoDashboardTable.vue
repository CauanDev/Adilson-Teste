<template>
    <div class="max-h-[364px] overflow-y-auto">
        <div class="flex items-center justify-center sm:ml-0 ml-4  p-2">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group select-none">
                    <tr>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('total')">
                                <IconArrowDown 
                                    v-if="sortColumn === 'total' && sortDirection === 'desc'" 
                                    class="mr-2 rotate-180" 
                                />
                                <IconArrowDown 
                                    v-else 
                                    class="mr-2" 
                                />
                                Valor
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('cliente_nome')">
                                <IconArrowDown 
                                    v-if="sortColumn === 'cliente_nome' && sortDirection === 'desc'" 
                                    class="mr-2 rotate-180" 
                                />
                                <IconArrowDown 
                                    v-else 
                                    class="mr-2" 
                                />
                                Nome Cliente
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('funcionario_nome')">
                                <IconArrowDown 
                                    v-if="sortColumn === 'funcionario_nome' && sortDirection === 'desc'" 
                                    class="mr-2 rotate-180" 
                                />
                                <IconArrowDown 
                                    v-else 
                                    class="mr-2" 
                                />
                                Nome Funcionario
                            </p>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('created_at')">
                                <IconArrowDown 
                                    v-if="sortColumn === 'created_at' && sortDirection === 'desc'" 
                                    class="mr-2 rotate-180" 
                                />
                                <IconArrowDown 
                                    v-else 
                                    class="mr-2" 
                                />
                                Data do Pedido
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class">
                        <td class="td-class text-center rounded-l-xl whitespace-nowrap" data-label="Valor">R$ {{ item.total }}</td>
                        <td class="td-class text-center" data-label="Nome Cliente">{{ item.cliente_nome || 'Carregando...' }}</td>
                        <td class="td-class text-center" data-label="Nome Funcionario">{{ item.funcionario_nome || 'Carregando...' }}</td>
                        <td class="td-class text-center rounded-r-xl" data-label="Data do Pedido">{{ formatDate(item.created_at,'dd/mm/yyyy') || 'Carregando...' }}</td>
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
            sortColumn: 'total',
            sortDirection: 'asc',
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                
                if (this.sortColumn === 'total') {
                    return (parseFloat(a[this.sortColumn].replace('R$', '').replace(',', '.')) - parseFloat(b[this.sortColumn].replace('R$', '').replace(',', '.'))) * modifier;
                }
                
                if (this.sortColumn === 'created_at') {
                    const dateA = new Date(a.created_at);
                    const dateB = new Date(b.created_at);
                    return (dateA - dateB) * modifier;
                }

                if (a[this.sortColumn] < b[this.sortColumn]) return -1 * modifier;
                if (a[this.sortColumn] > b[this.sortColumn]) return 1 * modifier;
                return 0;
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
        formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr); // Convert string to Date object
        const day = String(date.getDate()).padStart(2, '0'); // Get day and pad with leading zero if necessary
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Get month (add 1 because months are zero-indexed)
        const year = date.getFullYear(); // Get year
        return `${month}/${day}/${year}`; // Return formatted date
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
