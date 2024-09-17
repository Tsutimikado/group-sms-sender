<?php
$servername = "localhost";
$username = "smpp";
$password = "Smpp034";
$dbname = "smpp";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Initial character set is: " . mysqli_character_set_name($conn);

// Change character set to utf8
mysqli_set_charset($conn,"ut");

echo "Current character set is: " . mysqli_character_set_name($conn);
$content = mb_convert_encoding($content, "UTF-16BE", "ASCII");
$conn->query("set names utf8mb4");

$sql = "SELECT * FROM smpp";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["messageid"]. " - Date: " . $row["date"]. " " . $row["dst"]. " " . $row["src"]. " " . $row["content"]."<br>";
    }
} else {
    echo "0 results";
}



{

return $out_text;
}

$conn->close();


?> 
