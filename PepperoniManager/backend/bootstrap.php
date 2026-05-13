<?php
require_once 'DatabaseHelper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$GLOBALS['db'] = new DatabaseHelper();

enum Order_Status : string {
    case IN_ATTESA = "IN_ATTESA";
    case ACCETTATO = "ACCETTATO";
    case COMPLETATO = "COMPLETATO";

    public static function map(): array {
        return [
            'IN_ATTESA' => self::IN_ATTESA->value,
            'ACCETTATO' => self::ACCETTATO->value,
            'COMPLETATO' => self::COMPLETATO->value
        ];
    }
}

enum Delivery_Status : string {
    case CREATA = "CREATA";
    case IN_ATTESA = "IN_ATTESA";
    case IN_CONSEGNA = "IN_CONSEGNA";
    case CONSEGNATA = "CONSEGNATA";

    public static function map(): array {
        return [
            'CREATA' => self::CREATA->value,
            'IN_ATTESA' => self::IN_ATTESA->value,
            'IN_CONSEGNA' => self::IN_CONSEGNA->value,
            'CONSEGNATO' => self::CONSEGNATA->value
        ];
    }
}
