/* Admin Rewards — UX: previsualizar imágenes, arrastrar/soltar, marcar para borrar, confirmaciones */
(function () {
    const $ = (s, c = document) => c.querySelector(s),
        $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

    // ---- Previsualizar nuevas imágenes en create/edit ----
    function bindImagePicker() {
        const picker = document.querySelector(
            'input[type="file"][name="image[]"], input[type="file"][name="image"]'
        );
        const preview = document.querySelector("#ar-preview"); // contenedor .gallery
        const drop = document.querySelector("#ar-drop");

        if (!picker || !preview) return;

        const addPreview = (file) => {
            if (!file || !file.type.startsWith("image/")) return;
            const url = URL.createObjectURL(file);
            const wrap = document.createElement("div");
            wrap.className = "thumb";
            wrap.innerHTML = `<img src="${url}" alt=""><button type="button" class="x" title="Quitar">×</button>`;
            const removeBtn = wrap.querySelector(".x");
            on(removeBtn, "click", () => {
                wrap.remove();
                removeFromInput(file);
                URL.revokeObjectURL(url);
            });
            preview.appendChild(wrap);
        };

        const removeFromInput = (file) => {
            const dt = new DataTransfer();
            Array.from(picker.files).forEach((f) => {
                if (f !== file) dt.items.add(f);
            });
            picker.files = dt.files;
        };

        on(picker, "change", () => {
            if (!picker.files?.length) return;
            Array.from(picker.files).forEach(addPreview);
        });

        // Drag & drop (opcional)
        if (drop) {
            ["dragenter", "dragover"].forEach((ev) =>
                on(drop, ev, (e) => {
                    e.preventDefault();
                    drop.classList.add("drag");
                })
            );
            ["dragleave", "drop"].forEach((ev) =>
                on(drop, ev, (e) => {
                    e.preventDefault();
                    drop.classList.remove("drag");
                })
            );
            on(drop, "drop", (e) => {
                const files = e.dataTransfer?.files || [];
                if (!files.length) return;
                const dt = new DataTransfer();
                // merge actuales + nuevos
                Array.from(picker.files).forEach((f) => dt.items.add(f));
                Array.from(files).forEach((f) => {
                    dt.items.add(f);
                    addPreview(f);
                });
                picker.files = dt.files;
            });
        }
    }

    // ---- Confirmación al eliminar en index/edit ----
    function bindDeletes() {
        $$('form[data-confirm="delete"]').forEach((f) => {
            on(f, "submit", (e) => {
                if (
                    !confirm(
                        "¿Eliminar esta recompensa? Esta acción no se puede deshacer."
                    )
                )
                    e.preventDefault();
            });
        });
    }

    // ---- Filtros: autosubmit al cambiar selects en index ----
    function bindFilters() {
        const form = document.querySelector(
            'form[action*="admin/rewards"][method="get"]'
        );
        if (!form) return;
        $$("select", form).forEach((s) => on(s, "change", () => form.submit()));
    }

    // ---- Init ----
    document.addEventListener("DOMContentLoaded", () => {
        bindImagePicker();
        bindDeletes();
        bindFilters();
    });
})();
