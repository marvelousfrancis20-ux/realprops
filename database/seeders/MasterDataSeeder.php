<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\State;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private const STATES = [
        'Lagos' => 'LG',
        'Abuja FCT' => 'FC',
        'Rivers' => 'RV',
        'Kano' => 'KN',
        'Oyo' => 'OY',
        'Enugu' => 'EN',
        'Edo' => 'ED',
        'Kaduna' => 'KD',
        'Akwa Ibom' => 'AK',
        'Cross River' => 'CR',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const CITIES = [
        'Lagos' => ['Lagos', 'Ikeja', 'Victoria Island', 'Lekki', 'Yaba'],
        'Abuja FCT' => ['Abuja', 'Gwarinpa', 'Maitama', 'Asokoro', 'Wuse'],
        'Rivers' => ['Port Harcourt', 'Obio/Akpor', 'Eleme', 'Ikwerre', 'Degema'],
        'Kano' => ['Kano', 'Dala', 'Gwale', 'Nassarawa', 'Fagge'],
        'Oyo' => ['Ibadan', 'Oyo', 'Ogbomoso', 'Iseyin', 'Ekiti'],
        'Enugu' => ['Enugu', 'Nsukka', 'Udi', 'Oji River', 'Agbani'],
        'Edo' => ['Benin City', 'Auchi', 'Ekpoma', 'Igarra', 'Ubiaja'],
        'Kaduna' => ['Kaduna', 'Zaria', 'Kafanchan', 'Sabon Gari', 'Chikun'],
        'Akwa Ibom' => ['Uyo', 'Eket', 'Ikot Ekpene', 'Oron', 'Abak'],
        'Cross River' => ['Calabar', 'Ikom', 'Ogoja', 'Takum', 'Obudu'],
    ];

    /**
     * @var list<string>
     */
    private const TYPES = [
        'Apartment', 'Duplex', 'Bungalow', 'Villa', 'Terrace',
        'Penthouse', 'Studio', 'Mini Flat', 'Mansion', 'Land',
    ];

    /**
     * @var list<string>
     */
    private const AMENITIES = [
        'Swimming Pool', 'Gym', 'Parking', 'Security', 'Garden',
        '24/7 Electricity', 'Water Supply', 'Air Conditioning', 'WiFi', 'CCTV',
        'Fitted Kitchen', 'Wardrobe', 'Balcony', 'Borehole', 'Solar Power',
        'Generator', 'Elevator', 'Playground', 'Laundry Room', 'Study Room',
        'Fireplace', 'Rooftop Terrace', 'Gated Estate', 'Serviced', 'Furnished',
        'Pet Friendly', 'Boys Quarters', 'Cellar', 'Tennis Court', 'Basketball Court',
    ];

    public function run(): void
    {
        foreach (self::STATES as $name => $code) {
            State::firstOrCreate(['name' => $name], ['code' => $code, 'country' => 'Nigeria']);
        }

        foreach (self::CITIES as $stateName => $cities) {
            $state = State::where('name', $stateName)->first();

            foreach ($cities as $cityName) {
                City::firstOrCreate(
                    ['name' => $cityName, 'state_id' => $state->id],
                    ['latitude' => null, 'longitude' => null, 'is_active' => true]
                );
            }
        }

        foreach (self::TYPES as $index => $name) {
            PropertyType::firstOrCreate(
                ['name' => $name],
                ['description' => "{$name} listings", 'is_active' => true, 'sort_order' => $index]
            );
        }

        foreach (self::AMENITIES as $name) {
            Amenity::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
