document.addEventListener('DOMContentLoaded', function () {
    const commonColors = [
        { name: 'Rojo', value: '#FF0000', category: 'Básicos' },
            { name: 'Verde', value: '#00FF00', category: 'Básicos' },
            { name: 'Azul', value: '#0000FF', category: 'Básicos' },
            { name: 'Amarillo', value: '#FFFF00', category: 'Básicos' },
            { name: 'Magenta', value: '#FF00FF', category: 'Básicos' },
            { name: 'Cian', value: '#00FFFF', category: 'Básicos' },
            { name: 'Negro', value: '#000000', category: 'Básicos' },
            { name: 'Blanco', value: '#FFFFFF', category: 'Básicos' },
            
            // Rojos
            { name: 'Rojo Carmín', value: '#DC143C', category: 'Rojos' },
            { name: 'Rojo Ladrillo', value: '#B22222', category: 'Rojos' },
            { name: 'Rojo Fuego', value: '#FF4500', category: 'Rojos' },
            { name: 'Rojo Cereza', value: '#DE3163', category: 'Rojos' },
            { name: 'Rojo Granate', value: '#800020', category: 'Rojos' },
            { name: 'Rojo Escarlata', value: '#FF2400', category: 'Rojos' },
            { name: 'Rojo Bermellón', value: '#E34234', category: 'Rojos' },
            { name: 'Rojo Rubí', value: '#E0115F', category: 'Rojos' },
            { name: 'Rojo Sangre', value: '#8B0000', category: 'Rojos' },
            { name: 'Rojo Coral', value: '#FF7F50', category: 'Rojos' },
            
            // Azules
            { name: 'Azul Marino', value: '#000080', category: 'Azules' },
            { name: 'Azul Cielo', value: '#87CEEB', category: 'Azules' },
            { name: 'Azul Real', value: '#4169E1', category: 'Azules' },
            { name: 'Azul Medianoche', value: '#191970', category: 'Azules' },
            { name: 'Azul Cobalto', value: '#0047AB', category: 'Azules' },
            { name: 'Azul Turquesa', value: '#40E0D0', category: 'Azules' },
            { name: 'Azul Acero', value: '#4682B4', category: 'Azules' },
            { name: 'Azul Pavo Real', value: '#005F69', category: 'Azules' },
            { name: 'Azul Eléctrico', value: '#7DF9FF', category: 'Azules' },
            { name: 'Azul Zafiro', value: '#0F52BA', category: 'Azules' },
            
            // Verdes
            { name: 'Verde Esmeralda', value: '#50C878', category: 'Verdes' },
            { name: 'Verde Lima', value: '#32CD32', category: 'Verdes' },
            { name: 'Verde Bosque', value: '#228B22', category: 'Verdes' },
            { name: 'Verde Menta', value: '#98FB98', category: 'Verdes' },
            { name: 'Verde Oliva', value: '#808000', category: 'Verdes' },
            { name: 'Verde Jade', value: '#00A86B', category: 'Verdes' },
            { name: 'Verde Neón', value: '#39FF14', category: 'Verdes' },
            { name: 'Verde Pino', value: '#01796F', category: 'Verdes' },
            { name: 'Verde Musgo', value: '#8A9A5B', category: 'Verdes' },
            { name: 'Verde Aguacate', value: '#568203', category: 'Verdes' },
            
            // Amarillos
            { name: 'Amarillo Dorado', value: '#FFD700', category: 'Amarillos' },
            { name: 'Amarillo Limón', value: '#FFF700', category: 'Amarillos' },
            { name: 'Amarillo Mostaza', value: '#FFDB58', category: 'Amarillos' },
            { name: 'Amarillo Canario', value: '#FFEF00', category: 'Amarillos' },
            { name: 'Amarillo Ámbar', value: '#FFBF00', category: 'Amarillos' },
            { name: 'Amarillo Oro Viejo', value: '#CFB53B', category: 'Amarillos' },
            { name: 'Amarillo Maíz', value: '#FBEC5D', category: 'Amarillos' },
            { name: 'Amarillo Miel', value: '#FFC30F', category: 'Amarillos' },
            
            // Naranjas
            { name: 'Naranja', value: '#FFA500', category: 'Naranjas' },
            { name: 'Naranja Quemado', value: '#FF8C00', category: 'Naranjas' },
            { name: 'Naranja Mandarina', value: '#FF8243', category: 'Naranjas' },
            { name: 'Naranja Calabaza', value: '#FF7518', category: 'Naranjas' },
            { name: 'Naranja Durazno', value: '#FFCBA4', category: 'Naranjas' },
            { name: 'Naranja Zanahoria', value: '#ED9121', category: 'Naranjas' },
            
            // Púrpuras y Violetas
            { name: 'Púrpura', value: '#800080', category: 'Púrpuras' },
            { name: 'Violeta', value: '#8B00FF', category: 'Púrpuras' },
            { name: 'Lavanda', value: '#E6E6FA', category: 'Púrpuras' },
            { name: 'Lila', value: '#C8A2C8', category: 'Púrpuras' },
            { name: 'Índigo', value: '#4B0082', category: 'Púrpuras' },
            { name: 'Magenta Oscuro', value: '#8B008B', category: 'Púrpuras' },
            { name: 'Orquídea', value: '#DA70D6', category: 'Púrpuras' },
            { name: 'Púrpura Real', value: '#7851A9', category: 'Púrpuras' },
            
            // Rosas
            { name: 'Rosa', value: '#FFC0CB', category: 'Rosas' },
            { name: 'Rosa Fucsia', value: '#FF1493', category: 'Rosas' },
            { name: 'Rosa Salmón', value: '#FA8072', category: 'Rosas' },
            { name: 'Rosa Pálido', value: '#F8BBD0', category: 'Rosas' },
            { name: 'Rosa Intenso', value: '#C21807', category: 'Rosas' },
            { name: 'Rosa Chicle', value: '#FF69B4', category: 'Rosas' },
            
            // Marrones y Tierras
            { name: 'Marrón', value: '#8B4513', category: 'Marrones' },
            { name: 'Café', value: '#6F4E37', category: 'Marrones' },
            { name: 'Chocolate', value: '#D2691E', category: 'Marrones' },
            { name: 'Castaño', value: '#954535', category: 'Marrones' },
            { name: 'Cobre', value: '#B87333', category: 'Marrones' },
            { name: 'Bronce', value: '#CD7F32', category: 'Marrones' },
            { name: 'Beige', value: '#F5F5DC', category: 'Marrones' },
            { name: 'Canela', value: '#D2B48C', category: 'Marrones' },
            
            // Grises
            { name: 'Gris', value: '#808080', category: 'Grises' },
            { name: 'Gris Claro', value: '#D3D3D3', category: 'Grises' },
            { name: 'Gris Oscuro', value: '#A9A9A9', category: 'Grises' },
            { name: 'Gris Plata', value: '#C0C0C0', category: 'Grises' },
            { name: 'Gris Antracita', value: '#36454F', category: 'Grises' },
            { name: 'Gris Perla', value: '#E5E4E2', category: 'Grises' },
            
            // Colores Web Seguros
            { name: 'Aqua', value: '#00FFFF', category: 'Web Safe' },
            { name: 'Fuchsia', value: '#FF00FF', category: 'Web Safe' },
            { name: 'Lime', value: '#00FF00', category: 'Web Safe' },
            { name: 'Maroon', value: '#800000', category: 'Web Safe' },
            { name: 'Navy', value: '#000080', category: 'Web Safe' },
            { name: 'Olive', value: '#808000', category: 'Web Safe' },
            { name: 'Silver', value: '#C0C0C0', category: 'Web Safe' },
            { name: 'Teal', value: '#008080', category: 'Web Safe' },
            
            // Colores Modernos
            { name: 'Coral Viviente', value: '#FF6F61', category: 'Modernos' },
            { name: 'Ultra Violeta', value: '#645394', category: 'Modernos' },
            { name: 'Rosa Millennial', value: '#F7CAC9', category: 'Modernos' },
            { name: 'Verde Greenery', value: '#88B04B', category: 'Modernos' },
            { name: 'Azul Serenity', value: '#91A3B0', category: 'Modernos' },
            { name: 'Amarillo Mimosa', value: '#EFC050', category: 'Modernos' },
            
            // Metálicos
            { name: 'Oro', value: '#FFD700', category: 'Metálicos' },
            { name: 'Plata', value: '#C0C0C0', category: 'Metálicos' },
            { name: 'Platino', value: '#E5E4E2', category: 'Metálicos' },
            { name: 'Titanio', value: '#878681', category: 'Metálicos' },
            { name: 'Cromo', value: '#FFA700', category: 'Metálicos' },
            
            // Neones
            { name: 'Verde Neón', value: '#39FF14', category: 'Neones' },
            { name: 'Rosa Neón', value: '#FF6EC7', category: 'Neones' },
            { name: 'Azul Neón', value: '#1B03A3', category: 'Neones' },
            { name: 'Amarillo Neón', value: '#FFFF33', category: 'Neones' },
            { name: 'Naranja Neón', value: '#FF6600', category: 'Neones' }
        ];

    const choicesOptions = {
        choices: commonColors,
        removeItemButton: true,
        searchEnabled: true,
        searchChoices: true,
        searchFloor: 1,
        searchResultLimit: 5,
        placeholder: true,
        placeholderValue: 'Selecciona o escribe un color...',
        addItemText: (value) => {
            return `Presiona Enter para añadir "${value}"`;
        },
        allowHTML: false, // Por seguridad
    };

    const colorExteriorElement = document.getElementById('veh_color_exterior');
    const colorInteriorElement = document.getElementById('veh_color_interior');

    if (colorExteriorElement) {
        new Choices(colorExteriorElement, {
            ...choicesOptions,
            // Considerar si se quiere que el input original sea de tipo 'text' o 'select'
            // Si es 'text', Choices.js lo usará para búsqueda y añadir items.
        });
    }

    if (colorInteriorElement) {
        new Choices(colorInteriorElement, {
            ...choicesOptions,
            // Mismas consideraciones
        });
    }
});
