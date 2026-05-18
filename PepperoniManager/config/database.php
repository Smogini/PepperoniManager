<?php
class Database {
    private static ?mysqli $conn = null;

    public static function getConnection(): mysqli {
        if(self::$conn === null) {
            self::$conn = new mysqli(
                "localhost",
                "root",
                "",
                "pepperoni_db"
            );

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }

        return self::$conn;
    }
}
?>