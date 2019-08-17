<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>




<form class="form-horizontal mt-5" role="form" method="POST" action="<?php echo site_url('usuarios/login') ?>">
            <div class="form-row justify-content-start ">
                <div class="col-md-3"></div>
                <div class="col-auto">
                    <img width="50px" src="<?= base_url('assets/iconos/ico.png')?>">
                </div>
                <div class="col align-self-end">
                    <h2> Inicio de sesión</h2>
                </div>

                </div>

            </div>
            <div class="form-row mt-3">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group has-danger">
                        <label class="sr-only" for="correo">Correo Electrónico</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-at"></i></div>
                            <input type="text" name="correo" value="<?php echo set_value('correo'); ?>" class="form-control" id="correo"
                                   placeholder="correo@example.com" required autofocus>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-control-feedback">
                        <span class="text-danger align-middle">

                            <!--<i class="fa fa-close"></i>--> <?php echo form_error('correo'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="sr-only" for="clave">Contraseña</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-key"></i></div>
                            <input type="password" name="clave" value="<?php echo set_value('clave'); ?>" class="form-control" id="clave"
                                   placeholder="Contraseña" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-control-feedback">
                        <span class="text-danger align-middle">
                            <?php echo form_error('clave'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <!--
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="col-md-6" style="padding-top: .35rem">
                    <div class="form-check mb-2 mr-sm-2 mb-sm-0">
                        <label class="form-check-label">
                            <input class="form-check-input" name="remember"
                                   type="checkbox" >
                            <span style="padding-bottom: .15rem">Remember me</span>
                        </label>
                    </div>
                </div>
            </div>
            -->
            <?php if (isset($login_error)): ?>
            <div class="form-row" style="padding-top: 1rem">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <span class="text-danger align-middle">
                            <?php echo $login_error; ?>
                        </span>
                </div>
            </div>
            <?php endif ?>

            <div class="form-row" style="padding-top: 1rem">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success"><i class="fa fa-sign-in"></i> Login</button>

                    <a class="btn btn-link" href="/password/reset">Forgot Your Password?</a>

                </div>
            </div>
        </form>



