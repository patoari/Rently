<?php
if(!defined('ABSPATH')) exit;

class Ordivo_Rently_Review_System {
    public static function init(){
        add_action('wp_ajax_rently_submit_review',array(__CLASS__,'submit_review'));
        add_action('wp_ajax_nopriv_rently_submit_review',array(__CLASS__,'submit_review'));
        add_shortcode('rently_review_form',array(__CLASS__,'render_form'));
        add_filter('the_content',array(__CLASS__,'append_average_rating'));
    }

    public static function can_review($user_id,$property_id){
        global $wpdb;
        $table=$wpdb->prefix.'rently_bookings';
        return (bool)$wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE property_id=%d AND guest_id=%d AND status='completed'",$property_id,$user_id));
    }

    public static function render_form($atts){
        if(!is_user_logged_in()) return '<p>'.__('Please login to review','ordivo-rently-reviews').'</p>';
        $atts=shortcode_atts(array('property'=>0),$atts,'rently_review_form');
        $prop=intval($atts['property']);
        if(!$prop) return '';
        if(!self::can_review(get_current_user_id(),$prop)) return '<p>'.__('You must complete a stay to review','ordivo-rently-reviews').'</p>';
        ob_start();
        ?>
        <form id="rently-review-form">
            <?php wp_nonce_field('ordivo_review','ordivo_review_nonce'); ?>
            <input type="hidden" name="property_id" value="<?php echo esc_attr($prop);?>"/>
            <p><label><?php _e('Rating (1-5)','ordivo-rently-reviews');?><br/><select name="rating" required><?php for($i=1;$i<=5;$i++) echo '<option>'.$i.'</option>';?></select></label></p>
            <p><label><?php _e('Comment','ordivo-rently-reviews');?><br/><textarea name="comment" rows="3"></textarea></label></p>
            <button type="submit"><?php _e('Submit Review','ordivo-rently-reviews');?></button>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function submit_review(){
        check_ajax_referer('ordivo_review','nonce');
        if(!is_user_logged_in()){wp_send_json_error('login');}
        $user=get_current_user_id();
        $prop=intval($_POST['property_id']);
        $rating=intval($_POST['rating']);
        $comment=sanitize_textarea_field($_POST['comment']);
        $parent=0;
        if(isset($_POST['parent'])) $parent=intval($_POST['parent']);
        $data=array(
            'comment_post_ID'=>$prop,
            'comment_author'=>wp_get_current_user()->display_name,
            'comment_author_email'=>wp_get_current_user()->user_email,
            'comment_content'=>$comment,
            'comment_type'=>'review',
            'comment_parent'=>$parent,
            'user_id'=>$user,
        );
        $cid=wp_insert_comment($data);
        if($cid){
            add_comment_meta($cid,'rating',$rating,true);
            wp_send_json_success();
        }else wp_send_json_error('db');
    }

    public static function append_average_rating($content){
        if(is_singular('property') && in_the_loop()){
            $prop=get_the_ID();
            $comments=get_comments(array('post_id'=>$prop,'type'=>'review','status'=>'approve'));
            if($comments){
                $sum=0;foreach($comments as $c)$sum+=intval(get_comment_meta($c->comment_ID,'rating',true));
                $avg=round($sum/count($comments),1);
                $star='';for($i=0;$i<5;$i++)$star.='<span class="dashicons dashicons-star-filled"'.($i<$avg?'':' style="color:#ccc;"').'></span>';
                $content='<div class="property-average-rating">'.$star.' ('.$avg.')</div>'.$content;
            }
        }
        return $content;
    }
}

Ordivo_Rently_Review_System::init();
