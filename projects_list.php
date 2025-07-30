<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>รายการโครงการทั้งหมด</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .main-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      margin: 2rem auto;
      padding: 2rem;
    }
    
    .page-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      border-radius: 15px;
      margin-bottom: 2rem;
      text-align: center;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .page-header h1 {
      margin: 0;
      font-weight: 700;
      font-size: 2.5rem;
    }
    
    .page-header p {
      margin: 0.5rem 0 0 0;
      opacity: 0.9;
      font-size: 1.1rem;
    }
    
    .stats-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      border: none;
      transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
      transform: translateY(-5px);
    }
    
    .stats-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }
    
    .table-container {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }
    
    #projectTable {
      border: none !important;
    }
    
    #projectTable thead th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      color: white !important;
      border: none !important;
      font-weight: 600;
      text-align: center;
      vertical-align: middle;
    }
    
    #projectTable tbody tr {
      transition: all 0.3s ease;
    }
    
    #projectTable tbody tr:hover {
      background-color: rgba(102, 126, 234, 0.1) !important;
      transform: scale(1.02);
    }
    
    .btn-action {
      border-radius: 20px;
      padding: 0.4rem 0.8rem;
      margin: 0.1rem;
      font-size: 0.85rem;
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .badge-modern {
      border-radius: 20px;
      padding: 0.5rem 1rem;
      font-weight: 500;
    }
    
    .dataTables_wrapper .dataTables_filter input {
      border-radius: 20px;
      border: 2px solid #e9ecef;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .dataTables_wrapper .dataTables_length select {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 0.3rem 0.5rem;
    }
    
    .page-item.active .page-link {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-color: #667eea;
    }
    
    .page-link {
      border-radius: 10px;
      margin: 0 2px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }
    
    .page-link:hover {
      background-color: #667eea;
      border-color: #667eea;
      color: white;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="main-container">
    
    <!-- Page Header -->
    <div class="page-header">
      <h1><i class="fas fa-project-diagram"></i> ระบบจัดการโครงการ</h1>
      <p>รายการโครงการทั้งหมดในระบบ</p>
    </div>

    <!-- Statistics Cards -->
    <?php
    // Get statistics
    $total_projects = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
    $total_villages = $conn->query("SELECT COUNT(DISTINCT CONCAT(VillageName, '-', Subdistrict, '-', District)) as count FROM projectvillages WHERE VillageName IS NOT NULL")->fetch_assoc()['count'];
    $total_budget = $conn->query("SELECT SUM(ApprovedAmount) as total FROM budgetitems")->fetch_assoc()['total'];
    $total_districts = $conn->query("SELECT COUNT(DISTINCT District) as count FROM projectvillages WHERE District IS NOT NULL")->fetch_assoc()['count'];
    $total_schools = $conn->query("SELECT COUNT(DISTINCT SchoolName) as count FROM projectschools WHERE SchoolName IS NOT NULL")->fetch_assoc()['count'];
    $total_products = $conn->query("SELECT COUNT(DISTINCT ProductName) as count FROM projectproducts WHERE ProductName IS NOT NULL")->fetch_assoc()['count'];
    $total_enterprises = $conn->query("SELECT COUNT(*) as count FROM projectenterprises")->fetch_assoc()['count'];
    ?>
    
    <div class="row mb-4">
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-primary">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <h3 class="text-primary"><?= number_format($total_projects) ?></h3>
          <p class="text-muted mb-0">โครงการทั้งหมด</p>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-success">
            <i class="fas fa-home"></i>
          </div>
          <h3 class="text-success"><?= number_format($total_villages) ?></h3>
          <p class="text-muted mb-0">หมู่บ้านที่เข้าร่วม</p>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-info">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h3 class="text-info"><?= number_format($total_districts) ?></h3>
          <p class="text-muted mb-0">อำเภอที่เข้าร่วม</p>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-warning">
            <i class="fas fa-school"></i>
          </div>
          <h3 class="text-warning"><?= number_format($total_schools) ?></h3>
          <p class="text-muted mb-0">โรงเรียนที่เข้าร่วม</p>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-purple" style="color: #6f42c1;">
            <i class="fas fa-box"></i>
          </div>
          <h3 style="color: #6f42c1;"><?= number_format($total_products) ?></h3>
          <p class="text-muted mb-0">ผลิตภัณฑ์ทั้งหมด</p>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon text-danger">
            <i class="fas fa-industry"></i>
          </div>
          <h3 class="text-danger"><?= number_format($total_enterprises) ?></h3>
          <p class="text-muted mb-0">วิสาหกิจ/ผู้ประกอบการ</p>
        </div>
      </div>
    </div>
    
    <!-- Budget Summary Row -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
          <div class="row align-items-center">
            <div class="col-md-2 text-center">
              <div class="stats-icon" style="color: white; font-size: 3rem;">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
            <div class="col-md-10">
              <div class="row text-center">
                <div class="col-md-4">
                  <h2 style="color: white; margin: 0;"><?= number_format(($total_budget ?: 0) / 1000000, 1) ?> ล้านบาท</h2>
                  <p style="color: rgba(255,255,255,0.8); margin: 0;">งบประมาณรวมทั้งหมด</p>
                </div>
                <div class="col-md-4">
                  <h2 style="color: white; margin: 0;"><?= number_format(($total_budget ?: 0) / ($total_projects ?: 1), 0) ?> บาท</h2>
                  <p style="color: rgba(255,255,255,0.8); margin: 0;">งบประมาณเฉลี่ยต่อโครงการ</p>
                </div>
                <div class="col-md-4">
                  <h2 style="color: white; margin: 0;"><?= number_format(($total_budget ?: 0) / ($total_villages ?: 1), 0) ?> บาท</h2>
                  <p style="color: rgba(255,255,255,0.8); margin: 0;">งบประมาณเฉลี่ยต่อหมู่บ้าน</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-list"></i> รายการโครงการ</h4>
        <a href="add_project.php" class="btn btn-primary btn-action">
          <i class="fas fa-plus"></i> เพิ่มโครงการใหม่
        </a>
      </div>

      <table class="table table-hover align-middle" id="projectTable">
        <thead>
          <tr>
            <th><i class="fas fa-barcode"></i> รหัส</th>
            <th><i class="fas fa-calendar"></i> ปี</th>
            <th><i class="fas fa-project-diagram"></i> ชื่อโครงการ</th>
            <th><i class="fas fa-user-tie"></i> ผู้รับผิดชอบ</th>
            <th><i class="fas fa-folder"></i> โครงการหลัก</th>
            <th><i class="fas fa-chess"></i> ยุทธศาสตร์</th>
            <th><i class="fas fa-building"></i> หน่วยงาน</th>
            <th><i class="fas fa-map-marker-alt"></i> จังหวัด</th>
            <th><i class="fas fa-users"></i> กลุ่มเป้าหมาย</th>
            <th><i class="fas fa-home"></i> หมู่บ้าน</th>
            <th class="text-center"><i class="fas fa-cogs"></i> จัดการ</th>
          </tr>
        </thead>
        <tbody>
    <?php
        $result = $conn->query("
            SELECT p.*, 
                   s.StrategyName,
                   mp.MainProjectName,
                   mp.MainProjectCode,
                   GROUP_CONCAT(DISTINCT tg.GroupName SEPARATOR ', ') as TargetGroups,
                   COUNT(DISTINCT pv.ID) as VillageCount
            FROM projects p
            LEFT JOIN projecttargetcounts ptc ON p.ProjectID = ptc.ProjectID
            LEFT JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
            LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
            LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
            LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
            GROUP BY p.ProjectID
            ORDER BY p.ProjectID DESC
        ");
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $target_groups = $row['TargetGroups'] ?: 'ไม่ระบุ';
                $village_count = $row['VillageCount'] ?: 0;
                $main_project = $row['MainProjectName'] ? ($row['MainProjectCode'] . ' - ' . $row['MainProjectName']) : 'ไม่ระบุ';
                
                echo "<tr>
                <td><span class='badge badge-modern bg-secondary'>{$row['ProjectCode']}</span></td>
                <td><span class='badge badge-modern bg-info'>{$row['ProjectYear']}</span></td>
                <td><strong>" . htmlspecialchars($row['ProjectName']) . "</strong></td>
                <td><i class='fas fa-user text-muted me-1'></i>" . htmlspecialchars($row['ResponsiblePerson']) . "</td>
                <td><small class='text-muted'>" . htmlspecialchars($main_project) . "</small></td>
                <td><i class='fas fa-bullseye text-primary me-1'></i>" . htmlspecialchars($row['StrategyName']) . "</td>
                <td><i class='fas fa-building text-success me-1'></i>" . htmlspecialchars($row['AgencyName']) . "</td>
                <td><i class='fas fa-map-marker-alt text-danger me-1'></i>" . htmlspecialchars($row['Province']) . "</td>
                <td><small class='text-muted'>" . htmlspecialchars($target_groups) . "</small></td>
                <td class='text-center'><span class='badge badge-modern bg-success'>{$village_count} หมู่บ้าน</span></td>
                <td class='text-center'>
                  <a href='project_detail.php?id={$row['ProjectID']}' class='btn btn-sm btn-info btn-action' title='ดูรายละเอียด'>
                    <i class='fas fa-eye'></i>
                  </a>
                  <a href='edit_project.php?id={$row['ProjectID']}' class='btn btn-sm btn-warning btn-action' title='แก้ไข'>
                    <i class='fas fa-edit'></i>
                  </a>
                  <a href='delete_project.php?id={$row['ProjectID']}' class='btn btn-sm btn-danger btn-action' 
                     onclick=\"return confirm('คุณต้องการลบโครงการนี้หรือไม่?')\" title='ลบ'>
                    <i class='fas fa-trash'></i>
                  </a>
                </td>
              </tr>";
            }
        } else {
            echo "<tr><td colspan='11' class='text-center text-muted py-4'>
                    <i class='fas fa-inbox fa-3x mb-3 text-muted'></i><br>
                    <h5>ไม่มีข้อมูลโครงการ</h5>
                    <p class='mb-0'>เริ่มต้นด้วยการ<a href='add_project.php' class='text-decoration-none'> เพิ่มโครงการใหม่</a></p>
                  </td></tr>";
        }
    ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#projectTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm me-2',
                titleAttr: 'Export to Excel'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm me-2',
                titleAttr: 'Export to PDF'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm',
                titleAttr: 'Print Table'
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
            search: '_INPUT_',
            searchPlaceholder: 'ค้นหาโครงการ...',
            lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
            infoEmpty: 'แสดง 0 ถึง 0 จาก 0 รายการ',
            infoFiltered: '(กรองจาก _MAX_ รายการทั้งหมด)',
            zeroRecords: 'ไม่พบข้อมูลที่ค้นหา',
            emptyTable: 'ไม่มีข้อมูลในตาราง',
            paginate: {
                first: 'หน้าแรก',
                last: 'หน้าสุดท้าย',
                next: 'ถัดไป',
                previous: 'ก่อนหน้า'
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        order: [[1, 'desc']],
        columnDefs: [
            { targets: [10], orderable: false },
            { targets: [0, 1, 9, 10], className: 'text-center' }
        ]
    });
    
    // Custom search styling
    $('.dataTables_filter input').addClass('form-control');
    $('.dataTables_length select').addClass('form-select');
    
    // Animation for buttons
    $('.btn-action').hover(
        function() {
            $(this).addClass('animate__animated animate__pulse');
        },
        function() {
            $(this).removeClass('animate__animated animate__pulse');
        }
    );
});
</script>
