<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usuario_model extends CI_Model {

    // clave utilizada para codificar y decodificar
   	private $key = 'Rv9tWrYjuI7BCzA';
    private $rules = array();
   
   	public $correo;
   	public $clave;
   	public $nombre;

   	public function __construct()
   	{
   		parent::__construct();



   		$this->load->library('encrypt');
   		$this->load->library('form_validation');

      $this->rules['login'] = array(
        array(
          'field' => 'correo',
          'label' => 'Correo electrónico',       
          'rules' => 'trim|required|valid_email' 
        ),
        array(
          'field' => 'clave',
          'label' => 'Contraseña',
          'rules' => 'trim|required'
        )
      );

   	}

   	public function validar_datos_login( $data )
   	{
    	
        $this->form_validation->set_data($data);
    	
        $this->form_validation->set_rules($this->rules['login']);
        return $this->form_validation->run();
   	}

   	public function validar_clave( $data = array() )
   	{
   		// busca los datos del usuario mediante su correo

        $query = $this->db->get_where('usuarios', array('correo' => $data['correo']));

        // obtiene el primer resultado
        $row = $query->row();
      
        // verifica si se pudo obtener al menos un resultado
        if(isset($row))
        {
            // descodifica la clave almacenada
       	    $clave_cifrada = $this->encrypt->decode($row->clave, $this->key);

            // compara la clave inntroducida por el usuario y la clave en la base de datos
            // si las claves son iguales devuelve TRUE
            // si son diferentes devuelve FALSE

       	    return $data['clave'] == $clave_cifrada ? $row->id : 0;

        }
      
        // llegado a este punto significa que no hay ningún usuario
        // con el correo introducido, entonces devuelve FALSE

        return FALSE;
   	}

    public function debug_insertar()
    {

        $clave_cifrada = $this->encrypt->encode($this->clave, $this->key);

        $data = array(
            'correo' => $this->correo,
            'clave' => $clave_cifrada,
            'nombre' => $this->nombre
        );

        return $this->db->insert('usuarios', $data);
    }

    public function get_id( $correo )
    {
        $this->db->select('id');
        $this->db->where('correo', $correo);
        return $this->db->get('usuarios')->row()->id;
    }

    public function get_by_id( $id )
    {
        $this->db->get_where('usuarios', 'id', $id);
        return $this->db->get('usuarios')->row();
    }

    public function get_correo( $id )
    {
        $this->db->select('correo');
        $this->db->where('id', $id);
        return $this->db->get('usuarios')->row()->correo;
    }

    public function change_correo( $id_usuario, $email )
    {
        $usuario = $this->db->get_where('usuarios', ['id' => $id_usuario])->row();

        $usuario->correo = $email;
        $this->db->where('id', $id_usuario);
        $this->db->update('usuarios', $usuario);
    }

    public function change_password( $id_usuario, $password )
    {
        $usuario = $this->db->get_where('usuarios', ['id' => $id_usuario])->row();

        $usuario->clave = $this->encrypt->encode($password, $this->key);

        $this->db->where('id', $id_usuario);
        $this->db->update('usuarios', $usuario);
    }
}