<?php

namespace Ext\Triggers\RenterLogin;

use App\Core\BaseTrigger;
use App\Exceptions\IlegalUserActionException;

class BeforeSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {
        if ($data->tables[0]->data->renter_password !== $data->tables[0]->data->renter_password_comparation) {
            throw new IlegalUserActionException("Las contraseñas no coinciden, verifique su contraseña de nuevo");
        }

        if (strlen($data->tables[0]->data->renter_password) < 8) {
            throw new IlegalUserActionException("La contraseña debe tener al menos 8 caracteres");
        }

        if (!preg_match('/[A-Z]/', $data->tables[0]->data->renter_password)) {
            throw new IlegalUserActionException("La contraseña debe contener al menos una letra mayúscula");
        }

        if (!preg_match('/[a-z]/', $data->tables[0]->data->renter_password)) {
            throw new IlegalUserActionException("La contraseña debe contener al menos una letra minúscula");
        }

        if (!preg_match('/\d/', $data->tables[0]->data->renter_password)) {
            throw new IlegalUserActionException("La contraseña debe contener al menos un número");
        }

        if (!preg_match('/[\W_]/', $data->tables[0]->data->renter_password)) {
            throw new IlegalUserActionException("La contraseña debe contener al menos un carácter especial (ejemplo: @, #, $, %, etc.)");
        }

        $commonPasswords = ['A123456!', 'P@ssword123!', 'Qwerty1!', 'abc123', 'Letmein1!', 'password1', 'Password1@', 'A123456789a!'];
        if (in_array($data->tables[0]->data->renter_password, $commonPasswords)) {
            throw new IlegalUserActionException("La contraseña es demasiado común, elija una más segura");
        }

        if (stripos($data->tables[0]->data->renter_password, $data->tables[0]->data->renter_user) !== false) {
            throw new IlegalUserActionException("La contraseña no debe contener el nombre de usuario");
        }

        if (!preg_match('/^[0-9]{10}$/', $data->tables[0]->data->renter_user)) {
            throw new IlegalUserActionException("El nombre de usuario solo puede contener un maximo de 10 números (Solo acepta números no letras)");
        }

        $commonUsernames = ['1234567890', '0123456789', '0987654321', '9876543210'];
        if (in_array(strtolower($data->tables[0]->data->renter_user), $commonUsernames)) {
            throw new IlegalUserActionException("No puede usar este nombre de usuario, use su cedula de identidad");
        }

        $email = $data->tables[0]->data->renter_email;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new IlegalUserActionException("El formato del correo electrónico no es válido.");
        }

        if (strlen($email) > 50) {
            throw new IlegalUserActionException("El correo electrónico excede el límite de 50 caracteres.");
        }

        $domain = substr(strrchr($email, "@"), 1);
        $valid_domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'icloud.com', 'protonmail.com'];

        if (!in_array($domain, $valid_domains)) {
            throw new IlegalUserActionException("El dominio del correo electrónico no es válido. Solo se permiten los dominios: gmail.com, hotmail.com, outlook.com, yahoo.com, icloud.com, protonmail.com.");
        }

        $duplication_result = $this->app->coreModel->nodeModel("renter_login")
            ->fields(["renter_user"])
            ->Where("m.renter_user = :username")
            ->bindValue(":username", $data->tables[0]->data->renter_user)
            ->load();

        if ($is_update) {
            foreach ($duplication_result as $itm) {
                if ($itm->id != $data->tables[0]->data->id)
                    throw new IlegalUserActionException("El número de cedula ya fue registrado. Sino recuerda la contraseña ponerse en contacto con algún Gestor de Asistencia Humanitaria");
            }
        } else if (!$is_update && !empty($duplication_result)) {
            throw new IlegalUserActionException("El número de cedula ya fue registrado. Sino recuerda la contraseña ponerse en contacto con algún Gestor de Asistencia Humanitaria");
        }
    }
}
