<template>



    <div class="flex text-center bg-[#dbdbdb] p-2 rounded-lg mb-2 shadow-2xl border-[1px] border-gray-400">
        <div class="flex-1">
            <h2 class="text-xl font-bold mb-2">{{ this.funcionario ? 'Funcionários com Maior Valor de Vendas' : 'Clientes com Maior Valor de Compras' }}</h2>
            <apexchart ref="barChart" type="bar" :options="chartOptions" :series="chartSeries" />
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';

export default {
    props: {
        body: {
            type: Array,
            required: true
        },
        funcionario: {
            type: Boolean,
            defult: false
        }
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    id: 'top-clients-chart',
                },
                xaxis: {
                    categories: [], // Nomes dos clientes
                    title: {
                        text: 'Clientes'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total de Compras (R$)'
                    },
                    labels: {
                        formatter: function (value) {
                            return `R$ ${value.toFixed(2)}`; // Formatação dos valores no eixo Y
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (value) {
                        return `R$ ${value.toFixed(2)}`; // Formatação dos valores exibidos nos dados do gráfico
                    }
                },
            },
            chartSeries: [{
                name: 'Total Comprado',
                data: [] // Totais das compras dos clientes
            }],
        };
    },
    watch: {
        body: 'prepareChartData'
    },
    methods: {
        prepareChartData() {
            const totalByClient = {};

            // Calcular o total de compras por cliente
            this.body.forEach(order => {
                const cliente = this.funcionario ? order.funcionario_nome : order.cliente_nome;
                const totalCompra = parseFloat(order.total);

                if (totalByClient[cliente]) {
                    totalByClient[cliente] += totalCompra;
                } else {
                    totalByClient[cliente] = totalCompra;
                }
            });

            // Ordenar os clientes pelos que mais compraram
            const topClients = Object.entries(totalByClient)
                .sort(([, a], [, b]) => b - a) // Ordenar pelo valor total de compras (decrescente)
                .slice(0, 3); // Pegar os 3 primeiros

            // Atualizar os dados do gráfico
            this.chartOptions.xaxis.categories = topClients.map(([cliente]) => cliente);
            this.chartSeries[0].data = topClients.map(([, total]) => total);

            // Garantir que a atualização do gráfico aconteça após a DOM ser atualizada
            this.$nextTick(() => {
                if (this.$refs.barChart) {
                    this.$refs.barChart.updateOptions(this.chartOptions);
                    this.$refs.barChart.updateSeries(this.chartSeries);
                } else {
                    console.error('Referência ao gráfico não encontrada.');
                }
            });
        }
    }
};
</script>