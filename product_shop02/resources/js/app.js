import Alpine from 'alpinejs';
import { toPng } from 'html-to-image';

window.Alpine = Alpine;

/**
 * Renders the branded monthly report card (#report-card) to a PNG and
 * downloads it. Used by the merchant dashboard.
 */
Alpine.data('reportExport', () => ({
    busy: false,
    async exportCard() {
        const node = document.getElementById('report-card');
        if (!node || this.busy) return;

        this.busy = true;
        try {
            // Wait for web fonts so the captured size matches the final layout.
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }

            // Capture the node at its full rendered size. Explicit width/height
            // (and a reset margin) stop the image being clipped.
            const width = Math.ceil(node.scrollWidth);
            const height = Math.ceil(node.scrollHeight);
            const options = {
                pixelRatio: 2,
                backgroundColor: '#ffffff',
                cacheBust: true,
                width,
                height,
                style: { margin: '0' },
            };

            // First pass warms font/style embedding; the second is reliable.
            await toPng(node, options);
            const dataUrl = await toPng(node, options);

            const link = document.createElement('a');
            link.download = 'qaid-report.png';
            link.href = dataUrl;
            link.click();
        } catch (error) {
            console.error('Report export failed', error);
        } finally {
            this.busy = false;
        }
    },
}));

Alpine.start();
