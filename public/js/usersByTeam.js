document.addEventListener('DOMContentLoaded', function () {
    initPage(); // Initialisation après le premier chargement de la page
});

// Fonction pour ajouter les événements
function initPage() {
    // Sélectionner dynamiquement toutes les checkboxes d'équipe
    const teamCheckboxes = document.querySelectorAll('[id^="predictive_team_"]');
    const userContainer = document.querySelector('[data-role="user-select"]');
    
    if (!teamCheckboxes.length || !userContainer) {
        console.warn('Les éléments du formulaire ne sont pas trouvés.');
        return;
    }

    // Ajouter un écouteur d'événements sur chaque checkbox d'équipe
    teamCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            handleCheckboxChange(this); // Utiliser une fonction dédiée pour la logique
        });
    });

    // Si l'ID de l'équipe est 0, coche toutes les checkboxes
    const teamId = getTeamIdFromUrl(); // Fonction pour récupérer teamId (si nécessaire)
    if (teamId === 0) {
        teamCheckboxes.forEach(checkbox => checkbox.checked = false); // Assurer que toutes les checkboxes sont désélectionnées
    }

    updateUsers(); // Mettre à jour les utilisateurs au premier chargement
}

// Gérer les changements des checkboxes d'équipe
function handleCheckboxChange(checkbox) {
    const teamCheckboxes = document.querySelectorAll('[id^="predictive_team_"]');
    
    // Si la checkbox "Toutes les équipes" (id="predictive_team_0") est cochée/décrochée
    if (checkbox.value === "0") {
        const isChecked = checkbox.checked;
        teamCheckboxes.forEach(checkbox => {
            // Ne pas affecter la checkbox "Toutes les équipes"
            if (checkbox.value !== "0") {
                checkbox.checked = isChecked; // Cocher/décoche toutes les autres
            }
        });
    }

    updateUsers(); // Mettre à jour la liste des utilisateurs selon les équipes sélectionnées
}

function updateUsers() {
    const teamCheckboxes = document.querySelectorAll('[id^="predictive_team_"]');
    const userContainer = document.querySelector('[data-role="user-select"]');
    let selectedTeams = [];

    // Récupérer les équipes sélectionnées
    teamCheckboxes.forEach(checkbox => {
        if (checkbox.checked && checkbox.value !== "0") { // Ne pas inclure "Toutes les équipes" ici
            selectedTeams.push(checkbox.value);
        }
    });

    if (selectedTeams.length === 0) {
        userContainer.innerHTML = '<p>Aucune équipe sélectionnée</p>';
        return;
    }

    // Faire une requête AJAX pour récupérer les utilisateurs selon les équipes sélectionnées
    fetch(`/user/teams/by-teams?teams=${selectedTeams.join(',')}`)
        .then(response => response.json())
        .then(data => {
            userContainer.innerHTML = ''; // Vider les utilisateurs actuels

            data.forEach(user => {
                const div = document.createElement('div');
                div.classList.add('form-check'); // Ajouter une classe pour le style (si nécessaire)

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'users[]';
                checkbox.value = user.id;
                checkbox.id = `predictive_user_${user.id}`;
                checkbox.classList.add('user-checkbox');
                checkbox.classList.add('form-check-input'); // Si tu utilises Bootstrap

                const label = document.createElement('label');
                label.htmlFor = `predictive_user_${user.id}`;
                label.textContent = user.name;
                label.classList.add('form-check-label'); // Si tu utilises Bootstrap

                div.appendChild(checkbox);
                div.appendChild(label);
                userContainer.appendChild(div);
            });

            attachUserEventListeners(); // Ajouter les écouteurs pour les utilisateurs
        })
        .catch(error => console.error('Erreur lors du chargement des utilisateurs:', error));
}

function attachUserEventListeners() {
    // Sélectionner toutes les checkboxes utilisateurs générées
    const userCheckboxes = document.querySelectorAll('[id^="predictive_user_"]');

    userCheckboxes.forEach(userCheckbox => {
        userCheckbox.addEventListener('change', function () {
            console.log(`Utilisateur sélectionné: ${this.value} (${this.checked ? 'coché' : 'décoché'})`);
        });
    });
}

// Fonction pour récupérer teamId depuis l'URL (si nécessaire)
function getTeamIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return parseInt(urlParams.get('teamId')) || 0; // Retourne 0 si 'teamId' n'est pas présent dans l'URL
}
