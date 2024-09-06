<template>
    <LoadingCircle v-if="loading" />
    <TitleView title="Compras" />
    <ComprasFilter @applyFilter="applyFilter" @openModalFuncionario="openModalFuncionario" />
    <div class="w-full flex justify-center">
        <button type="button" @click="openModalProduto = true"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Adicionar
            Produto</button>
    </div>
    <ProdutoModal v-if="openModalProduto" @close="() => openModalProduto = false" @addProduto="addProduto"
        :allCompras="allCompras" />
    <ComprasModal v-if="openModal" @updateFuncionario="updateFuncionario" @close="close" :compra="compra" />

    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />


    <ComprasTable class="overflow-auto max-h-[500px] my-2" :body="compras" @delete="deleteFuncionario"
        @details="getDetails" @update="updateFuncionarioStatus" />

</template>

<script>
import ProdutoModal from "../src/components/Modal/Create/ProdutosModal.vue"
import ComprasModal from "../src/components/Modal/Update/ComprasModal.vue";
import ComprasTable from "../src/components/Tables/Compras/ComprasTable.vue";
import ComprasFilter from "../src/components/Filter/ComprasFilter.vue";
import TitleView from "../src/components/Title/TitleView.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue";
import SucessWarning from "../src/components/Warnings/SucessWarning.vue";
import http from "../src/services/http.js";
import { format, parse } from 'date-fns';

export default {
    name: 'ComprasView',
    components: { ProdutoModal, ComprasModal, ComprasTable, ComprasFilter, WrongWarning, SucessWarning, TitleView, LoadingCircle },
    data() {
        return {
            compras: [],
            allCompras: [],
            compra: {},
            openModalUpdate: false,
            loading: false,
            openModal: false,
            wrongWarning: false,
            sucessWarning: false,
            warning: '',
            openModalProduto: false
        };
    },
    methods: {
        async getCompras() {
            this.loading = true;
            try {
                const data = await http.get('/compras');
                this.allCompras = data.data.compras;
                this.mapCompras(this.allCompras);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async addProduto(produto) {
            console.log(produto)
            this.loading = true;
            try {
                await http.post('/store-produto', produto);
                this.sucessWarning = true;
                this.warning = "Produto Inserido";
                this.getCompras();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        mapCompras(array) {
            this.compras = array.map(compra => {
                return {
                    ...compra,
                    created_at: format(new Date(compra.created_at), 'dd/MM/yyyy'),
                };
            });
        },
        openModalFuncionario() {
            this.addFuncionarioModal = true;
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

            // Aplicar filtro no array allCompras
            const filteredCompras = this.allCompras.filter(compra => {
                let isValid = true;

                if (filter.fornecedor) {
                    const nameFornecedor = compra.produto.marca.fornecedor.name.toLowerCase();
                    const nameFornecedorFilter = filter.fornecedor.toLowerCase();
                    if (!nameFornecedor.includes(nameFornecedorFilter)) isValid = false;
                }

                // Verificar nome do produto
                if (filter.name) {
                    const nameProduto = compra.produto.name.toLowerCase();
                    const filterName = filter.name.toLowerCase();
                    if (!nameProduto.includes(filterName)) isValid = false;
                }

                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima)
                    const createdAt = format(new Date(compra.created_at), 'dd-MM-yyyy')
                    if (createdAt >= dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima)
                    const createdAt = format(new Date(compra.created_at), 'dd-MM-yyyy')
                    if (createdAt <= dataMaxima) isValid = false;
                }
                if (filter.valorMin) {
                    const valorMinNum = parseFloat(filter.valorMin.replace('R$', '').trim().replace(',', '.'));
                    if (compra.total < valorMinNum) isValid = false;
                }

                // Verificar valor máximo
                if (filter.valorMax) {
                    const valorMaxNum = parseFloat(filter.valorMax.replace('R$', '').trim().replace(',', '.'));
                    if (compra.total > valorMaxNum) isValid = false;
                }


                // Verificar quantidade mínima
                if (filter.quantidadeMin) {
                    if (compra.quantidade < filter.quantidadeMin) isValid = false;
                }

                // Verificar quantidade máxima
                if (filter.quantidadeMax) {
                    if (compra.quantidade > filter.quantidadeMax) isValid = false;
                }

                // Verificar marca
                if (filter.marca) {
                    const nameMarca = compra.produto.marca.nome.toLowerCase();
                    const filterMarca = filter.marca.toLowerCase();

                    if (!nameMarca.includes(filterMarca)) isValid = false;
                }
                // Verificar segmento
                if (filter.segmento) {
                    const nameSegmento = compra.produto.marca.segmento.toLowerCase();
                    const filterSegmento = filter.segmento.toLowerCase();
                    if (!nameSegmento.includes(filterSegmento)) isValid = false;
                }

                return isValid;
            });

            this.mapCompras(filteredCompras)

            this.loading = false;
        },

        async addFuncionario(funcionario) {
            this.loading = true;
            try {
                await http.post('/store-funcionario', funcionario);
                this.sucessWarning = true;
                this.warning = "Funcionário Inserido";
                this.getFuncionarios();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async updateFuncionario(newFuncionario) {
            this.loading = true;
            try {
                await http.post('/update-funcionario', newFuncionario);
                this.sucessWarning = true;
                this.warning = "Funcionário Atualizado";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        close() {
            this.openModal = this.wrongWarning = this.sucessWarning = false;
        },
        getDetails(value) {
            this.openModal = true;
            this.compra = value;
        },
        async deleteFuncionario(value) {
            this.loading = true;
            try {
                await http.delete('/funcionario/' + value.id);
                this.sucessWarning = true;
                this.warning = "Funcionário Deletado";
                this.getFuncionarios();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async updateFuncionarioStatus(value) {
            this.loading = true;
            try {
                await http.post('/update-funcionario', { "status": "change", id: value.id });
                this.sucessWarning = true;
                this.warning = "Status Atualizado";
                this.getFuncionarios();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        }
    },
    mounted() {
        this.getCompras();
    }
}
</script>
