/*
var map;
var markers = [];

*/

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
  var mapScreenShotDiv = document.createElement('div');
  var mapScreenShot = new mapScreenShot(mapScreenShotDiv, map);

  centerControlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);
  getMapData(param,value);
}

function mapScreenShot(controlDiv, map) {

        // Set CSS for the control border.
        var controlUI = document.createElement('div');
        controlUI.style.backgroundColor = '#fff';
        controlUI.style.border = '2px solid #fff';
        controlUI.style.borderRadius = '3px';
        controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
        controlUI.style.cursor = 'pointer';
        controlUI.style.marginBottom = '22px';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Click to recenter the map';
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior.
        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
        controlText.style.fontSize = '16px';
        controlText.style.lineHeight = '38px';
        controlText.style.paddingLeft = '5px';
        controlText.style.paddingRight = '5px';
        controlText.innerHTML = 'Center Map';
        controlUI.appendChild(controlText);

        // Setup the click event listeners: simply set the map to Chicago.
        controlUI.addEventListener('click', function() {
          //map.setCenter(chicago);
          console.log('screenshot!');
        });

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

function setAudMarkers(map) {

  console.log('currMapJson:');
  console.log(currMapJson);

  var bounds = new google.maps.LatLngBounds();

  var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
  //var infowindow = new google.maps.InfoWindow();

  var features = currMapJson;


    features.forEach(function(feature) {

      if (joUbiAudiencia.includes(feature.id)) {

        var point = new google.maps.LatLng(feature.latitude, feature.longitude);

        var marker = new google.maps.Marker({
          position: point,
          // cod: feature.idref,
          map: map
        });

        // marker.addListener('click', function() {
        //   //infowindow.setContent(getContent(this.id));
        //   getContent(this.cod, this.position, infowindow);
        //   infowindow.open(map, marker);
        // });

        bounds.extend(point);
        // markers.push(marker);
      }

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

  toggleBotBar();
  $(this).toggleClass('active');

  //$('.loaderFrame').show();

  /*
  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $(this).addClass('active');
  */

  event.preventDefault();

  /*
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
  */

});


$('#btnAudiencia').on("click", function(event) {

  event.preventDefault();

  var delta = $('#audiencias-wrapper').position().top - $('#outer').position().top;

  if ($("#wrapper").hasClass("hideaudiencia")) {
    $("#wrapper").toggleClass("hideaudiencia");
    $("#togglevista a, #btnShareMap a, #btnFavoritosGrid a").toggleClass("disabled");
    $("#togglevista, #btnShareMap, #btnFavoritosGrid").tooltip('disable');
    console.log('ir a audiencia');
    $('#audiencias-wrapper').height($('#audiencias-wrapper').height()+delta);
    //top: 108px
    //height: 100%
    if (!$("#wrapper").hasClass("hideside")) toggleLeftMenu();

    $.ajax({
      url: "mappingAudienciaAjax.php"
    })
      .done(function( data ) {
        $('#audiencias-wrapper').html(data);
        $_initAudienciaPop();
      });


    return;
  } else {
    $("#togglevista a, #btnShareMap a, #btnFavoritosGrid a").toggleClass("disabled");
    $("#togglevista, #btnShareMap, #btnFavoritosGrid").tooltip('enable');
    $("#wrapper").toggleClass("hideaudiencia");
    $('#audiencias-wrapper').height(0);
  }

});


$('#togglevista').on("click", function(event) {
  event.preventDefault();

  if (!$("#wrapper").hasClass("hideaudiencia")) return;

  /*
  $("#wrapper").toggleClass("hidebottom");
  if ($("#wrapper").hasClass("hidebottom")) resetBotBar();
  */



  var delta = $('#botbar-wrapper').position().top - $('#outer').position().top;

  /*
  if ($('#botbar-wrapper').height() > 261 && delta > 0) { // medio, ir a graph
    $('#botbar-wrapper').height($('#botbar-wrapper').height()+delta);
    autoAdjustBotBar();
    return;
  }
  */

  console.log('delta:'+delta);

  if ($("#wrapper").hasClass("hidebottom") && delta > 0) { // mapa, ir a medio
    console.log('full, ir a medio');
    $("#wrapper").toggleClass("hidebottom");
    autoAdjustBotBar();
    return;
  }

  if (!$("#wrapper").hasClass("hidebottom") && delta == 0) { // graph, ir a mapa
    console.log('graph, ir a mapa');
    $("#wrapper").toggleClass("hidebottom");
    if ($("#wrapper").hasClass("hidebottom")) resetBotBar();
    return;
  }

  if (!$("#wrapper").hasClass("hidebottom") && delta > 0) { // medio, ir a full graph
    console.log('medio, ir a full graph');
    $('#botbar-wrapper').height($('#botbar-wrapper').height()+delta);
    autoAdjustBotBar();
    return;
  }




  /*
  if (delta > 5) {
    //map off
    $('#botbar-wrapper').height($('#botbar-wrapper').height()+delta);
  } else {
    //map on
    //$('#botbar-wrapper').height(260);
    resetBotBar();
  }
  */


});


$('#vistamap').on("click", function(event) {

  event.preventDefault();
  /*
  $(".active").each(function() {
      $(this).removeClass('active');
  });
  $(this).addClass('active');
  */
  //initMap();
  //$('#botbar-wrapper').height($('#outer').position().top);
  var delta = $('#botbar-wrapper').position().top - $('#outer').position().top;

  if (delta > 5) {
    //map off
    $('#botbar-wrapper').height($('#botbar-wrapper').height()+delta);
  } else {
    //map on
    $('#botbar-wrapper').height(260);
  }


  //$('#botbar-wrapper').css('box-shadow', 'none');

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

function showMinimapAudiencias() {

  var minimap = new google.maps.Map(document.getElementById('minimapaudiencias'), {
    center: {lat: -36, lng: -58},
    zoom: 10,
    disableDefaultUI: true,
    zoomControl: false,
    scaleControl: true,
    styles: styles['hide'],
    mapTypeControlOptions: {
     mapTypeIds: ['roadmap']
    }
  });

  // var marker = new google.maps.Marker({
  //   position: position,
  //   map: minimap
  // });

  setAudMarkers(minimap);

}

function resetBotBar(){
    console.log('reset');
    var defaultHeight = 260;
    $("#botbar-wrapper").height(defaultHeight);
    $("#wrapper").css("padding-bottom","");
    //$('#botbar-wrapper').css('box-shadow', '-1px 0px 5px 1px #6c757d');
  }


function activateTransitions(){

    var newCSS = {'transition':'all 0.4s ease 0s'};
    $('#wrapper,#botbar-wrapper, #botbar-content').css(newCSS);

}

function deActivateTransitions(){

    var newCSS = {'transition':'none'};
    $('#wrapper,#botbar-wrapper, #botbar-content').css(newCSS);
}


function resetCampaniasModal(){
  $('#campaniasModal').modal('hide');
  $(".itemguardado").each(function() {
      $(this).css('background-color', '').removeClass('campaniaselected');
  });
  $('#cargarcampania').prop('disabled', true);
}



function activateQuitarFiltro() {
  $('.quitarfiltro').click(function(e) {
    $('#'+$(this).data('target')).prop('checked', false).trigger('change');
  });
}

function toggleBotBar(){
  $("#wrapper").toggleClass("hidebottom");
  if ($("#wrapper").hasClass("hidebottom")) resetBotBar();
  autoAdjustBotBar();
}

function widenBotBar(){
  $('#botbar-content').css('margin-left', '10px');
  $('#griplines').css('margin-left','0px');
}

function tightenBotBar(){
  $('#botbar-content').css('margin-left', '350px');
  $('#griplines').css('margin-left','175px');
}

function autoAdjustBotBar(){
  //if ($("#wrapper").hasClass("hideside") && !$("#wrapper").hasClass("hidebottom")) widenBotBar();
  //if (!$("#wrapper").hasClass("hideside") && !$("#wrapper").hasClass("hidebottom")) tightenBotBar();
  if ($("#wrapper").hasClass("hideside")) widenBotBar();
  if (!$("#wrapper").hasClass("hideside")) tightenBotBar();
}

function mostrarBotonesyGrilla(){
  $('#vistagraph').show();
  $('#vistamap').show();
  $('.toolsright').show();
}

function ocultarBotonesyGrilla(){
  $('#vistagraph').hide();
  $('#vistamap').hide();
  $('.toolsright').hide();
}

function init(){

  $('#vistamap, #vistagraph').hide();
  $('.toolsright').hide();

  $('.itemguardado').click(function(e){
    $('#cargarcampania').prop('disabled', false);
    $(".itemguardado").each(function() {
      $(this).css('background-color', '');
      });
    $(this).closest('tr').css('background-color', 'lightblue').addClass('campaniaselected');
    });

  // $('.custom-control-input').change(function(e) {
  //   //var cat = $(this).closest('.dropdown-toggle').text();
  //   var cat = $(this).closest('.filtro').data('filtro');
  //   var valor = $(this).next('label').text();
  //   var filtro = cat+': '+valor;
  //   if ($(this).is(':checked')) {
  //     if ($('#filtros').data('filtered') == 0) {
  //       $('#filtros')
  //         .html('<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>')
  //         .data('filtered',1);
  //     } else {
  //       $('#filtros')
  //       .html($('#filtros').html()+'<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>');
  //     }
  //     $('#ejecutar').prop('disabled', false);
  //
  //   } else {
  //     var filtros = $('#filtros').html();
  //     var pill = '<span class="badge badge-pill badge-secondary">'+filtro+' <i class="fas fa-times quitarfiltro" data-target="'+this.id+'"></i></span>';
  //     $('#filtros').html(filtros.replace(pill,''));
  //     if ($('#filtros').html() == '') {
  //       $('#filtros').html('<span class="darktext">Sin filtros...<span>').data('filtered',0);
  //       $('#ejecutar').prop('disabled', true);
  //     }
  //   }
  //   activateQuitarFiltro();
  // });

  $(document).on("click", '#cargarcampania', function(event) {

    /*
    $(".active").each(function() {
      $(this).removeClass('active');
    });
    $('#vistamap').addClass('active');
    */
      //initMap(4,'#carat');

      var findings = $('.campaniaselected').find('td');
          //.text()
          //console.log($(findings[0]).data('name'));
      $('#analisisNombre').val($(findings[0]).data('name'));
      resetCampaniasModal();
      $('#vistagraph').show();
      $('#vistamap').show();
      $('.toolsright').show();
      toggleBotBar();

  });

  $('#ejecutar').click(function(event) {

          /*
          $(".active").each(function() {
              $(this).removeClass('active');
          });
          $('#vistamap').addClass('active');
          */
          initMap(4,'#carat');
          $('#vistagraph').show();
          $('#vistamap').show();
          $('.toolsright').show();
          toggleBotBar();

  });

  $("#griplines").mousedown(function(e) {

    e.preventDefault();
    deActivateTransitions();
    $("#griplines").data('iniy',e.pageY);

    $(document).mousemove(function( event ) {
      var delta = event.pageY - $("#griplines").data('iniy');
      var doc = $(document).height();
      //console.log(doc);
      var height = $("#botbar-wrapper").height();
      height -= delta;
      //console.log(delta);
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

  $(".menu-toggle").click(function(e) {
    e.preventDefault();
    toggleLeftMenu();
  });

  $(".showresultados").click(function(e){
    console.log('click!');
    $('#audienciaTab li:nth-child(3) a').removeClass('disabled');
    $('#audienciaTab li:nth-child(3) a').tab('show');
    createCharts();
  });

}

function toggleLeftMenu(){
  $("#wrapper").toggleClass("hideside");
  $("#arrow").toggleClass("invert");
  autoAdjustBotBar();
}

function createCharts(){
  newDonut('#myChart1');
  newBar('#myChart2');
}

function newBar(elem){
  //var ctx = document.getElementById('myChart');
  //var ctx = document.getElementById('myChart').getContext('2d');
  var ctx = $(elem);
  //var ctx = 'myChart';

  var data = {
    datasets: [{
        label:['Impactos Totales'],
        data: [11106889],
				backgroundColor: ['#36a2eb',]
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
        'Impactos Totales',
    ]
  };

  var options = {
    scales: {
        yAxes: [
            {
                ticks: {
                    min: 0
                    ,max: 15000000
                    ,callback: function(val) {
                        if(val == 0 || val == 15000000) {
                            return null;
                        }
                        return Number.isInteger(val) ? val : null;
                    }
                }
            }
        ]
    }
};

  var myDoughnutChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options
  });
}

function newDonut(elem){
  //var ctx = document.getElementById('myChart');
  //var ctx = document.getElementById('myChart').getContext('2d');
  var ctx = $(elem);
  //var ctx = 'myChart';

  var data = {
    datasets: [{
        data: [2564094, 8320100-2564094 ],
				backgroundColor: ['#ff6384','#36a2eb',]
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
        'Cobertura Neta',
        'Resto'
    ]
  };

  var options = {};

  var myDoughnutChart = new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: options
  });
}

init();
mostrarBotonesyGrilla();
