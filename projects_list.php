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
    
    /* Search Highlight Styles */
    .highlight {
      background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%) !important;
      color: #333 !important;
      font-weight: bold !important;
      padding: 2px 4px !important;
      border-radius: 3px !important;
      box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3) !important;
    }
    
    .highlight-animation {
      animation: highlightPulse 1s ease-in-out;
    }
    
    @keyframes highlightPulse {
      0% { background-color: #ffd700; }
      50% { background-color: #ffed4e; }
      100% { background-color: #ffd700; }
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
            <th class="text-center"><i class="fas fa-cogs"></i> จัดการ</th>
          </tr>
        </thead>
        <tbody>
    <?php
        $result = $conn->query("
            SELECT p.*, 
                   s.StrategyName,
                   mp.MainProjectName,
                   mp.MainProjectCode
            FROM projects p
            LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
            LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
            ORDER BY p.ProjectID DESC
        ");
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $main_project = $row['MainProjectName'] ? ($row['MainProjectCode'] . ' - ' . $row['MainProjectName']) : 'ไม่ระบุ';
                
                echo "<tr>
                <td><span class='badge badge-modern bg-secondary'>{$row['ProjectCode']}</span></td>
                <td><span class='badge badge-modern bg-info'>{$row['ProjectYear']}</span></td>
                <td><strong>" . htmlspecialchars($row['ProjectName']) . "</strong></td>
                <td><i class='fas fa-user text-muted me-1'></i>" . htmlspecialchars($row['ResponsiblePerson']) . "</td>
                <td><small class='text-muted'>" . htmlspecialchars($main_project) . "</small></td>
                <td><i class='fas fa-bullseye text-primary me-1'></i>" . htmlspecialchars($row['StrategyName']) . "</td>
                <td><i class='fas fa-building text-success me-1'></i>" . htmlspecialchars($row['AgencyName']) . "</td>
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
            echo "<tr><td colspan='8' class='text-center text-muted py-4'>
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

<script>
$(document).ready(function() {
    var table = $('#projectTable').DataTable({
        responsive: true,
        dom: 'lfrtip',
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
        lengthMenu: [[25, 50, 75, 100, -1], [25, 50, 75, 100, "ทั้งหมด"]],
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [7], orderable: false },
            { targets: [0, 1, 7], className: 'text-center' }
        ]
    });
    
    // Function to highlight search terms
    function highlightSearchTerm(searchTerm) {
        // Remove previous highlights
        $('#projectTable tbody td').each(function() {
            var html = $(this).html();
            html = html.replace(/<span class="highlight[^>]*">(.*?)<\/span>/gi, '$1');
            $(this).html(html);
        });
        
        // Add new highlights if search term exists and is not just whitespace
        if (searchTerm && searchTerm.trim().length > 0) {
            var trimmedTerm = searchTerm.trim();
            var regex = new RegExp('(' + trimmedTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            
            $('#projectTable tbody td').each(function() {
                var cell = $(this);
                // Skip action column (last column)
                if (cell.index() === 7) return;
                
                var originalHtml = cell.html();
                
                // Only highlight if the cell contains text and the search term is meaningful
                if (originalHtml && originalHtml.trim() !== '' && trimmedTerm.length > 0) {
                    // Create a temporary div to work with text content
                    var tempDiv = $('<div>').html(originalHtml);
                    
                    // Function to highlight text nodes only
                    function highlightTextNodes(node) {
                        if (node.nodeType === 3) { // Text node
                            var text = node.nodeValue;
                            if (regex.test(text)) {
                                var highlightedText = text.replace(regex, '<span class="highlight highlight-animation">$1</span>');
                                var wrapper = document.createElement('span');
                                wrapper.innerHTML = highlightedText;
                                
                                // Replace the text node with highlighted content
                                var parent = node.parentNode;
                                while (wrapper.firstChild) {
                                    parent.insertBefore(wrapper.firstChild, node);
                                }
                                parent.removeChild(node);
                            }
                        } else if (node.nodeType === 1) { // Element node
                            // Skip script and style elements
                            if (node.tagName && (node.tagName.toLowerCase() === 'script' || node.tagName.toLowerCase() === 'style')) {
                                return;
                            }
                            
                            // Process child nodes
                            var children = Array.from(node.childNodes);
                            children.forEach(function(child) {
                                highlightTextNodes(child);
                            });
                        }
                    }
                    
                    // Apply highlighting to text nodes only
                    tempDiv.get(0).childNodes.forEach(function(node) {
                        highlightTextNodes(node);
                    });
                    
                    cell.html(tempDiv.html());
                }
            });
        }
    }
    
    // Listen for search input changes
    table.on('search.dt', function() {
        var searchTerm = table.search();
        highlightSearchTerm(searchTerm);
    });
    
    // Listen for draw events (pagination, sorting, etc.)
    table.on('draw.dt', function() {
        var searchTerm = table.search();
        if (searchTerm) {
            setTimeout(function() {
                highlightSearchTerm(searchTerm);
            }, 100);
        }
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
