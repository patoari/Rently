<?php
if(!defined('ABSPATH')) exit;

class Ordivo_Rently_Payments {
    public static function init() {
        // hooks
        add_action('ordivo_booking_created', array(__CLASS__,'on_booking_created'));
        add_action('ordivo_payment_success', array(__CLASS__,'on_payment_success'));
        add_action('ordivo_payment_failed', array(__CLASS__,'on_payment_failed'));
    }

    public static function on_booking_created($booking_id,$data){
        // trigger frontend flow or send invoice
    }

    public static function on_payment_success($booking_id,$payment_data){
        // update status to confirmed
        global $wpdb;
        $table=$wpdb->prefix.'rently_bookings';
        $wpdb->update($table,array('status'=>'confirmed'),array('id'=>$booking_id));
    }

    public static function on_payment_failed($booking_id,$error){
        // log failure
    }

    public static function charge($booking_id,$amount,$method){
        // placeholder
        do_action('ordivo_payment_success',$booking_id,array('method'=>$method,'amount'=>$amount));
    }
}

Ordivo_Rently_Payments::init();
