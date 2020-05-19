<!-- inject:js -->
<script src="<?php echo base_url('js/d3.min.js') ?>"></script>
<script src="<?php echo base_url('js/getmdl-select.min.js') ?>"></script>
<script src="<?php echo base_url('js/material.min.js') ?>"></script>
<script src="<?php echo base_url('js/nv.d3.min.js') ?>"></script>
<script src="<?php echo base_url('js/layout/layout.min.js') ?>"></script>
<script src="<?php echo base_url('js/scroll/scroll.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/charts/discreteBarChart.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/charts/linePlusBarChart.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/charts/stackedBarChart.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/employer-form/employer-form.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/line-chart/line-charts-nvd3.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/map/maps.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/pie-chart/pie-charts-nvd3.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/table/table.min.js') ?>"></script>
<script src="<?php echo base_url('js/widgets/todo/todo.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js" integrity="sha256-qSIshlknROr4J8GMHRlW3fGKrPki733tLq+qeMCR05Q=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-145830444-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-145830444-2');
</script> -->


<script type="text/javascript">
  window.onload = function() {
    <?php if ($_SESSION['msg_body'] != "") { ?>
      Swal.fire({
        type: '<?php echo  $_SESSION['msg_type']; ?>',
        title: '<?php echo  $_SESSION['msg_title']; ?>',
        text: '<?php echo  $_SESSION['msg_body']; ?>',
        footer: '<?php echo  $_SESSION['msg_footer']; ?>'
      });

      <?php $_SESSION['msg_title'] = ""; ?>
      <?php $_SESSION['msg_type'] = ""; ?>
      <?php $_SESSION['msg_body'] = ""; ?>
      <?php $_SESSION['msg_footer'] = ""; ?>
    <?php } ?>
  };
</script>

<script type="text/javascript">
  const options = {
    connectTimeout: 1000,
    // Authentication
    clientId: 'client_id_' + Math.floor((Math.random() * 1000000) + 1),
    username: '<?php echo MQTT_USER; ?>',
    password: '<?php echo MQTT_PASSWORD; ?>',
    keepalive: 60,
    clean: true,
  }

  // WebSocket connect url
  const WebSocket_URL = 'wss://ioticos.org:8094/mqtt';
  const client = mqtt.connect(WebSocket_URL, options)

  var device_topic = '<?php echo ROOT_TOPIC . "/" . $_SESSION['selected_topic'] . "/" ?>';
  client.on('connect', () => {
    console.log('Connect success');

    client.subscribe(device_topic + "data", {
      qos: 0
    }, (error) => {
      if (error) {
        console.log("Error at subscribe");
      } else {
        console.log("Subscribe ok");
      }

    })
  })

  client.on('message', (topic, message) => {
    console.log('Msg from topic: ', topic, ' ----> ', message.toString());

    if (topic == device_topic + "data") {
      var splitted = message.toString().split(",");

      var temp = splitted[0];
      var hum = splitted[1];
      var switch1 = splitted[2];
      var switch2 = splitted[3];
      var switch3 = splitted[4];

      $("#display_hum").html(hum);
      $("#display_temp").html(temp);

      if (switch1 == "1") {
        // $("#display_sw1").prop('checked', true);
        $("#indicador_1").removeClass('boton-off').addClass('boton-on');
      } else {
        // $("#display_sw1").prop('checked', "");
        $("#indicador_1").removeClass('boton-on').addClass('boton-off');
      }

      if (switch2 == "1") {
        // $("#display_sw2").prop('checked', true);
        $("#indicador_2").removeClass('boton-off').addClass('boton-on');
        alert("¡¡¡¡ Se ha activado la alarma !!! ");
      } else {
        // $("#display_sw2").prop('checked', "");
        $("#indicador_2").removeClass('boton-on').addClass('boton-off');
      }
      if (switch3 == "1") {
        // $("#display_sw3").prop('checked', true);
        $("#indicador_3").removeClass('boton-off').addClass('boton-on');
      } else {
        // $("#display_sw3").prop('checked', "");
        $("#indicador_3").removeClass('boton-on').addClass('boton-off');
      }

    }


  })

  client.on('reconnect', (error) => {
    console.log('reconnecting:', error);
  })

  client.on('error', (error) => {
    console.log('Connect Error:', error);
  })

  // funcion para almacenar un cambio en nuestra base de datos
  function save_change() {
    const actuadores = document.querySelectorAll(".actua");
    var data_ = [];
    for (i = 0; i < actuadores.length; i++) {
      if (actuadores[i].checked) {
        data_[i] = 1;
      } else {
        data_[i] = 0;
      }
    }
    const data_act = {
      "data_a": data_[0],
      "data_b": data_[1],
      "data_c": data_[2],
      "sn": <?php echo $_SESSION['device_sn'] ?>
    }
    $.ajax({
      data: data_act,
      url: '<?php echo base_url() . "Insertdata/setData" ?>',
      type: 'POST',
      // beforeSend: function(){
      //   $(".enviando").html("Enviando requerimiento, espere por favor...");
      // },
      success: function(response) {
        // $(".enviando").html("response")
      }
    });

  }

  function sw1_change() {
    if ($('#display_sw1').is(":checked")) {
      client.publish(device_topic + 'actions/sw1', "1");
    } else {
      client.publish(device_topic + 'actions/sw1', "0");
    }
    save_change()
  }

  function sw2_change() {
    if ($('#display_sw2').is(":checked")) {
      client.publish(device_topic + 'actions/sw2', "1");
    } else {
      client.publish(device_topic + 'actions/sw2', "0");
    }
    save_change()
  }

  function sw3_change() {
    if ($('#display_sw3').is(":checked")) {
      client.publish(device_topic + 'actions/sw3', "1");
    } else {
      client.publish(device_topic + 'actions/sw3', "0");
    }
    save_change()
  }

  function slider_change() {
    value = $('#display_slider').val();
    client.publish(device_topic + 'actions/slider', value);
  }
</script>
<!-- endinject -->

<style media="screen">
  /* The switch - the box around the slider */
  .switch {
    position: relative;
    display: inline-block;
    width: 42px;
    height: 24px;
  }

  /* Hide default HTML checkbox */
  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  /* The slider */
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(16px);
    -ms-transform: translateX(16px);
    transform: translateX(16px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 18px;
  }

  .slider.round:before {
    border-radius: 50%;
  }

  .boton-off {
    width: 30px;
    height: 30px;
    background-color: rgba(255, 112, 97, 0.25);
    margin: 5px;
    padding: 10px;
    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    font-size: 11px;
    line-height: 32px;
    text-transform: uppercase;
    float: left;
  }

  .boton-on {
    width: 30px;
    height: 30px;
    background-color: #ff5252;
    margin: 5px;
    padding: 10px;
    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    font-size: 11px;
    line-height: 32px;
    text-transform: uppercase;
    float: left;
  }

  .boton-off:hover {
    opacity: 0.50;
    -moz-opacity: .50;
    filter: alpha (opacity=50);
  }

  .boton-off a {
    color: #eee;
    text-decoration: none;
    padding: 5px 5px 5px 0;
  }
</style>