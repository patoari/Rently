<div class="wrap">
    <h1>Location Manager</h1>
    <p>Manage hierarchical locations: Division > District > Sub-district > Village/Ward/Road > House No</p>
    
    <div class="rently-locations-container">
        <div class="rently-locations-form">
            <h2>Add New Location</h2>
            
            <form id="rently-add-location-form">
                <table class="form-table">
                    <tr>
                        <th><label for="location-level">Location Level</label></th>
                        <td>
                            <select id="location-level" name="level" required>
                                <option value="">Select Level</option>
                                <option value="division">Division</option>
                                <option value="district">District</option>
                                <option value="sub_district">Sub-district</option>
                                <option value="village">Village/Ward/Road</option>
                                <option value="house">House No</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr id="parent-division-row" style="display:none;">
                        <th><label for="parent-division">Parent Division</label></th>
                        <td>
                            <select id="parent-division" name="parent_division">
                                <option value="0">Select Division</option>
                                <?php
                                $divisions = get_terms([
                                    'taxonomy' => 'location',
                                    'parent' => 0,
                                    'hide_empty' => false,
                                    'meta_query' => [
                                        [
                                            'key' => 'location_level',
                                            'value' => 'division'
                                        ]
                                    ]
                                ]);
                                foreach ($divisions as $division) {
                                    echo '<option value="' . $division->term_id . '">' . esc_html($division->name) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr id="parent-district-row" style="display:none;">
                        <th><label for="parent-district">Parent District</label></th>
                        <td>
                            <select id="parent-district" name="parent_district">
                                <option value="0">Select District</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr id="parent-subdistrict-row" style="display:none;">
                        <th><label for="parent-subdistrict">Parent Sub-district</label></th>
                        <td>
                            <select id="parent-subdistrict" name="parent_subdistrict">
                                <option value="0">Select Sub-district</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr id="parent-village-row" style="display:none;">
                        <th><label for="parent-village">Parent Village/Ward/Road</label></th>
                        <td>
                            <select id="parent-village" name="parent_village">
                                <option value="0">Select Village/Ward/Road</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="location-name">Location Name</label></th>
                        <td>
                            <input type="text" id="location-name" name="name" class="regular-text" required />
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">Add Location</button>
                </p>
                
                <div id="location-message" style="display:none;"></div>
            </form>
        </div>
        
        <div class="rently-locations-list">
            <h2>Existing Locations</h2>
            
            <div class="rently-location-browser">
                <div class="location-level">
                    <h3>Divisions</h3>
                    <ul id="divisions-list" class="location-items">
                        <?php
                        $divisions = get_terms([
                            'taxonomy' => 'location',
                            'parent' => 0,
                            'hide_empty' => false,
                            'meta_query' => [
                                [
                                    'key' => 'location_level',
                                    'value' => 'division'
                                ]
                            ]
                        ]);
                        
                        if (empty($divisions)) {
                            echo '<li class="no-items">No divisions yet</li>';
                        } else {
                            foreach ($divisions as $division) {
                                echo '<li class="location-item" data-id="' . $division->term_id . '" data-level="division">';
                                echo '<span class="location-name">' . esc_html($division->name) . '</span>';
                                echo '<span class="location-count">(' . $division->count . ')</span>';
                                echo '<button class="button-link delete-location" data-id="' . $division->term_id . '">Delete</button>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                
                <div class="location-level">
                    <h3>Districts</h3>
                    <ul id="districts-list" class="location-items">
                        <li class="no-items">Select a division</li>
                    </ul>
                </div>
                
                <div class="location-level">
                    <h3>Sub-districts</h3>
                    <ul id="subdistricts-list" class="location-items">
                        <li class="no-items">Select a district</li>
                    </ul>
                </div>
                
                <div class="location-level">
                    <h3>Villages/Wards/Roads</h3>
                    <ul id="villages-list" class="location-items">
                        <li class="no-items">Select a sub-district</li>
                    </ul>
                </div>
                
                <div class="location-level">
                    <h3>House Numbers</h3>
                    <ul id="houses-list" class="location-items">
                        <li class="no-items">Select a village/ward/road</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="rently-locations-help">
        <h3>Quick Guide</h3>
        <ol>
            <li><strong>Add Divisions first</strong> - These are the top-level locations (e.g., Dhaka, Chittagong)</li>
            <li><strong>Add Districts</strong> - Select parent division (e.g., Dhaka District, Cox's Bazar)</li>
            <li><strong>Add Sub-districts</strong> - Select parent district (e.g., Gulshan, Banani)</li>
            <li><strong>Add Villages/Wards/Roads</strong> - Select parent sub-district (e.g., Road 12, Ward 5)</li>
            <li><strong>Add House Numbers</strong> - Select parent village/ward/road (e.g., House 25, Building A)</li>
        </ol>
        
        <p><strong>Note:</strong> You can also manage locations from <a href="<?php echo admin_url('edit-tags.php?taxonomy=location&post_type=property'); ?>">Properties > Locations</a></p>
    </div>
</div>
