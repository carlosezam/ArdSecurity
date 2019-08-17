<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tipos_model extends CI_Model {

	protected $tabla = 'tipos_sensor';
   protected $fields = ['nombre'=>'nombre','notas'=>'notas','ranura'=>'ranura','id'=>'id'];
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

      return empty($where) ? $query->result_array() : $query->row();
   }

   public function create()
   {

      $this->_build_array_post();
   		
   	return $this->db->insert($this->tabla, $this->data );
   }
   
   public function update()
   {

      $this->_build_array_post();
         
      return $this->db->replace($this->tabla, $this->data );
   }

   public function delete($id)
   {
   	$this->db->where('id', $id);
   	return $this->db->delete($this->tabla );
   }
}