<?php
require_once "../../config.php";

$conn = new mysqli($server_host, $server_user_name, $server_password, $dbname);
$conn->error;
$id = $_GET["id"];
$checked = $_GET["checked"];

$stmt = $conn->prepare("UPDATE tagasiside SET vastatud = ? WHERE id = ?");
$stmt->bind_param("ii", $checked, $id);
$stmt->execute();

$stmt->close();
$conn->close();
?>