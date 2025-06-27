<?php
require_once __DIR__ . '../../../config/Database.php';
require_once __DIR__ . '../../../models/FPDF-master/fpdf.php';
require_once __DIR__ . '/../../controllers/helpers/auth.php';
verificarAutenticacion();
verificarRol('admin');

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_resultados.pdf"');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Resultados', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(50, 10, 'Usuario', 1);
$pdf->Cell(50, 10, 'Test', 1);
$pdf->Cell(30, 10, 'Puntaje', 1);
$pdf->Ln();

$sql = "SELECT r.fecha_resultado, u.nombre, u.apellido, t.nombre as test, r.puntaje_total FROM resultados r JOIN usuarios u ON r.id_usuario = u.id_usuario JOIN tests t ON r.id_test = t.id_test ORDER BY r.fecha_resultado DESC LIMIT 50";
$res = $conexion->query($sql);
$pdf->SetFont('Arial', '', 10);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $pdf->Cell(40, 8, date('d/m/Y H:i', strtotime($row['fecha_resultado'])), 1);
        $pdf->Cell(50, 8, $row['nombre'] . ' ' . $row['apellido'], 1);
        $pdf->Cell(50, 8, $row['test'], 1);
        $pdf->Cell(30, 8, $row['puntaje_total'], 1);
        $pdf->Ln();
    }
}
$pdf->Output();
