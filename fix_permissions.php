<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "
    DELETE FROM mysql.user WHERE User='root';
    INSERT INTO mysql.user (Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, Super_priv) 
    VALUES ('localhost', 'root', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');
    INSERT INTO mysql.user (Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, Super_priv) 
    VALUES ('127.0.0.1', 'root', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');
    FLUSH PRIVILEGES;
";

if ($conn->multi_query($sql)) {
    echo "✅ Permisos restaurados correctamente!";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
