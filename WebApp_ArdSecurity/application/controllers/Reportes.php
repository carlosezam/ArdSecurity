<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if( !isset($this->session->usuario)   ) redirect('usuarios/login');

        $this->load->model('reportes_model');
    }

    public function index()
    {
        $reportes = $this->reportes_model->get();
        $this->load->view('fragments/header');
        $this->load->view('fragments/navbar');
        $this->load->view('reportes/index', array('reportes'=>$reportes));
        $this->load->view('fragments/footer');
    }

    public function ajax()
    {
        $reportes = $this->reportes_model->get();
        echo json_encode( $reportes );
    }
}