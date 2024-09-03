<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Clientes" />

    <!-- Componente de filtro para clientes -->
    <ClientesFilter @applyFilter="applyFilter" />
    <div class="flex items-center justify-center">
        <button type="button" @click="() => { this.addClienteModal = true }"
            class="focus:outline-none w-[160px] text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Adicionar Cliente
        </button>
    </div>
    <!-- Modal para atualizar as informações do cliente, visível quando necessário -->
    <ClienteModalUpdate v-if="openModalUpdate" @updateCliente="updateCliente" @close="close" :cliente="cliente" />

    <!-- Aviso de erro, exibido quando ocorre um problema com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso, exibido quando uma ação é realizada com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal para adicionar um novo cliente, visível quando necessário -->
    <ClienteModal v-if="addClienteModal" @close="close" @addCliente="addCliente" />

    <!-- Tabela que exibe a lista de clientes com opções de delete, detalhes e atualização -->
    <ClienteTable class="overflow-auto max-h-[500px] my-2" :body="clientes" @delete="deleteCliente"
        @details="getDetails" @update="updateClienteStatus" />
</template>

<script>
import TitleView from "../src/components/Title/TitleView.vue"
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue"
import ClientesFilter from "../src/components/Filter/ClientesFilter.vue"
import WrongWarning from "../src/components/Warnings/WrongWarning.vue"
import SucessWarning from "../src/components/Warnings/SucessWarning.vue"
import ClienteTable from "../src/components/Tables/ClienteTable/ClienteTable.vue"
import ClienteModal from "../src/components/Modal/Create/ClienteModal.vue"
import http from "../src/services/http.js"
import ClienteModalUpdate from "../src/components/Modal/Update/ClienteModal.vue"
import { format } from 'date-fns';

export default {
    name: 'ClienteView',
    components: {
        ClienteModalUpdate, ClienteModal, ClienteTable, WrongWarning, SucessWarning, TitleView, LoadingCircle, ClientesFilter
    },
    data() {
        return {
            clientes: [], // Lista de clientes carregados
            cliente: {}, // Cliente atualmente selecionado para edição ou detalhes
            openModalUpdate: false, // Controle para mostrar/ocultar o modal de atualização
            loading: false, // Controle de estado de carregamento
            addClienteModal: false, // Controle para mostrar/ocultar o modal de adição de cliente
            wrongWarning: false, // Controle para mostrar/ocultar aviso de erro
            sucessWarning: false, // Controle para mostrar/ocultar aviso de sucesso
            warning: '' // Mensagem de aviso a ser exibida
        }
    },
    methods: {
        // Função para obter a lista de clientes do servidor
        async getClientes() {
            this.loading = true
            try {
                const data = await http.get('/clientes')
                this.mapCliente(data.data.clientes)
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Mapeia a resposta do servidor para o formato esperado pela tabela
        mapCliente(array) {
            this.clientes = array.map(cliente => {
                return {
                    id: cliente.id,
                    name: cliente.nome,
                    status: cliente.status,
                    idade: format(new Date(cliente.data_nasc), 'dd/MM/yyyy'),
                    sexo: cliente.sexo,
                    tipo: cliente.tipo,
                    email: cliente.email,
                    created_at: format(new Date(cliente.created_at), 'dd/MM/yyyy'),
                };
            })
        },

        // Aplica o filtro fornecido e atualiza a lista de clientes
        async applyFilter(filter) {
            this.loading = true
            try {
                const data = await http.post('/filter-cliente', filter)
                if (data.data.clientes.length <= 0) {
                    this.wrongWarning = true
                    this.warning = "Consulta Retornou Zero"
                } else {
                    this.mapCliente(data.data.clientes)
                }
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Adiciona um novo cliente e atualiza a lista
        async addCliente(cliente) {
            this.loading = true
            try {
                await http.post('/store-cliente', cliente)
                this.sucessWarning = true
                this.warning = "Cliente Inserido"
                this.getClientes();
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Atualiza as informações de um cliente existente
        async updateCliente(newCliente) {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(newCliente.idade)) newCliente.idade = format(new Date(newCliente.idade.replace(/(\d{2})(\d{2})(\d{4})/, '$2/$1/$3')), 'dd/MM/yyyy');


            this.loading = true
            try {
                await http.post('/update-cliente', newCliente)
                this.sucessWarning = true
                this.warning = "Cliente Atualizado"
                this.getClientes()
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Fecha todos os modais e avisos
        close() {
            this.addClienteModal = this.openModalUpdate = this.wrongWarning = this.sucessWarning = false;
        },

        // Abre o modal de atualização e define o cliente para edição
        getDetails(value) {
            this.openModalUpdate = true
            this.cliente = value
        },

        // Exclui um cliente e atualiza a lista de clientes
        async deleteCliente(value) {
            this.loading = true
            try {
                await http.delete('/cliente/' + value.id)
                this.sucessWarning = true
                this.warning = "Cliente Deletado"
                this.getClientes()
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Atualiza o status de um cliente e atualiza a lista de clientes
        async updateClienteStatus(value) {
            this.loading = true
            try {
                await http.post('/update-cliente', { "status": "change", id: value.id })
                this.sucessWarning = true
                this.warning = "Status Atualizado"
                this.getClientes()
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        }
    },
    mounted() {
        this.getClientes() // Carrega a lista de clientes ao montar o componente
    }
}
</script>
