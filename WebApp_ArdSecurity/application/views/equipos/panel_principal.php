<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php
  function set_show_card( $card )
  {

  }
  function set_class_card($sensor)
  {
    if( $sensor['habilitado'] == FALSE )
    {
      return 'text-white bg-secondary';
    } else if( $sensor['alarma'] == FALSE )
    {
      return 'text-white bg-info';
    } else {
      return 'text-white bg-danger';
    }
  }



    function set_card_buttons($sensor)
    {
        $buttons = '';
        $id = $sensor['id'];
        $class = ['class'=> 'btn '. set_class_card($sensor)];
        $btn_sensor_update = anchor('sensores/update/'.$id, fa_ico('edit'), $class );
        $btn_alarma_desactivar =  anchor('alarma/desactivar_alarma_sensor/'.$id, fa_ico('bell-slash'), $class);
        $btn_sensor_off = anchor('alarma/sensor_off/'.$id, fa_ico('toggle-on'), $class);
        $btn_sensor_on = anchor('alarma/sensor_on/'.$id, fa_ico('toggle-off'), $class);

        $buttons .= $sensor['alarma'] ? $btn_alarma_desactivar : $btn_sensor_update;

        $buttons .= $sensor['habilitado'] ? $btn_sensor_off : $btn_sensor_on;
        return $buttons;
    }


?>

<!-- Titulo del panel -->
<br>
<div class="row justify-content-between">
	<div class="col-auto">
		<h3>Lista de equipos </h3>
		
	</div>
	<div class="col-auto">
		<a href="<?=site_url('equipos/create')?>"><i class="fa fa-plus"></i> Añadir equipo</a>
	</div>
</div>
<hr>

<!-- Si no hay equipos registrados... -->
<?php if ( empty($equipos) OR count($equipos) < 1 ): ?>


<div class="card text-center">
  <div class="card-body" id="asd">
    <h4 class="card-title">Aun no has registrado tus equipos</h4>
    <p class="card-text">Despues de la instalacion, registralos y podras monitorearlos</p>
    <a class="btn btn-primary" href="<?=site_url('equipos/create')?>">Añadir equipo <i class="fa fa-plus"></i></a>
  </div>
</div>


<!-- Despliega los equipos registrados -->
<?php else: ?>

<div >

<!-- Recorre los equipos -->
<?php foreach ($equipos as $index_e => $equipo): ?>

  <!--Genera un card para cada equipo -->
  <div class="card m-3">
    <div class="card-header">
      <div class="row justify-content-between align-items-center">
        <div class="col-auto">

      <h4 class="mb-0">
        <?= $equipo['nombre'] ?>
      </h4>

      </div>
      <div class="col-auto">
      <div class="btn-group">
          <?php if ( $equipo['alarma']): ?>
            <a class="btn btn-success btn-sm" href="<?= site_url('alarma/desactivar_alarma_equipo/'.$equipo['id'])?>">
                <?= fa_ico('bell-slash') ?> Desactivar alarma
            </a>
          <?php endif ?>
        <a class="btn btn-info btn-sm" href="<?=site_url('sensores/create/'.$equipo['id'])?>">
          <i class="fa fa-plus"></i> Agregar sensor 
        </a>
        <a class="btn btn-info btn-sm" href="<?=site_url('equipos/update/'.$equipo['id'])?>">
          <i class="fa fa-edit"></i> Editar
        </a>

        </div>
    </div>
    </div>
</div>
    <div id="equipo<?= $equipo['id'] ?>"  >
      <div class="card-body">

      <!-- Verifica si hay sensores asosiados al equipo -->
      <?php if (count($equipo['sensores']) < 1 ): ?>

          <div class="row">
          <div class="col text-center"><h6>No tienes ningún sensor agregado</h6></div>
          </div>
          <div class="row justify-content-center mt-2">
            <div class="col-auto">
              <a class="btn btn-primary" href="<?=site_url('sensores/create/'.$equipo['id'])?>">
               <i class="fa fa-plus"></i> Agregar
              </a>
            </div>
          </div>


      <?php else: ?>

    
      <div class="row justify-content-around">
      
      <?php foreach ($equipo['sensores'] as $index_s => $sensor): ?>
      
      <div class="col-lg-4">
        <div class="card <?= set_class_card( $sensor, 'car' ) ?>">

        <div class="card-header pt-2 pb-2"><div class="row justify-content-between align-items-center">
          <div class="col-auto"><span><?= $sensor['nombre'] ?></span></div>
          <div class="col-auto">
            <div class="btn-group">
                <?= set_card_buttons( $sensor ) ?>
            </div>
          </div>
        </div>
        
        </div>
          <div class="card-body text-center">
            <h5 class="card-title"><?= $sensor['nombre'] ?></h5>
            <p class="card-text"><?= $sensor['notas'] ?></p>


              
            
          </div>
        </div>
      </div>

      <?php endforeach ?>
      </div>
      <?php endif ?>    
        
      </div>
      <div class="card-footer container">
        <div class="row justify-content-between">
        <div class="col-auto"><small class="text-muted"><?= $equipo['notas']?></small></div>
        <div class="col-auto"><small class="text-muted"><?= $equipo['domicilio']?></small></div>
        </div>
      </div>
    </div>

  </div>

<?php endforeach ?>

</div>


<?php endif ?>










<!--
<br><br><br>


<div class="row justify-content-around">
<div class="card-columns">
	{equipos}
	<div class="card bg-light mb-3 text-center" style="width: 20rem;">
	<div class="card-header">
	<div class="row justify-content-between">
	<div class="col-auto"><h6>{nombre}</h6></div>
	<div class="col-auto">
		<a class="card-link" href="<?=site_url('equipos/update/{id}')?>"><i class="fa fa-ellipsis-v"></i> </a>
	</div>
		</div>
		
	</div>
  <div class="card-body">
    
    <p class="card-text">{notas}</p>
    <a class="btn btn-info" href="<?=site_url('equipos/read/{id}')?>"><i class="fa fa-eye"></i> Monitorear</a>
    
  </div>
  <div class="card-footer">
      <small class="text-muted">{domicilio}</small>
    </div>
  </div>
  {/equipos}


</div>
</div>

-->

