<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Pedidos" />

    <!-- Filtro de pedidos e modal para criação de novos pedidos -->
    <PedidosFilter @applyFilter="applyFilter" />
    <div class="flex justify-center w-full">
        <button type="button" @click="() => this.addFuncionarioModal = true"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Adicionar Pedido
        </button>
    </div>
    <!-- Modal para atualização de pedidos -->
    <PedidosModalUpdate v-if="openModalUpdate" @close="close" @updatePedido="updatePedido" :pedido="pedido" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal para adicionar novos pedidos -->
    <PedidosModal v-if="addFuncionarioModal" @close="close" @addPedido="addPedido" />

    <!-- Tabela para exibir a lista de pedidos -->
    <PedidoTable class="overflow-auto max-h-[500px] my-2" :body="pedidos" @delete="deletePedido" @details="getDetails"
        @update="updatePedidoStatus" />
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
            pedidos: [], // Lista de pedidos a ser exibida na tabela
            pedido: {}, // Detalhes do pedido selecionado
            openModalUpdate: false, // Controle para exibir o modal de atualização de pedido
            loading: false, // Controle de estado de carregamento
            addFuncionarioModal: false, // Controle para exibir o modal de adição de pedido
            wrongWarning: false, // Controle para exibir aviso de erro
            sucessWarning: false, // Controle para exibir aviso de sucesso
            warning: '', // Mensagem a ser exibida nos avisos
            segmentos: [],
            allPedidos: []
        };
    },
    methods: {
        // Obtém a lista de pedidos e a mapeia para o formato esperado
        async getPedidos() {
            this.loading = true;
            try {
                const data = await http.get('/pedidos');
                this.allPedidos = data.data.pedidos
                this.mapPedido(data.data.pedidos);
                console.log(this.allPedidos)
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
            this.pedidos = array.map((pedido) => {
                return {
                    id: pedido.id,
                    preco: pedido.total,
                    status: pedido.status,
                    quantidade_produtos: pedido.produtos.length, // Contagem de produtos no pedido
                    produtos: pedido.produtos, // Todos os dados do pedido
                    created_at: format(new Date(pedido.created_at), 'dd/MM/yyyy'),
                    cliente_nome: pedido.cliente_nome ? pedido.cliente_nome : "Cliente Apagado",
                    funcionario_nome: pedido.funcionario_nome ? pedido.funcionario_nome : "Funcionário Apagado",
                    cliente_id:pedido.cliente_id,
                    funcionario_id:pedido.funcionario_id
                };
            });
        },
        formatDate(dateString) {
            // Verifica se a string tem 8 caracteres
            if (dateString.length !== 8) {
                throw new Error("A string deve ter 8 caracteres no formato 'ddmmyyyy'.");
            }

            // Extrair o dia, mês e ano da string
            let day = dateString.substring(0, 2);
            let month = dateString.substring(2, 4);
            let year = dateString.substring(4, 8);

            // Retorna a data formatada
            return `${day}-${month}-${year}`;
        },
        // Aplica o filtro para a lista de pedidos
        applyFilter(filter) {
            this.loading = true;
            const filteredProdutos = this.allPedidos.filter(pedido => {
                let isValid = true

                if (filter.nameCliente) {
                    const nameCliente = pedido.cliente_nome.toLowerCase();
                    const nameClientefilter = filter.nameCliente.toLowerCase();
                    if (!nameCliente.includes(nameClientefilter)) isValid = false;
                }

                if (filter.nameFuncionario) {
                    const nameFuncionario = pedido.funcionario_nome.toLowerCase();
                    const nameFuncionariofilter = filter.nameFuncionario.toLowerCase();
                    if (!nameFuncionario.includes(nameFuncionariofilter)) isValid = false;
                }

                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    const createdAt = format(new Date(pedido.created_at), 'dd-MM-yyyy')
                    if (createdAt >= dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    const createdAt = format(new Date(pedido.created_at), 'dd-MM-yyyy')
                    if (createdAt <= dataMaxima) isValid = false;
                }

                if (filter.valorMax) if (pedido.total > filter.valorMax) isValid = false;


                if (filter.valorMin) if (pedido.total < filter.valorMin) isValid = false;



                if (filter.status && filter.status !== 'all') if (pedido.status !== filter.status) isValid = false;



                if (filter.nomeProduto) {
                    const produtoNomeFilter = filter.nomeProduto.toLowerCase();
                    const hasProduto = pedido.produtos.some(produto =>
                        produto.name.toLowerCase().includes(produtoNomeFilter)
                    );
                    if (!hasProduto) isValid = false;
                }

                if (filter.segmento) {
                    const segmentoFilter = filter.segmento.toLowerCase();
                    const hasSegmento = pedido.produtos.some(produto =>
                        produto.segmento.toLowerCase().includes(segmentoFilter)
                    );
                    if (!hasSegmento) isValid = false;
                }

                return isValid
            });
            this.mapPedido(filteredProdutos)
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
            this.pedido = value;
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
