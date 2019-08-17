<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$input_username = array(
    'label' => 'Correo electrónico',
    'field' => 'correo',
    'hint' => 'Ingresa un nombre de usuario o email',
    'addon' => fa_ico('user')
);

$input_password = array(
    'label' => 'Contraseña',
    'type' => 'password',
    'field' => 'password',
    'hint' => 'Ingresa tu nueva contraseña',
    'addon' => fa_ico('asterisk')
);

$input_confirm = array(
    'label' => 'Confirmación',
    'type' => 'password',
    'field' => 'confirmacion',
    'hint' => 'Ingresa nuevamente tu contraseña',
    'addon' => fa_ico('asterisk')
);

$layaout = array(
    'label' => 'col-sm-2',
    'input' => 'col-sm-7'
);
?>

<br>


<div class="row justify-content-between">
    <div class="col-auto">
        <h3>Datos del usuario</h3>
    </div>
</div>
<hr>
<div class="row ">
    <div class="col">
        <form method="post">
            <?=  bs_form_group( $input_username, $layaout) ?>
            <?=  bs_form_group( $input_password, $layaout) ?>
            <?=  bs_form_group( $input_confirm, $layaout) ?>
            <div class="form-group row">
                <div class="col-sm-2">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Guardar</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?php


?>