document.addEventListener("DOMContentLoaded", function () {
    const departmentSelect = document.getElementById("department");
    const provinceSelect = document.getElementById("province");
    const municipalitySelect = document.getElementById("municipality");

    // Cargar departamentos al inicio
    fetch("/locations/departments")
        .then(response => response.json())
        .then(data => {
            data.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.department;
                option.textContent = dep.department;
                departmentSelect.appendChild(option);
            });
        });

    // Al cambiar departamento, cargar provincias
    departmentSelect.addEventListener("change", function () {
        provinceSelect.innerHTML = '<option value="">Seleccionar...</option>';
        municipalitySelect.innerHTML = '<option value="">Seleccionar...</option>';

        const department = departmentSelect.value;
        if (!department) return;

        fetch(`/locations/provinces?department=${encodeURIComponent(department)}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(prov => {
                    const option = document.createElement("option");
                    option.value = prov.province;
                    option.textContent = prov.province;
                    provinceSelect.appendChild(option);
                });
            });
    });

    // Al cambiar provincia, cargar municipios
    provinceSelect.addEventListener("change", function () {
        municipalitySelect.innerHTML = '<option value="">Seleccionar...</option>';

        const department = departmentSelect.value;
        const province = provinceSelect.value;
        if (!department || !province) return;

        fetch(`/locations/municipalities?department=${encodeURIComponent(department)}&province=${encodeURIComponent(province)}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(muni => {
                    const option = document.createElement("option");
                    option.value = muni.municipality;
                    option.textContent = muni.municipality;
                    municipalitySelect.appendChild(option);
                });
            });
    });
});
