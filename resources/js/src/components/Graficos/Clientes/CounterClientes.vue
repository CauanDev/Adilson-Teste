<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="body.length > 0">
            <h2 class="text-xl font-bold mb-2">Quantidade de Clientes por Tipo e Sexo</h2>
            <apexchart ref="barChart" type="bar" width="100%" :options="chartOptions" :series="chartSeries"></apexchart>
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
                    height: 400,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                    },
                },
                dataLabels: {
                    enabled: true,
                },
                xaxis: {
                    categories: ['Masculino', 'Feminino'], // Agrupado por sexo
                },
                colors: ['#007bff', '#FF69B4'], // Azul para Masculino, Rosa para Feminino
                legend: {
                    position: 'bottom',
                },
                fill: {
                    opacity: 1,
                },
            },
            chartSeries: [],
        };
    },
    watch: {
        body: {
            handler(newData) {
                this.updateChart(newData);
            },
            deep: true,
        },
    },
    mounted() {
        this.updateChart(this.body);
    },
    methods: {
        updateChart(data) {
            const clientCount = {
                Masculino: { Vip: 0, Normal: 0 },
                Feminino: { Vip: 0, Normal: 0 },
            };

            // Contar o número de clientes por sexo e tipo
            data.forEach(cliente => {
                const sexo = cliente.sexo;
                const tipo = cliente.tipo;

                if (clientCount[sexo]) {
                    clientCount[sexo][tipo] += 1;
                }
            });
            
            // Atualizar as séries do gráfico
            this.chartSeries = [
                {
                    name: 'VIP',
                    data: [clientCount.Masculino.Vip, clientCount.Feminino.Vip], // Quantidade VIP por sexo
                },
                {
                    name: 'Normal',
                    data: [clientCount.Masculino.Normal, clientCount.Feminino.Normal], // Quantidade Normal por sexo
                },
            ];
        },
    },
};
</script>
