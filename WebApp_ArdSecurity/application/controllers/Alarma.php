<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alarma extends CI_Controller {

	public function index()
	{

	}

	public function __construct()
    {
        parent::__construct();

        $this->load->model('reportes_model');
        $this->load->model('sensores_model');
        $this->load->model('equipos_model');
        $this->load->model('usuario_model');
    }

    public function notify()
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
			    $id_equipo = $this->equipos_model->get_id( $device['correo'], $device['nombre'] );

                $this->reportes_model->create( $id_sensor );
                $this->sensores_model->active( $id_sensor );
                $this->equipos_model->active( $id_equipo );

                $this->db->set('alarma', TRUE );
                $this->db->where('id', $this->usuario_model->get_id($device['correo']));
                $this->db->update('usuarios');

				$this->db->select('nombre, alarma, habilitado');
				$this->db->where('id', $id_sensor);
				$sensor = $this->db->get('sensores')->row();

				$this->send_email( $device['correo'] );
				
				echo json_encode($sensor);
			} else {
				echo "Sensor no encontrado ";
				echo json_encode($device);
			}
		}
	}

	public function send_email2()
    {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.googlemail.com',
            'smtp_user' => 'ardsec.tap@gmail.com', //Su Correo de Gmail Aqui
            'smtp_pass' => 'culito93', // Su Password de Gmail aqui
            'smtp_port' => '587',
            'smtp_crypto' => 'ssl',
            'mailtype' => 'html',
            'wordwrap' => TRUE,
            'charset' => 'utf-8'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('desde donde se envia el correo');
        $this->email->subject('Asunto del correo');
        $this->email->message('Aqui va el mensaje');
        $this->email->to('michellecb19@gmail.com');
        $this->email->send(FALSE);
    }

	public function send_email( $email = 'michellecb19@gmail.com' )
    {
        $this->load->library('email');

        $configGmail = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_user' => 'ardsec.tap@gmail.com',
            'smtp_pass' => 'culito93',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );

        $this->email->initialize( $configGmail );

        $this->email->from('ardsec.tap@gmail.com', 'ArdSecurity');
        $this->email->to($email);

        //$this->email->to('2d.roblero@gmail.com');
        //$this->email->to('carlos.ezam@gmail.com');
        //$this->email->cc('another@another-example.com');
        //$this->email->bcc('them@their-example.com');

        //$html = $this->load->view('fragments/header', NULL, TRUE );
        //$html .= $this->load->view('fragments/navbar', NULL, TRUE );
        //$html .= $this->load->view('usuarios/perfil', NULL, TRUE );

        $html = "<h2> Alarma Activada </h2><hr>
                 <h3>Numeros de emergencia</h3>
                 <br>
                 <p>Secretaria de Seguridad Pública Munucipal</p>
                 <p><b>Direeción:</b> Aeropuerto 538 Milenio Tapachula Centro</p>
                 <p><b>Tel:</b> 019626264500</p>
                 <br>
                 <p><b>Tel:</b> 911</p>
";
        $this->email->subject('Email Test');
        $this->email->message($html);

        $this->email->send();

        var_dump($this->email->print_debugger());
    }
	public function desactivar_alarma()
    {
        $this->db->set('alarma', FALSE );
        $this->db->where('id', $this->session->usuario->id);
        $this->db->update('usuarios');
        redirect('reportes');
    }
	public function desactivar_alarma_sensor( $id )
	{
        $this->db->set('alarma', FALSE );
        $this->db->where('id', $id);
        $this->db->update('sensores');
        redirect('/');
	}

    public function desactivar_alarma_equipo( $id )
    {
        $this->load->model('equipos_model');
        $this->equipos_model->push_command( $id, 'config sys alarma=false');
        $this->equipos_model->push_command( $id, 'config sys activo=true');

        $this->db->set('alarma', FALSE );
        $this->db->where('id', $id);
        $this->db->update('equipos');
        redirect('/');
    }

    public function sensor_on( $id )
    {
        $this->db->set('habilitado', TRUE );
        $this->db->where('id', $id);
        $this->db->update('sensores');
        redirect('/');
    }

    public function sensor_off( $id )
    {
        $this->db->set('habilitado', FALSE );
        $this->db->where('id', $id);
        $this->db->update('sensores');
        redirect('/');
    }

	public function checkout( $id )
    {
        $this->reportes_model->delete($id);
        redirect('reportes');
    }

    public function ajax()
    {
        $this->db->select('alarma');
        $this->db->where('id', $this->session->usuario->id);
        $data['alarma'] = $this->db->get('usuarios')->row()->alarma;
        $data['reportes'] = $this->reportes_model->count();
        $data['last'] = $this->reportes_model->get_last();
        echo json_encode($data);
    }
}
