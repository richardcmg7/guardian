<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Insertdata_model extends CI_Model
{

  public function insert($device_sn, $temp, $hum, $status_a, $status_b, $status_c){
    
    $this->db->SELECT('data_a, data_b, data_c');
    $this->db->FROM('data');
    $this->db->WHERE('data_device_sn', $device_sn);
    $this->db->ORDER_BY('data_id', 'desc');
    $result =  $this->db->get()->row();
    $data_a = $result->data_a;
    $data_b = $result->data_b;
    $data_c = $result->data_c;

    $data = array(
      'data_device_sn' => $device_sn,
      'data_temp' => $temp,
      'data_hum' => $hum,
      'data_a' => $data_a,
      'data_b' => $data_b,
      'data_c' => $data_c,
      'status_a'  => $status_a,
      'status_b' => $status_b,
      'status_c' => $status_c
    );

    if ($this->db->insert('data', $data)){
      return "1";
    }else {
      return "0";
    }

  }
  public function insert_act($device_sn, $data_a,$data_b,$data_c)
  {
    $data = array(
      'data_device_sn' => $device_sn,
      'data_a' => $data_a,
      'data_b' => $data_b,
      'data_c' => $data_c
    );

    if ($this->db->insert('data', $data)) {
      return "1";
    } else {
      return "0";
    }
  }
}
