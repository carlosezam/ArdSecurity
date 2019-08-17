<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sensores extends CI_Controller {

	protected $panel = 'equipos';

	public function __construct()
	{
		parent::__construct();
        if( !isset($this->session->usuario)   ) redirect('usuarios/login');

		$this->load->model('sensores_model');
		$this->load->model('tipos_model');
		$this->form_validation->set_error_delimiters('<small class="text-danger">','</small>');
	}

	public function index( $estilo = 'card')
	{
		redirect('equipos');
	}

	public function read($id)
	{
		if( empty($id) ) redirect($this->panel);

		if ( $this->session->sensor = $this->equipos_model->read($id) )
		{
			redirect('sensores');
		}

		redirect('equipos');
		
	}

	public function create( $id_equipo = NULL)
	{
		if( empty($id_equipo)) redirect('equipos');
		$this->session->id_equipo = $id_equipo;
		$data = array(
			'tipos' => $this->tipos_model->read(),
			'action' => 'create'
		);

		if ( $this->input->method() == 'get') {
			$this->_load_form( $data );
		}
		else
		{
			if( $this->form_validation->run() == FALSE )
			{

				$this->_load_form($data);
			}
			else
			{

				$this->sensores_model->create();

				redirect($this->panel);
			}
		}

	}

	public function update( $id = NULL )
	{
		if( $id === NULL ) redirect($this->panel);

		$data = array(
			'tipos' => $this->tipos_model->read(),
			'action' => 'update'
		);
		
		if ( $this->input->method() == 'get') {
			
			$_POST = (array)$this->sensores_model->read($id);

			$this->_load_form($data);
		}
		else
		{
			if( $this->form_validation->run() == FALSE )
			{
				

				$this->_load_form($data);
			}
			else
			{

				$this->sensores_model->update();

				redirect($this->panel);
			}
		}		
	}

	public function delete( $id )
	{
		if( $id === NULL ) redirect($this->panel);


		$this->sensores_model->delete( $id );

		redirect($this->panel);
	}

	protected function _load_form( $data = NULL )
	{
		$this->load->view('fragments/header');
		$this->load->view('fragments/navbar');
		$this->load->view('sensores/formulario', $data);
		$this->load->view('fragments/footer');
	}
}
