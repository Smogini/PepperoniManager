<?php
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

enum Review_Status : string {
    case PUBBLICATA = "PUBBLICATA";

    public static function map(): array {
        return [
            'PUBBLICATA' => self::PUBBLICATA->value,
        ];
    }
}
?>