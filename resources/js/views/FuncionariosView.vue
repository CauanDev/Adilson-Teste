<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />

    <!-- Componente para exibir o título da página -->
    <TitleView title="Funcionários" />

    <!-- Filtro de funcionários e modal para criação de novos funcionários -->

    <FuncionarioFilter @applyFilter="applyFilter" />
    <div class="flex justify-center">
        <button type="button" @click="() => this.addFuncionarioModal = true"
            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
            Adicionar Funcionario
        </button>
    </div>
    <!-- Modal para atualização de funcionários -->
    <FuncionarioModalUpdate v-if="openModalUpdate" @updateFuncionario="updateFuncionario" @close="close"
        :funcionario="funcionario" />

    <!-- Aviso de erro exibido quando há problemas com a consulta -->
    <WrongWarning v-if="wrongWarning" :title="warning" @close="close" />

    <!-- Aviso de sucesso exibido quando uma ação é concluída com sucesso -->
    <SucessWarning v-if="sucessWarning" :title="warning" @close="close" />

    <!-- Modal para adicionar novos funcionários -->
    <FuncionarioModal v-if="addFuncionarioModal" @close="close" @addFuncionario="addFuncionario" />

    <!-- Tabela para exibir a lista de funcionários -->
    <FuncionarioTable class="overflow-auto max-h-[500px] my-2" :body="funcionarios" @delete="deleteFuncionario"
        @details="getDetails" @update="updateFuncionarioStatus" />
</template>

<script>
import FuncionarioTable from "../src/components/Tables/FuncionarioTable/FuncionarioTable.vue";
import FuncionarioModalUpdate from "../src/components/Modal/Update/FuncionarioModal.vue";
import TitleView from "../src/components/Title/TitleView.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import FuncionarioFilter from "../src/components/Filter/FuncionarioFilter.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue";
import SucessWarning from "../src/components/Warnings/SucessWarning.vue";
import FuncionarioModal from "../src/components/Modal/Create/FuncionarioModal.vue";
import http from "../src/services/http.js";
import { format } from 'date-fns';
export default {
    name: 'FuncionarioView',
    components: {
        FuncionarioModalUpdate,
        FuncionarioModal,
        FuncionarioTable,
        WrongWarning,
        SucessWarning,
        TitleView,
        LoadingCircle,
        FuncionarioFilter
    },
    data() {
        return {
            allFuncionarios: [], // Armazena todos os funcionários
            funcionarios: [], // Lista de funcionários a ser exibida na tabela
            funcionario: {}, // Detalhes do funcionário selecionado
            openModalUpdate: false, // Controle para exibir o modal de atualização de funcionário
            loading: false, // Controle de estado de carregamento
            addFuncionarioModal: false, // Controle para exibir o modal de adição de funcionário
            wrongWarning: false, // Controle para exibir aviso de erro
            sucessWarning: false, // Controle para exibir aviso de sucesso
            warning: '' // Mensagem a ser exibida nos avisos
        };
    },
    methods: {
        // Obtém a lista de funcionários e a armazena em `allFuncionarios`
        async getFuncionarios() {
            this.loading = true;
            try {
                const data = await http.get('/funcionarios');
                this.allFuncionarios = data.data.funcionarios
                this.mapFuncionarios(data.data.funcionarios);
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Mapeia a lista de funcionários para o formato usado na tabela e armazena em `allFuncionarios`
        mapFuncionarios(array) {
            this.funcionarios = array.map(funcionario => {
                return {
                    id: funcionario.id,
                    name: funcionario.nome,
                    status: funcionario.status,
                    data_nasc: funcionario.data_nasc,
                    sexo: funcionario.sexo,
                    email: funcionario.email,
                    salario: funcionario.salario,
                    pedidos_count: funcionario.pedidos_count,
                    created_at: format(new Date(funcionario.created_at), 'dd/MM/yyyy'),
                };
            });
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
        // Aplica o filtro para a lista de funcionários
        applyFilter(filter) {
            this.loading = true;
            // Função para calcular a idade
            const calculateAge = (dateOfBirth) => {
                const today = new Date();
                const birthDate = new Date(dateOfBirth);
                let age = today.getFullYear() - birthDate.getFullYear();
                const month = today.getMonth() - birthDate.getMonth();
                if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            };

            // Aplicar filtro no array allFuncionarios
            const filteredFuncionarios = this.allFuncionarios.filter(funcionario => {
                let isValid = true;

                // Filtrar pelo nome
                if (filter.name) {
                    const name = funcionario.nome.toLowerCase();
                    const nameFilter = filter.name.toLowerCase();
                    if (!name.includes(nameFilter)) isValid = false;
                }

                // Filtrar pelo status
                if (filter.status) {
                    if (funcionario.status !== filter.status) isValid = false;
                }

                // Filtrar pelo sexo
                if (filter.sexo && filter.sexo !== 'all') {
                    if (funcionario.sexo !== filter.sexo) isValid = false;
                }

                // Filtrar pela faixa de idade
                const funcionarioAge = calculateAge(funcionario.data_nasc);
                if ((filter.idadeMin && funcionarioAge < filter.idadeMin) ||
                    (filter.idadeMax && funcionarioAge > filter.idadeMax)) {
                    isValid = false;
                }

                // Filtrar pela faixa de salário
                if ((filter.salarioMinimo && funcionario.salario < filter.salarioMinimo) ||
                    (filter.salarioMaximo && funcionario.salario > filter.salarioMaximo)) {
                    isValid = false;
                }
                if ((filter.quantidadeMin && funcionario.pedidos_count < filter.quantidadeMin) ||
                    (filter.quantidadeMax && funcionario.pedidos_count > filter.quantidadeMax)) {
                    isValid = false;
                }


                // Verificar data mínima
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    const createdAt = format(new Date(funcionario.created_at), 'dd-MM-yyyy');
                    if (createdAt < dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    const createdAt = format(new Date(funcionario.created_at), 'dd-MM-yyyy');
                    if (createdAt > dataMaxima) isValid = false;
                }

                return isValid;
            });

            // Atualizar a lista filtrada e parar o carregamento
            this.mapFuncionarios(filteredFuncionarios);
            this.loading = false;
        },

        // Adiciona um novo funcionário e exibe aviso de sucesso
        async addFuncionario(funcionario) {
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
                funcionario.data_nasc = convertDateFormat(funcionario.data_nasc);

                // Envia a requisição para adicionar o funcionário
                const data = await http.post('/store-funcionario', funcionario);
                this.sucessWarning = true; // Exibe aviso de sucesso após adicionar
                this.warning = "Funcionário Inserido";
                this.getFuncionarios(); // Atualiza a lista de funcionários
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Atualiza um funcionário existente e exibe aviso de sucesso
        async updateFuncionario(newFuncionario) {

            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(newFuncionario.data_nasc)) newFuncionario.data_nasc = format(new Date(newFuncionario.data_nasc.replace(/(\d{2})(\d{2})(\d{4})/, '$2/$1/$3')), 'dd/MM/yyyy');
            this.loading = true;
            try {
                const { data } = await http.post('/update-funcionario', newFuncionario);
                const updatedFuncionario = data.funcionario;

                this.sucessWarning = true; // Exibe aviso de sucesso após atualizar
                this.warning = "Funcionário Atualizado";

                // Atualiza o funcionário no array `funcionarios`
                this.funcionarios = this.funcionarios.map(funcionario =>
                    funcionario.id === updatedFuncionario.id
                        ? { ...funcionario, ...updatedFuncionario }
                        : funcionario
                );

                // Atualiza também o `allFuncionarios` se necessário
                this.allFuncionarios = this.allFuncionarios.map(funcionario =>
                    funcionario.id === updatedFuncionario.id
                        ? { ...funcionario, ...updatedFuncionario }
                        : funcionario
                );

            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Fecha todos os modais e avisos
        close() {
            this.addFuncionarioModal = this.openModalUpdate = this.wrongWarning = this.sucessWarning = false;
        },

        // Exibe os detalhes do funcionário em um modal de atualização
        getDetails(value) {
            this.openModalUpdate = true;
            this.funcionario = value;
        },

        // Deleta um funcionário e exibe aviso de sucesso
        async deleteFuncionario(value) {
            this.loading = true;
            try {
                await http.delete('/funcionario/' + value.id);
                this.sucessWarning = true; // Exibe aviso de sucesso após deletar
                this.warning = "Funcionário Deletado";
                this.getFuncionarios(); // Atualiza a lista de funcionários
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },

        // Atualiza o status de um funcionário e exibe aviso de sucesso
        async updateFuncionarioStatus(value) {
            this.loading = true;
            try {
                await http.post('/update-funcionario', { "status": "change", id: value.id });
                this.sucessWarning = true; // Exibe aviso de sucesso após atualizar status
                this.warning = "Status Atualizado";
                this.getFuncionarios(); // Atualiza a lista de funcionários
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        }
    },
    mounted() {
        this.getFuncionarios();
    }
};

</script>
