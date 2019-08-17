<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
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

<?php if (isset($estilo) && $estilo == 'card'): ?>


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

<?php else: ?>

<div class="row">
  <div class="col">

  	
  	<table class="table table-hover">
  	<thead class="thead-inverse">
    	<tr class="text-center">
	      <th class="text-center" width="20%">Nombre</th>
    	  <th class="text-center" width="60%">Notas</th>
	      <th class="text-center" width="20%">Acciones</th>
    	</tr>
  	</thead>
  	<tbody>
  		{equipos}
  		<tr class="" onclick="#sa">
  			<td class="text-center">{nombre}</td>
  			<td class="text-center">{notas}</td>
  			<td class="text-center" height="20%">
  				<div class="btn-group">
  					<a class="btn btn-info" href="#"><i class="fa fa-eye"></i> Monitorear</a>
  					<a class="btn btn-info" href="#"><i class="fa fa-edit"></i> Editar</a>
  					<a class="btn btn-info" href="#"><i class="fa fa-trash"></i> Eliminar</a>
  				</div>
  			</td>
  		</tr>
  		{/equipos}
  	</tbody>
  </table>
</div>
</div>

<?php endif ?>