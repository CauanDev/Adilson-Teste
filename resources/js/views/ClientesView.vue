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
import { format, parse } from 'date-fns';

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
            warning: '', // Mensagem de aviso a ser exibida
            allClientes: []
        }
    },
    methods: {
        // Função para obter a lista de clientes do servidor
        async getClientes() {
            this.loading = true
            try {
                const data = await http.get('/clientes')
                this.mapCliente(data.data.clientes)
                this.allClientes = data.data.clientes
                console.log(this.allClientes)
            } catch (error) {
                console.log(error)
            }
            this.loading = false
        },

        // Mapeia a resposta do servidor para o formato esperado pela tabela
        mapCliente(array) {
            this.clientes = array.map(cliente => {
                const parsedDate = parse(cliente.data_nasc, 'yyyy-MM-dd', new Date());
                const formattedDate = format(parsedDate, 'dd/MM/yyyy');
                return {
                    id: cliente.id,
                    name: cliente.nome,
                    status: cliente.status,
                    idade: formattedDate,
                    sexo: cliente.sexo,
                    tipo: cliente.tipo,
                    email: cliente.email,
                    produtos:cliente.pedidos.length,
                    created_at: format(new Date(cliente.created_at), 'dd/MM/yyyy'),
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

        // Aplica o filtro fornecido e atualiza a lista de clientes
        async applyFilter(filter) {
            this.loading = true
            const filteresClientes = this.allClientes.filter(cliente => {
                let isValid = true;
                if (filter.nome) {
                    const nomeFilter = filter.nome.toLowerCase();
                    const nomeCliente = cliente.nome.toLowerCase()
                    if (!nomeCliente.includes(nomeFilter)) isValid = false
                }

                if (filter.status && filter.status !== 'all') if (cliente.status !== filter.status) isValid = false;
                if (filter.sexo && filter.sexo !== 'all') if (cliente.sexo !== filter.sexo) isValid = false;
                if (filter.tipo && filter.tipo !== 'all') if (cliente.tipo !== filter.tipo) isValid = false;


                if ((filter.idadeMin && cliente.idade < filter.idadeMin) ||
                    (filter.idadeMax && cliente.idade > filter.idadeMax)) {
                    isValid = false;
                }

                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    const createdAt = format(new Date(cliente.created_at), 'dd-MM-yyyy');
                    if (createdAt < dataMinima) isValid = false;
                }

                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    const createdAt = format(new Date(cliente.created_at), 'dd-MM-yyyy');
                    if (createdAt > dataMaxima) isValid = false;
                }

                if (filter.quantidadeMinima) if (cliente.pedidos.length < quantidadeMinima) isValid = false;
                if (filter.quantidadeMaxima) if (cliente.pedidos.length > quantidadeMaxima) isValid = false;

                return isValid;
            })

            this.mapCliente(filteresClientes)
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
