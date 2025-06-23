document.addEventListener('DOMContentLoaded', function () {
    const txt_nombre = document.getElementById('txt_nombre');
    const btn_generar = document.getElementById('btn_generar');
    const checkboxesContainer = document.getElementById('checkboxesContainer');

    function crearCheckboxes() {
        checkboxesContainer.innerHTML = '';
        const texto = txt_nombre.value;
        if (texto.trim() === "") {
            return;
        }
        for (let i = 0; i < texto.length; i++) {
            const caracter = texto[i];

            // Crear el checkbox
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = 'char' + i;
            checkbox.name = 'char' + i;
            checkbox.value = caracter;

            // Crear la etiqueta para el checkbox
            const label = document.createElement('label');
            label.htmlFor = 'char' + i;
            label.appendChild(document.createTextNode(caracter));

            // ContenedOR para cada item de checkbox + label
            const itemContainer = document.createElement('div');
            itemContainer.classList.add('checkbox-item');
            itemContainer.appendChild(checkbox);
            itemContainer.appendChild(label);

            // Añadir el item al contenedor principal
            checkboxesContainer.appendChild(itemContainer);
        }
    }
    btn_generar.addEventListener('click', crearCheckboxes);
});