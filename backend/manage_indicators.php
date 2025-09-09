<?php
include '../db.php';
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการตัวชี้วัด</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">


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

        .year-tabs {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
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

        #indicatorsTable thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="main-container">

            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-chart-bar"></i> จัดการตัวชี้วัด</h1>
                <p class="mb-0">สร้างและจัดการตัวชี้วัดสำหรับโครงการ</p>
            </div>

            <!-- Year Filter Tabs -->
            <div class="year-tabs">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label for="yearFilter" class="form-label"><i class="fas fa-calendar"></i> เลือกปี:</label>
                        <select id="yearFilter" class="form-select">
                            <?php
                            $currentYear = date('Y') + 543; // ปี พ.ศ.
                            for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                $selected = $year == $currentYear ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#addIndicatorModal" onclick="openAddModal()">
                            <i class="fas fa-plus"></i> เพิ่มตัวชี้วัดใหม่
                        </button>
                    </div>
                </div>
            </div>

            <!-- Indicators Table -->
            <div class="table-container">
                <table class="table table-hover" id="indicatorsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-chart-line"></i> ชื่อตัวชี้วัด</th>
                            <th><i class="fas fa-balance-scale"></i> หน่วย</th>
                            <th><i class="fas fa-calendar"></i> ปี</th>
                            <th><i class="fas fa-toggle-on"></i> สถานะ</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Indicator Modal -->
    <div class="modal fade" id="addIndicatorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> เพิ่มตัวชี้วัดใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="indicatorForm">
                    <div class="modal-body">
                        <input type="hidden" id="indicatorId" name="indicatorId">

                        <div class="mb-3">
                            <label for="indicatorName" class="form-label">ชื่อตัวชี้วัด *</label>
                            <input type="text" class="form-control" id="indicatorName" name="indicatorName" required>
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">หน่วย</label>
                            <input type="text" class="form-control" id="unit" name="unit"
                                placeholder="เช่น คน, บาท, กลุ่ม">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">ปี *</label>
                            <select class="form-select" id="year" name="year" required>
                                <?php
                                for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                    $selected = $year == $currentYear ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isActive" name="isActive" checked>
                                <label class="form-check-label" for="isActive">
                                    เปิดใช้งาน
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let table;

        $(document).ready(function () {
            // Initialize DataTable
            table = $('#indicatorsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                pageLength: 25,
                order: [[4, 'desc'], [0, 'desc']] // Sort by Year desc, then ID desc
            });

            // Load indicators
            loadIndicators();

            // Year filter change
            $('#yearFilter').change(function () {
                loadIndicators();
            });

            // Form submission
            $('#indicatorForm').submit(function (e) {
                e.preventDefault();
                saveIndicator();
            });
        });

        function loadIndicators() {
            const year = $('#yearFilter').val();

            $.ajax({
                url: './api/get_indicators.php',
                method: 'GET',
                data: {
                    year: year
                },
                dataType: 'json',
                success: function (response) {
                    table.clear();

                    if (response.success && response.data.length > 0) {
                        response.data.forEach(function (indicator) {
                            const statusBadge = indicator.IsActive == 1 ?
                                '<span class="badge bg-success">ใช้งาน</span>' :
                                '<span class="badge bg-secondary">ปิดใช้งาน</span>';

                            const actions = `
                                <button class="btn btn-sm btn-warning btn-action" onclick="editIndicator(${indicator.IndicatorID})" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-action" onclick="deleteIndicator(${indicator.IndicatorID})" title="ลบ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;

                            table.row.add([
                                indicator.IndicatorID,
                                indicator.IndicatorName,
                                indicator.Unit || '-',
                                indicator.Year,
                                statusBadge,
                                actions
                            ]);
                        });
                    }

                    table.draw();
                },
                error: function () {
                    alert('เกิดข้อผิดพลาด! ไม่สามารถโหลดข้อมูลได้');
                }
            });
        }

        function openAddModal() {
            // ลบ backdrop เก่าและรีเซ็ต modal state
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');

            // รีเซ็ตฟอร์ม
            $('#indicatorForm')[0].reset();
            $('#indicatorId').val('');
            $('.modal-title').html('<i class="fas fa-plus"></i> เพิ่มตัวชี้วัดใหม่');
        }

        function saveIndicator() {
            const formData = {
                indicatorId: $('#indicatorId').val(),
                indicatorName: $('#indicatorName').val(),
                unit: $('#unit').val(),
                description: $('#description').val(),
                year: $('#year').val(),
                isActive: $('#isActive').is(':checked') ? 1 : 0
            };

            // Debug: แสดงข้อมูลที่จะส่ง
            console.log('Sending data:', formData);

            $.ajax({
                url: './api/save_indicator.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    // Debug: แสดง response ที่ได้รับ
                    console.log('Response received:', response);

                    if (response.success) {
                        $('#addIndicatorModal').modal('hide');
                        // ลบ backdrop ที่อาจเหลือค้าง
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');

                        $('#indicatorForm')[0].reset();
                        $('#indicatorId').val('');
                        $('.modal-title').html('<i class="fas fa-plus"></i> เพิ่มตัวชี้วัดใหม่');
                        loadIndicators();
                        alert('บันทึกข้อมูลเรียบร้อยแล้ว');
                    } else {
                        alert('เกิดข้อผิดพลาด! ' + (response.message || 'ไม่สามารถบันทึกข้อมูลได้'));
                    }
                },
                error: function () {
                    alert('เกิดข้อผิดพลาด! ไม่สามารถบันทึกข้อมูลได้');
                }
            });
        }

        function editIndicator(id) {
            // ปิด modal ที่เปิดอยู่ก่อน (ถ้ามี)
            $('#addIndicatorModal').modal('hide');

            $.ajax({
                url: './api/get_indicator.php',
                method: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        const data = response.data;
                        $('#indicatorId').val(data.IndicatorID);
                        $('#indicatorName').val(data.IndicatorName);
                        $('#unit').val(data.Unit);
                        $('#description').val(data.Description);
                        $('#year').val(data.Year);
                        $('#isActive').prop('checked', data.IsActive == 1);
                        $('.modal-title').html('<i class="fas fa-edit"></i> แก้ไขตัวชี้วัด');

                        // ลบ backdrop เก่าก่อนเปิด modal ใหม่
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');

                        $('#addIndicatorModal').modal('show');
                    } else {
                        alert('ไม่พบข้อมูล! ไม่พบข้อมูลตัวชี้วัดที่ต้องการ');
                    }
                },
                error: function () {
                    alert('เกิดข้อผิดพลาด! ไม่สามารถโหลดข้อมูลได้');
                }
            });
        }

        function deleteIndicator(id) {
            if (confirm('คุณต้องการลบตัวชี้วัดนี้หรือไม่?')) {
                $.ajax({
                    url: './api/delete_indicator.php',
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            loadIndicators();
                            alert('ลบข้อมูลสำเร็จ');
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                    }
                });
            }
        }

        // Reset form when modal closes
        $('#addIndicatorModal').on('hidden.bs.modal', function () {
            $('#indicatorForm')[0].reset();
            $('#indicatorId').val('');
            $('.modal-title').html('<i class="fas fa-plus"></i> เพิ่มตัวชี้วัดใหม่');
            // ลบ backdrop ที่อาจเหลือค้าง
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
        });
    </script>
</body>
<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</html>