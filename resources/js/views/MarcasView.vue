<template>
    <LoadingCircle v-if="loading" />
    <TitleView title="Marcas" />
    <MarcasFilter @applyFilter="applyFilter" @openModalMarca="openModalMarca" />

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
        openModalMarca() {
            console.log('ok')
            this.addMarcaModal = true;
        },
        convertToDisplayDate(dateStr) {
            if (!dateStr) return null;

            const [year, month, day] = dateStr.split('-').map(Number);

            // Valida se os valores extraídos são válidos
            if (isNaN(day) || isNaN(month) || isNaN(year) ||
                day < 1 || day > 31 || month < 1 || month > 12 || year < 1900) {
                console.error('Data inválida:', dateStr);
                return null;
            }

            // Cria a data e verifica se é válida
            const date = new Date(year, month - 1, day);
            if (isNaN(date.getTime())) {
                console.error('Data inválida:', dateStr);
                return null;
            }

            // Retorna a data no formato 'dd/MM/yyyy'
            return `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`;
        },
        applyFilter(filter) {
            
            // Desestruturando o filtro para fácil acesso
            const { nameFornecedor, nameMarca, status, dataMinima, dataMaxima } = filter;
            // Filtrando os dados com base no filtro
            let filteredMarcas = this.allMarcas;

            // Filtrando pelo nome da marca
            if (nameMarca) {
                filteredMarcas = filteredMarcas.filter(marca =>
                    marca.nome.toLowerCase().includes(nameMarca.toLowerCase())
                );
            }
            
            console.log(this.fornecedorID)
            // Filtrando pelo nome do fornecedor
            if (nameFornecedor) {
                filteredMarcas = filteredMarcas.filter(marca =>
                    marca.fornecedor_nome.toLowerCase().includes(nameFornecedor.toLowerCase())
                );
            }

            // Filtrando pelo status
            if (status && status !== 'all') {
                filteredMarcas = filteredMarcas.filter(marca =>
                    marca.status === status
                );
            }
            const dataMinimaDisplay = dataMinima ? this.convertToDisplayDate(dataMinima) : null;
            const dataMaximaDisplay = dataMaxima ? this.convertToDisplayDate(dataMaxima) : null;
            // Filtrando pela data mínima
            if (dataMinimaDisplay) {
                filteredMarcas = filteredMarcas.filter(marca => new Date((format(new Date(marca.created_at), 'dd/MM/yyyy'))) >= dataMinimaDisplay
                );
            }
            // Filtrando pela data máxima
            if (dataMaximaDisplay) {
                filteredMarcas = filteredMarcas.filter(marca =>
                    (format(new Date(marca.created_at), 'dd/MM/yyyy')) <= dataMaximaDisplay
                );
            }

            if (filteredMarcas.length <= 0) {
                this.wrongWarning = true;
                this.warning = "Consulta Retornou Zero";
            } else {
                this.sucessWarning = true;
                this.mapMarcas(filteredMarcas);
                this.warning = `${filteredMarcas.length} Linhas Retornadas `;
            }

            this.loading = false;
        },
        async addMarca(marca) {
            this.loading = true;
            try {
                // Converte a data para o formato aceito pelo backend
                const convertDateFormat = (dateStr) => {
                    if (!dateStr) return null;

                    const [day, month, year] = dateStr.split('/').map(Number);

                    // Valida se os valores extraídos são válidos
                    if (isNaN(day) || isNaN(month) || isNaN(year) ||
                        day < 1 || day > 31 || month < 1 || month > 12 || year < 1900) {
                        console.error('Data inválida:', dateStr);
                        return null;
                    }

                    // Cria a data e verifica se é válida
                    const date = new Date(year, month - 1, day);
                    if (isNaN(date.getTime())) {
                        console.error('Data inválida:', dateStr);
                        return null;
                    }

                    // Retorna a data no formato 'yyyy-MM-dd'
                    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                };

                // Converte a data para o formato 'yyyy-MM-dd'
                marca.created_at = convertDateFormat(marca.created_at);

                const data = await http.post('/store-marca', marca);
                this.sucessWarning = true;
                this.warning = "Marca Inserida";
                this.getMarcas();
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        async updateMarca(newMarca) {
            this.loading = true;
            try {
                // Converte a data para o formato aceito pelo backend
                const convertDateFormat = (dateStr) => {
                    if (!dateStr) return null;

                    const [day, month, year] = dateStr.split('/').map(Number);

                    // Valida se os valores extraídos são válidos
                    if (isNaN(day) || isNaN(month) || isNaN(year) ||
                        day < 1 || day > 31 || month < 1 || month > 12 || year < 1900) {
                        console.error('Data inválida:', dateStr);
                        return null;
                    }

                    // Cria a data e verifica se é válida
                    const date = new Date(year, month - 1, day);
                    if (isNaN(date.getTime())) {
                        console.error('Data inválida:', dateStr);
                        return null;
                    }

                    // Retorna a data no formato 'yyyy-MM-dd'
                    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                };

                // Converte a data para o formato 'yyyy-MM-dd'
                newMarca.created_at = convertDateFormat(newMarca.created_at);

                const { data } = await http.post('/update-marca', newMarca);
                this.allMarcas = this.allMarcas.map(marca => {
                    if (marca.id === data.id) {
                        return { ...marca, ...data };
                    }
                    return marca;
                });
                this.mapMarcas(this.allMarcas);
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
