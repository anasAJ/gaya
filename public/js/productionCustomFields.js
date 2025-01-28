document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner toutes les checkboxes des produits
    let checkboxes = document.querySelectorAll('input[type="checkbox"][name="production[product][]"]');

    // Ajouter un écouteur d'événements pour chaque checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let productId = this.value; // Récupérer l'ID du produit sélectionné
            let checkboxLabel = this.closest('.form-check'); // Trouver l'élément parent qui contient la checkbox
            let customFieldsContainer = checkboxLabel.querySelector('.custom-fields-container'); // Container des custom fields sous la checkbox

            // Si le produit est sélectionné, afficher les customFields
            if (this.checked) {
                // Vérifier si les custom fields pour ce produit sont déjà ajoutés
                if (!customFieldsContainer) {
                    // Créer un conteneur pour les champs personnalisés sous la checkbox
                    let newCustomFieldsContainer = document.createElement('div');
                    newCustomFieldsContainer.classList.add('custom-fields-container');

                    // Faire la requête pour récupérer les custom fields
                    fetch(`/product/api/${productId}/custom-fields`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.customFields.length > 0) {
                                let customFieldsHTML = ''; // Initialiser une variable pour l'HTML des custom fields
                                data.customFields.forEach(field => {
                                    customFieldsHTML += `
                                        <div class="form-group" id="custom-field-${productId}-${field.id}">
                                            <label for="${field.id}">${field.label}</label>
                                            <input type="${field.type}" name="customFields[${productId}][${field.name}]" id="${field.id}" class="form-control" />
                                        </div>
                                    `;
                                });
                                newCustomFieldsContainer.innerHTML = customFieldsHTML;

                                // Ajouter le conteneur des champs personnalisés juste sous la checkbox
                                checkboxLabel.appendChild(newCustomFieldsContainer);
                            }
                        })
                        .catch(error => console.error('Error fetching custom fields:', error));
                }
            } else {
                // Si le produit est désélectionné, supprimer les champs personnalisés associés à ce produit
                let customFieldDiv = checkboxLabel.querySelector('.custom-fields-container');
                if (customFieldDiv) {
                    customFieldDiv.remove();
                }
            }
        });
    });
});
