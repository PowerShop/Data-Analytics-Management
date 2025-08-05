<?php

// require_once '../vendor/autoload.php';
if (file_exists('./vendor/autoload.php')) {
    require_once './vendor/autoload.php';
} else {
    require_once '../vendor/autoload.php';
}

if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// รับค่าจาก filters
$project_year_start = $_GET['project_year_start'] ?? '';
$project_year_end = $_GET['project_year_end'] ?? '';
$subdistrict = $_GET['subdistrict'] ?? '';
$district = $_GET['district'] ?? '';
$province = $_GET['province'] ?? '';
$main_project = $_GET['main_project'] ?? '';
$strategy = $_GET['strategy'] ?? '';
$agency = $_GET['agency'] ?? '';
$target_group = $_GET['target_group'] ?? '';

// สร้าง WHERE clause สำหรับ filter
$where_conditions = [];

// ตรวจสอบช่วงปีโครงการ
if (!empty($project_year_start) && !empty($project_year_end)) {
    $where_conditions[] = "p.ProjectYear BETWEEN '" . $conn->real_escape_string($project_year_start) . "' AND '" . $conn->real_escape_string($project_year_end) . "'";
} elseif (!empty($project_year_start)) {
    $where_conditions[] = "p.ProjectYear >= '" . $conn->real_escape_string($project_year_start) . "'";
} elseif (!empty($project_year_end)) {
    $where_conditions[] = "p.ProjectYear <= '" . $conn->real_escape_string($project_year_end) . "'";
}

if (!empty($subdistrict)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "')";
}

if (!empty($district)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District = '" . $conn->real_escape_string($district) . "')";
}

if (!empty($province)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province = '" . $conn->real_escape_string($province) . "')";
}

if (!empty($main_project)) {
    $where_conditions[] = "p.MainProjectID = '" . $conn->real_escape_string($main_project) . "'";
}

if (!empty($strategy)) {
    $where_conditions[] = "p.StrategyID = '" . $conn->real_escape_string($strategy) . "'";
}

if (!empty($agency)) {
    $where_conditions[] = "p.AgencyName = '" . $conn->real_escape_string($agency) . "'";
}

if (!empty($target_group)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projecttargetcounts ptc WHERE ptc.ProjectID = p.ProjectID AND ptc.GroupID = '" . $conn->real_escape_string($target_group) . "')";
}

// สร้าง WHERE clause
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Query หลักสำหรับดึงข้อมูลโครงการ
$main_query = "
    SELECT p.ProjectID, p.ProjectCode, p.ProjectName, p.ProjectYear, p.ResponsiblePerson,
           p.AgencyName, mp.MainProjectName, s.StrategyName,
           COALESCE(SUM(bi.ApprovedAmount), 0) as TotalBudget
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    LEFT JOIN budgetitems bi ON p.ProjectID = bi.ProjectID
    $where_clause
    GROUP BY p.ProjectID, p.ProjectCode, p.ProjectName, p.ProjectYear, p.ResponsiblePerson,
             p.AgencyName, mp.MainProjectName, s.StrategyName
    ORDER BY p.ProjectYear DESC, p.ProjectID
";

$projects_result = $conn->query($main_query);

// สร้าง Spreadsheet ใหม่
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('รายงานโครงการ');

// ตั้งค่า Headers
$headers = [
    'ลำดับ', 'รหัสโครงการ', 'ชื่อโครงการ', 'ปีโครงการ', 'ผู้รับผิดชอบ', 'งบประมาณอนุมัติ',
    'โครงการหลัก', 'ยุทธศาสตร์', 'หน่วยงาน', 'ชื่อตัวชี้วัด', 'ค่าตัวชี้วัด',
    'หน่วยตัวชี้วัด', 'ชื่อผลิตภัณฑ์', 'ประเภทผลิตภัณฑ์', 'ชื่อโรงเรียน',
    'กลุ่มเป้าหมาย', 'จำนวนเป้าหมาย', 'ชื่อหมู่บ้าน/ชุมชน', 'หมู่',
    'ตำบล', 'อำเภอ', 'จังหวัด', 'ค่า SROI', 'คำอธิบาย SROI',
    'ชื่อวิสาหกิจ/ผู้ประกอบการ', 'ประเภทวิสาหกิจ', 'ชื่อองค์กรอื่น',
    'ประเภทองค์กร', 'บทบาทองค์กร', 'ชื่อเครือข่าย', 'ชื่อมหาวิทยาลัย',
    'ประเภทมหาวิทยาลัย', 'ความร่วมมือ', 'ชื่อองค์กรปกครองท้องถิ่น',
    'ประเภทองค์กรปกครอง', 'อำเภอองค์กร', 'การสนับสนุน'
];

// ใส่ Headers ในแถวแรก
$col = 1;
foreach ($headers as $header) {
    $sheet->setCellValue([$col, 1], $header);
    $col++;
}

// จัดรูปแบบ Headers
$headerRange = 'A1:' . $sheet->getCell([$col-1, 1])->getCoordinate();
$sheet->getStyle($headerRange)->applyFromArray([
    'font' => [
        'name' => 'TH SarabunPSK',
        'size' => 14,
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '2563EB'] // สีน้ำเงินโมเดิร์น (Blue-600)
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '1E3A8A'] // น้ำเงินเข้มสำหรับเส้นขอบ
        ]
    ]
]);

// กำหนดความสูงของแถว header
$sheet->getRowDimension(1)->setRowHeight(30);

// ตรึงแถวแรก (Freeze Pane)
$sheet->freezePane('A2');

$currentRow = 2;
$projectNumber = 1; // เพิ่มตัวแปรนับลำดับโครงการ

if ($projects_result->num_rows > 0) {
    while ($project = $projects_result->fetch_assoc()) {
        $project_id = $project['ProjectID'];
        
        // ดึงข้อมูลรายละเอียดทั้งหมด
        $indicators = [];
        $products = [];
        $schools = [];
        $targetGroups = [];
        $locations = [];
        $sroi = [];
        $enterprises = [];
        $others = [];
        $networks = [];
        $universities = [];
        $localAdmins = [];
        
        // ดึงตัวชี้วัด
        $indicator_query = "
            SELECT i.IndicatorName, pi.Value, i.Unit
            FROM project_indicators pi
            LEFT JOIN indicators i ON pi.IndicatorID = i.IndicatorID
            WHERE pi.ProjectID = $project_id
        ";
        $indicator_result = $conn->query($indicator_query);
        if ($indicator_result && $indicator_result->num_rows > 0) {
            while ($ind = $indicator_result->fetch_assoc()) {
                $indicators[] = $ind;
            }
        }
        
        // ดึงผลิตภัณฑ์
        $product_query = "SELECT ProductName, ProductType FROM projectproducts WHERE ProjectID = $project_id";
        $product_result = $conn->query($product_query);
        if ($product_result && $product_result->num_rows > 0) {
            while ($prod = $product_result->fetch_assoc()) {
                $products[] = $prod;
            }
        }
        
        // ดึงโรงเรียน
        $school_query = "SELECT SchoolName FROM projectschools WHERE ProjectID = $project_id";
        $school_result = $conn->query($school_query);
        if ($school_result && $school_result->num_rows > 0) {
            while ($sch = $school_result->fetch_assoc()) {
                $schools[] = $sch;
            }
        }
        
        // ดึงกลุ่มเป้าหมาย
        $target_query = "
            SELECT tg.GroupName, ptc.TargetCount
            FROM projecttargetcounts ptc
            LEFT JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
            WHERE ptc.ProjectID = $project_id
        ";
        $target_result = $conn->query($target_query);
        if ($target_result && $target_result->num_rows > 0) {
            while ($tg = $target_result->fetch_assoc()) {
                $targetGroups[] = $tg;
            }
        }
        
        // ดึงพื้นที่
        $location_query = "
            SELECT VillageName, Community, Moo, Subdistrict, District, Province
            FROM projectvillages
            WHERE ProjectID = $project_id
        ";
        $location_result = $conn->query($location_query);
        if ($location_result && $location_result->num_rows > 0) {
            while ($loc = $location_result->fetch_assoc()) {
                $locations[] = $loc;
            }
        }
        
        // ดึง SROI
        $sroi_query = "SELECT SROIResult, Description FROM projectsroi WHERE ProjectID = $project_id";
        $sroi_result = $conn->query($sroi_query);
        if ($sroi_result && $sroi_result->num_rows > 0) {
            while ($sr = $sroi_result->fetch_assoc()) {
                $sroi[] = $sr;
            }
        }
        
        // ดึงวิสาหกิจ
        $enterprise_query = "SELECT EnterpriseName, EnterpriseType FROM projectenterprises WHERE ProjectID = $project_id";
        $enterprise_result = $conn->query($enterprise_query);
        if ($enterprise_result && $enterprise_result->num_rows > 0) {
            while ($ent = $enterprise_result->fetch_assoc()) {
                $enterprises[] = $ent;
            }
        }
        
        // ดึงองค์กรอื่น
        $other_query = "SELECT OrganizationName, OrganizationType, Role FROM projectothers WHERE ProjectID = $project_id";
        $other_result = $conn->query($other_query);
        if ($other_result && $other_result->num_rows > 0) {
            while ($oth = $other_result->fetch_assoc()) {
                $others[] = $oth;
            }
        }
        
        // ดึงเครือข่าย
        $network_query = "SELECT NetworkName FROM projectnetworks WHERE ProjectID = $project_id";
        $network_result = $conn->query($network_query);
        if ($network_result && $network_result->num_rows > 0) {
            while ($net = $network_result->fetch_assoc()) {
                $networks[] = $net;
            }
        }
        
        // ดึงมหาวิทยาลัย
        $university_query = "SELECT UniversityName, UniversityType, Collaboration FROM projectuniversities WHERE ProjectID = $project_id";
        $university_result = $conn->query($university_query);
        if ($university_result && $university_result->num_rows > 0) {
            while ($uni = $university_result->fetch_assoc()) {
                $universities[] = $uni;
            }
        }
        
        // ดึงองค์กรปกครอง
        $localadmin_query = "SELECT AdminName, AdminType, District, SupportType FROM projectlocaladmins WHERE ProjectID = $project_id";
        $localadmin_result = $conn->query($localadmin_query);
        if ($localadmin_result && $localadmin_result->num_rows > 0) {
            while ($admin = $localadmin_result->fetch_assoc()) {
                $localAdmins[] = $admin;
            }
        }
        
        // หาจำนวนแถวที่ต้องการสำหรับโครงการนี้
        $maxRows = max(
            count($indicators) ?: 1,
            count($products) ?: 1,
            count($schools) ?: 1,
            count($targetGroups) ?: 1,
            count($locations) ?: 1,
            count($sroi) ?: 1,
            count($enterprises) ?: 1,
            count($others) ?: 1,
            count($networks) ?: 1,
            count($universities) ?: 1,
            count($localAdmins) ?: 1
        );
        
        $startRow = $currentRow;
        
        // เขียนข้อมูลสำหรับโครงการนี้
        for ($i = 0; $i < $maxRows; $i++) {
            $row = $currentRow + $i;
            
            // ข้อมูลหลักโครงการ (แสดงในทุกแถว)
            $sheet->setCellValue('A' . $row, $projectNumber);
            $sheet->setCellValue('B' . $row, $project['ProjectCode'] ?: '-');
            $sheet->setCellValue('C' . $row, $project['ProjectName'] ?: '-');
            $sheet->setCellValue('D' . $row, $project['ProjectYear'] ? 'พ.ศ. ' . $project['ProjectYear'] : '-');
            $sheet->setCellValue('E' . $row, $project['ResponsiblePerson'] ?: '-');
            $sheet->setCellValue('F' . $row, number_format($project['TotalBudget'], 2) . ' บาท');
            $sheet->setCellValue('G' . $row, $project['MainProjectName'] ?: '-');
            $sheet->setCellValue('H' . $row, $project['StrategyName'] ?: '-');
            $sheet->setCellValue('I' . $row, $project['AgencyName'] ?: '-');
            
            // ถ้าเป็นแถวแรกของโครงการ ให้ทำให้โดดเด่น
            if ($i == 0) {
                $mainRowRange = 'A' . $row . ':I' . $row;
                $sheet->getStyle($mainRowRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '1E40AF'] // สีน้ำเงินเข้มทันสมัย
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['rgb' => '2563EB'] // สีน้ำเงินโมเดิร์น
                        ]
                    ]
                ]);
            }
            
            // ข้อมูลรายละเอียดที่แยกเป็นแถว
            $sheet->setCellValue('J' . $row, isset($indicators[$i]) ? ($indicators[$i]['IndicatorName'] ?: '-') : '-');
            $sheet->setCellValue('K' . $row, isset($indicators[$i]) ? ($indicators[$i]['Value'] ?: '-') : '-');
            $sheet->setCellValue('L' . $row, isset($indicators[$i]) ? ($indicators[$i]['Unit'] ?: '-') : '-');
            
            $sheet->setCellValue('M' . $row, isset($products[$i]) ? ($products[$i]['ProductName'] ?: '-') : '-');
            $sheet->setCellValue('N' . $row, isset($products[$i]) ? ($products[$i]['ProductType'] ?: '-') : '-');
            
            $sheet->setCellValue('O' . $row, isset($schools[$i]) ? ($schools[$i]['SchoolName'] ?: '-') : '-');
            
            $sheet->setCellValue('P' . $row, isset($targetGroups[$i]) ? ($targetGroups[$i]['GroupName'] ?: '-') : '-');
            $sheet->setCellValue('Q' . $row, isset($targetGroups[$i]) ? ($targetGroups[$i]['TargetCount'] ?: '-') : '-');
            
            // พื้นที่
            if (isset($locations[$i])) {
                $villageName = $locations[$i]['VillageName'];
                if (!$villageName || $villageName === '-' || trim($villageName) === '') {
                    $villageName = $locations[$i]['Community'] ?: '-';
                }
                if ($villageName === 'ไม่ระบุ') {
                    $villageName = '-';
                }
                $sheet->setCellValue('R' . $row, $villageName);
                $sheet->setCellValue('S' . $row, $locations[$i]['Moo'] ?: '-');
                $sheet->setCellValue('T' . $row, $locations[$i]['Subdistrict'] ?: '-');
                $sheet->setCellValue('U' . $row, $locations[$i]['District'] ?: '-');
                $sheet->setCellValue('V' . $row, $locations[$i]['Province'] ?: '-');
            } else {
                $sheet->setCellValue('R' . $row, '-');
                $sheet->setCellValue('S' . $row, '-');
                $sheet->setCellValue('T' . $row, '-');
                $sheet->setCellValue('U' . $row, '-');
                $sheet->setCellValue('V' . $row, '-');
            }
            
            $sheet->setCellValue('W' . $row, isset($sroi[$i]) ? ($sroi[$i]['SROIResult'] ?: '-') : '-');
            $sheet->setCellValue('X' . $row, isset($sroi[$i]) ? ($sroi[$i]['Description'] ?: '-') : '-');
            
            $sheet->setCellValue('Y' . $row, isset($enterprises[$i]) ? ($enterprises[$i]['EnterpriseName'] ?: '-') : '-');
            $sheet->setCellValue('Z' . $row, isset($enterprises[$i]) ? ($enterprises[$i]['EnterpriseType'] ?: '-') : '-');
            
            $sheet->setCellValue('AA' . $row, isset($others[$i]) ? ($others[$i]['OrganizationName'] ?: '-') : '-');
            $sheet->setCellValue('AB' . $row, isset($others[$i]) ? ($others[$i]['OrganizationType'] ?: '-') : '-');
            $sheet->setCellValue('AC' . $row, isset($others[$i]) ? ($others[$i]['Role'] ?: '-') : '-');
            
            $sheet->setCellValue('AD' . $row, isset($networks[$i]) ? ($networks[$i]['NetworkName'] ?: '-') : '-');
            
            $sheet->setCellValue('AE' . $row, isset($universities[$i]) ? ($universities[$i]['UniversityName'] ?: '-') : '-');
            $sheet->setCellValue('AF' . $row, isset($universities[$i]) ? ($universities[$i]['UniversityType'] ?: '-') : '-');
            $sheet->setCellValue('AG' . $row, isset($universities[$i]) ? ($universities[$i]['Collaboration'] ?: '-') : '-');
            
            $sheet->setCellValue('AH' . $row, isset($localAdmins[$i]) ? ($localAdmins[$i]['AdminName'] ?: '-') : '-');
            $sheet->setCellValue('AI' . $row, isset($localAdmins[$i]) ? ($localAdmins[$i]['AdminType'] ?: '-') : '-');
            $sheet->setCellValue('AJ' . $row, isset($localAdmins[$i]) ? ($localAdmins[$i]['District'] ?: '-') : '-');
            $sheet->setCellValue('AK' . $row, isset($localAdmins[$i]) ? ($localAdmins[$i]['SupportType'] ?: '-') : '-');
        }
        
        // เพิ่มสีพื้นหลังสลับระหว่างโครงการ
        $backgroundColor = ($projectNumber % 2 == 1) ? 'EFF6FF' : 'DBEAFE'; // สีฟ้าอ่อนสวยงาม โมเดิร์น
        $dataRange = 'A' . $startRow . ':AK' . ($startRow + $maxRows - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $backgroundColor]
            ]
        ]);
        
        // เพิ่มเส้นหนากั้นระหว่างโครงการ (ด้านล่างของแถวสุดท้ายของโครงการ)
        if ($maxRows > 0) {
            $lastRowOfProject = $currentRow + $maxRows - 1;
            $borderRange = 'A' . $lastRowOfProject . ':AK' . $lastRowOfProject;
            $sheet->getStyle($borderRange)->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '3B82F6'] // สีน้ำเงินสดใสเป็นเส้นขอบ
                    ]
                ]
            ]);
        }
        
        // สร้าง Group สำหรับแต่ละโครงการ (ถ้ามีมากกว่า 1 แถว)
        if ($maxRows > 1) {
            // Group แถวย่อยของโครงการ (ยกเว้นแถวแรก)
            for ($groupRow = $startRow + 1; $groupRow <= $startRow + $maxRows - 1; $groupRow++) {
                $sheet->getRowDimension($groupRow)->setOutlineLevel(1);
                $sheet->getRowDimension($groupRow)->setCollapsed(false);
                $sheet->getRowDimension($groupRow)->setVisible(true);
            }
            
            // เพิ่มการตั้งค่าพิเศษเพื่อรักษา Grouping
            $sheet->getRowDimension($startRow)->setOutlineLevel(0); // แถวหลักไม่อยู่ใน group
        }
        
        // เพิ่มข้อมูล metadata สำหรับการจัดกลุ่ม (ใช้ hidden column หรือ comment)
        // เพิ่ม comment ในแถวแรกของแต่ละโครงการเพื่อระบุขอบเขตโครงการ
        $projectComment = $sheet->getComment('A' . $startRow);
        $projectComment->getText()->createTextRun("โครงการที่ {$projectNumber}: {$project['ProjectName']} (แถว {$startRow} - " . ($startRow + $maxRows - 1) . ")");
        $projectComment->setWidth('300px');
        $projectComment->setHeight('80px');
        
        $currentRow += $maxRows;
        $projectNumber++; // เพิ่มลำดับโครงการ
    }
} else {
    $sheet->setCellValue('A2', 'ไม่พบข้อมูลโครงการ');
    $sheet->mergeCells('A2:AK2');
    $sheet->getStyle('A2')->applyFromArray([
        'font' => [
            'name' => 'TH SarabunPSK',
            'size' => 14
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ]
    ]);
}

// เพิ่มคำอธิบายการใช้งาน Grouping และ AutoFilter ในเซลล์ A1 (comment)
if ($currentRow > 2) {
    $sheet->getComment('A1')->getText()->createTextRun('คำแนะนำการใช้งาน:
1. คลิกไอคอน + / - ที่ด้านซ้ายเพื่อขยาย/ย่อรายละเอียดโครงการ
2. ใช้ AutoFilter ที่หัวตารางเพื่อกรองข้อมูล
3. แถวหลักของแต่ละโครงการจะมีตัวหนา
4. สีพื้นหลังสลับเพื่อแยกแยะโครงการ
5. หากต้องการล้างตัวกรอง: Data > Filter > Clear หรือ Ctrl+Shift+L 2 ครั้ง
6. หากการจัดกลุ่มหลุด: Data > Group > Auto Outline');
}

// จัดรูปแบบข้อมูล
if ($currentRow > 2) {
    $dataRange = 'A2:AK' . ($currentRow - 1);
    $sheet->getStyle($dataRange)->applyFromArray([
        'font' => [
            'name' => 'TH SarabunPSK',
            'size' => 14,
            'color' => ['rgb' => '374151'] // สีเทาเข้มทันสมัย
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '9CA3AF'] // สีเทากลางสำหรับเส้นขอบ
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true
        ]
    ]);
    
    // เพิ่ม AutoFilter สำหรับทั้งตาราง
    $sheet->setAutoFilter('A1:AK' . ($currentRow - 1));
    
    // ตั้งค่า Grouping Options ให้เหมาะสมกับการกรอง
    $sheet->setShowSummaryBelow(false); // แสดง summary ด้านบน
    $sheet->setShowSummaryRight(false); // แสดง summary ด้านซ้าย
}

// ปรับขนาดคอลัมน์และแถว
foreach (range('A', 'AK') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// กำหนดความสูงขั้นต่ำสำหรับแถวข้อมูล
for ($row = 2; $row < $currentRow; $row++) {
    $sheet->getRowDimension($row)->setRowHeight(-1); // Auto height
}

// สร้างไฟล์ Excel
$filename = 'รายงานโครงการแบบรายละเอียด_' . date('Y-m-d_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
