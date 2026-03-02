<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ordivorently_map_search_render( $atts = array() ) {
    $atts = shortcode_atts( array( 'limit' => 50, 'center_lat' => 23.8103, 'center_lng' => 90.4125 ), $atts, 'rently_map_search' );
    $limit = intval( $atts['limit'] );
    $center_lat = floatval( $atts['center_lat'] );
    $center_lng = floatval( $atts['center_lng'] );
    
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $limit,
        'meta_query' => array(
            array(
                'key' => 'price_per_night',
                'compare' => 'EXISTS',
            ),
        ),
    );
    
    $q = new WP_Query( $args );
    $properties = array();
    
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $post_id = get_the_ID();
            $price = get_post_meta( $post_id, 'price_per_night', true );
            $lat = get_post_meta( $post_id, 'map_lat', true );
            $lng = get_post_meta( $post_id, 'map_lng', true );
            
            // Default to center if not set
            if ( ! $lat || ! $lng ) {
                $lat = $center_lat + ( rand( -100, 100 ) / 1000 );
                $lng = $center_lng + ( rand( -100, 100 ) / 1000 );
            }
            
            $properties[] = array(
                'id' => $post_id,
                'title' => get_the_title(),
                'price' => $price,
                'lat' => floatval( $lat ),
                'lng' => floatval( $lng ),
                'url' => get_permalink(),
                'thumb' => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
                'location' => get_post_meta( $post_id, 'location', true ),
            );
        }
    }
    wp_reset_postdata();
    
    $map_id = 'rently-map-' . uniqid();
    $list_id = 'rently-list-' . uniqid();
    
    ob_start();
    ?>
    <div class="ordivorently-map-search-container">
        <div class="map-view-toggle">
            <button class="toggle-btn active map-mode" data-mode="map"><?php esc_html_e( 'Map', 'ordivorently' ); ?></button>
            <button class="toggle-btn list-mode" data-mode="list"><?php esc_html_e( 'List', 'ordivorently' ); ?></button>
        </div>

        <!-- Map View -->
        <div class="map-view" id="<?php echo esc_attr( $map_id ); ?>" style="width:100%;height:600px;border-radius:10px;overflow:hidden;"></div>

        <!-- List View -->
        <div class="list-view hidden" id="<?php echo esc_attr( $list_id ); ?>" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;padding:16px;">
            <?php foreach ( $properties as $prop ): ?>
            <article class="map-list-card" data-property-id="<?php echo esc_attr( $prop['id'] ); ?>">
                <?php if ( $prop['thumb'] ): ?>
                <img src="<?php echo esc_url( $prop['thumb'] ); ?>" alt="<?php echo esc_attr( $prop['title'] ); ?>" class="card-img" />
                <?php endif; ?>
                <div class="card-content">
                    <h3><a href="<?php echo esc_url( $prop['url'] ); ?>" target="_blank"><?php echo esc_html( $prop['title'] ); ?></a></h3>
                    <?php if ( $prop['location'] ): ?>
                    <p class="card-location">📍 <?php echo esc_html( $prop['location'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $prop['price'] !== '' ): ?>
                    <p class="card-price"><?php echo esc_html( ordivorently_format_price_bdt( $prop['price'] ) ); ?> / night</p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    (function(){
        var mapContainer = document.getElementById('<?php echo esc_js( $map_id ); ?>');
        var listContainer = document.getElementById('<?php echo esc_js( $list_id ); ?>');
        var properties = <?php echo wp_json_encode( $properties ); ?>;
        var centerLat = <?php echo floatval( $center_lat ); ?>;
        var centerLng = <?php echo floatval( $center_lng ); ?>;
        var map, markers = [];

        function initMap() {
            map = new google.maps.Map(mapContainer, {
                zoom: 12,
                center: { lat: centerLat, lng: centerLng },
                styles: [
                    { featureType: 'all', elementType: 'labels.text.fill', stylers: [{ color: '#616161' }] },
                    { featureType: 'all', elementType: 'labels.text.stroke', stylers: [{ color: '#f5f5f5' }] },
                    { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#bdbdbd' }] },
                ]
            });

            properties.forEach(function(prop, idx) {
                var marker = new google.maps.Marker({
                    position: { lat: prop.lat, lng: prop.lng },
                    map: map,
                    title: prop.title,
                    label: {
                        text: '৳' + Math.floor(prop.price),
                        fontSize: '12px',
                        fontWeight: 'bold',
                        color: '#fff'
                    }
                });

                var infoContent = '<div class="gmap-info-window" style="padding:12px;max-width:200px;">' +
                    '<h4 style="margin:0 0 8px;font-size:14px;"><a href="' + prop.url + '" target="_blank">' + prop.title + '</a></h4>' +
                    (prop.thumb ? '<img src="' + prop.thumb + '" alt="" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px;" />' : '') +
                    (prop.location ? '<p style="margin:0 0 4px;font-size:12px;color:#6b7280;">📍 ' + prop.location + '</p>' : '') +
                    '<p style="margin:0;font-size:13px;font-weight:600;color:#FF385C;">৳' + Math.floor(prop.price) + ' / night</p>' +
                    '<a href="' + prop.url + '" target="_blank" style="display:inline-block;margin-top:8px;padding:6px 12px;background:#FF385C;color:#fff;text-decoration:none;border-radius:4px;font-size:12px;">View</a>' +
                    '</div>';

                var infoWindow = new google.maps.InfoWindow({ content: infoContent });

                marker.addListener('click', function() {
                    markers.forEach(function(m) { m.infoWindow && m.infoWindow.close(); });
                    infoWindow.open(map, marker);
                    marker.infoWindow = infoWindow;
                });

                marker.infoWindow = infoWindow;
                markers.push(marker);
            });
        }

        // Toggle map/list view
        document.querySelectorAll('.map-view-toggle .toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var mode = this.dataset.mode;
                document.querySelectorAll('.map-view-toggle .toggle-btn').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');

                if (mode === 'map') {
                    mapContainer.classList.remove('hidden');
                    listContainer.classList.add('hidden');
                    if (!map) initMap();
                    setTimeout(function() { google.maps.event.trigger(map, 'resize'); }, 100);
                } else {
                    mapContainer.classList.add('hidden');
                    listContainer.classList.remove('hidden');
                }
            });
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof google !== 'undefined' && google.maps) {
                initMap();
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rently_map_search', 'ordivorently_map_search_render' );
