<?php
$conn = mysqli_connect("localhost", "root", "", "bullying_db");
if (!$conn) {
    die("Koneksi database gagal!");
}
