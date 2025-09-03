<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Representante;
use App\Models\CategoriaMaquinaria;
use App\Models\TipoMaquinaria;
use App\Models\Mantenimiento;
use App\Models\Solicitud;
use App\Models\DetalleSolicitud;
use App\Models\Empleado;
use App\Models\SolicitudEmpleado;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'rol' => 'admin'
        ]);

        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'rol' => 'empleado'
        ]);

        User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'rol' => 'empleado'
        ]);

        // Crear empresas
        $empresa1 = Empresa::create([
            'nombre' => 'Argos S.A.',
            'nit' => '900123456-1',
            'direccion' => 'Calle 123 #45-67, Bogotá',
            'telefono' => '601-1234567',
            'email' => 'contacto@argos.com'
        ]);

        $empresa2 = Empresa::create([
            'nombre' => 'Constructora ABC',
            'nit' => '800987654-2',
            'direccion' => 'Carrera 78 #90-12, Medellín',
            'telefono' => '604-9876543',
            'email' => 'info@abc.com'
        ]);

        $empresa3 = Empresa::create([
            'nombre' => 'Ingeniería XYZ',
            'nit' => '700555666-3',
            'direccion' => 'Avenida 5 #23-45, Cali',
            'telefono' => '602-5556667',
            'email' => 'contacto@xyz.com'
        ]);

        // Crear representantes
        Representante::create([
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'documento' => '12345678',
            'telefono' => '300-1111111',
            'email' => 'carlos@argos.com',
            'empresa_id' => $empresa1->id
        ]);

        Representante::create([
            'nombre' => 'Ana',
            'apellido' => 'López',
            'documento' => '87654321',
            'telefono' => '300-2222222',
            'email' => 'ana@abc.com',
            'empresa_id' => $empresa2->id
        ]);

        // Crear categorías de maquinaria
        $categoriaPesada = CategoriaMaquinaria::create([
            'nombre' => 'Maquinaria Pesada',
            'descripcion' => 'Equipos de construcción pesados'
        ]);

        $categoriaLigera = CategoriaMaquinaria::create([
            'nombre' => 'Maquinaria Ligera',
            'descripcion' => 'Equipos de construcción ligeros'
        ]);

        // Crear tipos de maquinaria
        $retroexcavadora = TipoMaquinaria::create([
            'nombre' => 'Retroexcavadora',
            'descripcion' => 'Equipo para excavación y carga',
            'categoria_id' => $categoriaPesada->id
        ]);

        $excavadora = TipoMaquinaria::create([
            'nombre' => 'Excavadora',
            'descripcion' => 'Equipo para excavación profunda',
            'categoria_id' => $categoriaPesada->id
        ]);

        $bulldozer = TipoMaquinaria::create([
            'nombre' => 'Bulldozer',
            'descripcion' => 'Equipo para movimiento de tierra',
            'categoria_id' => $categoriaPesada->id
        ]);

        $vibrador = TipoMaquinaria::create([
            'nombre' => 'Vibrador de Concreto',
            'descripcion' => 'Equipo para compactación de concreto',
            'categoria_id' => $categoriaLigera->id
        ]);

        // Crear mantenimientos
        $mantenimiento1 = Mantenimiento::create([
            'codigo' => 'MANT-001',
            'nombre' => 'Mantenimiento Preventivo Retroexcavadora',
            'descripcion' => 'Mantenimiento preventivo completo de retroexcavadora',
            'costo' => 1500000,
            'tipo_maquinaria_id' => $retroexcavadora->id
        ]);

        $mantenimiento2 = Mantenimiento::create([
            'codigo' => 'MANT-002',
            'nombre' => 'Reparación Sistema Hidráulico',
            'descripcion' => 'Reparación del sistema hidráulico de excavadora',
            'costo' => 2500000,
            'tipo_maquinaria_id' => $excavadora->id
        ]);

        $mantenimiento3 = Mantenimiento::create([
            'codigo' => 'MANT-003',
            'nombre' => 'Cambio de Aceite y Filtros',
            'descripcion' => 'Cambio de aceite y filtros de bulldozer',
            'costo' => 800000,
            'tipo_maquinaria_id' => $bulldozer->id
        ]);

        $mantenimiento4 = Mantenimiento::create([
            'codigo' => 'MANT-004',
            'nombre' => 'Calibración Vibrador',
            'descripcion' => 'Calibración y ajuste de vibrador de concreto',
            'costo' => 300000,
            'tipo_maquinaria_id' => $vibrador->id
        ]);

        // Crear empleados
        $empleado1 = Empleado::create([
            'nombre' => 'Pedro',
            'apellido' => 'González',
            'documento' => '1057896547',
            'email' => 'pedro@empresa.com',
            'direccion' => 'Calle 45 #67-89, Bogotá',
            'telefono' => '300-3333333',
            'rol' => 'empleado'
        ]);

        $empleado2 = Empleado::create([
            'nombre' => 'Laura',
            'apellido' => 'Martínez',
            'documento' => '9876543210',
            'email' => 'laura@empresa.com',
            'direccion' => 'Carrera 12 #34-56, Medellín',
            'telefono' => '300-4444444',
            'rol' => 'supervisor'
        ]);

        // Crear solicitudes
        $solicitud1 = Solicitud::create([
            'codigo' => 'SOL-001',
            'fecha_solicitud' => '2023-10-15',
            'estado' => 'pendiente',
            'observaciones' => 'Mantenimiento urgente requerido',
            'empresa_id' => $empresa1->id
        ]);

        $solicitud2 = Solicitud::create([
            'codigo' => 'SOL-002',
            'fecha_solicitud' => '2023-10-20',
            'estado' => 'en_proceso',
            'observaciones' => 'Mantenimiento programado',
            'empresa_id' => $empresa2->id
        ]);

        $solicitud3 = Solicitud::create([
            'codigo' => 'SOL-003',
            'fecha_solicitud' => '2023-10-25',
            'estado' => 'completada',
            'observaciones' => 'Mantenimiento completado exitosamente',
            'empresa_id' => $empresa1->id
        ]);

        // Crear detalles de solicitud
        DetalleSolicitud::create([
            'solicitud_id' => $solicitud1->id,
            'mantenimiento_id' => $mantenimiento1->id,
            'cantidad_maquinas' => 2,
            'costo_total' => 3000000
        ]);

        DetalleSolicitud::create([
            'solicitud_id' => $solicitud2->id,
            'mantenimiento_id' => $mantenimiento2->id,
            'cantidad_maquinas' => 1,
            'costo_total' => 2500000
        ]);

        DetalleSolicitud::create([
            'solicitud_id' => $solicitud3->id,
            'mantenimiento_id' => $mantenimiento3->id,
            'cantidad_maquinas' => 3,
            'costo_total' => 2400000
        ]);

        // Asignar empleados a solicitudes
        SolicitudEmpleado::create([
            'solicitud_id' => $solicitud1->id,
            'empleado_id' => $empleado1->id,
            'estado' => 'asignado'
        ]);

        SolicitudEmpleado::create([
            'solicitud_id' => $solicitud1->id,
            'empleado_id' => $empleado2->id,
            'estado' => 'en_proceso'
        ]);

        SolicitudEmpleado::create([
            'solicitud_id' => $solicitud2->id,
            'empleado_id' => $empleado1->id,
            'estado' => 'completado'
        ]);
    }
}
