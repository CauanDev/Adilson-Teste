<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Fornecedores" />

    <!-- Filtro de fornecedores e modal para criação de novos fornecedores -->
    <FornecedorFilter @applyFilter="applyFilter" @openModalFornecedor="openModalFornecedor" />
    <FornecedorModalCreate v-if="fornecedorModal" @addFornecedor="addFornecedor" @close="close" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal de confirmação para adicionar um produto -->
    <ConfirmationModal v-if="confirmationModal" @close="close" title="Deseja Adicionar um Marca Agora?"
        greenButton="Sim, eu quero" redButton="Não, eu não quero" @greenButton="openModalMarca" />

    <!-- Modal para visualização e edição de fornecedores -->
    <FornecedorModal v-if="modal" :fornecedor="fornecedor" @close="close" @saveChanges="saveChanges"
        @openModalProduto="openModalProduto(1)" />


    <MarcaModal @close="close" @addMarca="addMarca" v-if="openMarcaModal" :fornecedor="fornecedor" />


    <!-- Modal para adicionar produtos ao fornecedor -->
    <!-- <ProdutosModal v-if="openModalMarca" @close="close" :fornecedor="fornecedor" @addProduto="addProduto" /> -->

    <!-- Tabela para exibir a lista de fornecedores -->
    <FornecedorTable :body="fornecedores" @delete="deleteFornecedor" @details="getDetails" @update="updateFornecedor" />
</template>

<script>
import TitleView from "../src/components/Title/TitleView.vue"
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue"
import FornecedorTable from "../src/components/Tables/FornecedorTable/FornecedorTable.vue"
import FornecedorModal from "../src/components/Modal/Update/FornecedorModal.vue"
import FornecedorModalCreate from "../src/components/Modal/Create/FornecedorModal.vue"
import ProdutosModal from "../src/components/Modal/Create/ProdutosModal.vue"
import FornecedorFilter from "../src/components/Filter/FornecedorFilter.vue"
import ConfirmationModal from "../src/components/Modal/ConfirmationModal.vue"
import WrongWarning from "../src/components/Warnings/WrongWarning.vue"
import SucessWarning from "../src/components/Warnings/SucessWarning.vue"
import MarcaModal from "../src/components/Modal/Create/MarcaModal.vue";
import http from "../src/services/http.js"
import { format } from 'date-fns';

export default {
    name: 'FornecedorView',
    components: {
        WrongWarning,
        SucessWarning,
        TitleView,
        FornecedorTable,
        FornecedorModal,
        LoadingCircle,
        ProdutosModal,
        FornecedorFilter,
        FornecedorModalCreate,
        ConfirmationModal,
        MarcaModal
    },
    data() {
        return {
            openMarcaModal: false,
            fornecedores: [], // Lista de fornecedores a ser exibida na tabela
            fornecedor: {}, // Detalhes do fornecedor selecionado
            modal: false, // Controle para exibir o modal de edição de fornecedor
            loading: false, // Controle de estado de carregamento
            fornecedorModal: false, // Controle para exibir o modal de criação de fornecedor
            confirmationModal: false, // Controle para exibir o modal de confirmação
            wrongWarning: false, // Controle para exibir aviso de erro
            sucessWarning: false, // Controle para exibir aviso de sucesso
            warning: '', // Mensagem a ser exibida nos avisos
            allFornecedores:[]

        }
    },
    methods: {
        // Obtém a lista de fornecedores e os mapeia para o formato esperado
        async getFornecedores() {
            this.loading = true;
            try {
                const data = await http.get('/fornecedores');
                this.allFornecedores = (data.data.fornecedores)
                this.mapFornecedor(data.data.fornecedores);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        openModalMarca() {
            this.close()
            this.openMarcaModal = true
        },
        async addMarca(marca) {
            this.loading = true;
            try {
                await http.post('/store-marca', marca);
                this.sucessWarning = true;
                this.warning = "Marca Inserido";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Mapeia a lista de fornecedores para o formato usado na tabela
        mapFornecedor(array) {
            this.fornecedores = array.map(fornecedor => {
                return {
                    id: fornecedor.id,
                    name: fornecedor.nome,
                    status: fornecedor.status,
                    email: fornecedor.email,
                    created_at: format(new Date(fornecedor.created_at), 'dd/MM/yyyy'),
                    marcas: fornecedor.marcas

                };
            });
        },

        // Adiciona um novo fornecedor e exibe modal de confirmação
        async addFornecedor(obj) {
            this.loading = true;
            try {
                const data = await http.post('/store-fornecedor', obj);
                this.fornecedor = data.data.fornecedor
                console.log(this.fornecedor)
                this.confirmationModal = true; // Exibe modal de confirmação após adicionar
                this.getFornecedores(); // Atualiza a lista de fornecedores
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Abre o modal de criação de fornecedor
        openModalFornecedor() {
            this.fornecedorModal = true;
        },

        // Abre o modal de produtos, com uma opção para inicializar a seleção
        openModalProduto(option) {
            this.close(); // Fecha todos os modais abertos antes de abrir o modal de produtos
            if (!option) this.fornecedor = null; // Limpa o fornecedor se opção não for fornecida
            this.produtosModal = true;
        },

        // Aplica o filtro para a lista de fornecedores
        async applyFilter(filter) {
            this.loading = true;

            try {
                // Clonar a lista de fornecedores para não modificar o original
                let filteredFornecedores = [...this.allFornecedores];

                // Aplicar os filtros
                if (filter.nome) {
                    filteredFornecedores = filteredFornecedores.filter(fornecedor => fornecedor.nome.toLowerCase().includes(filter.nome.toLowerCase())
                    );
                }
                if (filter.status) {
                    filteredFornecedores = filteredFornecedores.filter(fornecedor =>
                        fornecedor.status === filter.status
                    );
                }

                if (filter.dataMinima) {
                    const dataMinima = new Date(filter.dataMinima);
                    filteredFornecedores = filteredFornecedores.filter(fornecedor =>
                        new Date(fornecedor.created_at) >= dataMinima
                    );
                }

                if (filter.dataMaxima) {
                    const dataMaxima = new Date(filter.dataMaxima);
                    filteredFornecedores = filteredFornecedores.filter(fornecedor =>
                        new Date(fornecedor.created_at) <= dataMaxima
                    );
                }

                // Se necessário, você pode adicionar a lógica de ordenação aqui.
                // Por exemplo, ordenar por nome:
                if (filter.sortBy && filter.sortDirection) {
                    const { sortBy, sortDirection } = filter;

                    filteredFornecedores.sort((a, b) => {
                        let modifier = sortDirection === 'asc' ? 1 : -1;
                        if (a[sortBy] < b[sortBy]) return -1 * modifier;
                        if (a[sortBy] > b[sortBy]) return 1 * modifier;
                        return 0;
                    });
                }

                if(filteredFornecedores.length<=0)
                {
                    this.wrongWarning = true;
                    this.warning = "Consulta Retornou Zero"; 
                }
                else{
                    this.sucessWarning = true
                    this.warning = `${filteredFornecedores.length} Linhas Retornadas`
                    this.mapFornecedor(filteredFornecedores); // Atualizar a lista de fornecedores com os resultados filtrados e ordenados
                } 

                

            } catch (error) {
                console.log(error);
                this.wrongWarning = true;
                this.warning = "Erro ao aplicar filtro";
            }

            this.loading = false;
        },


    // Adiciona um novo produto e exibe aviso de sucesso
    async addProduto(produto) {
        console.log(produto);
        this.loading = true;
        try {
            await http.post('/store-produto', produto);
            this.sucessWarning = true; // Exibe aviso de sucesso após adicionar
            this.warning = "Produto Inserido";
        } catch (error) {
            console.log(error);
        }
        this.loading = false;
    },

    // Salva as alterações feitas no fornecedor
    async saveChanges(newFornecedor) {
        console.log(newFornecedor)
        this.loading = true;
        try {
            await http.post('/update-fornecedor', newFornecedor);
            this.close()
            this.sucessWarning = true; // Exibe aviso de sucesso após atualizar
            this.warning = "Fornecedor Atualizado";
            this.getFornecedores(); // Atualiza a lista de fornecedores
        } catch (error) {
            console.log(error);
        }
        this.loading = false;

    },

    // Fecha todos os modais e avisos
    close() {
        this.modal = false;
        this.fornecedorModal = false;
        this.confirmationModal = false;
        this.wrongWarning = false;
        this.sucessWarning = false;
        this.openMarcaModal = false
    },

    // Exibe os detalhes do fornecedor em um modal
    getDetails(value) {
        this.modal = true;
        this.fornecedor = value;
    },

    // Deleta um fornecedor e exibe aviso de sucesso
    async deleteFornecedor(value) {
        this.loading = true;
        try {
            await http.delete('/fornecedor/' + value.id);
            this.sucessWarning = true; // Exibe aviso de sucesso após deletar
            this.warning = "Fornecedor Deletado";
            this.getFornecedores(); // Atualiza a lista de fornecedores
        } catch (error) {
            console.log(error);
        }
        this.loading = false;
    },

    // Atualiza o status de um fornecedor e exibe aviso de sucesso
    async updateFornecedor(value) {
        this.loading = true;
        try {
            await http.get('/status-fornecedor/' + value.id);
            this.sucessWarning = true; // Exibe aviso de sucesso após atualizar status
            this.warning = "Status Atualizado";
            this.getFornecedores(); // Atualiza a lista de fornecedores
        } catch (error) {
            console.log(error);
        }
        this.loading = false;
    }
},
// Obtém a lista de fornecedores ao montar o componente
mounted() {
    this.getFornecedores();
}
}
</script>
