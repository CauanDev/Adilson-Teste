<template>

    <!-- Componente para exibir o título da página -->
    <TitleView title="DashBoard" />

    <!-- Menu de seleção para alterar a categoria exibida -->
    <SelectMenu @categoryChanged="handleCategoryChange" />


    <FuncionarioDashboard v-if="categoria === 'funcionarios'" />
    <ClienteDashboard v-if="categoria === 'clientes'" />
    <ProdutosDashboard v-if="categoria === 'produtos'" />
    <PedidosDashboard v-if="categoria === 'pedidos'" />
    <ComprasDashboard v-if="categoria === 'compras'"/>
</template>

<script>
import TitleView from "../src/components/Title/TitleView.vue";
import SelectMenu from "../src/components/Inputs/SelectMenu.vue";
import PedidosDashboard from "../src/components/DashBoard/Views/PedidosDashboard.vue";
import ComprasDashboard from "../src/components/DashBoard/Views/ComprasDashboard.vue";



import FuncionarioDashboard from "../src/components/DashBoard/Views/FuncionarioDashboard.vue"
import ClienteDashboard from "../src/components/DashBoard/Views/ClienteDashboard.vue";
import ProdutosDashboard from "../src/components/DashBoard/Views/ProdutosDashboard.vue";

export default {
    name: 'DashBoard',
    components: {
        ComprasDashboard,
        SelectMenu,
        TitleView,
        ClienteDashboard,
        ProdutosDashboard,
        FuncionarioDashboard,
        PedidosDashboard
    },
    data(){
        return{
            categoria:''
        }
    },
    methods: {
        handleCategoryChange(selectedCategory) {
            this.categoria = selectedCategory;
        },

    }
};
</script>
