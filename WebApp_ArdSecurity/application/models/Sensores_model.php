<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sensores_model extends CI_Model {


   protected $tabla = 'sensores';

   protected $fields = array(
      'id' => 'id',
      'nombre'=>'nombre',
      'ranura'=>'ranura',
      'notas'=>'notas',
      'id_tipo' => 'tipo'
   );
   protected $data = array();

   public function __construct()
   {
      parent::__construct();
      //$this->fields = ['nombre'=>'nombre','domicilio'=>'domicilio','notas'=>'notas'];
   }

   protected function _build_array_post()
   {
      foreach ($this->fields as $key => $value)
      {
         $this->data[$key] = $this->input->post($value);
      }
   }

   public function read( $where = array() )
   {
      //$this->db->where('id_usuario', $this->session->usuario['id']);

      if( (is_array($where) && count($where) > 0) )
      {
         foreach ($where as $key => $value) {
            $this->db->where($key, $value);
         }
      } else if( is_string($where))
      {
         $this->db->where('id',$where);
      }

      $query = $this->db->get($this->tabla);

      return empty($where) ? $query->result() : $query->row();
   }

   public function create()
   {

      $this->_build_array_post();

      $this->data['id_equipo'] = $this->session->id_equipo;
   		
   	return $this->db->insert($this->tabla, $this->data );
   }
   
   public function update()
   {

      $this->_build_array_post();

      $this->data['id_equipo'] = $this->db->where('id', $_POST['id'])->get($this->tabla)->row()->id_equipo;
      return $this->db->replace($this->tabla, $this->data );
   }

   public function delete($id)
   {
   	$this->db->where('id', $id);
   	return $this->db->delete( 'sensores' );
   }

   public function disable( $id )
   {
       $this->db->set('alarma',FALSE);
       $this->db->where('id', $id );
       return $this->db->update('sensores');
   }

   public function active( $id )
   {
       $this->db->set('alarma',TRUE);
       $this->db->where('id', $id );
       $this->db->update('sensores');
   }
   public function get_id( $correo, $nombre, $ranura )
   {
       $correo = trim( $correo );
       $nombre = trim( $nombre );
       $ranura = trim( $ranura );

       $this->db->select('e.nombre as equipo, s.nombre as sensor, s.ranura, s.id ');
       $this->db->from('usuarios as u');
       $this->db->join('equipos as e', 'u.id = e.id_usuario');
       $this->db->join('sensores as s', 'e.id = s.id_equipo');
       $this->db->where('u.correo',$correo);
       $this->db->where('e.nombre',$nombre);
       $this->db->where('s.ranura',$ranura);

       $query = $this->db->get();

       return $query->num_rows() > 0 ? $query->row()->id : 0;

   }
}