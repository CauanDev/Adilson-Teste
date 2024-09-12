<template>
    <div class="flex text-center bg-[#dbdbdb] w-1/2 p-2 rounded-lg mb-2 shadow-2xl border-[1px] border-gray-400">
        <div class="flex-1">
            <div v-if="series.length > 0">
                <h2 class="text-xl font-bold mb-2">Quantidade de Produtos Comprados por Dia</h2>
                <apexchart type="bar" width="99%" height="300" :options="chartOptions" :series="series"></apexchart>
            </div>
            <div v-else class="flex justify-center items-center h-full">
                <h2 class="text-xl font-bold mb-2 rotate-12">Sem Dados Disponíveis</h2>
            </div>
        </div>
    </div>
</template>


<script>
import VueApexCharts from "vue3-apexcharts";

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
            series: [],
            chartOptions: {
                chart: {
                    type: "bar",
                },
                xaxis: {
                    type: "category",
                    title: {
                        text: "Data",
                    },
                    labels: {
                        formatter: (value) => value, // Mostra a data como está
                    },
                },
                yaxis: {
                    title: {
                        text: "Quantidade de Produtos",
                    },
                },
                dataLabels: {
                    enabled: true,
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                    },
                },
                stroke: {
                    width: 1,
                    colors: ["#fff"],
                },
                grid: {
                    borderColor: "#e7e7e7",
                },
                legend: {
                    position: "bottom",
                },
                tooltip: {
                    x: {
                        formatter: (value) => value, // Mostra a data como está
                    },
                    y: {
                        formatter: (value) => `${value} produtos`,
                    },
                },
                colors: ["#007bff"],
            },
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
        formatDate(dateString) {
            const [year, month, day] = dateString.split("-"); // yyyy-mm-dd
            return `${year}/${month}/${day}`; // dd/mm/yyyy
        },
        updateChart(data) {
            // Agrupando os dados por data
            const groupedData = data.reduce((acc, pedido) => {
                const date = pedido.created_at.split("T")[0]; // Obtém yyyy-mm-dd
                const formattedDate = this.formatDate(date); // Converte para dd/mm/yyyy
                const quantity = parseInt(pedido.quantidade, 10);

                if (!acc[formattedDate]) {
                    acc[formattedDate] = 0;
                }

                acc[formattedDate] += quantity;

                return acc;
            }, {});
            // Ordena as datas
            const dates = Object.keys(groupedData).sort();

            // Prepara os dados para o gráfico de barras
            this.series = [
                {
                    name: "Quantidade de Produtos Vendidos",
                    data: dates.map((date) => ({
                        x: date, // Usa a data formatada como label
                        y: groupedData[date],
                    })),
                },
            ];

            // Configura os labels do eixo X com as datas em que houve compras
            this.chartOptions.xaxis.categories = dates;
        },
    },
};
</script>