function selectColor(color, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.value = color;

    // Remover bordes previos
    const allCircles = document.querySelectorAll('.color-circle');
    allCircles.forEach(circle => {
        circle.style.border = '2px solid transparent';
    });

    // Marcar seleccionado
    const selected = Array.from(allCircles).find(c => c.style.backgroundColor.toLowerCase() === color.toLowerCase());
    if (selected) {
        selected.style.border = '2px solid #000';
    }
}
