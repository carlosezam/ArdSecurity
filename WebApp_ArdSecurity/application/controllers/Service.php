<?php
/**
 * Created by PhpStorm.
 * User: Ezam
 * Date: 06/11/2017
 * Time: 03:36 PM
 */


class Service extends CI_Controller
{
    // funcion usada por arduino para recibir comandos de la plataforma web
    public function sync()
    {
        // Carga del modelo equipos
        $this->load->model('equipos_model');

        // Obtención de los datos enviados
        $device = array();
        $device['correo'] = isset( $_POST['correo'] ) ? trim($_POST['correo']) : NULL;
        $device['nombre'] = isset( $_POST['nombre'] ) ? trim($_POST['nombre']) : NULL;


        // Busqueda del id del equipo que solicita el sync mediante correo del usuario y nombre del equipo
        $id_equipo = $this->equipos_model->get_id( $device['correo'], $device['nombre']);

        // Obtiene un mando, si existe y lo elimina de la base de datos
        $command = $this->equipos_model->pop_command( $id_equipo );

        // Devuelve el comando a arduino
        echo $command;
    }

    public function push()
    {
        $device = array();
        $device['correo'] = isset( $_POST['correo'] ) ? trim($_POST['correo']) : NULL;
        $device['nombre'] = isset( $_POST['nombre'] ) ? trim($_POST['nombre']) : NULL;
        $device['ranura'] = isset( $_POST['ranura'] ) ? trim($_POST['ranura']) : NULL;

        if ( in_array(NULL, $device) )
        {
            echo "Datos incompletos ";
            echo json_encode($device);
        } else {

            $id_sensor = $this->sensores_model->get_id( $device['correo'], $device['nombre'], $device['ranura'] );

            if( $id_sensor != 0 )
            {
                $this->reportes_model->create( $id_sensor );
                $this->sensores_model->active( $id_sensor );

                $this->db->set('alarma', TRUE );
                $this->db->where('id', $this->usuario_model->get_id($device['correo']));
                $this->db->update('usuarios');

                $this->db->select('nombre, alarma, habilitado');
                $this->db->where('id', $id_sensor);
                $sensor = $this->db->get('sensores')->row();

                echo json_encode($sensor);
            } else {
                echo "Sensor no encontrado ";
                echo json_encode($device);
            }

        }

    }
}