<template>
    <LoadingCircle v-if="loading" />
    <ClientesFilter @applyFilter="applyFilter" />
    <div class="flex gap-2 p-2 mobile">
        <div class="flex flex-col gap-2 sm:w-1/2">
            <div class="flex gap-2 mobile">
                <MediaIdadeSexo :body="clientes" />
                <PedidosSexo :body="clientes" />
            </div>
            <div class="flex gap-2 mobile ">
                <ServicosPorSexo :body="pedidos" cliente />
                <CounterClientes :body="clientes" />
            </div>
        </div>
        <div class="flex flex-col justify-between">
            <ClienteDashboardTable :body="clientes" class="w-full" />
            <div class="flex gap-2 w-full mobile">
                <QuantidadeFuncionario :body="clientes" :title="'Clientes'" />
                <ProdutosMaisVendidos :body="pedidos" cliente />
            </div>
        </div>
    </div>

    <div class="flex gap-2 p-2 mobile">
        <VendasDiaSexo :body="pedidos" cliente />
        <PedidosDia :body="pedidos" cliente />
    </div>
</template>

<script>
import ClienteDashboardTable from '../../Tables/ClienteTable/ClienteDashboardTable.vue';
import LoadingCircle from '../../Loading/LoadingCircle.vue';
import QuantidadeFuncionario from '../../Graficos/Funcionarios/QuantidadeFuncionario.vue';
import MediaIdadeSexo from '../../Graficos/Funcionarios/MediaIdadeSexo.vue';
import http from "../../../services/http"
import PedidosSexo from '../../Graficos/Funcionarios/PedidosSexo.vue';
import ProdutosMaisVendidos from '../../Graficos/Funcionarios/ProdutosMaisVendidos.vue';
import ServicosPorSexo from '../../Graficos/Funcionarios/ServicosPorSexo.vue';
import PedidosDia from '../../Graficos/Funcionarios/PedidosDia.vue';
import CounterClientes from '../../Graficos/Clientes/CounterClientes.vue';
import ClientesFilter from '../../Filter/ClientesFilter.vue';
import VendasDiaSexo from '../../Graficos/Funcionarios/VendasDiaSexo.vue';
export default {
    name: "ClienteDashboard",
    components: { ClienteDashboardTable, VendasDiaSexo, ClientesFilter, CounterClientes, PedidosDia, LoadingCircle, ServicosPorSexo, MediaIdadeSexo, PedidosSexo, QuantidadeFuncionario, ProdutosMaisVendidos },
    data() {
        return {
            loading: false,
            pedidos: [],
            allPedidos: [],
            clientes: [],
            allClientes: []
        }
    },
    methods: {
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
        async getPedidos() {
            try {
                const data = await http.get('/pedidos')
                this.allPedidos = data.data.pedidos
                this.pedidos = this.allPedidos
            } catch (error) {
                console.log(error)
            }
        },
        mapClientes(array) {
            this.clientes = array.map(cliente => {
                return {
                    ...cliente,
                    idade: this.calcularIdade(cliente.data_nasc),
                    pedidos_count: cliente.pedidos.length
                }
            })
        },
        async getClientes() {
            try {
                const data = await http.get('/clientes')
                this.allClientes = data.data.clientes
                this.mapClientes(this.allClientes)

            } catch (error) {
                console.log(error)
            }
        },
        applyFilter(filter) {
            const filteredClientes = this.allClientes.filter(cliente => {
                let isValid = true
                if (filter.nome) {
                    if (!cliente.nome.toLowerCase().includes(filter.nome.toLowerCase())) isValid = false
                }
                if (filter.sexo && filter.sexo !== 'all') {
                    if (cliente.sexo !== filter.sexo) isValid = false
                }
                if (filter.status && filter.status !== 'all') {
                    if (cliente.status !== filter.status) isValid = false
                }
                if (filter.quantidadeMax) {
                    if (cliente.pedidos_count > filter.quantidadeMax) isValid = false
                }

                if (filter.quantidadeMin) {
                    if (cliente.pedidos_count < filter.quantidadeMin) isValid = false
                }

                if (filter.idadeMax) {
                    if (cliente.idade > filter.idadeMax) isValid = false
                }

                if (filter.idadeMin) {
                    if (cliente.idade < filter.idadeMin) isValid = false
                }

                if (filter.dataMinima) {
                    if (cliente.created_at < this.converterData(filter.dataMinima)) isValid = false;
                }

                if (filter.dataMaxima) {
                    if (cliente.created_at > this.converterData(filter.dataMaxima)) isValid = false;
                }

                if (filter.tipo && filter.tipo !== 'all') {
                    if (cliente.tipo !== filter.tipo) isValid = false;
                }
                return isValid;
            });

            this.mapClientes(filteredClientes)

            const filteredPedidos = this.allPedidos.filter(pedido => {
                return filteredClientes.some(cliente =>
                    pedido.cliente_nome.toLowerCase().includes(cliente.nome.toLowerCase())
                );
            });







            this.pedidos = filteredPedidos;
        }
    },
    mounted() {
        this.getPedidos();
        this.getClientes();
    }
}
</script>