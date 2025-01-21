document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("clientForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page

        let form = event.target;
        let formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            let flashMessage = document.getElementById("flashMessage");

            if (data.success) {
                flashMessage.classList.remove("d-none", "alert-danger");
                flashMessage.classList.add("alert-success");
                flashMessage.textContent = data.message;
            } else {
                flashMessage.classList.remove("d-none", "alert-success");
                flashMessage.classList.add("alert-danger");
                flashMessage.textContent = data.errors.join(", ");
            }

            setTimeout(() => {
                flashMessage.classList.add("d-none");
            }, 3000); // Cache le message après 3s
        })
        .catch(error => console.error("Erreur :", error));
    });
});
