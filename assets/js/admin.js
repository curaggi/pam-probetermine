(function($){
    $(function(){

        var canvas = document.getElementById('pam-pt-chart');
        if (canvas && window.Chart) {
            try {
                var labels = [];
                var values = [];

                if (canvas.dataset.labels) {
                    labels = JSON.parse(canvas.dataset.labels);
                }
                if (canvas.dataset.values) {
                    values = JSON.parse(canvas.dataset.values);
                }

                var ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tests',
                            data: values,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            } catch(e) {
                if (window.console && console.warn) {
                    console.warn('PAM Chart error', e);
                }
            }
        }

    });
})(jQuery);
