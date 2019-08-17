<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panel extends CI_Controller {

	protected $panel = 'equipos';

	public function __construct()
	{
		parent::__construct();
        if( !isset($this->session->usuario)   ) redirect('usuarios/login');

		$this->load->model('equipos_model');
		$this->form_validation->set_error_delimiters('<small class="text-danger">','</small>');
	}

	public function index( $estilo = 'card')
	{
		$this->load->view('fragments/header');
		$this->load->view('fragments/navbar');
		$this->load->view('panel');
		$this->load->view('fragments/footer');
	}


}
