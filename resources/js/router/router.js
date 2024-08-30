import { createRouter, createWebHistory } from 'vue-router';
import PedidosView from '../views/PedidosView.vue';
import MarcasView from '../views/MarcasView.vue';
import ProdutosView from '../views/ProdutosView.vue';
import FornecedorView from '../views/FornecedorView.vue';
import ClientesView from '../views/ClientesView.vue';
import ComprasView from '../views/ComprasView.vue';
import FuncionariosView from '../views/FuncionariosView.vue';
import DashBoard from '../views/DashBoard.vue';
const routes = [
    { path: '/',component:DashBoard,name:"DashBoard"},
    { path: '/pedidos', component: PedidosView, name: 'Pedidos' },
    { path: '/marcas', component: MarcasView, name: 'Marcas' },
    { path: '/produtos', component: ProdutosView, name: 'Produtos' },
    { path: '/fornecedor', component: FornecedorView, name: 'Fornecedor' },
    { path: '/clientes', component: ClientesView, name: 'Clientes' },
    { path: '/funcionarios', component: FuncionariosView, name: 'Funcionarios' },
    { path:"/compras",component:ComprasView,name:"Compras"}
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
