<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Equipos_model extends CI_Model {

    protected $tabla = 'equipos';
    protected $fields = ['nombre'=>'nombre','domicilio'=>'domicilio','notas'=>'notas','id'=>'id'];
    protected $data = array();

    public function __construct()
   {
        parent::__construct();
   }

    public function get_id( $correo, $nombre )
    {
        $correo = trim( $correo );
        $nombre = trim( $nombre );

        $this->db->select('e.id as id');
        $this->db->from('equipos as e');
        $this->db->join('usuarios as u', 'e.id_usuario = u.id');
        $this->db->where('u.correo',$correo);
        $this->db->where('e.nombre',$nombre);
        $query = $this->db->get();


        return $query->num_rows() > 0 ? $query->row()->id : 0;

    }

    public function pop_command( $id_equipo, $delete = true )
    {
        // Obtiene el comando mas antiguo del equipo
        $this->db->select('id, command');
        $this->db->from('sync');
        $this->db->where('id_equipo', $id_equipo);
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();

        // Si no hay comandos pendientes envia un mensaje informativo
        if( $query->num_rows() <= 0 )
        {
            return "print sync empty ";
        }

        // En caso de haber, obtiene el comando
        $sync = $query->row();

        // Si delete está establecido a TRUE elimina el comando de l BD despues de leerlo
        if( $delete )
        {
            $this->db->where('id', $sync->id );
            $this->db->delete('sync');
        }

        // Devuelve el comando
        return $sync->command;
    }

    public function push_command( $id_equipo, $command )
    {
        $data = array(
            'command' => $command,
            'id_equipo' => $id_equipo
        );
        $this->db->insert('sync', $data );
    }


   public function read_sensores( $id )
   {
      $this->db->where('id_equipo', $id );

      $query = $this->db->get('sensores');

      return $query->result();
   }

   public function read( $where = array() )
   {
      $this->db->where('id_usuario', $this->session->usuario->id);

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

   public function read_with_sensors( $id_equipo = NULL)
   {
      $this->db->where('id_usuario', $this->session->usuario->id);

      if( !empty($id_equipo) ) $this->db->where('id', $id_equipo);

      $query = $this->db->get($this->tabla);

      $result = $query->result_array();


      foreach ($result as $key => $value ) {
         $sensores = $this->db->get_where('sensores',[ 'id_equipo'=>$value['id'] ]);
         $sensores = $sensores->result_array();
         $result[$key]['sensores'] = $sensores;
      }

      return $result;

   }
   public function create()
   {
       $this->_build_array_post();

       $this->data['id_usuario'] = $this->session->usuario->id;
   		
       return $this->db->insert($this->tabla, $this->data );
   }

    public function active( $id )
    {
        $this->db->set('alarma',TRUE);
        $this->db->where('id', $id );
        $this->db->update('equipos');
    }

    public function disable( $id )
    {
        $this->db->set('alarma',FALSE);
        $this->db->where('id', $id );
        return $this->db->update('equipos');
    }

   public function update()
   {

       $this->_build_array_post();
       $this->data['id_usuario'] = $this->session->usuario->id;
       return $this->db->replace($this->tabla, $this->data );
   }

   public function delete($id)
   {

        $this->db->where('id_usuario', $this->session->usuario->id);
   	    $this->db->where('id', $id);
   	    return $this->db->delete($this->tabla );
   }

    protected function _build_array_post()
    {


        foreach ($this->fields as $key => $value)
        {
            $this->data[$key] = $this->input->post($value);
        }
    }
}