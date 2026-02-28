/**
 * Rently Theme Main JavaScript
 * 
 * @package Rently_Theme
 */

(function($) {
    'use strict';
    
    /**
     * Document Ready
     */
    $(document).ready(function() {
        
        // Initialize components
        initPropertySearch();
        initMobileMenu();
        
    });
    
    /**
     * Property Search with AJAX
     */
    function initPropertySearch() {
        const searchForm = $('#property-search-form');
        
        if (!searchForm.length) {
            return;
        }
        
        searchForm.on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                action: 'rently_search_properties',
                nonce: rentlyAjax.nonce,
                location: $('#search-location').val(),
                min_price: $('#min-price').val(),
                max_price: $('#max-price').val(),
                guests: $('#search-guests').val()
            };
            
            // Show loading
            $('.properties-grid').html('<div class="loading">Searching...</div>');
            
            // AJAX request
            $.ajax({
                url: rentlyAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        displayProperties(response.data.properties);
                    } else {
                        $('.properties-grid').html('<p>No properties found.</p>');
                    }
                },
                error: function() {
                    $('.properties-grid').html('<p>Error loading properties.</p>');
                }
            });
        });
    }
    
    /**
     * Display Properties
     */
    function displayProperties(properties) {
        const grid = $('.properties-grid');
        grid.empty();
        
        if (properties.length === 0) {
            grid.html('<p>No properties found matching your criteria.</p>');
            return;
        }
        
        properties.forEach(function(property) {
            const card = `
                <div class="property-card">
                    <a href="${property.permalink}">
                        <div class="property-image">
                            <img src="${property.image || ''}" alt="${property.title}">
                        </div>
                        <div class="property-info">
                            <h3>${property.title}</h3>
                            <p class="property-location">📍 ${property.location}</p>
                            <div class="property-meta">
                                <span>🛏️ ${property.rooms} rooms</span>
                                <span>👥 ${property.guests} guests</span>
                            </div>
                            <p class="property-price">৳${property.price} <span>/night</span></p>
                        </div>
                    </a>
                </div>
            `;
            grid.append(card);
        });
    }
    
    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = $('.mobile-menu-toggle');
        const nav = $('.main-nav');
        
        if (!menuToggle.length) {
            return;
        }
        
        menuToggle.on('click', function() {
            $(this).toggleClass('active');
            nav.toggleClass('active');
        });
    }
    
})(jQuery);
