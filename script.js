
function mostrarAviso(texto) {
  var aviso = document.createElement('div');
  aviso.textContent = texto;
  aviso.setAttribute('role', 'status');
  aviso.style.cssText = 'position:fixed;left:50%;bottom:90px;transform:translateX(-50%);' +
    'background:#171b2a;color:#fff;border:1px solid #65339b;border-radius:10px;' +
    'padding:12px 18px;font-size:14px;z-index:2000;box-shadow:0 8px 24px rgba(0,0,0,.4);' +
    'max-width:90%;text-align:center;';
  document.body.appendChild(aviso);
  setTimeout(function () { aviso.remove(); }, 2500);
}

function agregarCarrito(nombre) {
  mostrarAviso('🛒 ' + nombre + ' se agregó al carrito');
}

function mostrarMensaje() {
  var productos = document.getElementById('productos');
  if (productos) { productos.scrollIntoView({ behavior: 'smooth' }); }
  mostrarAviso('Elige un producto y pulsa 🛒 para agregarlo');
}
