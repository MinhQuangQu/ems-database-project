// public/assets/js/chart.js/highlight.js
const HighlightPlugin = {
    id: 'highlightPlugin', // unique id
    beforeDraw(chart, args, options) {
        const { ctx, chartArea: { top, bottom, left, right } } = chart;
        if (!options) return;

        ctx.save();
        if (options.highlightBars) {
            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                meta.data.forEach((bar, index) => {
                    if (dataset.data[index] > options.threshold) {
                        ctx.fillStyle = options.color || 'rgba(255,0,0,0.2)';
                        ctx.fillRect(bar.x - bar.width/2, bar.y, bar.width, bottom - bar.y);
                    }
                });
            });
        }
        ctx.restore();
    }
};

// Đăng ký plugin toàn cục
Chart.register(HighlightPlugin);

const CustomTooltip = {
    id: 'customTooltip',
    afterDraw(chart) {
        const ctx = chart.ctx;
        chart.data.datasets.forEach((dataset, i) => {
            const meta = chart.getDatasetMeta(i);
            meta.data.forEach((bar, index) => {
                const value = dataset.data[index];
                ctx.fillStyle = 'black';
                ctx.font = '12px Arial';
                ctx.fillText(`$${value}`, bar.x, bar.y - 5);
            });
        });
    }
};
Chart.register(CustomTooltip);
