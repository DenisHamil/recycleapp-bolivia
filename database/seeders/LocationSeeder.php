<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'La Paz' => [
                'Murillo' => ['La Paz', 'Mecapaca', 'Achocalla', 'El Alto', 'Palca'],
                'Omasuyos' => ['Achacachi', 'Ancoraimes', 'Chua Cocani', 'Huarina', 'Santiago de Huata', 'Huatajata'],
                'Pacajes' => ['Coro Coro', 'Caquiaviri', 'Calacoto', 'Comanche', 'Charaña', 'Waldo Ballivián', 'Nazacara de Pacajes', 'Santiago de Callapa', 'San Pedro de Curahuara'],
                'Caranavi' => ['Caranavi', 'Alto Beni'],
                'Nor Yungas' => ['Coroico', 'Coripata'],
                'Sud Yungas' => ['Chulumani', 'Irupana', 'Yanacachi', 'La Asunta'],
                'Los Andes' => ['Pucarani', 'Laja', 'Batallas', 'Puerto Carabuco', 'Viacha'],
                'Aroma' => ['Sica Sica', 'Umala', 'Ayo Ayo', 'Calamarca', 'Patacamaya', 'Collana', 'Colquencha', 'Colquiri'],
                'Ingavi' => ['Viacha', 'Guaqui', 'Tiahuanacu', 'Desaguadero', 'San Andrés de Machaca', 'Jesús de Machaca', 'Taraco'],
                'Larecaja' => ['Sorata', 'Guanay', 'Tacacoma', 'Quiabaya', 'Combaya', 'Tipuani', 'Mapiri', 'Teoponte'],
                'Franz Tamayo' => ['Apolo', 'Pelechuco'],
                'Bautista Saavedra' => ['Charazani', 'Curva', 'Humanata'],
                'Muñecas' => ['Chuma', 'Ayata', 'Aucapata'],
                'Camacho' => ['Puerto Acosta', 'Mocomoco', 'Puerto Carabuco'],
                'Inquisivi' => ['Inquisivi', 'Quime', 'Cajuata', 'Colquiri', 'Ichoca', 'Villa Libertad', 'Licoma'],
                'Loayza' => ['Luribay', 'Sapahaqui', 'Yaco', 'Malla', 'Cairoma'],
                'Abel Iturralde' => ['Ixiamas', 'San Buenaventura'],
                'Manco Kapac' => ['Copacabana', 'San Pedro de Tiquina', 'Tito Yupanqui']
            ],
            'Cochabamba' => [
                'Cercado' => ['Cochabamba'],
                'Campero' => ['Aiquile', 'Pasorapa', 'Omereque'],
                'Capinota' => ['Capinota', 'Santiváñez', 'Sicaya'],
                'Germán Jordán' => ['Cliza', 'Toco', 'Tolata'],
                'Punata' => ['Punata', 'Villa Rivero', 'San Benito', 'Tacachi'],
                'Quillacollo' => ['Quillacollo', 'Sipe Sipe', 'Tiquipaya', 'Vinto', 'Colcapirhua'],
                'Tapacarí' => ['Tapacarí', 'Arbieto'],
                'Esteban Arze' => ['Tarata', 'Anzaldo', 'Arbieto', 'Sacabamba'],
                'Ayopaya' => ['Independencia', 'Morochata', 'Cocapata'],
                'Mizque' => ['Mizque', 'Vila Vila', 'Alalay'],
                'Chapare' => ['Sacaba', 'Colomi', 'Villa Tunari'],
                'Carrasco' => ['Totora', 'Pojo', 'Pocona', 'Chimoré'],
                'Arque' => ['Arque', 'Tacopaya', 'Bolívar'],
                'Tiraque' => ['Tiraque', 'Shinahota'],
                'Rodríguez' => ['El Puente'],
                'Arani' => ['Arani', 'Vacas']
            ],
            'Santa Cruz' => [
                'Andrés Ibáñez' => ['Santa Cruz de la Sierra', 'La Guardia', 'El Torno', 'Cotoca', 'Porongo'],
                'Warnes' => ['Warnes', 'Okinawa Uno'],
                'Velasco' => ['San Ignacio de Velasco', 'San Miguel de Velasco', 'San Rafael de Velasco'],
                'Ichilo' => ['Buena Vista', 'San Carlos', 'Yapacaní'],
                'Chiquitos' => ['San José de Chiquitos', 'Pailón', 'Roboré'],
                'Sara' => ['Portachuelo', 'Santa Rosa del Sara', 'Colpa Bélgica'],
                'Cordillera' => ['Lagunillas', 'Charagua', 'Cabezas'],
                'Vallegrande' => ['Vallegrande', 'Trigal', 'Moro Moro', 'Postrer Valle', 'Pucará'],
                'Florida' => ['Samaipata', 'Pampa Grande', 'Mairana'],
                'Caballero' => ['Comarapa', 'Saipina'],
                'Germán Busch' => ['Puerto Suárez', 'Puerto Quijarro', 'Carmen Rivero Tórrez'],
                'Guarayos' => ['Ascención de Guarayos', 'Urubichá', 'El Puente'],
                'Ñuflo de Chávez' => ['Concepción', 'San Javier', 'San Ramón', 'San Julián', 'San Antonio de Lomerío', 'Cuatro Cañadas'],
                'Ángel Sandoval' => ['San Matías']
            ],
            'Chuquisaca' => [
                'Oropeza' => ['Sucre', 'Yotala', 'Poroma'],
                'Nor Cinti' => ['Camargo', 'San Lucas'],
                'Sur Cinti' => ['Culpina', 'Las Carreras'],
                'Yamparáez' => ['Tarabuco', 'Yamparáez'],
                'Tomina' => ['Padilla', 'Tomina', 'Sopachuy', 'Villa Alcalá', 'El Villar'],
                'Hernando Siles' => ['Monteagudo', 'Huacareta'],
                'Jaime Zudáñez' => ['Zudáñez', 'Presto', 'Mojocoya', 'Icla'],
                'Belisario Boeto' => ['Villa Serrano'],
                'Azurduy' => ['Azurduy', 'Tarvita'],
                'Luis Calvo' => ['Muyupampa', 'Huacaya', 'Macharetí']
            ],
            'Tarija' => [
                'Cercado' => ['Tarija', 'San Lorenzo'],
                'Arce' => ['Padcaya', 'Bermejo'],
                'Gran Chaco' => ['Yacuiba', 'Caraparí', 'Villamontes'],
                'Avilés' => ['Uriondo', 'Yunchará'],
                'Méndez' => ['Villa San Lorenzo', 'El Puente'],
                'O\'Connor' => ['Entre Ríos']
            ],
            'Beni' => [
                'Cercado' => ['Trinidad'],
                'Vaca Díez' => ['Guayaramerín', 'Riberalta'],
                'José Ballivián' => ['Reyes', 'San Borja', 'Santa Rosa'],
                'Yacuma' => ['Santa Ana del Yacuma', 'Exaltación'],
                'Moxos' => ['San Ignacio', 'Loreto', 'San Andrés'],
                'Mamoré' => ['San Joaquín', 'San Ramón', 'Puerto Siles'],
                'Iténez' => ['Magdalena', 'Baures', 'Huacaraje'],
                'Marbán' => ['Loreto']
            ],
            'Pando' => [
                'Nicolás Suárez' => ['Cobija', 'Porvenir', 'Filadelfia', 'Bolpebra', 'Bella Flor'],
                'Manuripi' => ['Puerto Rico', 'San Pedro', 'Nueva Esperanza'],
                'Abuna' => ['Santa Rosa del Abuná'],
                'Madre de Dios' => ['Puerto Gonzalo Moreno', 'San Lorenzo', 'Sena'],
                'Federico Román' => ['Villa Nueva']
            ],
            'Potosí' => [
                'Tomás Frías' => ['Potosí', 'Tinguipaya', 'Yocalla', 'Urmiri'],
                'Rafael Bustillo' => ['Uncía', 'Chayanta', 'Llallagua', 'Machacamarca'],
                'Cornelio Saavedra' => ['Betanzos', 'Chaqui', 'Tacobamba'],
                'Charcas' => ['San Pedro de Buena Vista', 'Toro Toro'],
                'Chayanta' => ['Colquechaca', 'Ravelo', 'Pocoata', 'Ocurí'],
                'Nor Chichas' => ['Cotagaita', 'Vitichi'],
                'Alonso de Ibáñez' => ['Sacaca', 'Caripuyo'],
                'Sur Chichas' => ['Tupiza', 'Atocha'],
                'Nor Lípez' => ['Colcha K'],
                'Sur Lípez' => ['San Pablo de Lípez', 'Mojinete', 'San Antonio de Esmoruco'],
                'Daniel Campos' => ['Llica', 'Tahua'],
                'Modesto Omiste' => ['Villazón'],
                'Enrique Baldivieso' => ['San Agustín'],
                'Antonio Quijarro' => ['Uyuni', 'Tomave', 'Porco'],
                'José María Linares' => ['Puna', 'Caiza D', 'Ckochas']
            ],
            'Oruro' => [
                'Cercado' => ['Oruro', 'Caracollo'],
                'Pantaleón Dalence' => ['Machacamarca', 'Poopó'],
                'Poopó' => ['Poopó', 'Pazña', 'Antequera'],
                'Sajama' => ['Curahuara de Carangas', 'Turco'],
                'San Pedro de Totora' => ['Totora'],
                'Saucarí' => ['Toledo'],
                'Sebastián Pagador' => ['Santiago de Huari'],
                'Sud Carangas' => ['Santiago de Huayllamarca', 'Belén de Andamarca'],
                'Tomás Barrón' => ['Eucaliptus'],
                'Litoral' => ['Huachacalla', 'Escara', 'Cruz de Machacamarca', 'Yunguyo del Litoral'],
                'Nor Carangas' => ['Huayllamarca'],
                'Ladislao Cabrera' => ['Salinas de Garci Mendoza'],
                'Eduardo Avaroa' => ['Challapata']
            ]
        ];

        foreach ($locations as $department => $provinces) {
            foreach ($provinces as $province => $municipalities) {
                foreach ($municipalities as $municipality) {
                    DB::table('locations')->insert([
                        'id' => Str::uuid(),
                        'department' => $department,
                        'province' => $province,
                        'municipality' => $municipality,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
