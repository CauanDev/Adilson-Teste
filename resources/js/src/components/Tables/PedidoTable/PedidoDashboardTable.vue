<template>
    <div>
        <div class="flex items-center justify-center sm:ml-0 ml-4 whitespace-nowrap">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full max-w-sm mx-auto">
                <thead class="hidden sm:table-header-group">
                    <tr>
                        <th class="px-4 py-2 text-center">Valor</th>
                        <th class="px-4 py-2 text-center">Nome Cliente</th>
                        <th class="px-4 py-2 text-center ">Nome Funcionario</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Pedido Em</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in body" :key="index" class="tr-class mobile">
                        <td class="td-class text-center rounded-l-xl whitespace-nowrap" data-label="Valor">R$ {{
                            item.preco }}</td>
                        <td class="td-class text-center" data-label="Nome Cliente">{{ item.cliente_nome ||
                            'Carregando...' }}</td>
                        <td class="td-class text-center" data-label="Nome Funcionario">{{ item.funcionario_nome ||
                            'Carregando...' }}</td>
                        <td class="td-class text-center" data-label="Status">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Entregue',
                                'bg-red-600 text-white whitespace-nowrap': item.status === 'Não Processado'
                            }" class="rounded-md px-2 py-1 text-xs font-semibold uppercase antialiased">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center rounded-r-xl" data-label="Nome Funcionario">{{formatDate(item.created_at)}}</td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

export default {
    name: "PedidoTable",
    emits: ['delete', 'details', 'update'],
    props: {
        body: {
            type: Array,
            required: true,
        },
    },
    methods: {
        getStatus(item) {
            if (item.status === 'Não Processado') return "Entregar Pedido";
            return "Cancelar Pedido";

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


    },
    mounted()
    {
        console.log(this.body)
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
        width: 210%;
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
    .texto{
        font-size: 10px;
       
    }
}
</style>
