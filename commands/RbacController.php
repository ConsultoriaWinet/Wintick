<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Limpia todo
        $auth->removeAll();

        /* =========================================================
         *                     PERMISOS
         * ========================================================= */

        // Tickets
        $verTickets = $auth->createPermission('verTickets');
        $verTickets->description = 'Ver tickets';
        $auth->add($verTickets);

        $crearTicket = $auth->createPermission('crearTicket');
        $crearTicket->description = 'Crear tickets';
        $auth->add($crearTicket);

        $actualizarTicket = $auth->createPermission('actualizarTicket');
        $actualizarTicket->description = 'Actualizar tickets';
        $auth->add($actualizarTicket);

        $asignarTicket = $auth->createPermission('asignarTicket');
        $asignarTicket->description = 'Asignar tickets a otros usuarios';
        $auth->add($asignarTicket);

        // Clientes
        $verClientes = $auth->createPermission('verClientes');
        $verClientes->description = 'Ver clientes';
        $auth->add($verClientes);

        $administrarClientes = $auth->createPermission('administrarClientes');
        $administrarClientes->description = 'Crear, editar y eliminar clientes';
        $auth->add($administrarClientes);

        // Reportes
        $verReportes = $auth->createPermission('verReportes');
        $verReportes->description = 'Ver reportes del sistema';
        $auth->add($verReportes);

        // Usuarios
        $verUsuarios = $auth->createPermission('verUsuarios');
        $verUsuarios->description = 'Ver módulo/listado de usuarios';
        $auth->add($verUsuarios);

        $administrarUsuarios = $auth->createPermission('administrarUsuarios');
        $administrarUsuarios->description = 'Administrar usuarios, roles y permisos (CRUD completo)';
        $auth->add($administrarUsuarios);


        /* =========================================================
         *                     ROLES
         * ========================================================= */

        $consultores     = $auth->createRole('Consultores');
        $administracion  = $auth->createRole('Administracion');
        $supervisores    = $auth->createRole('Supervisores');
        $administradores = $auth->createRole('Administradores');
        $desarrolladores = $auth->createRole('Desarrolladores');

        $auth->add($consultores);
        $auth->add($administracion);
        $auth->add($supervisores);
        $auth->add($administradores);
        $auth->add($desarrolladores);


        /* =========================================================
         *            PERMISOS POR ROL (JERARQUÍA)
         * ========================================================= */

        // Consultores (nivel 1)
        $auth->addChild($consultores, $verTickets);
        $auth->addChild($consultores, $crearTicket);
        $auth->addChild($consultores, $actualizarTicket);

        // Administración (nivel 2)
        $auth->addChild($administracion, $consultores); // hereda tickets
        $auth->addChild($administracion, $verClientes);
        $auth->addChild($administracion, $verReportes);
        $auth->addChild($administracion, $verUsuarios);

        // Supervisores (nivel 3)
        $auth->addChild($supervisores, $administracion); // hereda lo anterior
        $auth->addChild($supervisores, $asignarTicket);
        $auth->addChild($supervisores, $administrarClientes);
        $auth->addChild($supervisores, $verUsuarios);

        // Administradores (nivel 4)
        $auth->addChild($administradores, $supervisores); // hereda todo lo anterior
        $auth->addChild($administradores, $administrarUsuarios);

        // Desarrolladores (nivel 5)
        $auth->addChild($desarrolladores, $administradores);


        echo "✔ RBAC inicializado correctamente con permisos, 5 roles y jerarquías.\n";

        return ExitCode::OK;
    }
}
