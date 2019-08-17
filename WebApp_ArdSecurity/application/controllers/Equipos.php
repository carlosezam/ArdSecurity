<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Equipos extends CI_Controller {

	protected $panel = 'equipos';

	public function __construct()
	{
		parent::__construct();
        if( !isset($this->session->usuario)   ) redirect('usuarios/login');


		$this->load->model('equipos_model');
        $this->load->model('reportes_model');
		$this->form_validation->set_error_delimiters('<small class="text-danger">','</small>');
	}

    public function dump_session()
    {
        var_dump($this->session);
    }

	public function index( $estilo = 'card')
	{

		$reportes = $this->reportes_model->count();

		$equipos = $this->equipos_model->read_with_sensors();
		$data = array(
			'equipos' => $equipos,
			'estilo' => $estilo
		);

		$this->load->view('fragments/header');
		$this->load->view('fragments/navbar', array('reportes'=>$reportes));
		$this->load->view('equipos/panel_principal', $data);
		$this->load->view('fragments/footer');
	}

	public function read($id)
	{
		if( empty($id) ) redirect($this->panel);

		//$this->load->model('sensores_model');

		$equipo = $this->equipos_model->read($id);
		$sensores = $this->equipos_model->read_sensores($id);
		
		$data = array(
			'equipo' => $equipo,
			'sensores' => $sensores
		);

		
		$this->load->view('fragments/header');
		$this->load->view('fragments/navbar');
		$this->parser->parse('equipos/panel_equipo', $data);
		$this->load->view('fragments/footer');
		
	}

	public function create()
	{
		if ( ! $this->input->method() == 'post') {
			$this->_load_form();
		}
		else
		{
			if( $this->form_validation->run() == FALSE )
			{
				$this->_load_form();
			}
			else
			{
				$this->equipos_model->create();
				redirect($this->panel);
			}
		}

	}

	public function update( $id = NULL )
	{
		if( $id === NULL ) redirect($this->panel);

		if ( $this->input->method() == 'get') {
			$_POST = (array)$this->equipos_model->read($id);
			$this->_load_form();
		}
		else
		{
			if( $this->form_validation->run() == FALSE )
			{
				$this->_load_form();
			}
			else
			{

				$this->equipos_model->update();
				redirect($this->panel);
			}
		}		
	}

	public function delete( $id )
	{
		if( $id === NULL ) redirect($this->panel);

		$this->equipos_model->delete( $id );

		redirect($this->panel);
	}

	protected function _load_form()
	{
		$this->load->view('fragments/header');
		$this->load->view('fragments/navbar');
		$this->load->view('equipos/formulario');
		$this->load->view('fragments/footer');
	}
}
