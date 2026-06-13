<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminSettingsController
{
    public function __construct()
    {
        if (Session::get("user_role_slug") !== "admin") {
            header("Location: /");
            exit();
        }
    }

    public function index()
    {
        $db = Database::getInstance();
        $settings = $db->fetchAll(
            "SELECT * FROM settings ORDER BY `key_name` ASC",
        );
        $settingsMap = [];
        foreach ($settings as $s) {
            $settingsMap[$s["key"]] = $s["value"];
        }
        View::render(
            "admin/settings",
            compact("settings", "settingsMap"),
            "admin",
        );
    }

    public function update()
    {
        $db = Database::getInstance();
        $keys = Request::post("keys", []);
        $values = Request::post("values", []);

        foreach ($keys as $i => $key) {
            $value = $values[$i] ?? "";
            $existing = $db->fetch("SELECT id FROM settings WHERE `key` = ?", [
                $key,
            ]);
            if ($existing) {
                $db->query("UPDATE settings SET `value` = ? WHERE `key` = ?", [
                    $value,
                    $key,
                ]);
            } else {
                $db->insert(
                    "INSERT INTO settings (`key`, `value`) VALUES (?, ?)",
                    [$key, $value],
                );
            }
        }

        Session::setFlash("success", "Paramètres mis à jour.");
        Request::redirect("/admin/settings");
    }
}
