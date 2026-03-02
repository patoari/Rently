<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Lightweight Elementor widget registration (only if Elementor active)
if ( class_exists( '\\Elementor\\Widget_Base' ) ) {
    class Ordivorently_Elementor_Property_Card extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_property_card'; }
        public function get_title(){ return 'Ordivorently Property Card'; }
        public function get_icon(){ return 'eicon-post-carousel'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){
            $settings = $this->get_settings_for_display();
            echo ordivorently_property_card_render( array( 'id' => get_the_ID() ) );
        }
    }

    class Ordivorently_Elementor_Search_Bar extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_search_bar'; }
        public function get_title(){ return 'Ordivorently Search Bar'; }
        public function get_icon(){ return 'eicon-search'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_search_bar_render(); }
    }

    class Ordivorently_Elementor_Host_Badge extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_host_badge'; }
        public function get_title(){ return 'Ordivorently Host Badge'; }
        public function get_icon(){ return 'eicon-bullet-list'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_host_badge_render(); }
    }

    class Ordivorently_Elementor_Wishlist extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_wishlist'; }
        public function get_title(){ return 'Ordivorently Wishlist Button'; }
        public function get_icon(){ return 'eicon-heart'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_wishlist_button_render(); }
    }

    class Ordivorently_Elementor_Hero_Search extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_hero_search'; }
        public function get_title(){ return 'Ordivorently Hero Search'; }
        public function get_icon(){ return 'eicon-search'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_hero_search_render( array( 'sticky' => 1 ) ); }
    }

    class Ordivorently_Elementor_Filter_Sidebar extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_filter_sidebar'; }
        public function get_title(){ return 'Ordivorently Filter Sidebar'; }
        public function get_icon(){ return 'eicon-filter'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_filter_sidebar_render(); }
    }

    class Ordivorently_Elementor_Property_Grid extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_property_grid'; }
        public function get_title(){ return 'Ordivorently Property Grid'; }
        public function get_icon(){ return 'eicon-gallery-grid'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_property_grid_render( array( 'limit' => 12, 'columns' => 4 ) ); }
    }

    class Ordivorently_Elementor_Map_Search extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_map_search'; }
        public function get_title(){ return 'Ordivorently Map Search'; }
        public function get_icon(){ return 'eicon-google-maps'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_map_search_render( array( 'limit' => 50 ) ); }
    }

    class Ordivorently_Elementor_Booking_Form extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_booking_form'; }
        public function get_title(){ return 'Ordivorently Booking Form'; }
        public function get_icon(){ return 'eicon-form-horizontal'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_booking_form_render( array( 'property_id' => get_the_ID() ) ); }
    }

    class Ordivorently_Elementor_Availability_Calendar extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_availability_calendar'; }
        public function get_title(){ return 'Ordivorently Availability Calendar'; }
        public function get_icon(){ return 'eicon-calendar'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_render_availability_calendar( array( 'property_id' => get_the_ID(), 'show_legend' => 1 ) ); }
    }

    class Ordivorently_Elementor_Reviews extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_reviews'; }
        public function get_title(){ return 'Ordivorently Reviews'; }
        public function get_icon(){ return 'eicon-comments'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_render_reviews_widget( array( 'property_id' => get_the_ID(), 'show_form' => 1 ) ); }
    }

    class Ordivorently_Elementor_Host_Profile extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_host_profile'; }
        public function get_title(){ return 'Ordivorently Host Profile'; }
        public function get_icon(){ return 'eicon-person'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){
            $property_id = get_the_ID();
            $user_id = $property_id ? get_post_field( 'post_author', $property_id ) : 0;
            echo ordivorently_render_host_profile( array( 'user_id' => $user_id, 'show_contact' => 1, 'show_profile' => 1 ) );
        }
    }

    class Ordivorently_Elementor_Wishlist extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_wishlist'; }
        public function get_title(){ return 'Ordivorently Wishlist'; }
        public function get_icon(){ return 'eicon-heart'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_render_wishlist_widget( array( 'per_page' => 12, 'columns' => 3 ) ); }
    }

    class Ordivorently_Elementor_Host_Dashboard extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_host_dashboard'; }
        public function get_title(){ return 'Ordivorently Host Dashboard'; }
        public function get_icon(){ return 'eicon-editor-list'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_render_host_dashboard( array( 'properties_per' => 6, 'bookings_per' => 5 ) ); }
    }

    class Ordivorently_Elementor_Guest_Dashboard extends \Elementor\Widget_Base {
        public function get_name(){ return 'ordivorently_guest_dashboard'; }
        public function get_title(){ return 'Ordivorently Guest Dashboard'; }
        public function get_icon(){ return 'eicon-dashboard'; }
        public function get_categories(){ return array('general'); }
        protected function register_controls(){}
        protected function render(){ echo ordivorently_render_guest_dashboard( array( 'upcoming_per' => 5, 'past_per' => 5, 'reviews_per' => 5 ) ); }
    }

    add_action( 'elementor/widgets/register', function( $widgets_manager ){
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Property_Card() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Search_Bar() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Host_Badge() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Wishlist() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Hero_Search() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Filter_Sidebar() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Property_Grid() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Map_Search() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Booking_Form() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Availability_Calendar() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Reviews() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Host_Profile() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Wishlist() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Host_Dashboard() );
        $widgets_manager->register_widget_type( new Ordivorently_Elementor_Guest_Dashboard() );
    } );
}
