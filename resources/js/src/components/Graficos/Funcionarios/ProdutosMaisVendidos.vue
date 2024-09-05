<template>
    <div class="flex-1 text-center bg-[#dbdbdb] shadow-2xl rounded-lg p-2 border-[1px] border-gray-400">
        <div v-if="barChartSeries.length > 0">
            <h2 class="text-xl font-bold mb-2">Produtos Mais {{this.cliente?'Comprados ':'Vendidos ' }}por Sexo</h2>
            <apexchart ref="barChart" type="bar" width="100%" :options="barChartOptions" :series="barChartSeries">
            </apexchart>
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
            type:Boolean,
            default: false
        }
    },
    data() {
        return {
            barChartOptions: {
                chart: {
                    type: 'bar',
                    height: 200,
                },
                xaxis: {
                    categories: [], // Nomes dos produtos
                    labels: {
                        style: {
                            fontSize: '9px',
                            colors: '#333',
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: this.cliente? 'Quantidade Comprada':'Quantidade Vendida',
                    },
                },
                dataLabels: {
                    enabled: true,
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    }
                },
                grid: {
                    borderColor: '#e7e7e7',
                },
                legend: {
                    position: 'bottom',
                },
                colors: ['#007bff', '#FF69B4'],
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
            const productCountBySex = {
                Masculino: {},
                Feminino: {},
            };

            data.forEach(pedido => {
                const sexo = this.cliente ? pedido.cliente_sexo : pedido.funcionario_sexo;
                pedido.produtos.forEach(produto => {
                    if (!productCountBySex[sexo][produto.name]) {
                        productCountBySex[sexo][produto.name] = 0;
                    }
                    productCountBySex[sexo][produto.name] += produto.quantidade;
                });
            });

            // Atualizando o gráfico de barras
            const productNames = Object.keys(productCountBySex.Masculino)
                .concat(Object.keys(productCountBySex.Feminino))
                .filter((value, index, self) => self.indexOf(value) === index);

            const quantitiesMasculino = productNames.map(name => productCountBySex.Masculino[name] || 0);
            const quantitiesFeminino = productNames.map(name => productCountBySex.Feminino[name] || 0);

            this.barChartOptions.xaxis.categories = productNames;
            this.barChartSeries = [
                {
                    name: 'Masculino',
                    data: quantitiesMasculino,
                },
                {
                    name: 'Feminino',
                    data: quantitiesFeminino,
                },
            ];
            this.$nextTick(() => {
                this.$refs.barChart.updateOptions(this.barChartOptions);
                this.$refs.barChart.updateSeries(this.barChartSeries);
            });
        },
    },
};
</script>