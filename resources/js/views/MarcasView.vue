<template>
    <LoadingCircle v-if="loading" />
    <TitleView title="Marcas" />
    <MarcasFilter @applyFilter="applyFilter" />
    <div class="flex justify-center w-full">
        <button type="button" @click="() => this.addMarcaModal = true"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Adicionar Marca
        </button>
    </div>
    <MarcaModalUpdate v-if="openModalUpdate" @updateMarca="updateMarca" @close="close" :marca="marca" />

    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <MarcaModal v-if="addMarcaModal" @close="close" @addMarca="addMarca" />

    <MarcasTable class="overflow-auto max-h-[500px] my-2" :body="marcas" @delete="deleteMarca" @details="getDetails"
        @update="updateMarcaStatus" />
</template>

<script>
import MarcaModal from "../src/components/Modal/Create/MarcaModal.vue";
import MarcaModalUpdate from "../src/components/Modal/Update/MarcaModal.vue";
import MarcasTable from "../src/components/Tables/MarcasTable/MarcasTable.vue";
import TitleView from "../src/components/Title/TitleView.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import MarcasFilter from "../src/components/Filter/MarcasFilter.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue";
import SucessWarning from "../src/components/Warnings/SucessWarning.vue";
import http from "../src/services/http.js";
import { format } from 'date-fns';

export default {
    name: 'MarcasView',
    components: { MarcaModal, MarcaModalUpdate, MarcasTable, WrongWarning, SucessWarning, TitleView, LoadingCircle, MarcasFilter },
    data() {
        return {
            allMarcas: [],
            marcas: [],
            openModalUpdate: false,
            loading: false,
            addMarcaModal: false,
            wrongWarning: false,
            sucessWarning: false,
            warning: '',
            sortKey: '',
            sortOrder: 'asc'
        };
    },
    methods: {
        async getMarcas() {
            this.loading = true;
            try {
                const data = await http.get('/marcas');
                this.allMarcas = data.data.marcas;
                console.log(this.allMarcas)
                this.mapMarcas(this.allMarcas);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        mapMarcas(array) {
            this.marcas = array.map(marca => {
                return {
                    id: marca.id,
                    nome: marca.nome,
                    status: marca.status,
                    fornecedorNome: marca.fornecedor_nome ? marca.fornecedor_nome : "Fornecedor Apagado",
                    segmento: marca.segmento,
                    produtos: marca.produtos,
                    created_at: format(new Date(marca.created_at), 'dd/MM/yyyy'),
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

            // Aplicar filtro no array allMarcas
            const filteredMarcas = this.allMarcas.filter(marca => {
                let isValid = true;

                // Filtrar pelo nome da marca
                if (filter.nameMarca) {
                    const nameMarca = marca.nome.toLowerCase();
                    const nameMarcaFilter = filter.nameMarca.toLowerCase();
                    if (!nameMarca.includes(nameMarcaFilter)) isValid = false;
                }

                // Filtrar pelo nome do fornecedor
                if (filter.nameFornecedor) {
                    const nameFornecedor = marca.fornecedor_nome.toLowerCase();
                    const nameFornecedorFilter = filter.nameFornecedor.toLowerCase();
                    if (!nameFornecedor.includes(nameFornecedorFilter)) isValid = false;
                }

                // Filtrar pelo status
                if (filter.status && filter.status !== 'all') {
                    if (marca.status !== filter.status) isValid = false;
                }

                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    const createdAt = format(new Date(marca.created_at), 'dd-MM-yyyy')
                    if (createdAt >= dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    const createdAt = format(new Date(marca.created_at), 'dd-MM-yyyy')
                    if (createdAt <= dataMaxima) isValid = false;
                }

                // Filtrar pela quantidade mínima
                if (filter.quantidadeMinima !== undefined) {
                    console.log(marca.quantidade)
                    if (marca.produtos.length < filter.quantidadeMinima) isValid = false;
                }

                // Filtrar pela quantidade máxima
                if (filter.quantidadeMaxima !== undefined) {
                    if (marca.produtos.length > filter.quantidadeMaxima) isValid = false;
                }

                // Filtrar pelo segmento
                if (filter.segmento) {
                    const segmento = marca.segmento.toLowerCase();
                    const segmentoFilter = filter.segmento.toLowerCase();
                    if (!segmento.includes(segmentoFilter)) isValid = false;
                }

                return isValid;
            });

            this.mapMarcas(filteredMarcas);
            this.loading = false;
        },

        async addMarca(marca) {
            this.loading = true;
            try {
                await http.post('/store-marca', marca);
                this.getMarcas();
                this.sucessWarning = true;
                this.warning = "Marca Inserida";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async updateMarca(newMarca) {
            this.loading = true;
            try {

                await http.post('/update-marca', newMarca);
                this.getMarcas();
                this.sucessWarning = true;
                this.warning = "Marca Atualizada";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        close() {
            this.addMarcaModal = this.openModalUpdate = this.wrongWarning = this.sucessWarning = false;
        },
        getDetails(value) {
            this.openModalUpdate = true;
            this.marca = value;
        },
        async deleteMarca(value) {
            this.loading = true;
            try {
                await http.delete('/marca/' + value.id);
                this.allMarcas = this.allMarcas.filter(marca => marca.id !== value.id);
                this.mapMarcas(this.allMarcas);
                this.sucessWarning = true;
                this.warning = "Marca Deletada";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async updateMarcaStatus(value) {
            this.loading = true;
            try {
                await http.post('/update-marca', { "status": "change", id: value.id });
                this.allMarcas = this.allMarcas.map(marca => {
                    if (marca.id === value.id) {
                        const novoStatus = marca.status === "Ativo" ? "Suspenso" : "Ativo";
                        return { ...marca, status: novoStatus };
                    }
                    return marca;
                });
                this.mapMarcas(this.allMarcas);
                this.sucessWarning = true;
                this.warning = "Status Atualizado";
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        }
    },
    mounted() {
        this.getMarcas();
    }
}
</script>
