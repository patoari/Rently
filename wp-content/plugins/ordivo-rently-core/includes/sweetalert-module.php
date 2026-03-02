<?php
/**
 * SweetAlert2 Integration Module
 * 
 * Enqueues SweetAlert2 library and provides helper functions
 */

if (!defined('ABSPATH')) exit;

class Ordivo_Rently_SweetAlert {
    
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin']);
    }
    
    /**
     * Enqueue SweetAlert2 on frontend
     */
    public static function enqueue_frontend() {
        // SweetAlert2 CSS
        wp_enqueue_style(
            'sweetalert2',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
            [],
            '11.0.0'
        );
        
        // SweetAlert2 JS
        wp_enqueue_script(
            'sweetalert2',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
            [],
            '11.0.0',
            true
        );
        
        // Custom SweetAlert wrapper
        wp_add_inline_script('sweetalert2', self::get_wrapper_script());
    }
    
    /**
     * Enqueue SweetAlert2 on admin
     */
    public static function enqueue_admin() {
        // SweetAlert2 CSS
        wp_enqueue_style(
            'sweetalert2-admin',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
            [],
            '11.0.0'
        );
        
        // SweetAlert2 JS
        wp_enqueue_script(
            'sweetalert2-admin',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
            [],
            '11.0.0',
            true
        );
        
        // Custom SweetAlert wrapper
        wp_add_inline_script('sweetalert2-admin', self::get_wrapper_script());
    }
    
    /**
     * Get wrapper script for easier SweetAlert usage
     */
    private static function get_wrapper_script() {
        return "
        // SweetAlert2 Helper Functions
        window.RentlyAlert = {
            success: function(message, title) {
                return Swal.fire({
                    icon: 'success',
                    title: title || 'Success!',
                    text: message,
                    confirmButtonColor: '#00a32a',
                    confirmButtonText: 'OK'
                });
            },
            
            error: function(message, title) {
                return Swal.fire({
                    icon: 'error',
                    title: title || 'Error!',
                    text: message,
                    confirmButtonColor: '#d63638',
                    confirmButtonText: 'OK'
                });
            },
            
            warning: function(message, title) {
                return Swal.fire({
                    icon: 'warning',
                    title: title || 'Warning!',
                    text: message,
                    confirmButtonColor: '#dba617',
                    confirmButtonText: 'OK'
                });
            },
            
            info: function(message, title) {
                return Swal.fire({
                    icon: 'info',
                    title: title || 'Information',
                    text: message,
                    confirmButtonColor: '#2271b1',
                    confirmButtonText: 'OK'
                });
            },
            
            confirm: function(message, title, confirmText, cancelText) {
                return Swal.fire({
                    icon: 'question',
                    title: title || 'Are you sure?',
                    text: message,
                    showCancelButton: true,
                    confirmButtonColor: '#2271b1',
                    cancelButtonColor: '#d63638',
                    confirmButtonText: confirmText || 'Yes',
                    cancelButtonText: cancelText || 'Cancel'
                });
            },
            
            loading: function(message) {
                return Swal.fire({
                    title: message || 'Processing...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            
            close: function() {
                Swal.close();
            }
        };
        
        // Override native alert for backward compatibility
        window.nativeAlert = window.alert;
        window.alert = function(message) {
            RentlyAlert.info(message);
        };
        
        // Override native confirm for backward compatibility
        window.nativeConfirm = window.confirm;
        window.confirm = function(message) {
            return RentlyAlert.confirm(message).then(result => result.isConfirmed);
        };
        ";
    }
}

Ordivo_Rently_SweetAlert::init();
