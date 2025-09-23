document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form.rating-form");
    if (form) {
        form.addEventListener("submit", function () {
            const btn = form.querySelector("button[type=submit]");
            btn.disabled = true;
            btn.innerText = "Enviando...";
            btn.classList.add("opacity-50");

            setTimeout(() => {
                form.classList.add("d-none");
                const successMsg = document.createElement("div");
                successMsg.className = "alert alert-success mt-3";
                successMsg.innerText = "✅ ¡Calificación enviada correctamente!";
                form.parentElement.appendChild(successMsg);
            }, 400);
        });
    }
});
