<template>
    <div class="flex text-center bg-[#dbdbdb] w-1/2 p-2 rounded-lg mb-2 shadow-xl border-[1px] border-gray-400">
        <div class="w-full">
            <h2 class="text-xl font-bold mb-2">{{this.cliente? 'Compras ' :'Pedidos '}} por Dia e Sexo</h2>            
            <apexchart type="line" width="100%" height="200" :options="barChartOptions" :series="barChartSeries" />

        </div>
    </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';

export default {
    name: 'SalesCharts',
    components: {
        apexchart: VueApexCharts,
    },
    props: {
        body: {
            type: Array,
            required: true,
        },
        cliente:{
            type:Boolean,
            default: false
        }
    },
    data() {
        return {
            barChartOptions: {},
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
        formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },
        updateBarChart(data) {
            const vendasPorDiaSexo = {
                Masculino: {},
                Feminino: {},
            };

            data.forEach(pedido => {
                const date = this.formatDate(pedido.created_at);
                const sexo = this.cliente? pedido.cliente_sexo :pedido.funcionario_sexo;
                if (!vendasPorDiaSexo[sexo][date]) {
                    vendasPorDiaSexo[sexo][date] = 0;
                }
                vendasPorDiaSexo[sexo][date]++;
            });

            // Ordena as datas do menor para o maior
            const dates = [...new Set([
                ...Object.keys(vendasPorDiaSexo.Masculino),
                ...Object.keys(vendasPorDiaSexo.Feminino),
            ])].sort((a, b) => {
                const [dayA, monthA, yearA] = a.split('/').map(Number);
                const [dayB, monthB, yearB] = b.split('/').map(Number);
                const dateA = new Date(yearA, monthA - 1, dayA);
                const dateB = new Date(yearB, monthB - 1, dayB);
                return dateA - dateB;
            });

            const seriesMasculino = dates.map(date => vendasPorDiaSexo.Masculino[date] || 0);
            const seriesFeminino = dates.map(date => vendasPorDiaSexo.Feminino[date] || 0);

            this.barChartOptions = {
                chart: {
                    id: 'bar-chart',
                    type: 'line',
                    height: 300,
                },
                xaxis: {
                    categories: dates,
                    labels: {
                        formatter: (value) => value,
                    },
                },
                yaxis: {
                    title: {
                        text: 'Número de Pedidos',
                    },
                },
                colors: ['#007bff', '#ff69b4'], // Azul para Masculino, Rosa para Feminino
                legend: {
                    position: 'bottom',
                },
            };

            this.barChartSeries = [
                {
                    name: 'Masculino',
                    data: seriesMasculino,
                },
                {
                    name: 'Feminino',
                    data: seriesFeminino,
                },
            ];
        },
    },
};
</script>
