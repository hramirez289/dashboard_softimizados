
      // Inicialización de gráficos
      let chart1, chart2, chart3, chart4, chart5;
      Chart.register(ChartDataLabels);

      function crearGraficos(data) {
        // Chart 1 (estatus)
        if (chart1) chart1.destroy();
        const ctx = document.getElementById('myChart');
        chart1 = new Chart(ctx, {
          type: 'bar',
          data: { 
            labels: ['abiertas','cerradas'],
            datasets: [{
              label: 'Estatus OT',
              data: [data.estado_ot_a, data.estado_ot_c],
              backgroundColor: ['#0077B6', '#F4A261'],
              borderWidth: 1
            }]
          },
          options: { 
            responsive: true,
            indexAxis: 'y',
            plugins: {
              datalabels: {
                color: '#e4dfdfff',
                font: {
                  size: 14,
                  weight: 'bold'
                },
                anchor: 'center',
                align: 'center'
              }
            }
          }
        });

        // Chart 2 (tipos) 
        if (chart2) chart2.destroy();
        const ctx2 = document.getElementById('myChart2');
        chart2 = new Chart(ctx2, {
          type: 'bar',
          data: { 
            labels: ['Preventivo','Correctivo','Reparacion','Otros','Carta Gant'],
            datasets: [{
              label: 'Tipo de mantencion',
              data: [data.confec, data.prev, data.corr, data.otros, data.prevcg],
              backgroundColor: ['#0077B6', '#F4A261', '#0077B6', '#F4A261', '#0077B6'],
              borderWidth: 1
            }]
          },
          options: { 
            responsive: true,
            plugins: {
              datalabels: {
                color: '#e4dfdfff',
                font: {
                  size: 14,
                  weight: 'bold'
                },
                anchor: 'center',
                align: 'center'
              }
            }
          }
        });

        // Chart 4 (Con / Sin cotización) - GRÁFICO DE DONA
        if (chart4) chart4.destroy();
        const ctx4 = document.getElementById('myChart4');
        chart4 = new Chart(ctx4, {
          type: 'doughnut',
          data: {
            labels: ['Con cotización','Sin cotización'],
            datasets: [{
              label: '',
              data: [data.conCotizacion || 0, data.sinCotizacion || 0],
              backgroundColor: ['#0077B6', '#F4A261'],
              borderWidth: 1
            }]
          },
          options: { 
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            plugins: {
              datalabels: {
                color: '#e4dfdfff',
                font: {
                  size: 14,
                  weight: 'bold'
                },
                formatter: (value, context) => {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                  return `${value}\n(${percentage}%)`;
                }
              }
            }
          }
        });
      }

