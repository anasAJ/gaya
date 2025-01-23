document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("adressForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page

        let form = this;
        let formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                let addressTable = document.getElementById("addressTable").querySelector("tbody");

                // Supprimer la ligne "Aucune adresse ajoutée" si elle existe
                let noAddressRow = document.getElementById("noAddressRow");
                if (noAddressRow) {
                    noAddressRow.remove();
                }

                // Ajouter la nouvelle adresse au tableau
                let newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td><b>${data.designation}</b></td>
                    <td>${data.address_1}</td>
                    <td>${data.address_2}</td>
                    <td>${data.city}</td>
                    <td>${data.zip}</td>
                    <td>${data.country}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-address" data-id="${data.id}">Supprimer</button>
                    </td>
                `;
                addressTable.appendChild(newRow);

                // Fermer la modal
                let modal = bootstrap.Modal.getInstance(document.getElementById("AdresseForm"));
                modal.hide();

                // Réinitialiser le formulaire
                form.reset();
            }
        })
        .catch(error => console.error("Erreur :", error));
    });

    addressTable.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-address")) {
            let button = event.target;
            let addressId = button.getAttribute("data-id");
    
            // Remplacer {id} dans l'URL avec l'ID de l'adresse et s'assurer que l'URL est absolue
            let url = window.location.origin + "/addresses/api/delete/" + addressId;
    
    
            fetch(url, {
                method: "DELETE",
                headers: { 
                    "X-Requested-With": "XMLHttpRequest" 
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let row = button.closest("tr");
                    row.remove();
    
                    // Si le tableau est vide, ajouter la ligne "Aucune adresse ajoutée"
                    if (addressTable.children.length === 0) {
                        let noAddressRow = document.createElement("tr");
                        noAddressRow.id = "noAddressRow";
                        noAddressRow.innerHTML = `<td colspan="7" class="text-center">Aucune adresse ajoutée</td>`;
                        addressTable.appendChild(noAddressRow);
                    }
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error("Erreur :", error));
        }
    });
    
});