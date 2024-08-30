<template>
    <div class="w-[50%]">
        <Pie :data="chartData" :options="chartOptions" />
    </div>
</template>

<script>
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

export default {
    name: 'PieChart',
    components: { Pie },
    props: {
        body: {
            type: Array,
            required: true
        }
    },
    computed: {
        chartData() {
            // Count occurrences of each client type
            const clientsByType = this.body.reduce((acc, item) => {
                const type = item.tipo.trim(); // Extract client type
                
                if (!acc[type]) {
                    acc[type] = 0;
                }
                acc[type] += 1; // Increment the count of clients for this type
                return acc;
            }, {});

            // Prepare chart labels and data
            const labels = Object.keys(clientsByType);
            const data = Object.values(clientsByType);

            // Define colors based on client type
            const colorMap = {
                'VIP': '#FFD700', // Gold color for 'VIP'
                'Normal': '#4CAF50' // Green color for 'Normal'
            };

            return {
                labels: labels,
                datasets: [{
                    label: 'Quantidade de Clientes por Tipo',
                    data: data,
                    backgroundColor: labels.map(type => colorMap[type] || '#CCCCCC'), // Map colors based on type
                    borderColor: labels.map(type => colorMap[type] || '#CCCCCC'), // Border colors based on type
                    borderWidth: 1 
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
                                return `${context.label}: ${context.raw}`; // Format tooltip labels
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Quantidade de Clientes por Tipo',
                        font: {
                            size: 16 // Font size for the title
                        },
                        padding: {
                            top: 10,
                            bottom: 10 // Padding around the title
                        }
                    }
                }
            };
        }
    }
}
</script>

