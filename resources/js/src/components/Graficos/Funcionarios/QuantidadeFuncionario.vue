<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="donutChartSeries.length > 0">
            <h2 class="text-md font-bold mb-2">Quantidade de {{title}} por Sexo</h2>
            <apexchart type="donut" :options="donutChartOptions" :series="donutChartSeries" width="100%" />
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
        },
        title:{
            type: String,
            default:"Funcionários"
        }
    },
    data() {
        return {
            donutChartOptions: {},
            donutChartSeries: []
        };
    },
    watch: {
        body: {
            immediate: true,
            handler() {
                this.updateCharts();
            }
        }
    },
    methods: {
        updateCharts() {
            const categorias = [...new Set(this.body.map(item => item.sexo))];

            // Garantir que sempre haja dados para 'Masculino' e 'Feminino'
            if (!categorias.includes('Masculino')) categorias.push('Masculino');
            if (!categorias.includes('Feminino')) categorias.push('Feminino');

            const totalFuncionariosPorSexo = categorias.map(sexo => {
                return {
                    name: sexo,
                    data: this.body.filter(item => item.sexo === sexo).length
                };
            });
            const totalFuncionarios = totalFuncionariosPorSexo.reduce((acc, curr) => acc + curr.data, 0);

            this.donutChartOptions = {
                chart: {
                    id: 'donut-chart',
                    type: 'donut'
                },
                labels: categorias,
                dataLabels: {
                    enabled: true,
                },
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    color: '#333',
                                    formatter: () => totalFuncionarios
                                }
                            }
                        }
                    }
                },
                colors: categorias.map(sexo => sexo === 'Masculino' ? '#007bff' : '#ff69b4') // Azul para masculino, rosa para feminino
            };

            this.donutChartSeries = totalFuncionariosPorSexo.map(item => item.data);
        }
    }
};
</script>
