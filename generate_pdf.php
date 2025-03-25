<?php
require_once('tcpdf/tcpdf.php');
include('connection.php');

function generatePDF() {
    global $conn;

    // Create a new PDF document
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Category List');

    // Add a page
    $pdf->AddPage();

    // Table header
    $html = '<h3>Category List</h3>';
    $html .= '<table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Category Code</th>
                        <th>Brand</th>
                    </tr>
                </thead>
                <tbody>';

    // Fetch data from the database
    $sql = "SELECT categories.category_name, categories.category_code, brands.name 
            FROM categories 
            LEFT JOIN brands ON categories.brand_id = brands.id";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['category_name']) . '</td>
                    <td>' . htmlspecialchars($row['category_code']) . '</td>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html);

    // Output PDF
    $pdf->Output('Category_List.pdf', 'D'); // 'D' forces download
}
?>
