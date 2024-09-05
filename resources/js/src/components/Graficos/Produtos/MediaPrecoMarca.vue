<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="barChartSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Média de Preço por Marca</h2>
            <apexchart ref="barChart" type="bar" width="100%" :options="barChartOptions" :series="barChartSeries" />
        </div>
        <div v-else class="flex justify-center items-center h-full">
            <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
        </div>
    </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';

export default {
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        body: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            barChartOptions: {
                chart: {
                    type: 'bar',
                    height: 200,
                },
                xaxis: {
                    categories: [], // Nomes das marcas
                    labels: {
                        style: {
                            fontSize: '10px',
                            colors: '#333',
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: 'Média de Preço',
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => `R$ ${val.toFixed(2)}`,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4,
                    },
                },
                grid: {
                    borderColor: '#e7e7e7',
                },
                legend: {
                    position: 'bottom',
                },
                colors: ['#007bff'],
            },
            barChartSeries: [],
        };
    },
    watch: {
        body: {
            handler(newData) {
                this.updateBarChart(newData);
            },
            deep: true,
        },
    },
    mounted() {
        this.updateBarChart(this.body);
    },
    methods: {
        updateBarChart(data) {
            const marcaPrecoSum = {};
            const marcaProdutoCount = {};

            // Calcular a soma dos preços e contagem de produtos por marca
            data.forEach((produto) => {
                const marca = produto.marca;
                const preco = parseFloat(produto.preco);

                if (!marcaPrecoSum[marca]) {
                    marcaPrecoSum[marca] = 0;
                    marcaProdutoCount[marca] = 0;
                }

                // Soma o preço e incrementa a contagem de produtos
                marcaPrecoSum[marca] += preco;
                marcaProdutoCount[marca] += 1;
            });

            // Calcular a média de preço por marca
            const mediaPrecos = Object.keys(marcaPrecoSum).map((marca) => {
                return (marcaPrecoSum[marca] / marcaProdutoCount[marca]).toFixed(2);
            });

            // Atualizar o gráfico
            this.barChartOptions.xaxis.categories = Object.keys(marcaPrecoSum); // Nomes das marcas
            this.barChartSeries = [
                {
                    name: 'Média de Preço',
                    data: mediaPrecos, // Médias de preço calculadas
                },
            ];

            // Atualiza o gráfico dinamicamente após montar o DOM
            this.$nextTick(() => {
                this.$refs.barChart.updateOptions(this.barChartOptions);
                this.$refs.barChart.updateSeries(this.barChartSeries);
            });
        },
    },
};
</script>