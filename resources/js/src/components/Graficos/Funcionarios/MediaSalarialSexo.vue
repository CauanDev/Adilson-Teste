<!-- MediaSalarialPorSexoChart.vue -->
<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="this.body.length>0">
            <h2 class="text-xl font-bold mt-2">Média Salarial por Sexo</h2>
            <apexchart type="bar" :options="barChartOptions" :series="barChartSeries" width="100%" />
        </div>
        <div v-else class="flex justify-center items-center h-full">
            <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>

        </div>
    </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts'; // Certifique-se de que este import está correto

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
            barChartOptions: {},
            barChartSeries: []
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
            const mediaSalarialData = categorias.map(sexo => {
                const salarios = this.body.filter(item => item.sexo === sexo).map(item => parseFloat(item.salario));
                const mediaSalario = salarios.length > 0 ? (salarios.reduce((acc, salario) => acc + salario, 0) / salarios.length).toFixed(2) : 0;
                return {
                    name: sexo,
                    data: [parseFloat(mediaSalario)]
                };
            });

            this.barChartOptions = {
                chart: {
                    id: 'bar-chart-salario',
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '100%', // Ajuste para controlar o espaço entre as barras
                        endingShape: 'rounded'
                    }
                },
                xaxis: {
                    categories: categorias,
                    labels: {
                        show: false
                    },
                    tickPlacement: 'on'
                },
                dataLabels: {
                    enabled: true,
                    offsetX: 0
                },
                colors: categorias.map(sexo => sexo === 'Masculino' ? '#007bff' : '#ff69b4'), // Azul para masculino, rosa para feminino
                grid: {
                    padding: {
                        left: 20,
                        right: 20
                    }
                }
            };
            this.barChartSeries = mediaSalarialData.length > 0 ? mediaSalarialData : [{
                name: 'Sem Dados',
                data: [0]
            }];
        }
    }
};
</script>