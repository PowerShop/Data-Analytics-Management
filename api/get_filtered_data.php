<?php
error_reporting(0);
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $type = $_GET['type'] ?? '';
    $project_year_start = $_GET['project_year_start'] ?? '';
    $project_year_end = $_GET['project_year_end'] ?? '';
    $province = $_GET['province'] ?? '';
    $district = $_GET['district'] ?? '';
    $subdistrict = $_GET['subdistrict'] ?? '';
    $main_project = $_GET['main_project'] ?? '';
    $strategy = $_GET['strategy'] ?? '';
    $agency = $_GET['agency'] ?? '';

    $data = [];

    switch ($type) {
        case 'provinces':
            $query = "SELECT DISTINCT pv.Province 
                     FROM projectvillages pv 
                     INNER JOIN projects p ON pv.ProjectID = p.ProjectID 
                     WHERE pv.Province IS NOT NULL AND pv.Province != ''";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            if ($subdistrict) {
                $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
            }
            if ($district) {
                $query .= " AND pv.District = '" . $conn->real_escape_string($district) . "'";
            }
            
            $query .= " ORDER BY pv.Province";
            break;

        case 'districts':
            $query = "SELECT DISTINCT pv.District 
                     FROM projectvillages pv 
                     INNER JOIN projects p ON pv.ProjectID = p.ProjectID 
                     WHERE pv.District IS NOT NULL AND pv.District != ''";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            if ($subdistrict) {
                $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
            }
            
            $query .= " ORDER BY pv.District";
            break;

        case 'subdistricts':
            $query = "SELECT DISTINCT pv.Subdistrict 
                     FROM projectvillages pv 
                     INNER JOIN projects p ON pv.ProjectID = p.ProjectID 
                     WHERE pv.Subdistrict IS NOT NULL AND pv.Subdistrict != ''";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            
            $query .= " ORDER BY pv.Subdistrict";
            break;

        case 'main_projects':
            $query = "SELECT mp.MainProjectID, mp.MainProjectName 
                     FROM mainprojects mp 
                     INNER JOIN projects p ON mp.MainProjectID = p.MainProjectID 
                     WHERE 1=1";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            
            // เพิ่มการกรองตาม location ถ้ามี
            if ($province || $district || $subdistrict) {
                $query .= " AND EXISTS (
                    SELECT 1 FROM projectvillages pv 
                    WHERE pv.ProjectID = p.ProjectID";
                
                if ($province) {
                    $query .= " AND pv.Province = '" . $conn->real_escape_string($province) . "'";
                }
                if ($district) {
                    $query .= " AND pv.District = '" . $conn->real_escape_string($district) . "'";
                }
                if ($subdistrict) {
                    $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
                }
                
                $query .= ")";
            }
            
            $query .= " GROUP BY mp.MainProjectID, mp.MainProjectName ORDER BY mp.MainProjectName";
            break;

        case 'strategies':
            $query = "SELECT s.StrategyID, s.StrategyName 
                     FROM strategies s 
                     INNER JOIN projects p ON s.StrategyID = p.StrategyID 
                     WHERE 1=1";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            
            // เพิ่มการกรองตาม location ถ้ามี
            if ($province || $district || $subdistrict) {
                $query .= " AND EXISTS (
                    SELECT 1 FROM projectvillages pv 
                    WHERE pv.ProjectID = p.ProjectID";
                
                if ($province) {
                    $query .= " AND pv.Province = '" . $conn->real_escape_string($province) . "'";
                }
                if ($district) {
                    $query .= " AND pv.District = '" . $conn->real_escape_string($district) . "'";
                }
                if ($subdistrict) {
                    $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
                }
                
                $query .= ")";
            }
            
            if ($main_project) {
                $query .= " AND p.MainProjectID = " . intval($main_project);
            }
            
            $query .= " GROUP BY s.StrategyID, s.StrategyName ORDER BY s.StrategyName";
            break;

        case 'agencies':
            $query = "SELECT DISTINCT p.AgencyName 
                     FROM projects p 
                     WHERE p.AgencyName IS NOT NULL AND p.AgencyName != ''";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            
            // เพิ่มการกรองตาม location ถ้ามี
            if ($province || $district || $subdistrict) {
                $query .= " AND EXISTS (
                    SELECT 1 FROM projectvillages pv 
                    WHERE pv.ProjectID = p.ProjectID";
                
                if ($province) {
                    $query .= " AND pv.Province = '" . $conn->real_escape_string($province) . "'";
                }
                if ($district) {
                    $query .= " AND pv.District = '" . $conn->real_escape_string($district) . "'";
                }
                if ($subdistrict) {
                    $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
                }
                
                $query .= ")";
            }
            
            if ($main_project) {
                $query .= " AND p.MainProjectID = " . intval($main_project);
            }
            if ($strategy) {
                $query .= " AND p.StrategyID = " . intval($strategy);
            }
            
            $query .= " ORDER BY p.AgencyName";
            break;

        case 'target_groups':
            $query = "SELECT tg.GroupID, tg.GroupName 
                     FROM targetgroups tg 
                     INNER JOIN projecttargetgroups ptg ON tg.GroupID = ptg.GroupID 
                     INNER JOIN projects p ON ptg.ProjectID = p.ProjectID 
                     WHERE 1=1";
            
            if ($project_year_start) {
                $query .= " AND p.ProjectYear >= " . intval($project_year_start);
            }
            if ($project_year_end) {
                $query .= " AND p.ProjectYear <= " . intval($project_year_end);
            }
            
            // เพิ่มการกรองตาม location ถ้ามี
            if ($province || $district || $subdistrict) {
                $query .= " AND EXISTS (
                    SELECT 1 FROM projectvillages pv 
                    WHERE pv.ProjectID = p.ProjectID";
                
                if ($province) {
                    $query .= " AND pv.Province = '" . $conn->real_escape_string($province) . "'";
                }
                if ($district) {
                    $query .= " AND pv.District = '" . $conn->real_escape_string($district) . "'";
                }
                if ($subdistrict) {
                    $query .= " AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "'";
                }
                
                $query .= ")";
            }
            
            if ($main_project) {
                $query .= " AND p.MainProjectID = " . intval($main_project);
            }
            if ($strategy) {
                $query .= " AND p.StrategyID = " . intval($strategy);
            }
            if ($agency) {
                $query .= " AND p.AgencyName = '" . $conn->real_escape_string($agency) . "'";
            }
            
            $query .= " GROUP BY tg.GroupID, tg.GroupName ORDER BY tg.GroupName";
            break;

        default:
            echo json_encode(['error' => 'Invalid type']);
            exit;
    }

    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
