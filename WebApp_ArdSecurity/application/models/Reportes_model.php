<?php
/**
 * Created by PhpStorm.
 * User: Ezam
 * Date: 02/11/2017
 * Time: 09:03 PM
 */

class Reportes_model extends CI_Model
{
    public function get()
    {
        $this->db->select('e.nombre as equipo');
        $this->db->select('s.nombre as sensor');
        $this->db->select('r.id as id');
        $this->db->select('r.momento as momento');
        $this->db->from('sensores as s');
        $this->db->join('equipos as e', 'e.id = s.id_equipo');
        $this->db->join('reportes as r', 'r.id_sensor = s.id');
        $this->db->where('e.id_usuario', $this->session->usuario->id);
        $this->db->order_by('r.momento', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_last()
    {
        $this->db->select('e.nombre as equipo');
        $this->db->select('s.nombre as sensor');
        $this->db->select('r.id as id');
        $this->db->select('r.momento as momento');
        $this->db->from('sensores as s');
        $this->db->join('equipos as e', 'e.id = s.id_equipo');
        $this->db->join('reportes as r', 'r.id_sensor = s.id');
        $this->db->where('e.id_usuario', $this->session->usuario->id);
        $this->db->order_by('r.momento', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row();
    }

    public function count()
    {
        $this->db->from('sensores as s');
        $this->db->join('equipos as e', 'e.id = s.id_equipo');
        $this->db->join('reportes as r', 'r.id_sensor = s.id');
        $this->db->where('e.id_usuario', $this->session->usuario->id);
        return $this->db->count_all_results();
    }

    public function delete( $id )
    {
        $this->db->where('id', $id);
        $this->db->delete('reportes');
    }

    public function create( $id_sensor )
    {
        return $this->db->insert('reportes', array('id_sensor'=>$id_sensor) );
    }
}