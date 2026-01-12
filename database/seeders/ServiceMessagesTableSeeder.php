<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceMessagesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('service_messages')->insert([
            [
                'message' => '¿Necesitas datos de matrícula escolar para tu investigación?','service' => 'Datos de Matrícula','boton' => 'Solicitar info','whatsapp' => 'Hola, quiero información sobre matrícula escolar.', 'created_at'=>now(), 'updated_at'=>now()
            ],
            [
                'message' => 'Solicita reportes de infraestructura educativa actualizados.','service' => 'Infraestructura','boton' => 'Pedir Reporte','whatsapp' => 'Hola, me interesa un reporte de infraestructura.', 'created_at'=>now(), 'updated_at'=>now()
            ],
            [
                'message' => '¿Buscas estadísticas de abandono escolar?','service' => 'Estadísticas de Abandono','boton' => 'Contactar','whatsapp' => 'Hola, deseo estadísticas de abandono escolar.', 'created_at'=>now(), 'updated_at'=>now()
            ],
            [
                'message' => 'Accede a datos de conectividad y tecnología en colegios.','service' => 'Conectividad','boton' => 'Solicitar info','whatsapp' => 'Hola, quiero saber sobre conectividad en colegios.', 'created_at'=>now(), 'updated_at'=>now()
            ],
            [
                'message' => '¿Eres periodista? Solicita datos oficiales para tu reportaje.','service' => 'Atención a Prensa','boton' => 'Solicitar','whatsapp' => 'Hola, soy periodista y requiero datos oficiales.', 'created_at'=>now(), 'updated_at'=>now()
            ],
        ]);
    }
}