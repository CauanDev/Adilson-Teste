<!-- MediaIdadePorSexoChart.vue -->
<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="barChartSeriesIdade.length > 0">
            <h2 class="text-xl font-bold mb-2">Média de Idade por Sexo</h2>
            <apexchart type="bar" :options="barChartOptionsIdade" :series="barChartSeriesIdade" width="100%" />
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
            required: true
        }
    },
    data() {
        return {
            barChartOptionsIdade: {},
            barChartSeriesIdade: []
        };
    },
    watch: {
        body: {
            immediate: true,
            handler() {
                this.updateChart();
            }
        }
    },
    methods: {
        updateChart() {
            const categorias = [...new Set(this.body.map(item => item.sexo))];
            const mediaIdadeData = categorias.map(sexo => {
                const idades = this.body.filter(item => item.sexo === sexo).map(item => parseInt(item.idade, 10));
                const mediaIdade = idades.length > 0 ? (idades.reduce((acc, idade) => acc + idade, 0) / idades.length).toFixed(2) : 0;
                return {
                    name: sexo,
                    data: [parseFloat(mediaIdade)]
                };
            });

            this.barChartOptionsIdade = {
                chart: {
                    id: 'bar-chart-idade',
                    type: 'bar',
                },
                xaxis: {
                    categories: categorias,
                    tickPlacement: 'on'
                },
                dataLabels: {
                    enabled: true,
                    offsetX: 0
                },
                colors: categorias.map(sexo => sexo === 'Masculino' ? '#007bff' : '#ff69b4') // Azul para masculino, rosa para feminino
            };

            this.barChartSeriesIdade = mediaIdadeData.length > 0 ? mediaIdadeData : [{
                name: 'Sem Dados',
                data: [0]
            }];
        }
    }
};
</script>
