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
                        <!-- Cabeçalho para a coluna Sexo -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('sexo')">
                                <IconArrowDown v-if="sortColumn === 'sexo' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Sexo
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Salário -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('salario')">
                                <IconArrowDown v-if="sortColumn === 'salario' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Salário
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
                        <!-- Cabeçalho para a coluna Idade -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('idade')">
                                <IconArrowDown v-if="sortColumn === 'idade' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Idade
                            </p>
                        </th>
                        <!-- Cabeçalho para a coluna Criado em -->
                        <th class="px-4 py-2 text-center">
                            <p class="flex items-center justify-center cursor-pointer" @click="sortTable('created_at')">
                                <IconArrowDown v-if="sortColumn === 'created_at' && sortDirection === 'desc'"
                                    class="mr-2 rotate-180" />
                                <IconArrowDown v-else class="mr-2" />
                                Criado em
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Itera sobre a lista de itens ordenados -->
                    <tr v-for="(item, index) in sortedBody" :key="index" class="tr-class">
                        <td class="td-class text-center rounded-l-xl whitespace-nowrap" data-label="Nome">{{ item.nome
                            }}</td>
                        <td class="td-class text-center" data-label="Sexo">{{ item.sexo }}</td>
                        <td class="td-class text-center" data-label="Salário">R${{ item.salario }}</td>
                        <td class="td-class text-center cursor-pointer" data-label="Status"
                            @click="$emit('update', item)">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Ativo',
                                'bg-red-600 text-white whitespace-nowrap': item.status === 'Suspenso'
                            }"
                                class="rounded-md px-2 py-1 text-xs font-semibold uppercase antialiased border border-black">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center" data-label="Idade">{{ item.idade?item.idade:calcularIdade(item.data_nasc)}}</td>
                        <td class="td-class text-center rounded-r-xl texto" data-label="Criado em">{{
                            formatDate(item.created_at) }}</td>

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
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            sortColumn: 'nome', // Define a coluna inicial para ordenação
            sortDirection: 'asc', // Define a direção inicial da ordenação
            processedBody: []
        };
    },
    computed: {
        sortedBody() {
            return [...this.body].sort((a, b) => {
                let modifier = this.sortDirection === 'asc' ? 1 : -1;
                if (this.sortColumn === 'salario' || this.sortColumn === 'idade') {
                    // Ordena valores numéricos com base na coluna 'salario' e 'idade'
                    return (a[this.sortColumn] - b[this.sortColumn]) * modifier;
                } else if (this.sortColumn === 'created_at') {
                    // Ordena datas com base na coluna 'created_at'
                    return (new Date(a[this.sortColumn]) - new Date(b[this.sortColumn])) * modifier;
                } else {
                    // Ordena strings com base nas outras colunas
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
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        },
        calcularIdade(dataNasc) {
            const today = new Date();
            const birthDate = new Date(dataNasc);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDifference = today.getMonth() - birthDate.getMonth();
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        },
        calcularIdade(dataNasc) {
            const hoje = new Date();
            const nascimento = new Date(dataNasc);

            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mes = hoje.getMonth() - nascimento.getMonth();

            // Ajusta a idade se o mês de nascimento ainda não foi alcançado ou se a data de nascimento ainda não passou este ano
            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }

            return idade;
        },
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
        padding-right: 15px;
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

    .texto {
        font-size: 10px;

    }
}
</style>
