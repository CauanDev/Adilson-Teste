<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="donutChartSeries.length > 0" >
            <h2 class="text-xl font-bold mb-2">Quantidade de {{this.cliente? 'Compras ':'Serviços '}} por Sexo</h2>
            <apexchart ref="donutChart" type="donut" width="100%" :options="donutChartOptions"
                :series="donutChartSeries"></apexchart>
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
        cliente:{
            type: Boolean,
            default:false
        }
    },
    data() {
        return {
            donutChartOptions: {
                chart: {
                    type: 'donut',
                },
                labels: ['Masculino', 'Feminino'],
                legend: {
                    position: 'bottom',
                },
                colors: ['#007bff', '#FF69B4'],
                dataLabels: {
                    enabled: true,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    color: '#373d3f',
                                    formatter: () => this.totalPedidos,
                                },
                            },
                        },
                    },
                },
            },
            donutChartSeries: [],
            totalPedidos: 0,
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
            const serviceCountBySex = {
                Masculino: 0,
                Feminino: 0,
            };

            data.forEach(pedido => {
                const sexo = this.cliente? pedido.cliente_sexo :pedido.funcionario_sexo;

                // Contar o número de pedidos por sexo
                serviceCountBySex[sexo] += 1;
            });

            this.donutChartSeries = [
                serviceCountBySex.Masculino,
                serviceCountBySex.Feminino,
            ];

            // Calcular o total de pedidos
            this.totalPedidos = serviceCountBySex.Masculino + serviceCountBySex.Feminino;
        },
    },
};
</script>
