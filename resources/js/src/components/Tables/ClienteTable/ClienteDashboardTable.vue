<template>
    <div class="flex justify-start">
        <div class="w-1/2 sm:w-3/4 lg:w-1/2 mx-4">
            <table class="border-separate border-spacing-y-0.5 text-sm w-full">
                <thead class="hidden sm:table-header-group">
                    <tr>
                        <th class="px-4 py-2 text-center">Nome</th>
                        <th class="px-4 py-2 text-center">Sexo</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Idade</th>
                        <th class="px-4 py-2 text-center">Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in body" :key="index" class="tr-class whitespace-nowrap">
                        <td class="td-class text-center rounded-l-xl" data-label="Nome">{{ item.nome }}</td>
                        <td class="td-class text-center" data-label="Sexo">{{ item.sexo }}</td>
                        <td class="td-class text-center" data-label="Status">
                            <span :class="{
                                'bg-[#66ff66] text-black': item.status === 'Ativo',
                                'bg-red-600 text-white': item.status === 'Suspenso'
                            }" class="px-2 py-1 text-xs rounded-md font-semibold uppercase antialiased">
                                {{ item.status }}
                            </span>
                        </td>
                        <td class="td-class text-center" data-label="Idade">{{ item.idade }}</td>
                        <td class="td-class text-center rounded-r-xl texto" data-label="Criado em">{{
                            formatDate(item.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "FuncionarioTable",
    props: {
        body: {
            required: true,
        },
    },
    methods: {
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

/* Mobile-specific styles */
@media (max-width: 639px) {
    table {
        @apply w-full max-w-xs mx-auto;
        border-collapse: collapse;
    }

    .td-class {
        @apply text-gray-900;
        display: block;
        width: 200%;
        position: relative;
        padding-left: 50%;
        box-sizing: border-box;
        font-size: 0.875rem;
        /* smaller font size */
        border-width: 2px; /* Increase border thickness */
        border-color: #D3D3D3; /* Border color */
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


    .td-class {
        @apply rounded-none;
    }


}
</style>
