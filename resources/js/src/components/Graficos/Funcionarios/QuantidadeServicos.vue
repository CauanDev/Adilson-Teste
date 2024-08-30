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
            // Calculate the count of services per employee and aggregate by sex
            const servicesBySex = this.body.reduce((acc, item) => {
                const sexo = item.sexo.trim(); // Extract employee sex
                
                if (!acc[sexo]) {
                    acc[sexo] = 0;
                }
                acc[sexo] += 1; // Increment the count of services for this sex
                return acc;
            }, {});

            // Prepare chart labels and data
            const labels = Object.keys(servicesBySex);
            const data = Object.values(servicesBySex);

            // Define colors based on sex
            const colorMap = {
                'Masculino': '#36A2EB', // Color for 'Masculino'
                'Feminino': '#FF6384'  // Color for 'Feminino'
            };

            return {
                labels: labels,
                datasets: [{
                    label: 'Quantidade de Serviços por Sexo',
                    data: data,
                    backgroundColor: labels.map(sex => colorMap[sex] || '#CCCCCC'), // Map colors based on sex
                    borderColor: labels.map(sex => colorMap[sex] || '#CCCCCC'), // Border colors based on sex
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
                        text: 'Quantidade de Serviços por Sexo',
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
