<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
        'equipos/create' => array(
                array(
                        'field' => 'nombre',
                        'label' => 'Nombre del equipo',
                        'rules' => 'trim|required|alpha_numeric_spaces'
                ),
                array(
                        'field' => 'domicilio',
                        'label' => 'Domicilio del equipo',
                        'rules' => 'trim|required'
                ),
                array(
                        'field' => 'notas',
                        'label' => 'Notas extra para el equipo',
                        'rules' => 'trim|alpha_numeric_spaces'
                )
        ),
        'equipos/update' => array(
                array(
                        'field' => 'nombre',
                        'label' => 'Nombre del equipo',
                        'rules' => 'trim|required|alpha_numeric_spaces'
                ),
                array(
                        'field' => 'domicilio',
                        'label' => 'Domicilio del equipo',
                        'rules' => 'trim|required'
                ),
                array(
                        'field' => 'notas',
                        'label' => 'Notas extra para el equipo',
                        'rules' => 'trim|alpha_numeric_spaces'
                )
        ),
        'sensores/create' => array(
                array(
                        'field' => 'nombre',
                        'label' => 'Nombre del sensor',
                        'rules' => 'trim|required|max_length[50]'
                ),
                array(
                        'field' => 'ranura',
                        'label' => 'Ranura del sensor',
                        'rules' => 'trim|required|integer'
                ),
                array(
                        'field' => 'notas',
                        'label' => 'Notas extra del sensor',
                        'rules' => 'trim|required'
                )
        ),
         'sensores/update' => array(
                array(
                        'field' => 'nombre',
                        'label' => 'Nombre del sensor',
                        'rules' => 'trim|required|max_length[50]'
                ),
                array(
                        'field' => 'ranura',
                        'label' => 'Ranura del sensor',
                        'rules' => 'trim|required|integer'
                ),
                array(
                        'field' => 'notas',
                        'label' => 'Notas extra del sensor',
                        'rules' => 'trim|required'
                )
        )
);