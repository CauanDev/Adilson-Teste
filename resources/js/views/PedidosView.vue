<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Pedidos" />

    <!-- Filtro de pedidos e modal para criação de novos pedidos -->
    <PedidosFilter @applyFilter="applyFilter" @openModalFuncionario="openModalFuncionario"
        :segmentos="Array.from(segmentos)" />

    <!-- Modal para atualização de pedidos -->
    <PedidosModalUpdate v-if="openModalUpdate" @close="close" @updatePedido="updatePedido" :pedido="pedido" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal para adicionar novos pedidos -->
    <PedidosModal v-if="addFuncionarioModal" @close="close" @addPedido="addPedido" />

    <!-- Tabela para exibir a lista de pedidos -->
    <PedidoTable class="overflow-auto max-h-[500px] my-2" :body="funcionarios" @delete="deletePedido"
        @details="getDetails" @update="updatePedidoStatus" />
</template>

<script>
import PedidosModalUpdate from "../src/components/Modal/Update/PedidosModal.vue";
import PedidoTable from "../src/components/Tables/PedidoTable/PedidoTable.vue";
import TitleView from "../src/components/Title/TitleView.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import PedidosFilter from "../src/components/Filter/PedidosFilter.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue";
import SucessWarning from "../src/components/Warnings/SucessWarning.vue";
import PedidosModal from "../src/components/Modal/Create/PedidosModal.vue";
import http from "../src/services/http.js";
import { format } from 'date-fns';

export default {
    name: 'PedidosView',
    components: {
        PedidosModalUpdate,
        PedidosModal,
        PedidoTable,
        WrongWarning,
        SucessWarning,
        TitleView,
        LoadingCircle,
        PedidosFilter
    },
    data() {
        return {
            funcionarios: [], // Lista de pedidos a ser exibida na tabela
            pedido: {}, // Detalhes do pedido selecionado
            openModalUpdate: false, // Controle para exibir o modal de atualização de pedido
            loading: false, // Controle de estado de carregamento
            addFuncionarioModal: false, // Controle para exibir o modal de adição de pedido
            wrongWarning: false, // Controle para exibir aviso de erro
            sucessWarning: false, // Controle para exibir aviso de sucesso
            warning: '', // Mensagem a ser exibida nos avisos
            segmentos: [],
            teste: ''
        };
    },
    methods: {
        // Obtém a lista de pedidos e a mapeia para o formato esperado
        async getPedidos() {
            this.loading = true;
            try {
                const data = await http.get('/pedidos');
                this.mapPedido(data.data.pedidos);

                const segmentosSet = new Set();

                data.data.pedidos.forEach(item => {
                    item.produtos.forEach(produto => {
                        if (produto.segmento) {
                            segmentosSet.add(produto.segmento);
                        }
                    });
                });

                this.segmentos = segmentosSet;
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Mapeia a lista de pedidos para o formato usado na tabela
        async mapPedido(array) {
            this.funcionarios = await Promise.all(array.map(async funcionario => {
                console.log()
                // Fazer as requisições para obter detalhes do cliente e do funcionário
                const [clienteResponse, funcionarioResponse] = await Promise.all([
                    http.get(`/cliente/${funcionario.cliente_id}`),
                    http.get(`/funcionario/${funcionario.funcionario_id}`)
                ]);

                return {
                    id: funcionario.id,
                    preco: funcionario.total,
                    status: funcionario.status,
                    produtos: funcionario.produtos.length, // Contagem de produtos no pedido
                    tudo: funcionario, // Todos os dados do pedido
                    created_at: format(new Date(funcionario.created_at), 'dd/MM/yyyy'),
                    cliente_nome: clienteResponse.data.cliente[0].nome,
                    funcionario_nome: funcionarioResponse.data.funcionario[0].nome
                };
            }));




        },


        // Abre o modal para adicionar um novo pedido
        openModalFuncionario() {
            this.addFuncionarioModal = true;
        },

        // Aplica o filtro para a lista de pedidos
        async applyFilter(filter) {
            this.loading = true;
            try {
                const {data} = await http.post('/filter-pedidos', filter);
                console.log(data.pedidos)
                if (data.pedidos.length <= 0) {
                    this.wrongWarning = true; // Exibe aviso se nenhum pedido for encontrado
                    this.warning = "Consulta Retornou Zero";
                } else {
                    this.mapPedido(data.pedidos); // Atualiza a lista de pedidos com o filtro aplicado
                }
            } catch (error) {
                console.log(error);
            }
            this.loading = false;


        },

        // Adiciona um novo pedido e exibe aviso de sucesso
        async addPedido(pedido) {
            this.loading = true;
            try {
                await http.post('/store-pedido', pedido);
                this.sucessWarning = true; // Exibe aviso de sucesso após adicionar
                this.warning = "Pedido Inserido";
                this.getPedidos(); // Atualiza a lista de pedidos
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Atualiza um pedido existente e exibe aviso de sucesso
        async updatePedido(newPedido) {
            newPedido.id = this.pedido.id
            this.loading = true;
            try {
                await http.post('/update-pedido', newPedido);
                this.sucessWarning = true; // Exibe aviso de sucesso após atualizar
                this.warning = "Pedido Atualizado";
                this.getPedidos() // Atualiza a lista de pedidos
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Fecha todos os modais e avisos
        close() {
            this.addFuncionarioModal = this.openModalUpdate = this.wrongWarning = this.sucessWarning = false;
        },

        // Exibe os detalhes do pedido em um modal de atualização
        getDetails(value) {
            this.openModalUpdate = true;
            this.pedido = value.tudo;
        },

        // Deleta um pedido e exibe aviso de sucesso
        async deletePedido(value) {
            this.loading = true;
            try {
                await http.delete('/pedido/' + value.id);
                this.sucessWarning = true; // Exibe aviso de sucesso após deletar
                this.warning = "Pedido Deletado";
                this.getPedidos(); // Atualiza a lista de pedidos
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Atualiza o status de um pedido e exibe aviso de sucesso
        async updatePedidoStatus(value) {
            this.loading = true;
            try {
                await http.post('/update-pedido', { "status": "change", id: value.id });
                this.sucessWarning = true; // Exibe aviso de sucesso após atualizar status
                this.warning = "Status Atualizado";
                this.getPedidos(); // Atualiza a lista de pedidos
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        }
    },
    // Obtém a lista de pedidos ao montar o componente
    mounted() {
        this.getPedidos();
    }
}
</script>
