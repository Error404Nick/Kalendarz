const modal = document.getElementById('modal-zadania');
        const inputData = document.getElementById('wybrana-data-input');
        const pokazDate = document.getElementById('pokaz-date');

        function otworzFormularz(data) {
            inputData.value = data;
            pokazDate.textContent = data;
            modal.showModal();
        }

        function zamknijFormularz() {
            modal.close();        
        }