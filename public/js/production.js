

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("productionForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page

        let productionForm = this;
        let productionFormData = new FormData(productionForm);

        fetch(productionForm.action, {
            method: productionForm.method,
            body: productionFormData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                let productionTable = document.getElementById("productionTable").querySelector("tbody");

                // Supprimer la ligne "Aucune adresse ajoutée" si elle existe
                let noProductionRow = document.getElementById("noProductionRow");
                if (noProductionRow) {
                    noProductionRow.remove();
                }
                //console.log("data.products:", data.products);
                let productList = '';
                data.products.forEach(product => {
                    productList += `<li>${product.name}</li>`;
                });
                // Ajouter la nouvelle adresse au tableau
                let newProductionRow = document.createElement("tr");
                newProductionRow.innerHTML = `
                    <td><b>${productList}</b></td>
                    <td>${data.signature_provider}</td>
                    <td>${data.app_fees}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-production" data-id="${data.id}">Supprimer</button>
                    </td>
                `;
                productionTable.appendChild(newProductionRow);

                // Fermer la modal
                let modal = bootstrap.Modal.getInstance(document.getElementById("ProductionForm"));
                modal.hide();

                // Réinitialiser le formulaire
                form.reset();
            }
        })
        .catch(error => console.error("Erreur :", error));
    });

    productionTable.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-production")) {
            let button = event.target;
            let productionId = button.getAttribute("data-id");
    
            // Remplacer {id} dans l'URL avec l'ID de l'adresse et s'assurer que l'URL est absolue
            let url = window.location.origin + "/production/api/delete/" + productionId;
    
    
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
                    if (productionTable.children.length === 0) {
                        let noProductionRow = document.createElement("tr");
                        noProductionRow.id = "noProductionRow";
                        noProductionRow.innerHTML = `<td colspan="4" class="text-center">Aucune production ajoutée</td>`;
                        productionTable.appendChild(noProductionRow);
                    }
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error("Erreur :", error));
        }
    });
    
});