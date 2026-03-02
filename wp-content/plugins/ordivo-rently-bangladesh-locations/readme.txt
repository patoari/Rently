=== Ordivo Rently Bangladesh Locations Installer ===
Contributors: Ordivo
Tags: bangladesh, locations, divisions, districts, installer
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later

Automatically installs all divisions, districts, and sub-districts of Bangladesh.

== Description ==

This plugin automatically adds all administrative divisions, districts, and sub-districts (upazilas) of Bangladesh to your Rently property system.

Includes:
* 8 Divisions
* 64 Districts
* 490+ Sub-districts (Upazilas)

Village/Ward/Road and House numbers can be added manually as needed.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ordivo-rently-bangladesh-locations/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. All locations will be automatically installed
4. You can deactivate the plugin after installation (locations will remain)

== Divisions Included ==

1. Dhaka Division (13 districts)
2. Chittagong Division (11 districts)
3. Rajshahi Division (8 districts)
4. Khulna Division (10 districts)
5. Barisal Division (6 districts)
6. Sylhet Division (4 districts)
7. Rangpur Division (8 districts)
8. Mymensingh Division (4 districts)

== Districts by Division ==

Dhaka Division:
- Dhaka, Faridpur, Gazipur, Gopalganj, Kishoreganj, Madaripur, Manikganj, Munshiganj, Narayanganj, Narsingdi, Rajbari, Shariatpur, Tangail

Chittagong Division:
- Bandarban, Brahmanbaria, Chandpur, Chittagong, Comilla, Cox's Bazar, Feni, Khagrachhari, Lakshmipur, Noakhali, Rangamati

Rajshahi Division:
- Bogra, Joypurhat, Naogaon, Natore, Chapainawabganj, Pabna, Rajshahi, Sirajganj

Khulna Division:
- Bagerhat, Chuadanga, Jessore, Jhenaidah, Khulna, Kushtia, Magura, Meherpur, Narail, Satkhira

Barisal Division:
- Barguna, Barisal, Bhola, Jhalokati, Patuakhali, Pirojpur

Sylhet Division:
- Habiganj, Moulvibazar, Sunamganj, Sylhet

Rangpur Division:
- Dinajpur, Gaibandha, Kurigram, Lalmonirhat, Nilphamari, Panchagarh, Rangpur, Thakurgaon

Mymensingh Division:
- Jamalpur, Mymensingh, Netrokona, Sherpur

== Usage ==

After activation:
1. Go to Properties > Location Manager
2. Browse through all divisions, districts, and sub-districts
3. Add Village/Ward/Road manually for specific areas
4. Add House numbers under Village/Ward/Road as needed

== Example Location Structure ==

```
Dhaka (Division)
  └── Dhaka (District)
      └── Dhamrai (Sub-district)
          └── [Add Village/Ward/Road manually]
              └── [Add House No manually]
```

== Adding Manual Locations ==

Village/Ward/Road:
1. Go to Properties > Location Manager
2. Select "Village/Ward/Road" level
3. Select Division, District, Sub-district
4. Enter name (e.g., "Road 11", "Ward 5")
5. Click Add Location

House Numbers:
1. Select "House No" level
2. Select Division, District, Sub-district, Village/Ward/Road
3. Enter house number (e.g., "House 25")
4. Click Add Location

== Frequently Asked Questions ==

= Can I deactivate the plugin after installation? =
Yes! Once activated, all locations are permanently added to your database. You can safely deactivate or delete the plugin.

= What if I already have some locations? =
The plugin checks for existing locations and only adds new ones. Existing locations won't be duplicated.

= Can I add more sub-districts? =
Yes, you can manually add any missing sub-districts through Properties > Location Manager.

= How do I add villages and house numbers? =
These must be added manually through Properties > Location Manager as they vary by property.

= Can I delete locations I don't need? =
Yes, go to Properties > Locations and delete any locations you don't want to use.

== Changelog ==

= 1.0.0 =
* Initial release
* 8 Divisions
* 64 Districts
* 490+ Sub-districts (Upazilas)
* Automatic installation on activation
* Duplicate prevention
* Installation statistics
