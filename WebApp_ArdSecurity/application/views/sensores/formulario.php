<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<br>
<div class="row justify-content-between">
	<div class="col-auto">
		<h3>Datos del sensor</h3>
	</div>
</div>
<hr>
<div class="row ">
  <div class="col">

  	<form method="POST">
  
    <?= form_hidden('id',set_value('id') )?>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Nombre</label>
      <div class="col-sm-10 col-lg-7">
      <div class="input-group">
        <input class="form-control <?=bs_is_invalid('nombre')?>" type="text" name="nombre" value="<?=set_value('nombre')?>">
        <div class="input-group-addon"><i class="fa fa-tag"></i></div>
      </div>
      
      <small class="form-text text-muted">
          Asigna un nombre a tu sensor para que te sea mas facil de indentificar
        </small>
        <?=form_error('nombre')?>
        
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Ranura</label>
      <div class="col-sm-10 col-lg-7">
      <div class="input-group">
        <input class="form-control <?=bs_is_invalid('ranura')?>" type="text" name="ranura" value="<?=set_value('ranura')?>">
        <div class="input-group-addon"><i class="fa fa-wrench"></i></div>
      </div>
        <small class="form-text text-muted">Ingresa el ranura donde se encuentra conectado el sensor</small>
        <?=form_error('ranura')?>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Tipo</label>
      <div class="col-sm-10 col-lg-7">
      <div class="input-group">
        <select class="form-control <?=bs_is_invalid('tipo')?>" name="tipo"">
          <?php foreach ($tipos as $key => $value): ?>
            <option value="<?= $value['id'] ?>" <?= set_select('tipo',$value['id'])?> >
              <?= $value['nombre'] ?>
            </option>
          <?php endforeach ?>
        </select>
        <div class="input-group-addon"><i class="fa fa-wrench"></i></div>
      </div>
        <small class="form-text text-muted">Ingresa el ranura donde se encuntra instalado el equipo</small>
        <?=form_error('tipo')?>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Notas</label>

      <div class="col-sm-10 col-lg-7">
        <div class="input-group">
          <input class="form-control <?=bs_is_invalid('notas')?>" type="text" name="notas" value="<?=set_value('notas')?>" />
          <div class="input-group-addon"><i class="fa fa-sticky-note"></i></div>
        </div>
        <small class="form-text text-muted">Puedes agregar alguna nota extra</small>
        <?=form_error('notas')?>
      </div>
    </div>

    <div class="form-row">
      <div class="col-sm-2"></div>
      <div class="col-sm-10 col-lg-7">
      <div class="btn-group">

        <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
        <?php if ( !empty($action) && $action == 'create'): ?>
          <button type="reset" class="btn btn-info"><i class="fa fa-eraser"></i> Reset</button>
        <?php else: ?>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#confirmacion">
        	Eliminar <i class="fa fa-trash"></i>
        </button>
          
        <?php endif ?>
        <a class="btn btn-info" href="<?=site_url('equipos')?>"><i class="fa fa-times"></i> Cancelar</a>
        </div>
      </div>
    </div>
    </form>
  </div>
</div>



<!--modal -->

<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Confirme la acción</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Está apunto de eliminar los datos de este equipo y los sensores conectados a el</p>
				<p>¿Desea continuar?</p>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-info" data-dismiss="modal">Close <i class="fa fa-times"></i></button>
			<a class="btn btn-danger" href="<?=site_url('sensores/delete/'.set_value('id'))?>">
				Eliminar <i class="fa fa-trash"></i>
			</a>
			
			</div>
		</div>
	</div>
</div>