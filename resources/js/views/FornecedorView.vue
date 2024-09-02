<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Fornecedores" />

    <!-- Filtro de fornecedores e modal para criação de novos fornecedores -->
    <FornecedorFilter @applyFilter="applyFilter" />

    <div class="flex justify-center">
        <button type="button" @click="() => { fornecedorModal = true }"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Adicionar
            Fornecedor</button>
    </div>

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
            allFornecedores: []

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

        // Abre o modal de produtos, com uma opção para inicializar a seleção
        openModalProduto(option) {
            this.close(); // Fecha todos os modais abertos antes de abrir o modal de produtos
            if (!option) this.fornecedor = null; // Limpa o fornecedor se opção não for fornecida
            this.produtosModal = true;
        },

        // Aplica o filtro para a lista de fornecedores
        applyFilter(filter) {
            this.loading = true;

            // Aplicar filtro no array allFornecedores
            const filteredFornecedores = this.allFornecedores.filter(fornecedor => {
                let isValid = true;
                // Filtrar pelo nome do fornecedor
                if (filter.nomeFornecedor) {
                    const nomeFornecedor = fornecedor.nome.toLowerCase();
                    const nomeFilter = filter.nomeFornecedor.toLowerCase();
                    if (!nomeFornecedor.includes(nomeFilter)) isValid = false;
                }

                if (filter.nomeMarca) {
                    let valid = false
                    const filterMarca = filter.nomeMarca.toLowerCase()
                    fornecedor.marcas.forEach(marca => {
                        let nomeMarca = marca.nome.toLowerCase()
                        if (nomeMarca.includes(filterMarca)) valid = true
                    })
                    isValid = valid
                }

                // Filtrar pelo status
                if (filter.status && filter.status !== 'all') {
                    if (fornecedor.status !== filter.status) isValid = false;
                }

                // Filtrar pelo Segmento
                if (filter.segmento) {
                    let valid = false
                    const filterSegmento = filter.segmento.toLowerCase();
                    fornecedor.marcas.forEach(marca => {
                        let marcaSegmento = marca.segmento.toLowerCase();
                        if (marcaSegmento.includes(filterSegmento)) valid = true
                    });
                    isValid = valid
                }

                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = new Date(filter.dataMinima);
                    const createdAt = new Date(fornecedor.created_at);
                    if (createdAt < dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = new Date(filter.dataMaxima);
                    const createdAt = new Date(fornecedor.created_at);
                    if (createdAt > dataMaxima) isValid = false;
                }

                if (filter.quantidadeMinima) if (filter.quantidadeMinima < fornecedor.marcas.length) isValid = false;


                if (filter.quantidadeMaxima) if (filter.quantidadeMaxima > fornecedor.marcas.length) isValid = false;



                return isValid;
            });

            this.mapFornecedor(filteredFornecedores);
            this.loading = false;
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
