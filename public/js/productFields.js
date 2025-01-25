document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("addCustomField").addEventListener("click", function () {
        let label = document.getElementById("fieldLabel").value;
        let type = document.getElementById("fieldType").value;
        let ctx = document.getElementById("fieldCtx").value;
        let productId = document.getElementById("productId").value; // ID du produit
        
        if (!label || !type) {
            alert("Veuillez remplir tous les champs !");
            return;
        }

        // Envoi des données au serveur via AJAX
        fetch(`/product/api/${productId}/add-custom-field`, {
            method: "POST",
            body: JSON.stringify({ label: label, type: type, ctx: ctx }),
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                // ✅ Ajouter la nouvelle ligne dans le tableau
                let tbody = document.getElementById("customFieldsTable").querySelector("tbody");

                let newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td>${data.label}</td>
                    <td>${data.type}</td>
                    <td>${data.ctx}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-field" data-id="${data.id}">Supprimer</button>
                    </td>
                `;

                tbody.appendChild(newRow); // Ajoute la nouvelle ligne sans effacer les autres

                let modal = bootstrap.Modal.getInstance(document.getElementById("customFieldModal"));
                modal.hide();

                // Vider les champs
                document.getElementById("fieldLabel").value = "";
                document.getElementById("fieldType").value = "text";
                document.getElementById("fieldCtx").value = "";
            }
        })
        .catch(error => console.error("Erreur :", error));
    });

    // Fonction pour supprimer un champ personnalisé
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-field")) {
            event.preventDefault();
            let button = event.target;
            let fieldId = button.getAttribute("data-id");
            let productId = document.getElementById("productId").value;
    
            console.log("Suppression du champ :", fieldId, "pour le produit :", productId); // Debug
    
            fetch(`/product/api/${productId}/delete-custom-field/${fieldId}`, {  // Vérifie que cette URL est correcte
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.closest("tr").remove();
                    console.log("Champ supprimé avec succès !");
                } else {
                    alert("Erreur lors de la suppression !");
                }
            })
            .catch(error => console.error("Erreur AJAX :", error));
        }
    });    
});

