document.addEventListener('DOMContentLoaded', function () {
    const donorType = document.getElementById('donor_type');
    const orgFields = document.getElementById('organizationFields');
    const imageInput = document.getElementById('profile_image');
    const preview = document.getElementById('preview');
    const selectAllCheckbox = document.getElementById('select-all');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const form = document.querySelector('form');

    // Mostrar/ocultar campos adicionales para organización
    if (donorType && orgFields) {
        donorType.addEventListener('change', function () {
            orgFields.classList.toggle('d-none', this.value !== 'organization');
        });
    }

    // Vista previa de la imagen seleccionada
    if (imageInput && preview) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    }

    // Seleccionar/desmarcar todas las categorías
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }

    // Sincronizar "Seleccionar todas" con categorías individuales
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const total = categoryCheckboxes.length;
            const checked = document.querySelectorAll('.category-checkbox:checked').length;
            selectAllCheckbox.checked = total === checked;
        });
    });

    // Eliminar campos si el tipo no es "organization"
    if (form) {
        form.addEventListener('submit', function () {
            const selectedType = donorType.value;
            if (selectedType !== 'organization') {
                const orgNameInput = document.querySelector('[name="organization_name"]');
                const repNameInput = document.querySelector('[name="representative_name"]');
                if (orgNameInput) orgNameInput.remove();
                if (repNameInput) repNameInput.remove();
            }
        });
    }
});
