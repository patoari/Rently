<?php
/**
 * Plugin Name: Ordivo Rently Bangladesh Locations Installer
 * Description: Automatically installs all divisions, districts, and sub-districts of Bangladesh
 * Version: 1.0.0
 * Author: Ordivo
 */

if (!defined('ABSPATH')) exit;

class Rently_Bangladesh_Locations_Installer {
    
    public static function activate() {
        self::install_locations();
    }
    
    public static function install_locations() {
        $locations = self::get_bangladesh_locations();
        
        $stats = [
            'divisions' => 0,
            'districts' => 0,
            'subdistricts' => 0,
            'skipped' => 0
        ];
        
        foreach ($locations as $division_name => $division_data) {
            // Create Division
            $division_term = term_exists($division_name, 'location');
            
            if (!$division_term) {
                $division_term = wp_insert_term($division_name, 'location');
                if (!is_wp_error($division_term)) {
                    update_term_meta($division_term['term_id'], 'location_level', 'division');
                    $stats['divisions']++;
                }
            } else {
                $stats['skipped']++;
            }
            
            $division_id = is_array($division_term) ? $division_term['term_id'] : $division_term;
            
            // Create Districts
            foreach ($division_data['districts'] as $district_name => $subdistricts) {
                $district_term = term_exists($district_name, 'location');
                
                if (!$district_term) {
                    $district_term = wp_insert_term($district_name, 'location', [
                        'parent' => $division_id
                    ]);
                    if (!is_wp_error($district_term)) {
                        update_term_meta($district_term['term_id'], 'location_level', 'district');
                        $stats['districts']++;
                    }
                } else {
                    $stats['skipped']++;
                }
                
                $district_id = is_array($district_term) ? $district_term['term_id'] : $district_term;
                
                // Create Sub-districts
                foreach ($subdistricts as $subdistrict_name) {
                    $subdistrict_term = term_exists($subdistrict_name, 'location');
                    
                    if (!$subdistrict_term) {
                        $subdistrict_term = wp_insert_term($subdistrict_name, 'location', [
                            'parent' => $district_id
                        ]);
                        if (!is_wp_error($subdistrict_term)) {
                            update_term_meta($subdistrict_term['term_id'], 'location_level', 'sub_district');
                            $stats['subdistricts']++;
                        }
                    } else {
                        $stats['skipped']++;
                    }
                }
            }
        }
        
        // Store installation status
        update_option('rently_bd_locations_installed', true);
        update_option('rently_bd_locations_stats', $stats);
        update_option('rently_bd_locations_install_date', current_time('mysql'));
    }
    
    public static function get_bangladesh_locations() {
        return [
            'Dhaka' => [
                'districts' => [
                    'Dhaka' => ['Dhamrai', 'Dohar', 'Keraniganj', 'Nawabganj', 'Savar'],
                    'Faridpur' => ['Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Faridpur Sadar', 'Madhukhali', 'Nagarkanda', 'Sadarpur', 'Saltha'],
                    'Gazipur' => ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
                    'Gopalganj' => ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara'],
                    'Kishoreganj' => ['Austagram', 'Bajitpur', 'Bhairab', 'Hossainpur', 'Itna', 'Karimganj', 'Katiadi', 'Kishoreganj Sadar', 'Kuliarchar', 'Mithamain', 'Nikli', 'Pakundia', 'Tarail'],
                    'Madaripur' => ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar'],
                    'Manikganj' => ['Manikganj Sadar', 'Daulatpur', 'Ghior', 'Harirampur', 'Saturia', 'Shibalaya', 'Singair'],
                    'Munshiganj' => ['Gazaria', 'Lohajang', 'Munshiganj Sadar', 'Sirajdikhan', 'Sreenagar', 'Tongibari'],
                    'Narayanganj' => ['Araihazar', 'Bandar', 'Narayanganj Sadar', 'Rupganj', 'Sonargaon'],
                    'Narsingdi' => ['Belabo', 'Monohardi', 'Narsingdi Sadar', 'Palash', 'Raipura', 'Shibpur'],
                    'Rajbari' => ['Baliakandi', 'Goalandaghat', 'Pangsha', 'Rajbari Sadar', 'Kalukhali'],
                    'Shariatpur' => ['Bhedarganj', 'Damudya', 'Gosairhat', 'Naria', 'Shariatpur Sadar', 'Zajira'],
                    'Tangail' => ['Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur', 'Tangail Sadar', 'Dhanbari']
                ]
            ],
            'Chittagong' => [
                'districts' => [
                    'Bandarban' => ['Bandarban Sadar', 'Thanchi', 'Lama', 'Naikhongchhari', 'Ali Kadam', 'Rowangchhari', 'Ruma'],
                    'Brahmanbaria' => ['Brahmanbaria Sadar', 'Kasba', 'Nasirnagar', 'Sarail', 'Ashuganj', 'Akhaura', 'Nabinagar', 'Bancharampur', 'Bijoynagar'],
                    'Chandpur' => ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Haziganj', 'Kachua', 'Matlab Dakshin', 'Matlab Uttar', 'Shahrasti'],
                    'Chittagong' => ['Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
                    'Comilla' => ['Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Muradnagar', 'Nangalkot', 'Comilla Sadar', 'Meghna', 'Monohargonj', 'Titas'],
                    'Cox\'s Bazar' => ['Chakaria', 'Cox\'s Bazar Sadar', 'Kutubdia', 'Maheshkhali', 'Ramu', 'Teknaf', 'Ukhia', 'Pekua'],
                    'Feni' => ['Chhagalnaiya', 'Daganbhuiyan', 'Feni Sadar', 'Parshuram', 'Sonagazi', 'Fulgazi'],
                    'Khagrachhari' => ['Dighinala', 'Khagrachhari Sadar', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh'],
                    'Lakshmipur' => ['Lakshmipur Sadar', 'Raipur', 'Ramganj', 'Ramgati', 'Kamalnagar'],
                    'Noakhali' => ['Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Noakhali Sadar', 'Senbagh', 'Sonaimuri', 'Subarnachar', 'Kabirhat'],
                    'Rangamati' => ['Bagaichhari', 'Barkal', 'Kawkhali', 'Belaichhari', 'Kaptai', 'Juraichhari', 'Langadu', 'Naniarchar', 'Rajasthali', 'Rangamati Sadar']
                ]
            ],
            'Rajshahi' => [
                'districts' => [
                    'Bogra' => ['Adamdighi', 'Bogra Sadar', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Shajahanpur', 'Sherpur', 'Shibganj', 'Sonatala'],
                    'Joypurhat' => ['Akkelpur', 'Joypurhat Sadar', 'Kalai', 'Khetlal', 'Panchbibi'],
                    'Naogaon' => ['Atrai', 'Badalgachhi', 'Manda', 'Dhamoirhat', 'Mohadevpur', 'Naogaon Sadar', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'],
                    'Natore' => ['Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Natore Sadar', 'Singra'],
                    'Chapainawabganj' => ['Bholahat', 'Gomastapur', 'Nachole', 'Chapainawabganj Sadar', 'Shibganj'],
                    'Pabna' => ['Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Pabna Sadar', 'Santhia', 'Sujanagar'],
                    'Rajshahi' => ['Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
                    'Sirajganj' => ['Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Sirajganj Sadar', 'Tarash', 'Ullahpara']
                ]
            ]
        ];
    }
}

// Continue with more divisions...


// Continue adding remaining divisions
add_filter('rently_bangladesh_locations_data', function($locations) {
    $locations['Khulna'] = [
        'districts' => [
            'Bagerhat' => ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola'],
            'Chuadanga' => ['Alamdanga', 'Chuadanga Sadar', 'Damurhuda', 'Jibannagar'],
            'Jessore' => ['Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Jessore Sadar', 'Manirampur', 'Sharsha'],
            'Jhenaidah' => ['Harinakunda', 'Jhenaidah Sadar', 'Kaliganj', 'Kotchandpur', 'Maheshpur', 'Shailkupa'],
            'Khulna' => ['Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsa', 'Terokhada'],
            'Kushtia' => ['Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Kushtia Sadar', 'Mirpur'],
            'Magura' => ['Magura Sadar', 'Mohammadpur', 'Shalikha', 'Sreepur'],
            'Meherpur' => ['Gangni', 'Meherpur Sadar', 'Mujibnagar'],
            'Narail' => ['Kalia', 'Lohagara', 'Narail Sadar'],
            'Satkhira' => ['Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Satkhira Sadar', 'Shyamnagar', 'Tala']
        ]
    ];
    
    $locations['Barisal'] = [
        'districts' => [
            'Barguna' => ['Amtali', 'Bamna', 'Barguna Sadar', 'Betagi', 'Patharghata', 'Taltali'],
            'Barisal' => ['Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gaurnadi', 'Hizla', 'Barisal Sadar', 'Mehendiganj', 'Muladi', 'Wazirpur'],
            'Bhola' => ['Bhola Sadar', 'Burhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin'],
            'Jhalokati' => ['Jhalokati Sadar', 'Kathalia', 'Nalchity', 'Rajapur'],
            'Patuakhali' => ['Bauphal', 'Dashmina', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Patuakhali Sadar', 'Rangabali', 'Dumki'],
            'Pirojpur' => ['Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad', 'Pirojpur Sadar', 'Indurkani']
        ]
    ];
    
    $locations['Sylhet'] = [
        'districts' => [
            'Habiganj' => ['Ajmiriganj', 'Bahubal', 'Baniyachong', 'Chunarughat', 'Habiganj Sadar', 'Lakhai', 'Madhabpur', 'Nabiganj', 'Sayestaganj'],
            'Moulvibazar' => ['Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Moulvibazar Sadar', 'Rajnagar', 'Sreemangal'],
            'Sunamganj' => ['Bishwamvarpur', 'Chhatak', 'Derai', 'Dharamapasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Sullah', 'Sunamganj Sadar', 'Tahirpur'],
            'Sylhet' => ['Balaganj', 'Beanibazar', 'Bishwanath', 'Companigonj', 'Fenchuganj', 'Golapganj', 'Gowainghat', 'Jaintiapur', 'Kanaighat', 'Sylhet Sadar', 'Zakiganj', 'Dakshin Surma']
        ]
    ];
    
    $locations['Rangpur'] = [
        'districts' => [
            'Dinajpur' => ['Birampur', 'Birganj', 'Biral', 'Bochaganj', 'Chirirbandar', 'Phulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Dinajpur Sadar', 'Nawabganj', 'Parbatipur'],
            'Gaibandha' => ['Fulchhari', 'Gaibandha Sadar', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Saghata', 'Sundarganj'],
            'Kurigram' => ['Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Phulbari', 'Kurigram Sadar', 'Nageshwari', 'Rajarhat', 'Raomari', 'Ulipur'],
            'Lalmonirhat' => ['Aditmari', 'Hatibandha', 'Kaliganj', 'Lalmonirhat Sadar', 'Patgram'],
            'Nilphamari' => ['Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Nilphamari Sadar', 'Saidpur'],
            'Panchagarh' => ['Atwari', 'Boda', 'Debiganj', 'Panchagarh Sadar', 'Tetulia'],
            'Rangpur' => ['Badarganj', 'Gangachhara', 'Kaunia', 'Rangpur Sadar', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj'],
            'Thakurgaon' => ['Baliadangi', 'Haripur', 'Pirganj', 'Ranisankail', 'Thakurgaon Sadar']
        ]
    ];
    
    $locations['Mymensingh'] = [
        'districts' => [
            'Jamalpur' => ['Baksiganj', 'Dewanganj', 'Islampur', 'Jamalpur Sadar', 'Madarganj', 'Melandaha', 'Sarishabari'],
            'Mymensingh' => ['Bhaluka', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur', 'Haluaghat', 'Ishwarganj', 'Mymensingh Sadar', 'Muktagachha', 'Nandail', 'Phulpur', 'Trishal', 'Tara Khanda'],
            'Netrokona' => ['Atpara', 'Barhatta', 'Durgapur', 'Khaliajuri', 'Kalmakanda', 'Kendua', 'Madan', 'Mohanganj', 'Netrokona Sadar', 'Purbadhala'],
            'Sherpur' => ['Jhenaigati', 'Nakla', 'Nalitabari', 'Sherpur Sadar', 'Sreebardi']
        ]
    ];
    
    return $locations;
});

register_activation_hook(__FILE__, ['Rently_Bangladesh_Locations_Installer', 'activate']);

// Admin notice
add_action('admin_notices', function() {
    if (get_option('rently_bd_locations_installed') && !get_option('rently_bd_locations_notice_dismissed')) {
        $stats = get_option('rently_bd_locations_stats', []);
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Bangladesh Locations Installer:</strong> Successfully installed!</p>
            <ul>
                <li>Divisions: <?php echo $stats['divisions'] ?? 0; ?></li>
                <li>Districts: <?php echo $stats['districts'] ?? 0; ?></li>
                <li>Sub-districts: <?php echo $stats['subdistricts'] ?? 0; ?></li>
                <?php if (($stats['skipped'] ?? 0) > 0): ?>
                    <li>Skipped (already existed): <?php echo $stats['skipped']; ?></li>
                <?php endif; ?>
            </ul>
            <p>Go to <a href="<?php echo admin_url('edit.php?post_type=property&page=rently-locations'); ?>">Properties > Location Manager</a> to view all locations.</p>
            <p>You can now deactivate this plugin. Village/Ward/Road and House numbers can be added manually.</p>
        </div>
        <?php
        update_option('rently_bd_locations_notice_dismissed', true);
    }
});
