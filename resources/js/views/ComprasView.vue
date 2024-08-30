<template>
    <LoadingCircle v-if="loading" />
    <TitleView title="Compras" />
    <ComprasFilter @applyFilter="applyFilter" @openModalFuncionario="openModalFuncionario" />

    <ComprasModal v-if="openModal" @updateFuncionario="updateFuncionario" @close="close"
        :compra="compra" />

    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />


    <ComprasTable class="overflow-auto max-h-[500px] my-2" :body="compras" @delete="deleteFuncionario"
        @details="getDetails" @update="updateFuncionarioStatus" />
</template>

<script>
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
    components: { ComprasModal,ComprasTable, ComprasFilter, WrongWarning, SucessWarning, TitleView, LoadingCircle },
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
        };
    },
    methods: {
        async getCompras() {
            this.loading = true;
            try {
                const data = await http.get('/compras');
                this.allCompras = data.data.compras;
                console.log(this.allCompras)
                this.mapCompras(this.allCompras);
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
        applyFilter(filter) {
            this.loading = true;

            // Aplicar filtro no array allCompras
            const filteredCompras = this.allCompras.filter(compra => {
                let isValid = true;
                
                if(filter.fornecedor){
                    const nameFornecedor = compra.produto.marca.fornecedor.name.toLowerCase();
                    const nameFornecedorFilter =filter.fornecedor.toLowerCase();
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
                    const dataMinima = parse(filter.dataMinima, 'yyyy-MM-dd', new Date());
                    const createdAt = parse(compra.created_at, 'yyyy-MM-dd', new Date()); // Alterado para o formato correto
                    if (createdAt < dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = parse(filter.dataMaxima, 'yyyy-MM-dd', new Date());
                    const createdAt = parse(compra.created_at, 'yyyy-MM-dd', new Date()); // Alterado para o formato correto
                    if (createdAt > dataMaxima) isValid = false;
                }
                if (filter.valorMin) {
                    if (compra.total < filter.valorMin) isValid = false;
                }

                // Verificar valor máximo
                if (filter.valorMax) {
                    if (compra.total > filter.valorMax) isValid = false;
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
                if (filter.marca && compra.produto.marca.nome !== filter.marca) isValid = false;

                // Verificar segmento
                if (filter.segmento && compra.produto.marca.segmento !== filter.segmento) isValid = false;

                return isValid;
            });


            if (filteredCompras.length <= 0) {
                this.wrongWarning = true;
                this.warning = "Consulta retornou zero";
            }
            else {
                this.sucessWarning = true;
                this.warning = `${filteredCompras.length} Linhas Retornadas`;
                this.mapCompras(filteredCompras)
            }

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
