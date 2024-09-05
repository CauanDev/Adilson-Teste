<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="chartSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Marcas por Segmento</h2>
            <apexchart ref="donutChart" type="donut" width="100%" :options="chartOptions" :series="chartSeries" />
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
                    type: 'donut',
                },
                labels: [], // Labels dinâmicos que serão preenchidos com segmentos únicos
                legend: {
                    position: 'bottom',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total de Marcas',
                                    fontSize: '22px',
                                    color: '#373d3f',
                                    formatter: () => this.getTotalMarcas(), // Chama a função para mostrar o total
                                },
                            },
                        },
                    },
                },
                colors: ['#FF4560', '#00E396', '#008FFB', '#FEB019'], // Cores personalizadas
            },
            chartSeries: [], // Quantidade de marcas por segmento
        };
    },
    watch: {
        body: {
            handler(newData) {
                this.updateDonutChart(newData);
            },
            deep: true,
        },
    },
    mounted() {
        this.updateDonutChart(this.body);
    },
    methods: {
        updateDonutChart(data) {
            const segmentCount = {};

            // Contar marcas por segmento, sem duplicar marcas
            data.forEach(product => {
                const segmento = product.segmento;
                const marca = product.marca;

                if (!segmentCount[segmento]) {
                    segmentCount[segmento] = new Set();
                }

                // Adiciona a marca ao conjunto do segmento correspondente
                segmentCount[segmento].add(marca);
            });

            // Atualizar labels e séries do gráfico
            this.chartOptions.labels = Object.keys(segmentCount); // Segmentos
            this.chartSeries = Object.values(segmentCount).map(marcas => marcas.size); // Quantidade de marcas por segmento

            // Atualiza o gráfico após a montagem do DOM
            this.$nextTick(() => {
                // this.$refs.donutChart.updateOptions(this.chartOptions);
                // this.$refs.donutChart.updateSeries(this.chartSeries);
            });
        },
        getTotalMarcas() {
            return this.chartSeries.reduce((acc, value) => acc + value, 0);
        },
    },
};
</script>

