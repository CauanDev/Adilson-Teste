<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="chartSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Top 3 Produtos com Maior Estoque</h2>
            <apexchart ref="barChart" type="bar" width="100%" :options="chartOptions" :series="chartSeries" />
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
            chartOptions: {
                chart: {
                    type: 'bar',
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                    },
                },
                xaxis: {
                    categories: [], // Labels dinâmicos dos produtos
                    labels: {
                        rotate: 0, // Rotaciona os rótulos para melhor visualização
                        style: {
                            fontSize: '9px', // Tamanho da fonte dos rótulos
                        },
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                colors: ['#008FFB', '#00E396', '#FEB019'],
                title: {
                    text: 'Estoque por Produto',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold',
                    },
                },
            },
            chartSeries: [
                {
                    name: 'Quantidade',
                    data: [], // Quantidade em estoque
                },
            ],
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
            // Ordenar produtos pela quantidade em estoque (do maior para o menor)
            const sortedProducts = data
                .filter(product => product.quantidade > 0) // Apenas produtos com quantidade em estoque
                .sort((a, b) => b.quantidade - a.quantidade)
                .slice(0, 3); // Pegar os 3 produtos com maior estoque

            // Atualizar categorias (nomes dos produtos) e a série (quantidade)
            this.chartOptions.xaxis.categories = sortedProducts.map(product => product.name);
            this.chartSeries = [{
                name: 'Quantidade',
                data: sortedProducts.map(product => product.quantidade),
            }];
            this.chartOptions.colors = ['#008FFB', '#00E396', '#FEB019'];

            // Forçar renderização do gráfico após a atualização dos dados
            this.$nextTick(() => {
                if (this.$refs.barChart) {
                    this.$refs.barChart.updateOptions(this.chartOptions);
                    this.$refs.barChart.updateSeries(this.chartSeries);
                }
            });
        },
    },
};
</script>
