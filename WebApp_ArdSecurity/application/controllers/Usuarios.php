<?php
/**
 * Created by PhpStorm.
 * User: Ezam
 * Date: 03/11/2017
 * Time: 07:36 AM
 */

class Usuarios extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //if( !isset($this->session->usuario)   ) redirect('usuarios/login');

        // Carga del modelo usuario
        $this->load->model('usuario_model');
    }

    public function index()
    {
        $this->load->model('usuario_model');

        $user_data = $this->usuario_model->get_by_id($this->session->usuario->id);



        if ($this->input->method() == 'post' )
        {

            $this->form_validation->set_rules('correo', 'Correo electrónico', 'required|valid_email');

            if( ! empty($_POST['password']) )
            {
                $this->form_validation->set_rules('password', 'Contraseña', 'min_length[5]');
                $this->form_validation->set_rules('confirmacion', 'Confirmación', 'matches[password]');
            }


            if( $this->form_validation->run()) {

                $this->usuario_model->change_correo( $user_data->id, $_POST['correo']);

                if ( !empty($_POST['password']))
                {
                    $this->usuario_model->change_password( $user_data->id, $_POST['password']);
                }

                redirect('usuarios/index');
            }

        } else {
            $_POST['correo'] = $user_data->correo;
        }

        $this->display_template('perfil');
    }

    public function login()
    {
        if( $this->input->method() !== "post" )
        {
            $this->display_template('login',NULL,FALSE);
        } else {
            $this->_login();
        }

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    public function _login()
    {

        // Se obtienen los datos enviados por el formulario
        // los datos son recibidos mediante el metodo POST
        $data = array(
            'correo' => $this->input->post('correo'),
            'clave' => $this->input->post('clave'),
            'nombre' => $this->input->post('nombre')
        );

        $result_validacion = $this->usuario_model->validar_datos_login( $data );

        if( $result_validacion == FALSE )
        {
            $this->display_template('login', array(), FALSE);
        }

        $result_autenticacion = $this->usuario_model->validar_clave( $data );

        if( $result_autenticacion == 0 )
        {
            $data = array('login_error' => 'Datos invalidos');
            $this->display_template('login', $data, FALSE);
        }
        else
        {
            $this->session->logged = TRUE;
            $this->session->usuario = $this->db->select('id, nombre')
                                                ->where('correo', $data['correo'])
                                                ->get('usuarios')
                                                ->row();
            redirect('equipos');
        }
    }

    protected function display_template( $view, $data = NULL, $navbar = TRUE )
    {
        $this->load->view('fragments/header', $data);
        $navbar AND $this->load->view('fragments/navbar', $data);
        $this->load->view('usuarios/'.$view, $data);
        $this->load->view('fragments/footer', $data);
        $this->output->_display();
        exit;
    }
}