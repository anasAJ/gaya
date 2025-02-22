document.addEventListener('DOMContentLoaded', function () {
    const phaseSelect = document.getElementById('client_filter_phase');
    const statusSelect = document.getElementById('client_filter_status');

    phaseSelect.addEventListener('change', function () {
        const phaseId = phaseSelect.value;

        statusSelect.innerHTML = '<option value="">Sélectionnez un statut</option>';

        if (phaseId) {
            fetch(`/client/status/${phaseId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(status => {
                        const option = document.createElement('option');
                        option.value = status.id;
                        option.textContent = status.name;
                        statusSelect.appendChild(option);
                    });
                    statusSelect.disabled = false;
                })
                .catch(error => console.error('Erreur:', error));
        } else {
            statusSelect.disabled = true;
        }
    });
});