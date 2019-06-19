<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General extends Admin_Controller {

    public $data = array();

    function __construct() {
        parent::__construct();
        parent::load_version_model('admin/General_model', 'general');
    }

    public function index() {
        $this->data['headTitle'] = $this->lang->line("dashboard_header_title");
        $this->data['module'] = "dashboard";
        $this->load->view('admin/mainpage', $this->data);
    }

    public function get_booking_detail_by_id($type = "array") {
        $bookingid = $this->input->post('id');
        $usertype = $this->input->post('usertype');
        $resulttype = $type;
        $bookhtml = "";
        $bookarray = array();
        $htmlarray = array();
        $_getdetails = $this->general->getBookingDetailsById($bookingid, $field = "*,b.id as bookingid,b.name as clientname,(select name from  tbl_extra_services where id = bd.taskId) as taskname");
        $title = "";
        $personal = "";
        $address = "";
        if ($usertype == 'cleaner'):
            $title = "Working Details";
            $personal = "Client's Details";
            $address = "Working Area";
        else:
            $title = "Booking Details";
            $personal = "Personal Details";
            $address = "Address Details";
        endif;
        if ($resulttype == "model") {
            if (count($_getdetails) > 0) {
                foreach ($_getdetails as $key => $val) {
                    $bookhtml .= '<div class="col-md-12">';
                    if ($key == 0) {
                        if ($val['isproduct'] == 0):
                            $cleaning_product = "Bring cleaning products (+£4.00)";
                        else:
                            $cleaning_product = "I will provide";
                        endif;
                        $bookhtml .= '<div class="form-group col-md-3">Booking ID :</div>
                        <div class="form-group col-md-9">
                             <label class="control-label" > Booking' . $val['bookingid'] . '</label></div>
                               <div class="form-group col-md-3"> No of bathrooms :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['bathroom'] . '</label></div>
                                 
                           <div class="form-group col-md-3"> No of bedrooms :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['bedroom'] . '</label></div>
                                 
                            <div class="form-group col-md-3">  Hours :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['hours'] . '</label></div>
                                 
                             <div class="form-group col-md-3">cleaning products :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" > ' . $cleaning_product . '</label></div>
                                  <h6>' . $personal . ' </h6>
                           <div class="form-group col-md-3"> Name :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['clientname'] . '</label></div>
                            <div class="form-group col-md-3"> Email :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['email'] . '</label></div>
                           <div class="form-group col-md-3"> Mobile :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['mobile'] . '</label></div>
                                   <h6>' . $address . ' </h6>
                                    <div class="form-group col-md-3">Postcode :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['postcode'] . '</label></div>
                           <div class="form-group col-md-3"> Address Line1 :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['addressline_one'] . '</label></div>
                             <div class="form-group col-md-3"> Address Line2 :</div>
                                          <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['addressline_two'] . '</label></div>
                                 <div class="form-group col-md-3">Ammount :</div>
                                  <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['ammount'] . '</label>
                                                    </div><h6>Task Details </h6>';
                    }
                    $bookhtml .= '<div class="form-group col-md-3">Frequance Name :</div>
                                <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['taskname'] . '</label></div>
                            <div class="form-group col-md-3">Quantity :</div>
                                <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['quantity'] . '</label></div>
                            <div class="form-group col-md-3">Price :</div>
                                <div class="form-group col-md-9">
                             <label class="control-label" >' . $val['price'] . '</label></div>
                         </div>';
                }
            }
            $htmlarray['html'] = $bookhtml;
            $htmlarray['status'] = 200;
            $htmlarray['title'] = $title;
            $htmlarray['booking_id'] = $bookingid;


            echo json_encode($htmlarray);
            exit;
        }else if ($resulttype == "array") {
            return $_getdetails;
        }
    }

    public function removeImage() {
        $content['status'] = 401;
        $content['message'] = $this->data['language']['err_something_went_wrong'];
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $table = $this->input->post('table');
            $field = $this->input->post('fieldname');
            $delId = $this->input->post('delfield');
            if (!empty($id)) {
                if ($table = "tbl_cleaner_gallery") {
                    $filename = $this->input->post('filename');
                    if (file_exists(FCPATH . SITE_UPD . 'gallery/' . $filename)) {
                        unlink(FCPATH . SITE_UPD . 'gallery/' . $filename);
                    }
                    $this->db->where($delId, $id);
                    $this->db->delete($table);
                    $content['status'] = 200;
                    $content['message'] = "Image Deletd Successfully";
                } else {
                    $dataArray = array(
                        $field => ''
                    );
                    $this->db->update($table, $dataArray, array($delId => $id));
                    $content['status'] = 200;
                    $content['message'] = $this->lang->line('succ_profile_rm_pro_pic');
                }
            }
        }
        echo json_encode($content);
        die();
    }

    public function getReviewbyId() {
        $content['status'] = 401;
        $content['message'] = $this->data['language']['err_something_went_wrong'];
        $content['html'] = "";
        $_result = array();
        $reviewHtml = "";
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            if (!empty($id)) {
                $whrArr = array('tr.id' => $id);
                $query = $this->db->select('*')
                                ->from('tbl_rattings_review as tr')
                                ->where($whrArr)->get();
                $_result = $query->result_array();
                if (count($_result) > 0)
                    $reviewHtml = $_result[0]['review'];

                $content['status'] = 200;
                $content['html'] = $reviewHtml;
            }
        }
        echo json_encode($content);
        die();
    }

}
