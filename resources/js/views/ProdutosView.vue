<template>
    <!-- Indicador de carregamento exibido enquanto os dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Produtos" />

    <!-- Filtro de produtos e modal para criação de novos produtos -->
    <ProdutoFilter @applyFilter="applyFilter" />
    <div class="flex justify-center">
        <router-link to="/compras">
            <button type="button"
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Adicionar Produto
            </button>
        </router-link>
    </div>
    <!-- Modal para atualização de produtos -->
    <ProdutosModalUpdate v-if="openModalUpdate" @saveChanges="saveChanges" :produto="produto" @close="close" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />


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
                    totalCompras: produto.total_vendas,
                    segmento: produto.segmento,
                    marcaID: produto.marca_id,
                    created_at: format(new Date(produto.created_at), 'dd/MM/yyyy'),
                    id: produto.id
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
        applyFilter(filter) {
            this.loading = true;

            // Filtra a lista allProdutos com base no objeto filter
            const filteredProdutos = this.allProdutos.filter(produto => {
                let isValid = true;

                if (filter.name && !produto.name.toLowerCase().includes(filter.name.toLowerCase())) isValid = false;

                if (filter.marca && !produto.marca.toLowerCase().includes(filter.marca.toLowerCase())) isValid = false;

                if (filter.segmento && !produto.segmento.toLowerCase().includes(filter.segmento.toLowerCase())) isValid = false;


                if (filter.status && produto.status != filter.status) isValid = false;

                if (filter.valorMax) if (produto.preco > parseFloat(filter.valorMax.replace('R$', '').trim())) isValid = false;
                if (filter.valorMin) if (produto.preco < parseFloat(filter.valorMin.replace('R$', '').trim())) isValid = false;

                if (filter.pedidosMax) if (produto.total_vendas > filter.pedidosMax) isValid = false;
                if (filter.pedidosMin) if (produto.total_vendas < filter.pedidosMin) isValid = false;

                if (filter.quantidadeMax) if (produto.quantidade > filter.quantidadeMax) isValid = false;
                if (filter.quantidadeMin) if (produto.quantidade < filter.quantidadeMin) isValid = false;

                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    const createdAt = format(new Date(produto.created_at), 'dd-MM-yyyy');
                    if (createdAt < dataMinima) isValid = false;
                }

                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    const createdAt = format(new Date(produto.created_at), 'dd-MM-yyyy');
                    if (createdAt > dataMaxima) isValid = false;
                }
                return isValid;
            });


            this.mapProdutos(filteredProdutos)
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
            if (value.quantidade !== 0) {
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
            }
            else
            {
                this.wrongWarning = true
                this.warning = "O Produto Não Possui Estoque";
            }

            this.loading = false;
        },
    },

    mounted() {
        this.getProdutos();
    }



}
</script>
