<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>



<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-around">


	<a class="navbar-brand" href="<?php echo site_url() ?>">
		<img src="<?php echo base_url('assets/iconos/ico.png') ?>" width="50" height="50" class="d-inline-block align-center" alt="">
        <img src="<?php echo base_url('assets/iconos/texto.png') ?>"  height="40" class="d-inline-block align-bottom" alt="">

    </a>

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
     <div class="navbar-nav">

         <?php
            $uri = uri_string();
            $items = ['equipos','reportes','usuarios'];
            $active = [];

            foreach ( $items as $i => $v )
            {
                $active[ $v ] = ($uri == $v ? 'active' : '');
            }
         ?>
      
      <!-- Si el usuario inició sesion -->
      <?php if (TRUE OR $this->session->logged === TRUE ): ?>
      
        <a class="nav-item nav-link <?= $active['equipos'] ?>" href="<?php echo site_url('equipos') ?>">Equipos</a>
        <a class="nav-item nav-link <?= $active['reportes'] ?>" href="<?php echo site_url('reportes') ?>">Reportes <span class="badge badge-danger" id="badge">x</span></a>
        <a class="nav-item nav-link <?= $active['usuarios'] ?>" href="<?php echo site_url('usuarios') ?>">Perfil</a>
        <a class="nav-item nav-link" href="<?php echo site_url('usuarios/logout') ?>">Salir</a>
      
      <!-- Si no... -->
      <?php else: ?>
      
        <a class="nav-item nav-link" href="<?php echo site_url('usuario/login') ?>">Login</a>
      
      <?php endif ?>
      
      
    </div>
    

    <?php if ( isset($search) ): ?>
      <form class="form-inline my-2 my-lg-0" method="POST" action="<?php echo current_url() ?>">
      <input class="form-control mr-sm-2" type="text" value="<?php echo set_value('keysearch') ?>" name="keysearch" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>  
    <?php endif ?>
    
  </div>
</nav>


<script>

    $(document).ready(function () {
        $('#badge').hide();
        var update_reportes = function () {

            $.getJSON('<?= site_url("alarma/ajax")?>', function( data ) {
                if( parseInt(data.reportes) != 0 ) {
                    $('#badge').show();
                    $('#badge').html(parseInt(data.reportes));
                }

                if( data.alarma == '1' ) {
                    var text = 'Ultimo incidente<br>' +
                        'Equipo: ' + data.last.equipo + '<br>' +
                        'Sensor: ' + data.last.sensor + '<br>' +
                        'Momento: ' + data.last.momento;
                    $('#modal-body').html( text );

                    $('#modal-alarma').modal('show');
                    /*if( $('#modal-alarma').is(':hidden') ) {
                        $('#modal-alarma').modal('toggle');
                    }*/

                } else {
                    $('#modal-alarma').modal('hide');
                }
            })
        }

        update_reportes();
        setInterval( update_reportes, 500 );
    });



</script>


<!-- Modal -->
<div class="modal fade" id="modal-alarma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-danger">
                <h5 class="modal-title" id="exampleModalLabel">Alarma</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-danger" id="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-danger" href="<?= site_url('alarma/desactivar_alarma')?>">Desactivar Alarma y ver los Reportes </a>


            </div>
        </div>
    </div>
</div>


<div class="container">


