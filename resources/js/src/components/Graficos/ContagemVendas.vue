<template>
    <div>
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script>
  import { Bar } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
  
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
  
  export default {
    name: 'SalesChart',
    components: { Bar },
    props: {
      salesData: Array,
    },
    computed: {
      chartData() {
        // Agrupar vendas por dia
        const salesByDate = this.salesData.reduce((acc, sale) => {
          const date = new Date(sale.created_at).toLocaleDateString('pt-BR'); // Formata a data no formato DD/MM/YYYY
          acc[date] = (acc[date] || 0) + 1; // Conta o número de vendas por dia
          return acc;
        }, {});
  
        // Preparar labels e dados do gráfico
        const labels = Object.keys(salesByDate);
        const data = Object.values(salesByDate);
  
        return {
          labels,
          datasets: [{
            label: 'Quantidade de Vendas por Dia',
            data,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
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
                label: function(context) {
                  return `${context.label}: ${context.raw} vendas`; // Formatar rótulos
                }
              }
            },
            title: {
              display: true,
              text: 'Quantidade de Vendas por Dia',
              font: {
                size: 16 // Tamanho da fonte do título
              },
              padding: {
                top: 10,
                bottom: 10 // Padding ao redor do título
              }
            }
          },
          scales: {
            x: {
              title: {
                display: true,
                text: 'Data',
                font: {
                  size: 14
                }
              }
            },
            y: {
              title: {
                display: true,
                text: 'Quantidade Vendas',
                font: {
                  size: 14
                }
              },
              beginAtZero: true
            }
          }
        };
      }
    }
  };
  </script>
  