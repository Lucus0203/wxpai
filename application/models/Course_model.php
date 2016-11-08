<?php
/**
 * 
 * @author lucus
 * 课程
 *
 */
class Course_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
        //查所有
	public function get_all($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course', $where );
		return $query->result_array ();
	}
        
	// 查
	public function get_row($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'course', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'course', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'course', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'course' );
	}
    //查找数量
    public function get_count($where=FALSE){
        $this->db->where ($where);
        return $this->db->count_all_results('annual_course');
    }
        
        //SQL查询
        public function get_sql($sql){
                $query = $this->db->query($sql);
                return $query->result_array();
        }
	
}