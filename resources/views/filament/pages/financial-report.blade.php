<x-filament-panels::page>
    <form wire:submit.prevent="download">
        {{ $this->form }}

        <div class="mt-6 flex justify-start">
            <x-filament::button type="submit" icon="heroicon-o-arrow-down-tray" color="success">
                Download Laporan PDF
            </x-filament::button>
        </div>
    </form>

    <script>
        window.addEventListener('download-file', event => {
            const link = document.createElement('a');
            link.href = 'data:application/pdf;base64,' + event.detail[0].content;
            link.download = event.detail[0].filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</x-filament-panels::page>
