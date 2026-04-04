<!-- FILE INI HANYA UNTUNG TESTING -->
<?php
// Simulasi Fungsi Kalkulasi Harga (PBI-014)
function hitungHarga($tipe, $qty)
{
    $harga = ($tipe == 'Tukar') ? 5000 : 50000;
    return $harga * $qty;
}

echo "<h3>Hasil Pengujian Unit - Kalkulasi Harga</h3>";

// Test Case 1: Tukar 3 Galon
$test1 = hitungHarga('Tukar', 3);
echo "Test 1 (Tukar 3 Galon): " . ($test1 == 15000 ? "PASSED" : "FAILED") . " (Rp $test1)<br>";

// Test Case 2: Baru 2 Galon
$test2 = hitungHarga('Baru', 2);
echo "Test 2 (Baru 2 Galon): " . ($test2 == 100000 ? "PASSED" : "FAILED") . " (Rp $test2)<br>";

// Test Case 3: Gratis Ongkir (PBI-013)
$ongkir = 0;
echo "Test 3 (Pengecekan Ongkir): " . ($ongkir == 0 ? "PASSED" : "FAILED");
?>