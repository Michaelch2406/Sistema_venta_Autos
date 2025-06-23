function mostrarImagen(comida) {
  const imagen = document.getElementById('imagenComida');
  let src = '';

  if (comida === 'apanado') {
    src = 'https://lacasadellibrillo.com/wp-content/uploads/2020/09/apanado.jpg';
  } else if (comida === 'arrozrelleno') {
    src = 'https://www.elsabor.com.ec/wp-content/uploads/2022/02/arroz-pollo.jpg'; 
  } else if (comida === 'encebollado') {
    src = 'https://www.cocina-ecuatoriana.com/base/stock/Recipe/encebollado-manaba/encebollado-manaba_web.jpg.webp'; 
  }

  if(src) {
    imagen.innerHTML = `<img src="${src}" alt="${comida}" width="300">`;
  } else {
    imagen.innerHTML = '';
  }
}

function procesarTexto() {
  const texto = document.getElementById('texto').value;
  let resultado = '';
  for (let i = 0; i < texto.length; i++) {
    const char = texto[i];
    if (i % 2 === 0) {
      resultado += '<b>' + char.toUpperCase() + '</b>';
    } else {
      resultado += char.toLowerCase() + ' (' + char.charCodeAt(0) + ')';
    }
  }
  document.getElementById('resultado').innerHTML = resultado;
}

window.onload = function() {
  var boton1 = document.getElementById('boton1');
  var boton2 = document.getElementById('boton2');

  boton1.onmouseover = function() {
    boton2.style.backgroundColor = 'cyan';
  }

  boton1.onmouseout = function() {
    boton2.style.backgroundColor = '';
  }
  
  boton2.onclick = function() {
    boton1.innerText = 'Hola Mundo';
  }
}
