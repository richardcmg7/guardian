<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main_model extends CI_Model
{
  //recuperamos los históricos del dispo seleccionado
  public function get_data($user_id,$device_id)
  {
    $this->db->SELECT();
    $this->db->FROM('devices_full');
    $this->db->WHERE('device_id',$device_id);
    $result =	$this->db->get()->result_array();
    return $result;
  }
  public function get_data_act($device_id)
  {
    $this->db->SELECT('data_device_sn, data_a, data_b, data_c');
    $this->db->FROM('devices_full');
    $this->db->WHERE('device_id', $device_id);
    $this->db->ORDER_BY('data_id','desc');
    $result =  $this->db->get()->row();
    return $result;
  }
}
