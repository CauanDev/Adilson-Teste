<template>
    <div>
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
      data: {
        type: Array,
        required: true
      }
    },
    computed: {
      chartData() {
        // Contar a quantidade de produtos por marca
        const brandCount = this.data.reduce((acc, item) => {
          acc[item.marca] = (acc[item.marca] || 0) + 1;
          return acc;
        }, {});
  
        console.log('Brand Count:', brandCount); // Verifique a saída aqui
  
        // Preparar labels e dados do gráfico
        const labels = Object.keys(brandCount);
        const data = Object.values(brandCount);
  
        // Definir cores para cada marca
        const backgroundColor = labels.map((_, index) => {
          // Gerar uma cor aleatória para cada marca
          const randomColor = `hsl(${Math.random() * 360}, 70%, 70%)`;
          return randomColor;
        });
  
        return {
          labels: labels,
          datasets: [{
            label: 'Quantidade de Produtos por Marca',
            data: data,
            backgroundColor: backgroundColor
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
                  return `${context.label}: ${context.raw} produtos`; // Formatar rótulos
                }
              }
            },
            title: {
              display: true,
              text: 'Quantidade de Produtos por Marca',
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
    }
  };
  </script>
  