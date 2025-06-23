function logeo(){
  var usu = $('#usu').val(),
      cla = $('#cla').val();

  $.ajax({
    url: "../AJAX/inicio1.php?usu=" + encodeURIComponent(usu) + "&cla=" + encodeURIComponent(cla),
    type: "GET",
    success: function(res) {
      if (res === "1") {
        window.location.href = "escritorio.php";
      } else {
        alert("Ingreso incorrecto");
      }
    }
  });
}
