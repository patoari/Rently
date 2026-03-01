<?php
/**
 * Bangladesh Location Data
 * Division > District > Thana hierarchy
 */

if (!defined('ABSPATH')) exit;

function rently_get_location_data() {
    return array(
        'dhaka' => array(
            'name' => 'Dhaka',
            'districts' => array(
                'dhaka' => array(
                    'name' => 'Dhaka',
                    'thanas' => array('dhanmondi', 'gulshan', 'banani', 'mohammadpur', 'mirpur', 'uttara', 'motijheel', 'ramna', 'tejgaon', 'badda', 'rampura', 'khilgaon', 'demra')
                ),
                'gazipur' => array(
                    'name' => 'Gazipur',
                    'thanas' => array('gazipur_sadar', 'kaliakair', 'kapasia', 'sreepur', 'kaliganj')
                ),
                'narayanganj' => array(
                    'name' => 'Narayanganj',
                    'thanas' => array('narayanganj_sadar', 'bandar', 'rupganj', 'sonargaon', 'araihazar')
                ),
                'tangail' => array(
                    'name' => 'Tangail',
                    'thanas' => array('tangail_sadar', 'basail', 'bhuapur', 'delduar', 'ghatail', 'kalihati', 'madhupur', 'mirzapur', 'nagarpur', 'sakhipur')
                )
            )
        ),
        'chittagong' => array(
            'name' => 'Chittagong',
            'districts' => array(
                'chittagong' => array(
                    'name' => 'Chittagong',
                    'thanas' => array('panchlaish', 'khulshi', 'halishahar', 'agrabad', 'kotwali', 'pahartali', 'chandgaon', 'bakalia', 'bayazid', 'double_mooring')
                ),
                'coxs_bazar' => array(
                    'name' => "Cox's Bazar",
                    'thanas' => array('coxs_bazar_sadar', 'chakaria', 'kutubdia', 'maheshkhali', 'ramu', 'teknaf', 'ukhia', 'pekua')
                ),
                'comilla' => array(
                    'name' => 'Comilla',
                    'thanas' => array('comilla_sadar', 'barura', 'brahmanpara', 'burichang', 'chandina', 'chauddagram', 'daudkandi', 'debidwar', 'homna', 'laksam', 'muradnagar', 'nangalkot', 'meghna')
                )
            )
        ),
        'rajshahi' => array(
            'name' => 'Rajshahi',
            'districts' => array(
                'rajshahi' => array(
                    'name' => 'Rajshahi',
                    'thanas' => array('boalia', 'rajpara', 'shah_makhdum', 'motihar', 'paba', 'durgapur', 'godagari', 'mohanpur', 'tanore')
                ),
                'bogra' => array(
                    'name' => 'Bogra',
                    'thanas' => array('bogra_sadar', 'adamdighi', 'dhunat', 'gabtali', 'kahaloo', 'nandigram', 'sariakandi', 'shajahanpur', 'sherpur', 'shibganj', 'sonatala')
                )
            )
        ),
        'khulna' => array(
            'name' => 'Khulna',
            'districts' => array(
                'khulna' => array(
                    'name' => 'Khulna',
                    'thanas' => array('daulatpur', 'khalishpur', 'khan_jahan_ali', 'kotwali', 'sonadanga', 'harintana', 'batiaghata', 'dacope', 'dumuria', 'dighalia', 'koyra', 'paikgachha', 'phultala', 'rupsa', 'terokhada')
                ),
                'jessore' => array(
                    'name' => 'Jessore',
                    'thanas' => array('jessore_sadar', 'abhaynagar', 'bagherpara', 'chaugachha', 'jhikargachha', 'keshabpur', 'manirampur', 'sharsha')
                )
            )
        ),
        'barisal' => array(
            'name' => 'Barisal',
            'districts' => array(
                'barisal' => array(
                    'name' => 'Barisal',
                    'thanas' => array('barisal_sadar', 'bakerganj', 'babuganj', 'wazirpur', 'banaripara', 'gournadi', 'agailjhara', 'mehendiganj', 'muladi', 'hizla')
                ),
                'patuakhali' => array(
                    'name' => 'Patuakhali',
                    'thanas' => array('patuakhali_sadar', 'bauphal', 'dashmina', 'galachipa', 'kalapara', 'mirzaganj', 'dumki', 'rangabali')
                )
            )
        ),
        'sylhet' => array(
            'name' => 'Sylhet',
            'districts' => array(
                'sylhet' => array(
                    'name' => 'Sylhet',
                    'thanas' => array('sylhet_sadar', 'beanibazar', 'bishwanath', 'companiganj', 'fenchuganj', 'golapganj', 'gowainghat', 'jaintiapur', 'kanaighat', 'zakiganj', 'south_surma')
                ),
                'moulvibazar' => array(
                    'name' => 'Moulvibazar',
                    'thanas' => array('moulvibazar_sadar', 'barlekha', 'juri', 'kamalganj', 'kulaura', 'rajnagar', 'sreemangal')
                )
            )
        ),
        'rangpur' => array(
            'name' => 'Rangpur',
            'districts' => array(
                'rangpur' => array(
                    'name' => 'Rangpur',
                    'thanas' => array('rangpur_sadar', 'badarganj', 'gangachara', 'kaunia', 'mithapukur', 'pirgachha', 'pirganj', 'taraganj')
                ),
                'dinajpur' => array(
                    'name' => 'Dinajpur',
                    'thanas' => array('dinajpur_sadar', 'birampur', 'birganj', 'biral', 'bochaganj', 'chirirbandar', 'fulbari', 'ghoraghat', 'hakimpur', 'kaharole', 'khansama', 'nawabganj', 'parbatipur')
                )
            )
        ),
        'mymensingh' => array(
            'name' => 'Mymensingh',
            'districts' => array(
                'mymensingh' => array(
                    'name' => 'Mymensingh',
                    'thanas' => array('mymensingh_sadar', 'bhaluka', 'dhobaura', 'fulbaria', 'gaffargaon', 'gauripur', 'haluaghat', 'ishwarganj', 'muktagachha', 'nandail', 'phulpur', 'trishal')
                ),
                'jamalpur' => array(
                    'name' => 'Jamalpur',
                    'thanas' => array('jamalpur_sadar', 'baksiganj', 'dewanganj', 'islampur', 'madarganj', 'melandaha', 'sarishabari')
                )
            )
        )
    );
}
