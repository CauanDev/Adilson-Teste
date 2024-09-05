<template>
    <LoadingCircle v-if="loading" />
    <FuncionarioFilter @applyFilter="applyFilter" />
    <div class="flex gap-2 p-2 mobile">
        <div class="flex flex-col gap-2 sm:w-1/2 ">
            <div class="flex gap-2 mobile">
                <PedidosSexo :body="funcionarios" />
                <MediaSalarialSexo :body="funcionarios" />
            </div>
            <div class="flex gap-2 mobile">
                <QuantidadeFuncionario :body="funcionarios" />
                <ServicosPorSexo :body="pedidos"  />

            </div>           
        </div>
        <div class="flex flex-col justify-between">
            <FuncionarioDashboardTable :body="funcionarios" class="w-full"/>
            <div class="flex gap-2 w-full mobile">
                <ProdutosMaisVendidos :body="pedidos" />
                <MediaIdadeSexo :body="funcionarios" />

            </div> 
        </div>

    </div>

    <div class="flex gap-2 p-2 mobile">

        <VendasDiaSexo :body="pedidos"  />
        <PedidosDia :body="pedidos"  />
    </div>

</template>
<script>
import MediaSalarialSexo from "../../Graficos/Funcionarios/MediaSalarialSexo.vue";
import PedidosSexo from "../../Graficos/Funcionarios/PedidosSexo.vue";
import MediaIdadeSexo from "../../Graficos/Funcionarios/MediaIdadeSexo.vue";
import QuantidadeFuncionario from "../../Graficos/Funcionarios/QuantidadeFuncionario.vue";
import ProdutosMaisVendidos from "../../Graficos/Funcionarios/ProdutosMaisVendidos.vue";
import ServicosPorSexo from "../../Graficos/Funcionarios/ServicosPorSexo.vue";
import FuncionarioDashboardTable from "../../Tables/FuncionarioTable/FuncionarioDashboardTable.vue";


import FuncionarioFilter from "../../Filter/FuncionarioFilter.vue"
import PedidosDia from "../../Graficos/Funcionarios/PedidosDia.vue";
import http from "../../../services/http"
import VendasDiaSexo from "../../Graficos/Funcionarios/VendasDiaSexo.vue";
import LoadingCircle from "../../Loading/LoadingCircle.vue";
import { format } from 'date-fns';

export default {
    name: "FuncionarioDashboard",
    components: {
        FuncionarioDashboardTable,
        ServicosPorSexo, PedidosSexo, MediaSalarialSexo, MediaIdadeSexo, QuantidadeFuncionario, ProdutosMaisVendidos,
        LoadingCircle, FuncionarioFilter, PedidosDia, VendasDiaSexo
    },
    data() {
        return {
            allFuncionarios: [],
            funcionarios: [],
            allPedidos: [],
            pedidos: [],
            loading: false,
        }
    },
    methods: {
        applyFilter(filter) {
            // Filtra funcionários com base no nome
            const filteredFuncionarios = this.allFuncionarios.filter(funcionario => {
                let isValid = true
                if (filter.name) {
                    if (!funcionario.nome.toLowerCase().includes(filter.name.toLowerCase())) isValid = false
                }
                if (filter.sexo && filter.sexo !== 'all') {
                    if (funcionario.sexo !== filter.sexo) isValid = false
                }
                if (filter.status && filter.status !== 'all') {
                    if (funcionario.status !== filter.status) isValid = false
                }
                if (filter.salarioMaximo) {
                    if (funcionario.salario > filter.salarioMaximo) isValid = false

                }
                if (filter.salarioMinimo) {
                    if (funcionario.salario < filter.salarioMinimo) isValid = false
                }

                if (filter.quantidadeMax) {
                    if (funcionario.pedidos_count > filter.quantidadeMax) isValid = false
                }

                if (filter.quantidadeMin) {
                    if (funcionario.pedidos_count < filter.quantidadeMin) isValid = false
                }

                if (filter.idadeMax) {
                    if (funcionario.idade > filter.idadeMax) isValid = false
                }

                if (filter.idadeMin) {
                    if (funcionario.idade < filter.idadeMin) isValid = false
                }

                if (filter.dataMinima) {
                    if (funcionario.created_at < this.converterData(filter.dataMinima)) isValid = false;
                }

                if (filter.dataMaxima) {
                    if (funcionario.created_at > this.converterData(filter.dataMaxima)) isValid = false;
                }
                return isValid;
            });
            this.funcionarios = filteredFuncionarios;


            // Filtra pedidos com base nos funcionários filtrados
            const filteredPedidos = this.allPedidos.filter(pedido => {
                // Verifica se o nome do funcionário associado ao pedido está na lista de funcionários filtrados
                return filteredFuncionarios.some(funcionario =>
                    pedido.funcionario_nome.toLowerCase().includes(funcionario.nome.toLowerCase())
                );
            });





            this.pedidos = filteredPedidos;

            // Atualiza a lista de funcionários e pedidos filtrados
        },
        calcularIdade(dataNascimento) {
            const hoje = new Date();
            const [ano, mes, dia] = dataNascimento.split('-').map(Number);
            const nascimento = new Date(ano, mes - 1, dia); // Meses são 0-indexados em JavaScript
            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mesAtual = hoje.getMonth();
            const diaAtual = hoje.getDate();

            // Ajusta a idade se o aniversário ainda não ocorreu este ano
            if (mesAtual < nascimento.getMonth() || (mesAtual === nascimento.getMonth() && diaAtual < nascimento.getDate())) {
                idade--;
            }

            return idade;
        },
        converterData(dataStr) {
            const dia = dataStr.substring(0, 2);
            const mes = dataStr.substring(2, 4);
            const ano = dataStr.substring(4, 8);
            return `${dia}-${mes}-${ano}`;
        },
        async getFuncionarios() {
            this.loading = true

            try {
                const data = await http.get('/funcionarios')
                this.mapFuncionario(data.data.funcionarios)

                this.allFuncionarios = this.funcionarios
            }
            catch (error) {
                console.log(error)
            }
        },
        async getPedidos() {
            try {
                const data = await http.get('/pedidos')
                this.allPedidos = data.data.pedidos
                this.pedidos = this.allPedidos
            } catch (error) {
                console.log(error)
            }
            this.loading = false

        },
        mapFuncionario(array) {
            this.funcionarios = array.map(funcionario => {
                return {
                    ...funcionario,
                    idade: this.calcularIdade(funcionario.data_nasc),
                    created_at: format(new Date(funcionario.created_at), 'dd-MM-yyyy')

                }
            })
        }
    },
    mounted() {
        this.getFuncionarios()
        this.getPedidos()

    }
}
</script>