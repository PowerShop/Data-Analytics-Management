<?php

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

// === Sheet 1: รายงานรายละเอียดโครงการ (เหมือนเดิม) ===
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('รายงานรายละเอียด');

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

// === Sheet 2: รวมโครงการตามโครงการหลัก -> ยุทธศาสตร์ ===
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('แยกตามยุทธศาสตร์');

// Query ข้อมูลสำหรับการจัดกลุ่มแบบ Hierarchical
$hierarchical_query = "
    SELECT 
        s.StrategyName,
        mp.MainProjectName,
        p.ProjectName,
        p.ProjectYear,
        p.ResponsiblePerson,
        p.AgencyName,
        COALESCE(SUM(bi.ApprovedAmount), 0) as ProjectBudget
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    LEFT JOIN budgetitems bi ON p.ProjectID = bi.ProjectID
    $where_clause
    GROUP BY s.StrategyID, mp.MainProjectID, p.ProjectID, s.StrategyName, mp.MainProjectName, p.ProjectName, p.ProjectYear, p.ResponsiblePerson, p.AgencyName
    ORDER BY s.StrategyName, mp.MainProjectName, p.ProjectName
";

$hierarchical_result = $conn->query($hierarchical_query);

// ตั้งค่า Headers สำหรับ Sheet 2
$headers2 = [
    'ยุทธศาสตร์/โครงการหลัก/โครงการ', 'ปี', 'ผู้รับผิดชอบ', 'หน่วยงาน', 'งบประมาณ (บาท)'
];

// ใส่ Headers
$col = 1;
foreach ($headers2 as $header) {
    $sheet2->setCellValue([$col, 1], $header);
    $col++;
}

// จัดรูปแบบ Headers
$headerRange2 = 'A1:E1';
$sheet2->getStyle($headerRange2)->applyFromArray([
    'font' => [
        'name' => 'TH SarabunPSK',
        'size' => 14,
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '059669'] // สีเขียว
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '047857']
        ]
    ]
]);

$sheet2->getRowDimension(1)->setRowHeight(30);
$sheet2->freezePane('A2');

$currentRow2 = 2;

if ($hierarchical_result->num_rows > 0) {
    $currentStrategy = '';
    $currentMainProject = '';
    $projectNumber = 0;
    
    while ($row = $hierarchical_result->fetch_assoc()) {
        // ถ้าเป็นยุทธศาสตร์ใหม่ (ระดับ 1)
        if ($currentStrategy !== $row['StrategyName']) {
            if ($currentStrategy !== '') {
                $currentRow2++; // เว้นบรรทัดระหว่างยุทธศาสตร์
            }
            
            // แสดงหัวข้อยุทธศาสตร์ใหม่
            $sheet2->setCellValue('A' . $currentRow2, $row['StrategyName'] ?: 'ไม่ระบุยุทธศาสตร์');
            $sheet2->setCellValue('B' . $currentRow2, '');
            $sheet2->setCellValue('C' . $currentRow2, '');
            $sheet2->setCellValue('D' . $currentRow2, '');
            $sheet2->setCellValue('E' . $currentRow2, '');
            
            $sheet2->getStyle('A' . $currentRow2 . ':E' . $currentRow2)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '1976D2']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => '1976D2']
                    ]
                ]
            ]);
            
            $currentStrategy = $row['StrategyName'];
            $currentMainProject = '';
            $currentRow2++;
        }
        
        // ถ้าเป็นโครงการหลักใหม่ (ระดับ 2)
        if ($currentMainProject !== $row['MainProjectName']) {
            // แสดงหัวข้อโครงการหลักใหม่
            $sheet2->setCellValue('A' . $currentRow2, "  " . ($row['MainProjectName'] ?: 'ไม่ระบุโครงการหลัก'));
            $sheet2->setCellValue('B' . $currentRow2, '');
            $sheet2->setCellValue('C' . $currentRow2, '');
            $sheet2->setCellValue('D' . $currentRow2, '');
            $sheet2->setCellValue('E' . $currentRow2, '');
            
            $sheet2->getStyle('A' . $currentRow2 . ':E' . $currentRow2)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '059669']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8']
                ]
            ]);
            
            $currentMainProject = $row['MainProjectName'];
            $currentRow2++;
        }
        
        // แสดงโครงการ (ระดับ 3)
        $projectNumber++;
        $sheet2->setCellValue('A' . $currentRow2, "    {$projectNumber}. " . ($row['ProjectName'] ?: 'ไม่ระบุชื่อโครงการ'));
        $sheet2->setCellValue('B' . $currentRow2, $row['ProjectYear'] ? 'พ.ศ. ' . $row['ProjectYear'] : '-');
        $sheet2->setCellValue('C' . $currentRow2, $row['ResponsiblePerson'] ?: '-');
        $sheet2->setCellValue('D' . $currentRow2, $row['AgencyName'] ?: '-');
        $sheet2->setCellValue('E' . $currentRow2, number_format($row['ProjectBudget'], 2));
        
        $currentRow2++;
    }
}

// จัดรูปแบบข้อมูล Sheet 2
if ($currentRow2 > 2) {
    $dataRange2 = 'A2:E' . ($currentRow2 - 1);
    $sheet2->getStyle($dataRange2)->applyFromArray([
        'font' => [
            'name' => 'TH SarabunPSK',
            'size' => 14
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '9CA3AF']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true
        ]
    ]);
}

// ปรับขนาดคอลัมน์ Sheet 2
$sheet2->getColumnDimension('A')->setWidth(50); // คอลัมน์หลักกว้างขึ้น
foreach (range('B', 'E') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

// === Sheet 3+: แยกตามปี ===
// ดึงข้อมูลปีที่มีอยู่
$years_query = "
    SELECT DISTINCT p.ProjectYear 
    FROM projects p 
    $where_clause 
    ORDER BY p.ProjectYear DESC
";
$years_result = $conn->query($years_query);

$years = [];
if ($years_result->num_rows > 0) {
    while ($year_row = $years_result->fetch_assoc()) {
        if ($year_row['ProjectYear']) {
            $years[] = $year_row['ProjectYear'];
        }
    }
}

// สร้าง Sheet สำหรับแต่ละปี
foreach ($years as $year) {
    $yearSheet = $spreadsheet->createSheet();
    $yearSheet->setTitle('ปี ' . $year);
    
    // Query ข้อมูลสำหรับปีนี้
    $year_where = $where_clause ? $where_clause . " AND p.ProjectYear = '$year'" : "WHERE p.ProjectYear = '$year'";
    
    $year_query = "
        SELECT p.ProjectID, p.ProjectCode, p.ProjectName, p.ResponsiblePerson,
               p.AgencyName, mp.MainProjectName, s.StrategyName,
               COALESCE(SUM(bi.ApprovedAmount), 0) as TotalBudget
        FROM projects p
        LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
        LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
        LEFT JOIN budgetitems bi ON p.ProjectID = bi.ProjectID
        $year_where
        GROUP BY p.ProjectID, p.ProjectCode, p.ProjectName, p.ResponsiblePerson,
                 p.AgencyName, mp.MainProjectName, s.StrategyName
        ORDER BY p.ProjectID
    ";
    
    $year_result = $conn->query($year_query);
    
    // Headers สำหรับ Sheet ปี
    $headers_year = [
        'ลำดับ', 'ชื่อโครงการ', 'ผู้รับผิดชอบ',
        'หน่วยงาน', 'โครงการหลัก', 'ยุทธศาสตร์', 'งบประมาณ (บาท)'
    ];
    
    // ใส่ Headers
    $col = 1;
    foreach ($headers_year as $header) {
        $yearSheet->setCellValue([$col, 1], $header);
        $col++;
    }
    
    // จัดรูปแบบ Headers
    $headerRangeYear = 'A1:G1';
    $yearSheet->getStyle($headerRangeYear)->applyFromArray([
        'font' => [
            'name' => 'TH SarabunPSK',
            'size' => 14,
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2196F3'] // สีน้ำเงินฟ้า
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '1976D2']
            ]
        ]
    ]);
    
    $yearSheet->getRowDimension(1)->setRowHeight(30);
    $yearSheet->freezePane('A2');
    
    $currentRowYear = 2;
    $yearRowNumber = 1;
    $yearTotalBudget = 0;
    
    if ($year_result->num_rows > 0) {
        while ($project = $year_result->fetch_assoc()) {
            $yearSheet->setCellValue('A' . $currentRowYear, $yearRowNumber);
            $yearSheet->setCellValue('B' . $currentRowYear, $project['ProjectName'] ?: '-');
            $yearSheet->setCellValue('C' . $currentRowYear, $project['ResponsiblePerson'] ?: '-');
            $yearSheet->setCellValue('D' . $currentRowYear, $project['AgencyName'] ?: '-');
            $yearSheet->setCellValue('E' . $currentRowYear, $project['MainProjectName'] ?: '-');
            $yearSheet->setCellValue('F' . $currentRowYear, $project['StrategyName'] ?: '-');
            $yearSheet->setCellValue('G' . $currentRowYear, number_format($project['TotalBudget'], 2));
            
            $yearTotalBudget += $project['TotalBudget'];
            $currentRowYear++;
            $yearRowNumber++;
        }
        
        // แถวรวม
        $yearSheet->setCellValue('A' . $currentRowYear, '');
        $yearSheet->setCellValue('B' . $currentRowYear, 'รวมทั้งหมด');
        $yearSheet->setCellValue('C' . $currentRowYear, '');
        $yearSheet->setCellValue('D' . $currentRowYear, '');
        $yearSheet->setCellValue('E' . $currentRowYear, '');
        $yearSheet->setCellValue('F' . $currentRowYear, ($yearRowNumber - 1) . ' โครงการ');
        $yearSheet->setCellValue('G' . $currentRowYear, number_format($yearTotalBudget, 2));
        
        // จัดรูปแบบแถวรวม
        $yearSheet->getStyle('A' . $currentRowYear . ':G' . $currentRowYear)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '2196F3']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '2196F3']]
            ]
        ]);
    } else {
        $yearSheet->setCellValue('A2', 'ไม่พบข้อมูลโครงการในปี ' . $year);
        $yearSheet->mergeCells('A2:G2');
        $yearSheet->getStyle('A2')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }
    
    // จัดรูปแบบข้อมูล
    if ($currentRowYear > 2) {
        $dataRangeYear = 'A2:G' . ($currentRowYear - 1);
        $yearSheet->getStyle($dataRangeYear)->applyFromArray([
            'font' => [
                'name' => 'TH SarabunPSK',
                'size' => 14
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '9CA3AF']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);
        
        $yearSheet->setAutoFilter('A1:G' . ($currentRowYear - 1));
    }
    
    // ปรับขนาดคอลัมน์
    foreach (range('A', 'G') as $col) {
        $yearSheet->getColumnDimension($col)->setAutoSize(true);
    }
}

// === Sheet สำหรับหมู่บ้านที่มีโครงการซ้ำกัน ===
$villageSheet = $spreadsheet->createSheet();
$villageSheet->setTitle('หมู่บ้านที่มีโครงการซ้ำ');

// Query ข้อมูลหมู่บ้าน/ชุมชนที่มีโครงการซ้ำกัน
$village_query = "
    SELECT 
        COALESCE(NULLIF(pv.VillageName, ''), NULLIF(pv.Community, ''), 'ไม่ระบุชื่อ') as VillageName,
        pv.Moo,
        pv.Subdistrict,
        pv.District,
        pv.Province,
        COUNT(DISTINCT p.ProjectID) as ProjectCount,
        COALESCE(SUM(bi.ApprovedAmount), 0) as TotalBudget,
        GROUP_CONCAT(DISTINCT p.ProjectYear ORDER BY p.ProjectYear DESC SEPARATOR ', ') as Years
    FROM projectvillages pv
    LEFT JOIN projects p ON pv.ProjectID = p.ProjectID
    LEFT JOIN budgetitems bi ON p.ProjectID = bi.ProjectID
    " . str_replace('p.', 'p.', $where_clause) . "
    GROUP BY 
        COALESCE(NULLIF(pv.VillageName, ''), NULLIF(pv.Community, ''), 'ไม่ระบุชื่อ'),
        pv.Moo, pv.Subdistrict, pv.District, pv.Province
    HAVING COUNT(DISTINCT p.ProjectID) > 1
    ORDER BY ProjectCount DESC, TotalBudget DESC
";

$village_result = $conn->query($village_query);

// Headers สำหรับ Sheet หมู่บ้าน
$headers_village = [
    'ลำดับ', 'หมู่บ้าน/ชุมชน', 'หมู่', 'ตำบล', 'อำเภอ', 'จังหวัด', 
    'จำนวนโครงการ', 'ปีที่ดำเนินการ', 'งบประมาณรวม (บาท)', 'ผลิตภัณฑ์', 'ตัวชี้วัด', 'รายชื่อโครงการ'
];

// ใส่ Headers
$col = 1;
foreach ($headers_village as $header) {
    $villageSheet->setCellValue([$col, 1], $header);
    $col++;
}

// จัดรูปแบบ Headers
$headerRangeVillage = 'A1:L1';
$villageSheet->getStyle($headerRangeVillage)->applyFromArray([
    'font' => [
        'name' => 'TH SarabunPSK',
        'size' => 14,
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '2563EB'] // สีน้ำเงินโมเดิร์น เหมือน Sheet อื่น
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '1E3A8A'] // น้ำเงินเข้มเหมือน Sheet อื่น
        ]
    ]
]);

$villageSheet->getRowDimension(1)->setRowHeight(30);
$villageSheet->freezePane('A2');

$currentRowVillage = 2;
$villageRowNumber = 1;
$totalVillages = 0;
$totalDuplicateProjects = 0;
$maxProjectsInVillage = 0;
$totalVillageBudget = 0;

if ($village_result && $village_result->num_rows > 0) {
    while ($village = $village_result->fetch_assoc()) {
        // ดึงรายชื่อโครงการแยกต่างหากเพื่อใส่เลขกำกับ
        $village_name = $village['VillageName'];
        $moo = $village['Moo'];
        $subdistrict = $village['Subdistrict'];
        $district = $village['District'];
        $province = $village['Province'];
        
        $project_list_query = "
            SELECT DISTINCT p.ProjectName, p.ProjectYear
            FROM projectvillages pv
            LEFT JOIN projects p ON pv.ProjectID = p.ProjectID
            WHERE COALESCE(NULLIF(pv.VillageName, ''), NULLIF(pv.Community, ''), 'ไม่ระบุชื่อ') = '$village_name'
            AND pv.Moo = '$moo'
            AND pv.Subdistrict = '$subdistrict'
            AND pv.District = '$district'
            AND pv.Province = '$province'
            ORDER BY p.ProjectName
        ";
        
        $project_list_result = $conn->query($project_list_query);
        $numbered_projects = [];
        $project_number = 1;
        
        if ($project_list_result && $project_list_result->num_rows > 0) {
            while ($proj = $project_list_result->fetch_assoc()) {
                $numbered_projects[] = $project_number . ". " . $proj['ProjectName'] . " (" . ($proj['ProjectYear'] ?: 'ไม่ระบุปี') . ")";
                $project_number++;
            }
        }
        
        $project_list_text = implode("\n", $numbered_projects);
        
        // ดึงข้อมูลผลิตภัณฑ์สำหรับหมู่บ้านนี้
        $product_query = "
            SELECT DISTINCT pp.ProductName, pp.ProductType
            FROM projectvillages pv
            LEFT JOIN projects p ON pv.ProjectID = p.ProjectID
            LEFT JOIN projectproducts pp ON p.ProjectID = pp.ProjectID
            WHERE COALESCE(NULLIF(pv.VillageName, ''), NULLIF(pv.Community, ''), 'ไม่ระบุชื่อ') = '$village_name'
            AND pv.Moo = '$moo'
            AND pv.Subdistrict = '$subdistrict'
            AND pv.District = '$district'
            AND pv.Province = '$province'
            AND pp.ProductName IS NOT NULL
            ORDER BY pp.ProductName
        ";
        
        $product_result = $conn->query($product_query);
        $products = [];
        $product_number = 1;
        
        if ($product_result && $product_result->num_rows > 0) {
            while ($prod = $product_result->fetch_assoc()) {
                $products[] = $product_number . ". " . $prod['ProductName'] . " (" . ($prod['ProductType'] ?: 'ไม่ระบุประเภท') . ")";
                $product_number++;
            }
        }
        
        $product_text = implode("\n", $products) ?: '-';
        
        // ดึงข้อมูลตัวชี้วัดสำหรับหมู่บ้านนี้
        $indicator_query = "
            SELECT DISTINCT i.IndicatorName, pi.Value, i.Unit
            FROM projectvillages pv
            LEFT JOIN projects p ON pv.ProjectID = p.ProjectID
            LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            LEFT JOIN indicators i ON pi.IndicatorID = i.IndicatorID
            WHERE COALESCE(NULLIF(pv.VillageName, ''), NULLIF(pv.Community, ''), 'ไม่ระบุชื่อ') = '$village_name'
            AND pv.Moo = '$moo'
            AND pv.Subdistrict = '$subdistrict'
            AND pv.District = '$district'
            AND pv.Province = '$province'
            AND i.IndicatorName IS NOT NULL
            ORDER BY i.IndicatorName
        ";
        
        $indicator_result = $conn->query($indicator_query);
        $indicators = [];
        $indicator_number = 1;
        
        if ($indicator_result && $indicator_result->num_rows > 0) {
            while ($ind = $indicator_result->fetch_assoc()) {
                $value = $ind['Value'] ?: '-';
                if ($value !== '-' && is_numeric($value)) {
                    $value = number_format($value, 2);
                }
                $value_unit = $value . ' ' . ($ind['Unit'] ?: '');
                $indicators[] = $indicator_number . ". " . $ind['IndicatorName'] . " (" . trim($value_unit) . ")";
                $indicator_number++;
            }
        }
        
        $indicator_text = implode("\n", $indicators) ?: '-';
        
        $villageSheet->setCellValue('A' . $currentRowVillage, $villageRowNumber);
        $villageSheet->setCellValue('B' . $currentRowVillage, $village['VillageName'] ?: 'ไม่ระบุชื่อ');
        $villageSheet->setCellValue('C' . $currentRowVillage, $village['Moo'] ? 'หมู่ ' . $village['Moo'] : '-');
        $villageSheet->setCellValue('D' . $currentRowVillage, $village['Subdistrict'] ?: '-');
        $villageSheet->setCellValue('E' . $currentRowVillage, $village['District'] ?: '-');
        $villageSheet->setCellValue('F' . $currentRowVillage, $village['Province'] ?: '-');
        $villageSheet->setCellValue('G' . $currentRowVillage, $village['ProjectCount']);
        $villageSheet->setCellValue('H' . $currentRowVillage, $village['Years'] ?: '-');
        $villageSheet->setCellValue('I' . $currentRowVillage, number_format($village['TotalBudget'], 2));
        $villageSheet->setCellValue('J' . $currentRowVillage, $product_text);
        $villageSheet->setCellValue('K' . $currentRowVillage, $indicator_text);
        $villageSheet->setCellValue('L' . $currentRowVillage, $project_list_text ?: '-');
        
        // เพิ่มสีพื้นหลังตามจำนวนโครงการ
        $projectCount = intval($village['ProjectCount']);
        $backgroundColor = 'FFFFFF';
        
        if ($projectCount >= 5) {
            $backgroundColor = 'FEE2E2'; // แดงอ่อน - โครงการเยอะมาก
        } elseif ($projectCount >= 3) {
            $backgroundColor = 'FEF3C7'; // เหลืองอ่อน - โครงการปานกลาง
        } else {
            $backgroundColor = 'F0FDF4'; // เขียวอ่อน - โครงการน้อย
        }
        
        $villageSheet->getStyle('A' . $currentRowVillage . ':L' . $currentRowVillage)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $backgroundColor]
            ]
        ]);
        
        // สะสมข้อมูลสถิติ
        $totalVillages++;
        $totalDuplicateProjects += $projectCount;
        if ($projectCount > $maxProjectsInVillage) {
            $maxProjectsInVillage = $projectCount;
        }
        $totalVillageBudget += floatval($village['TotalBudget']);
        
        $currentRowVillage++;
        $villageRowNumber++;
    }
    
    // สร้างสถิติสรุป
    $currentRowVillage += 2;
    $villageSheet->setCellValue('A' . $currentRowVillage, 'สถิติสรุป');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':L' . $currentRowVillage);
    $villageSheet->getStyle('A' . $currentRowVillage . ':L' . $currentRowVillage)->applyFromArray([
        'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'DC2626']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    $currentRowVillage++;
    
    $villageSheet->setCellValue('A' . $currentRowVillage, 'จำนวนหมู่บ้าน/ชุมชนที่มีโครงการซ้ำ:');
    $villageSheet->setCellValue('C' . $currentRowVillage, number_format($totalVillages, 2) . ' แห่ง');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':B' . $currentRowVillage);
    $currentRowVillage++;
    
    $villageSheet->setCellValue('A' . $currentRowVillage, 'จำนวนโครงการทั้งหมดในพื้นที่ซ้ำ:');
    $villageSheet->setCellValue('C' . $currentRowVillage, number_format($totalDuplicateProjects, 2) . ' โครงการ');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':B' . $currentRowVillage);
    $currentRowVillage++;
    
    $villageSheet->setCellValue('A' . $currentRowVillage, 'หมู่บ้านที่มีโครงการมากที่สุด:');
    $villageSheet->setCellValue('C' . $currentRowVillage, number_format($maxProjectsInVillage, 2) . ' โครงการ');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':B' . $currentRowVillage);
    $currentRowVillage++;
    
    $avgProjects = round($totalDuplicateProjects / $totalVillages, 2);
    $villageSheet->setCellValue('A' . $currentRowVillage, 'เฉลี่ยโครงการต่อหมู่บ้าน:');
    $villageSheet->setCellValue('C' . $currentRowVillage, number_format($avgProjects, 2) . ' โครงการ');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':B' . $currentRowVillage);
    $currentRowVillage++;
    
    $villageSheet->setCellValue('A' . $currentRowVillage, 'งบประมาณรวมในพื้นที่ซ้ำ:');
    $villageSheet->setCellValue('C' . $currentRowVillage, number_format($totalVillageBudget, 2) . ' บาท');
    $villageSheet->mergeCells('A' . $currentRowVillage . ':B' . $currentRowVillage);
    
    // จัดรูปแบบสถิติ
    $statsRange = 'A' . ($currentRowVillage - 4) . ':L' . $currentRowVillage;
    $villageSheet->getStyle($statsRange)->applyFromArray([
        'font' => ['name' => 'TH SarabunPSK', 'size' => 14],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'DC2626']
            ]
        ]
    ]);
    
} else {
    $villageSheet->setCellValue('A2', 'ไม่พบหมู่บ้าน/ชุมชนที่มีโครงการซ้ำกัน');
    $villageSheet->mergeCells('A2:L2');
    $villageSheet->getStyle('A2')->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'font' => ['size' => 14, 'color' => ['rgb' => 'DC2626']]
    ]);
}

// จัดรูปแบบข้อมูล
if ($villageRowNumber > 1) {
    $dataRangeVillage = 'A2:L' . ($villageRowNumber + 1);
    $villageSheet->getStyle($dataRangeVillage)->applyFromArray([
        'font' => [
            'name' => 'TH SarabunPSK',
            'size' => 14
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '9CA3AF']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true
        ]
    ]);
    
    $villageSheet->setAutoFilter('A1:L' . ($villageRowNumber + 1));
}

// ปรับขนาดคอลัมน์
$villageSheet->getColumnDimension('A')->setWidth(8);  // ลำดับ
$villageSheet->getColumnDimension('B')->setWidth(25); // หมู่บ้าน/ชุมชน
$villageSheet->getColumnDimension('C')->setWidth(10); // หมู่
$villageSheet->getColumnDimension('D')->setWidth(15); // ตำบล
$villageSheet->getColumnDimension('E')->setWidth(15); // อำเภอ
$villageSheet->getColumnDimension('F')->setWidth(15); // จังหวัด
$villageSheet->getColumnDimension('G')->setWidth(12); // จำนวนโครงการ
$villageSheet->getColumnDimension('H')->setWidth(20); // ปีที่ดำเนินการ
$villageSheet->getColumnDimension('I')->setWidth(18); // งบประมาณ
$villageSheet->getColumnDimension('J')->setWidth(40); // ผลิตภัณฑ์
$villageSheet->getColumnDimension('K')->setWidth(40); // ตัวชี้วัด
$villageSheet->getColumnDimension('L')->setWidth(60); // รายชื่อโครงการ

// ตั้งค่าความสูงของแถวให้ปรับตามเนื้อหาอัตโนมัติ
for ($row = 2; $row < $currentRowVillage; $row++) {
    $villageSheet->getRowDimension($row)->setRowHeight(-1); // Auto height
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
