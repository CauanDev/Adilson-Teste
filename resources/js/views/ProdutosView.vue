<template>
    <!-- Indicador de carregamento exibido enquanto os dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Produtos" />

    <!-- Filtro de produtos e modal para criação de novos produtos -->
    <ProdutoFilter @applyFilter="applyFilter" @openModalProduto="openModalProduto" />

    <!-- Modal para atualização de produtos -->
    <ProdutosModalUpdate v-if="openModalUpdate" @saveChanges="saveChanges" :produto="produto" @close="close" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal para adicionar novos produtos -->
    <ProdutosModal v-if="produtosModal" @close="close" @addProduto="addProduto" />

    <!-- Tabela para exibir a lista de produtos -->
    <ProdutosTable class="overflow-auto max-h-[500px] my-2" :body="produtos" @delete="deleteProduto"
        @details="getDetails" @update="updateProduto" />
</template>

<script>
import TitleView from "../src/components/Title/TitleView.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import ProdutosTable from "../src/components/Tables/ProdutosTable/ProdutosTable.vue";
import FornecedorModal from "../src/components/Modal/Update/FornecedorModal.vue";
import ProdutosModalUpdate from "../src/components/Modal/Update/ProdutosModal.vue";
import ProdutosModal from "../src/components/Modal/Create/ProdutosModal.vue";
import ProdutoFilter from "../src/components/Filter/ProdutoFilter.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue";
import SucessWarning from "../src/components/Warnings/SucessWarning.vue";
import http from "../src/services/http.js";
import { format } from 'date-fns';

export default {
    name: 'FornecedorView', // Pode ser mais apropriado como 'ProdutosView'
    components: {
        WrongWarning,
        SucessWarning,
        TitleView,
        ProdutosTable,
        FornecedorModal,
        LoadingCircle,
        ProdutosModal,
        ProdutoFilter,
        ProdutosModalUpdate
    },
    data() {
        return {
            produtos: [], // Lista de produtos a ser exibida na tabela
            produto: {}, // Detalhes do produto selecionado
            openModalUpdate: false, // Controle para exibir o modal de atualização de produto
            modal: false, // Controle para um modal genérico
            loading: false, // Controle de estado de carregamento
            produtosModal: false, // Controle para exibir o modal de adição de produto
            confirmationModal: false, // Controle para exibir um modal de confirmação
            wrongWarning: false, // Controle para exibir aviso de erro
            sucessWarning: false, // Controle para exibir aviso de sucesso
            warning: '', // Mensagem a ser exibida nos avisos
            allProdutos: []
        };
    },
    methods: {
        async getProdutos() {
            this.loading = true;
            try {
                const data = await http.get('/produtos');
                this.allProdutos = data.data.produtos;
                this.mapProdutos(this.allProdutos);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        mapProdutos(array) {
            this.produtos = array.map(produto => {
                return {
                    name: produto.name,
                    status: produto.status,
                    marca: produto.marca,
                    preco: produto.preco,
                    quantidade: produto.quantidade,
                    totalCompras:produto.total_compras,
                    segmento: produto.segmento,
                    marcaID: produto.marca_id,
                    created_at: format(new Date(produto.created_at), 'dd/MM/yyyy'),
                    id: produto.id
                };
            });
            console.log(this.produtos);
        },

        openModalProduto() {
            this.produtosModal = true;
        },

        applyFilter(filter) {
            this.loading = true;

            // Filtra a lista allProdutos com base no objeto filter
            const filteredProdutos = this.allProdutos.filter(produto => {
                let matches = true;

                if (filter.name && !produto.name.toLowerCase().includes(filter.name.toLowerCase())) {
                    matches = false;
                }

                if (filter.marca_id && produto.marca_id != filter.marca_id) {
                    matches = false;
                }

                if (filter.segmento && produto.segmento != filter.segmento) {
                    matches = false;
                }

                if (filter.quantidadeMin && produto.quantidade < parseInt(filter.quantidadeMin)) {
                    matches = false;
                }

                if (filter.quantidadeMax && produto.quantidade > parseInt(filter.quantidadeMax)) {
                    matches = false;
                }

                if (filter.status && produto.status != filter.status) {
                    matches = false;
                }

                return matches;
            });


            // Atualiza a lista de produtos com os resultados filtrados
            if (filteredProdutos.length <= 0) {
                this.wrongWarning = true;
                this.warning = "Consulta Retornou Zero";
            } else {
                this.sucessWarning = true;
                this.warning = `${filteredProdutos.length} Linhas Retornadas`;
                this.produtos = [...filteredProdutos];
            }

            this.loading = false;
        },

        async addProduto(produto) {
            console.log(produto);
            this.loading = true;
            try {
                await http.post('/store-produto', produto);
                this.sucessWarning = true;
                this.warning = "Produto Inserido";
                this.getProdutos();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        async saveChanges(newProduto) {
            newProduto.preco = newProduto.preco.replace('R$', '').replace(',', '.');
            this.loading = true;
            try {
                delete newProduto.status;
                await http.post('/update-produto', newProduto);
                this.allProdutos = this.allProdutos.map(produto => {
                    if (produto.id === newProduto.id) {
                        return { ...produto, ...newProduto };
                    }
                    return produto;
                });
                this.mapProdutos(this.allProdutos);
                this.close();
                this.sucessWarning = true;
                this.warning = "Produto Atualizado";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        close() {
            this.modal = false;
            this.produtosModal = false;
            this.openModalUpdate = false;
            this.wrongWarning = false;
            this.sucessWarning = false;
        },

        getDetails(value) {
            this.openModalUpdate = true;
            this.produto = value;
        },

        async deleteProduto(value) {
            this.loading = true;
            try {
                await http.delete('/produto/' + value.id);
                this.sucessWarning = true;
                this.warning = "Produto Deletado";
                this.allProdutos = this.allProdutos.filter(produto => produto.id !== value.id);
                this.mapProdutos(this.allProdutos);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        async updateProduto(value) {
            this.loading = true;
            try {
                await http.post('/update-produto', { "status": "change", id: value.id });
                this.sucessWarning = true;
                this.warning = "Status Atualizado";
                this.allProdutos = this.allProdutos.map(produto => {
                    if (produto.id === value.id) {
                        const novoStatus = produto.status === "Ativo" ? "Suspenso" : "Ativo";
                        return { ...produto, status: novoStatus };
                    }
                    return produto;
                });
                this.mapProdutos(this.allProdutos);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
    },

    mounted() {
        this.getProdutos();
    }



}
</script>
