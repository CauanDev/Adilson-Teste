<template>
    <div class="p-2">
      <!-- Filtro de Pedidos -->
      <PedidosFilter @applyFilter="applyFilter" />
  
      <div class="flex gap-2 mobile">
        <!-- Coluna de Top Clientes -->
        <div class="sm:w-1/2">
          <TopClientes :body="pedidos" />
          <TopClientes :body="pedidos" funcionario />
        </div>
  
        <!-- Coluna de Tabelas e Dashboard -->
        <div class="w-full flex flex-col gap-2 ">
          <PedidoDashboardTable :body="pedidos" />
          <PedidosDia :body="pedidos" class="w-full" />
        </div>
      </div>
    </div>
  </template>
<script>
import PedidoDashboardTable from '../../Tables/PedidoTable/PedidoDashboardTable.vue';
import PedidosFilter from '../../Filter/PedidosFilter.vue';
import TopClientes from '../../Graficos/Pedidos/TopClientes.vue';
import http from "../../../services/http"
import { format } from 'date-fns';
import PedidosDia from '../../Graficos/Funcionarios/PedidosDia.vue';

export default {
    name: "PedidosDashboard",
    components: { PedidosDia, PedidosFilter, PedidoDashboardTable, TopClientes },
    data() {
        return {
            allPedidos: [],
            pedidos: []
        }
    },
    methods: {
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
            const filteredPedidos = this.allPedidos.filter(pedido => {
                let isValid = true
                if (filter.nameCliente) if (!pedido.cliente_nome.toLowerCase().includes(filter.nameCliente.toLowerCase())) isValid = false




                if (filter.nameFuncionario) if (!pedido.funcionario_nome.toLowerCase().includes(filter.nameFuncionario.toLowerCase())) isValid = false


                if (filter.status && filter.status !== 'all') if (pedido.status !== filter.status) isValid = false;
                if (filter.dataMinima) {
                    const dataMinima = this.formatDate(filter.dataMinima);
                    if (pedido.created_at <= dataMinima) isValid = false;
                }

                // Verificar data máxima
                if (filter.dataMaxima) {
                    const dataMaxima = this.formatDate(filter.dataMaxima);
                    console.log(pedido.created_at)
                    console.log(dataMaxima)
                    if (pedido.created_at >= dataMaxima) isValid = false;
                }





                if (filter.valorMax) if (pedido.total > filter.valorMax) isValid = false;


                if (filter.valorMin) if (pedido.total < filter.valorMin) isValid = false;
                if (filter.nomeProduto) {
                    const produtoNomeFilter = filter.nomeProduto.toLowerCase();
                    const hasProduto = pedido.produtos.some(produto =>
                        produto.name.toLowerCase().includes(produtoNomeFilter)
                    );
                    if (!hasProduto) isValid = false;
                }

                if (filter.segmento) {
                    const segmentoFilter = filter.segmento.toLowerCase();
                    const hasSegmento = pedido.produtos.some(produto =>
                        produto.segmento.toLowerCase().includes(segmentoFilter)
                    );
                    if (!hasSegmento) isValid = false;
                }
                return isValid
            })

            this.pedidos = filteredPedidos
        },
        async getPedidos() {
            try {
                const data = await http.get('/pedidos')
                this.allPedidos = data.data.pedidos.map(pedidos => {
                    return {
                        ...pedidos,
                        created_at: format(new Date(pedidos.created_at), 'dd-MM-yyyy')
                    }
                })
                this.pedidos = this.allPedidos
                console.log(this.pedidos)
            }
            catch (error) {
                console.log(error)
            }
        },
    },
    mounted() {
        this.getPedidos()
    }
}
</script>