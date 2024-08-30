<template>
    <div >
        <Pie :data="chartData" :options="chartOptions" />
    </div>
</template>

<script>
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

export default {
    name: 'PieChart',
    components: { Pie },
    props: {
        produtos: Array,
    },
    computed: {
        chartData() {
            // Preparar labels e dados do gráfico
            const labels = this.produtos.map(produto => produto.name);
            const data = this.produtos.map(produto => produto.tempoMedioDias);
            
            // Definir cores para cada item
            const backgroundColor = this.getRandomColors(data.length);

            return {
                labels,
                datasets: [{
                    label: 'Tempo Médio de Compra (Dias)',
                    data,
                    backgroundColor
                }]
            };
        },
        chartOptions() {
            return {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw} dias`; // Formatar rótulos
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Tempo Médio de Compra por Produto',
                        font: {
                            size: 16 // Tamanho da fonte do título
                        },
                        padding: {
                            top: 10,
                            bottom: 10 // Padding ao redor do título
                        }
                    }
                }
            };
        }
    },
    methods: {
        getRandomColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                colors.push(`hsl(${Math.random() * 360}, 70%, 70%)`);
            }
            return colors;
        }
    }
};
</script>