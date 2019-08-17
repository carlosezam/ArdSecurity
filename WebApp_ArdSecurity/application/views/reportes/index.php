<br>
<h3> Reportes</h3>



<div class="row mt-3">
    <div class="col">

        <?php if ( count($reportes) == 0 ): ?>

            <div class="card text-center">
                <div class="card-s" id="asd">
                    <h4 class="card-title">No ha habido ningún incidente</h4>
                    <p class="card-text">Cuando se detecte alguna anomalia quedará registrada en esta sección</p>
                    <a class="btn btn-primary" href="<?=site_url('equipos/')?>">Ir al panel <i class="fa fa-eye"></i></a>
                </div>
            </div>

        <?php else: ?>

        <table class="table table-hover">
            <thead class="thead-inverse">
                <tr>
                    <th>Equipo</th>
                    <th>Sensor</th>
                    <th>Momento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $r): ?>
                    <tr>
                        <td><?= $r['equipo']?></td>
                        <td><?= $r['sensor']?></td>
                        <td><?= $r['momento']?></td>
                        <th><a class="btn btn-danger" href="<?= site_url('alarma/checkout/'.$r["id"]) ?>"><i class="fa fa-trash"></i></a></th>
                    </tr>
                <?php endforeach ?>
            </tbody>

        </table>

        <?php endif ?>

    </div>
</div>

