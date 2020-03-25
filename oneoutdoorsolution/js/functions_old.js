var map;
var markers = [];

var styles = {
        default: null,
        hide: [
          {
            featureType: 'poi',
            stylers: [{visibility: 'off'}]
          },
          {
            featureType: 'transit',
            elementType: 'labels.icon',
            stylers: [{visibility: 'off'}]
          }
        ]
      };

function initMap(zoom = 4,param='', value='') {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: -36, lng: -58},
    zoom: zoom,
    disableDefaultUI: true,
    zoomControl: true,
    scaleControl: true,
    styles: styles['hide'],
    gestureHandling: 'greedy',
    mapTypeControlOptions: {
     mapTypeIds: ['roadmap']
    }
  });

  getMapData(param,value);
}

function getMapData(param = '',value = ''){

  $('.loaderFrame').show();

  $.ajax({cache: false,
          url: 'getmapdata.php',
          type: 'POST',
          data: {queryparam:param, value: value},
          success: function(html) {
            //JSON.stringify(html);
            var data = JSON.parse(html);
            //console.log(data);
            populateMap(data);
          },
      complete: function(){
        $('.loaderFrame').hide();
      }
      });

}



function populateMap(data) {

        reloadMarkers(data);

}

function reloadMarkers(data) {

    // Loop through markers and set map to null for each
    for (var i=0; i<markers.length; i++) {

        markers[i].setMap(null);
    }

    // Reset the markers array
    markers = [];

    // Call set markers to re-add markers
    if (data.hasOwnProperty('features')) setMarkers(data);
}


function setMarkers(data) {

  var bounds = new google.maps.LatLngBounds();

  var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
  var infowindow = new google.maps.InfoWindow();

  var features = data.features;


    features.forEach(function(feature) {

      var point = new google.maps.LatLng(feature.lat, feature.long);

      var marker = new google.maps.Marker({
        position: point,
        cod: feature.idref,
        map: map
      });

      marker.addListener('click', function() {
        //infowindow.setContent(getContent(this.id));
        getContent(this.cod, this.position, infowindow);
        infowindow.open(map, marker);
      });

      bounds.extend(point);

      markers.push(marker);

    });

    map.fitBounds(bounds);

  /*
  var legend = document.getElementById('legend');
  while (legend.firstChild) {
    legend.removeChild(legend.firstChild);
    }
  legend.innerHTML = '<h6>Leyenda</h6>';
  for (var key in icons) {
    var type = icons[key];
    var name = type.name;
    var icon = type.icon;
    var div = document.createElement('div');
    //div.innerHTML = '<a href="#" class="legendlink" data-tiposoporte="'+key+'"><img src="' + icon + '"> ' + name+'</a>';
    div.innerHTML = '<a href="#" class="legendlink" data-tiposoporte="'+key+'"><img src="' + icon + '"> ' + name+'</a>';
    //div.style.height = '25px';
    legend.appendChild(div);
  }

  map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
  */
}

function getContent(cod, position, infowindow) {


  $('.loaderFrame').show();


  var contentString;
  console.log('cod='+cod);

  $.ajax({cache: false,
          url: 'getinfowin.php',
          type: 'POST',
          data: {cod : cod},
          success: function(html) {
            infowindow.setContent(html);
            showMinimap(position);
          },
          complete: function(){
            $('.loaderFrame').hide();
          }
      });
}

document.addEventListener( 'click', clickHandler );

function clickHandler(event){
  var element = event.target;
  //if(element.tagName == 'A' && element.classList.contains('someBtn')){
  if(element.classList.contains('starr')){
    var cod = $(element).data('codigo');
    console.log(cod);
    toggleStar(cod, element);
    }

  if (element.classList.contains('legendlink')){
    var tiposoporte = $(element).data('tiposoporte');
    //console.log(tiposoporte);
    getData('soportes.tiposoporte',tiposoporte);
    }
}



function toggleStar(cod, element) {

  $.ajax({cache: false,
          url: 'togglestar.php',
          type: 'POST',
          data: {cod : cod},
          success: function(html) {
            console.log(html);
            $(element).removeClass('starron starroff').addClass(html);
            udpatecookies();
          }
      });
}

function udpatecookies(){
    var createCookie = function(name, value, days) {
      var expires;
      if (days) {
          var date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toGMTString();
      }
      else {
          expires = "";
      }
      document.cookie = name + "=" + value + expires + "; path=/";
  }

}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

/*

$('#pepe').on('click', function (e) {

    //e.preventDefault();

    var $this = $(this), data = $this.data();
    var targeted_popup_class = jQuery(this).attr('data-popup-open');
    $('[data-popup='' + targeted_popup_class + '']').fadeIn(350);
    //Aca podría reubicar el popup
    var idcorreccion = $this.data('id');
    var order = $this.text();
    getDetalleCorreccion(idcorreccion,order);

    console.log(cod);
});

$('.starr').click (function() {
  console.log('click2!');
});

*/



$('#guardados').on("click", function(event) {

  $('.loaderFrame').show();

  event.preventDefault();
  $(".navbar-collapse").collapse('hide');

  $(".active").each(function() {
      $(this).removeClass('active');
  });

  $(this).addClass('active');


   formurl = 'guardados.php';

    $.ajax({cache: false,
    url: formurl,
    type: 'GET',
    success: function(html)
            {
                $("#map").html(html);
            },
    complete: function(){
                $('.loaderFrame').hide();
            }
    });

});

function activateFavRows(){

  var el = document.getElementsByClassName('quitarfav');
  //alert(el.length);
  for (var i=0;i<el.length; i++) {
      //console.log('test');
      el[i].onclick = quitarFav;
      }

      $('#enviarFormPresupuesto').on("click", function(event) {

        $('#enviarFormPresupuesto').prop('disabled', true);
        $('#enviarFormPresupuesto').html('Enviando...');
        event.preventDefault();

         formurl = 'enviar.php';

         //var strfavoritos = JSON.stringify(favoritos);
         var result = '';
         var parsed_match = JSON.parse(favoritos);

         for(var k in parsed_match) {
            //result += `${parsed_match[k].apuser},${parsed_match[k].latitude},${parsed_match[k].longitude},${parsed_match[k].horizontal_accuracy},${parsed_match[k].utc_timestamp},${parsed_match[k].course},${parsed_match[k].speed},${altitude},${parsed_match[k].ad_id},${parsed_match[k].ad_opt_out},${parsed_match[k].id_type},${parsed_match[k].location_method}`+'\n';
            result += `${parsed_match[k].codigo},${parsed_match[k].tiposoportetxt},${parsed_match[k].direccion},${parsed_match[k].contratacion}`+'<br>';
            }

        /*
         var json = jsonfavoritos.items;
         var fields = Object.keys(json[0]);
         var replacer = function(key, value) { return value === null ? '' : value };
         var csv = json.map(function(row){
          return fields.map(function(fieldName){
            return JSON.stringify(row[fieldName], replacer);
            }).join(',')
          });
         csv.unshift(fields.join(',')); // add header column

         console.log(csv.join('\r\n'));
         */

         var data = $("#formPresupuesto").serializeArray();
         data.push({name: 'favoritos', value: result});


         $('.loaderFrame').show();


          $.ajax({cache: false,
          url: formurl,
          type: 'POST',
          data: data,
          success: function(html)
                  {
                      $('#enviarFormPresupuesto').html('Enviado!');
                  },
          error: function(xhr, status, error){
                      $('#enviarFormPresupuesto').html('Ups. Hubo un error...');

                  },
          complete: function(){
                $('.loaderFrame').hide();
              }
          });

      });


}

function quitarFav() {
  var codigo = $(this).parent().parent().attr('id');

  console.log(codigo);
/*
  var fullUrl = 'favoritos.php';
  //if ($(this).data('contenido') != 1) fullUrl = 'includes/_addcontenido.php';

  $.ajax({cache: false,
      url: fullUrl,
      type: 'POST',
      data: {codigo:codigo},
      success: function(html)
              {
                  $("#mainw").html(html);
              }
  });
*/

var message = '¿Quitar '+codigo+' de los Favoritos?';
var titulo = 'Eliminar';

$('<div id="dialog-confirm" title="'+titulo+'"></div>')
.appendTo('body')
.html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>'+message+'</p>')
.dialog({
  modal: true,
  autoOpen: true,
  height: 'auto',
  width: 'auto',
  resizable: false,
  buttons: {
    'Eliminar': function () {

      var formurl = 'togglestar.php';
      $.ajax({cache: false,
      url: formurl,
      type: 'POST',
      data: {cod:codigo},
      success: function(html)
              {
                $('#favoritos').click();
              }
      });

      $(this).dialog("close");
      },
    'Cancelar': function () {
      $(this).dialog("close");
      }
    }
  });



}



$('#buscarbot').on("click", function(event) {

  $('.loaderFrame').show();

  event.preventDefault();

   formurl = 'resultados.php';

    $.ajax({cache: false,
    url: formurl,
    type: 'POST',
    data: {phrase: $('#buscartxt').val()},
    success: function(html)
            {
                $("#map").html(html);
            },
    complete: function(){
                $('.loaderFrame').hide();
            }
    });

});

$('#vistagraph').on("click", function(event) {

  $('.loaderFrame').show();

  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $(this).addClass('active');

  event.preventDefault();

   formurl = 'datos.php';

    $.ajax({
    cache: false,
    url: formurl,
    type: 'POST',
    data: {data: 'dummy'},
    success: function(html)
            {
                $("#map").html(html);
            },
    complete: function(){
                $('.loaderFrame').hide();
            }
    });

});

$('#vistamap').on("click", function(event) {
  event.preventDefault();
  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $(this).addClass('active');
  initMap();
});



function showMinimap(position) {

  var minimap = new google.maps.Map(document.getElementById('minimap'), {
    center: position,
    zoom: 17,
    disableDefaultUI: true,
    zoomControl: true,
    scaleControl: true,
    styles: styles['hide'],
    mapTypeControlOptions: {
     mapTypeIds: ['roadmap']
    }
  });

  var marker = new google.maps.Marker({
    position: position,
    map: minimap
  });

}


/*
$( document ).bind('ajaxStart', function(){
    $('.loaderFrame').show();
}).bind('ajaxStop', function(){
    $('.loaderFrame').hide();
});
*/

/*Menu-toggle*/
  $(".menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("hideside");
      $("#arrow").toggleClass("invert");

      $("#wrapper").toggleClass("hidebottom");
      if ($("#wrapper").hasClass("hidebottom")) resetBotBar();

  });

  function resetBotBar(){
    console.log('reset');
    var defaultHeight = 260;
    $("#botbar-wrapper").height(defaultHeight);
    $("#wrapper").css("padding-bottom","");
  }

  /*
  var left =

  $("#griplines").css("left",left);
  */

  $("#griplines").mousedown(function(e) {
    e.preventDefault();
    deActivateTransitions();
    $("#griplines").data('iniy',e.pageY);

    $(document).mousemove(function( event ) {

      /*
      var msg = "Handler for .mousemove() called at ";
      msg += event.pageX + ", " + event.pageY;
      console.log(msg);
      var doc = $(window).width();
      console.log(doc);
      */
      var delta = event.pageY - $("#griplines").data('iniy');

      var doc = $(document).height();
      var height = $("#botbar-wrapper").height();
      height -= delta;
      console.log(delta);
      if (height < doc * 0.8 && height > doc * 0.2 ) {
        $("#griplines").data('iniy',event.pageY);
        $("#botbar-wrapper").height(height);
        $("#wrapper").css("padding-bottom",height);
      }

    });
    $(document).mouseup(function() {
      $(document).off('mousemove');
      activateTransitions();
    });
  });


function activateTransitions(){

    var newCSS = {'transition':'all 0.4s ease 0s'};
    $('#wrapper,#botbar-wrapper').css(newCSS);

}

function deActivateTransitions(){

    var newCSS = {'transition':'none'};
    $('#wrapper,#botbar-wrapper').css(newCSS);
}

  /*
  $("#dragtext").draggable({
    axis: "y",
    helper: "clone"
  });

  $("#dragtext").on("drag", function( event, ui ){
    //console.log();
    var doc = $(document).height();
    var height = $("#botbar-wrapper").height();
    height -= ui.position.top;
    if (height < doc * 0.8 && height > doc * 0.2 ) $("#botbar-wrapper").height(height);

  });
  */

  /*
  $("#botbar-wrapper").resizable({
    handles: "n",
    minHeight: 100
  });

  $("#botbar-wrapper").on("resizestop", function( event, ui ){

  });
  */



$(document).on("click", '#cargarcampania', function(event) {

  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $('#vistamap').addClass('active');
  initMap(4,'#carat');
  var findings = $('.campaniaselected').find('td');
  //.text()
  //console.log($(findings[0]).data('name'));
  $('#analisisNombre').val($(findings[0]).data('name'));
  resetCampaniasModal();

  $('#vistagraph').show();

  });

$('#ejecutar').click(function(event) {

  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $('#vistamap').addClass('active');
  initMap(4,'#carat');

  $('#vistagraph').show();

  });

function resetCampaniasModal(){
  $('#campaniasModal').modal('hide');
  $(".itemguardado").each(function() {
      $(this).css('background-color', '').removeClass('campaniaselected');
  });
  $('#cargarcampania').prop('disabled', true);
}

$('.itemguardado').click(function(e){

  $('#cargarcampania').prop('disabled', false);

  $(".itemguardado").each(function() {
      $(this).css('background-color', '');
  });
  $(this).closest('tr').css('background-color', 'lightblue').addClass('campaniaselected');;

});

$('.custom-control-input').change(function(e) {
    //var cat = $(this).closest('.dropdown-toggle').text();

    var cat = $(this).closest('.filtro').data('filtro');
    var valor = $(this).next('label').text();
    var filtro = cat+': '+valor;

    if ($(this).is(':checked')) {

      if ($('#filtros').data('filtered') == 0) {
        $('#filtros')
        .html('<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>')
        .data('filtered',1);
      } else {
        $('#filtros')
        .html($('#filtros').html()+'<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>');
      }
      $('#ejecutar').prop('disabled', false);

    } else {
      var filtros = $('#filtros').html();
      var pill = '<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>';
      $('#filtros').html(filtros.replace(pill,''));
      if ($('#filtros').html() == '') {
        $('#filtros').html('<span class="darktext">Sin filtros...<span>').data('filtered',0);
        $('#ejecutar').prop('disabled', true);
      }
    }

    activateQuitarFiltro();


});

function activateQuitarFiltro() {
$('.quitarfiltro').click(function(e) {
  $('#'+$(this).data('target')).prop('checked', false).trigger('change');
});
}
