<?php

include_once "./includes/Connection.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$isUser = $_SESSION['user_type'] == 'student' || $_SESSION['user_type'] == 'instructor';

$CONNECTION = new Connection();

$users = $CONNECTION->Select("user", ["user_type" => "student"], true);
$admins = $CONNECTION->Select("user", ["user_type" => "admin"], true);
$equipments = $CONNECTION->Select("equipment_details", ['deleted' => '0'], true);
$categories = $CONNECTION->Select("equipment_info", ['deleted' => '0'], true);
$borrows = $CONNECTION->Select("borrow_requests", $isUser ? ["user_id" => $_SESSION['user_id']] : null, true);
$materialRequests = $CONNECTION->Select("material_get_requests", $isUser ? ["user_id" => $_SESSION['user_id']] : null, true);

$inUsed = $CONNECTION->Select("equipment_details", ["in_used" => "yes"], true);

$alertItemsQuery = "
    SELECT ed.*, ei.name, ei.category, ei.alert_level, ei.picture
    FROM equipment_details ed
    JOIN equipment_info ei ON ed.equipment_id = ei.id
    WHERE ed.deleted = '0' 
    AND ei.category = 'material' 
    AND ed.quantity <= ed.alert_level
    AND ed.borrow_availability = 0
";

$alertItems = $CONNECTION->Query($alertItemsQuery, true);
$alertItemsCount = count($alertItems);

// Get the 5 most recently borrowed equipment and tools without repetition
$recentlyBorrowedQuery = "
    SELECT ei.category, ed.id, ei.name, ed.serials, COUNT(*) as borrow_count
    FROM borrow_requests br
    JOIN equipment_details ed ON br.item_id = ed.id
    JOIN equipment_info ei ON ed.equipment_id = ei.id
    WHERE ei.category IN ('equipment', 'tools')
    " . ($isUser ? "AND br.user_id = '" . $_SESSION['user_id'] . "'" : "") . "
    GROUP BY ei.category, ed.id, ei.name, ed.serials
    ORDER BY MAX(br.date_created) DESC
    LIMIT 10
";

$recentlyBorrowedResult = $CONNECTION->Query($recentlyBorrowedQuery, true);
$recentlyBorrowed = [
    'equipment' => [],
    'tools' => []
];

foreach ($recentlyBorrowedResult as $item) {
    if (count($recentlyBorrowed[$item['category']]) < 5) {
        $recentlyBorrowed[$item['category']][] = $item;
    }
    if (count($recentlyBorrowed['equipment']) == 5 && count($recentlyBorrowed['tools']) == 5) {
        break;
    }
}

$alertEquipments = array_filter($categories, function ($category) use ($CONNECTION, $equipments) {
    $count = $CONNECTION->CountRow("equipment_details", ["equipment_id" => $category['id'], "deleted" => '0']);
    if ($category['category'] == 'material') {
        $count = count(array_filter($equipments, function ($equipment) use ($category) {
            return $equipment['equipment_id'] == $category['id'] && $equipment['quantity'] > 0;
        }));
    }
    return $count <= $category['alert_level'];
});

// Define card data
$availableCards = [
    [
        "title" => "In-Use Equipment",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "equipment" && $record["in_used"] === "yes";
        })),
        "icon" => "fas fa-cogs mr-2",
        "link" => "catalog.php?availability=available"
    ],
    [
        "title" => "In-Use Tools",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "tools" && $record["in_used"] === "yes";
        })),
        "icon" => "fas fa-tools mr-2",
        "link" => "catalog.php?availability=available"
    ]
];

// New cards for Equipment and Tools Availability
$equipmentAvailabilityCards = [
    [
        "title" => "Available Equipment",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "equipment" && $record["in_used"] === "no";
        })),
        "icon" => "fas fa-check-circle mr-2",
        "link" => "catalog.php?availability=available&category=equipment"
    ],
    [
        "title" => "Not Available Equipment",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "equipment" && $record["in_used"] === "yes";
        })),
        "icon" => "fas fa-times-circle mr-2",
        "link" => "catalog.php?availability=not_available&category=equipment"
    ]
];

$toolsAvailabilityCards = [
    [
        "title" => "Available Tools",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "tools" && $record["in_used"] === "no";
        })),
        "icon" => "fas fa-check-circle mr-2",
        "link" => "catalog.php?availability=available&category=tools"
    ],
    [
        "title" => "Not Available Tools",
        "count" => count(array_filter($equipments, function ($record) use ($CONNECTION) {
            $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]], false);
            return $equipment["category"] === "tools" && $record["in_used"] === "yes";
        })),
        "icon" => "fas fa-times-circle mr-2",
        "link" => "catalog.php?availability=not_available&category=tools"
    ]
];

if ($isUser) {
    $EquipmentborrowRequestsCards = [
        [
            "title" => "Pending",
            "count" => count(array_filter($borrows, function ($borrow) use ($CONNECTION) {
                $item = $CONNECTION->Select("equipment_details", ["id" => $borrow["item_id"]], false);
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $item["equipment_id"]], false);
                return $equipment["category"] === "equipment" && $borrow["request_status"] === "pending";
            })),
            "icon" => "fas fa-clock mr-2",
            "link" => "borrow_requests.php"
        ],
        [
            "title" => "Accepted",
            "count" => count(array_filter($borrows, function ($borrow) use ($CONNECTION) {
                $item = $CONNECTION->Select("equipment_details", ["id" => $borrow["item_id"]], false);
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $item["equipment_id"]], false);
                return $equipment["category"] === "equipment" && $borrow["request_status"] === "accepted";
            })),
            "icon" => "fas fa-check-circle mr-2",
            "link" => "borrow_requests.php"
        ],
    ];

    $ToolsborrowRequestsCards = [
        [
            "title" => "Pending",
            "count" => count(array_filter($borrows, function ($borrow) use ($CONNECTION) {
                $item = $CONNECTION->Select("equipment_details", ["id" => $borrow["item_id"]], false);
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $item["equipment_id"]], false);
                return $equipment["category"] === "tools" && $borrow["request_status"] === "pending";
            })),
            "icon" => "fas fa-clock mr-2",
            "link" => "borrow_requests.php"
        ],
        [
            "title" => "Accepted",
            "count" => count(array_filter($borrows, function ($borrow) use ($CONNECTION) {
                $item = $CONNECTION->Select("equipment_details", ["id" => $borrow["item_id"]], false);
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $item["equipment_id"]], false);
                return $equipment["category"] === "tools" && $borrow["request_status"] === "accepted";
            })),
            "icon" => "fas fa-check-circle mr-2",
            "link" => "borrow_requests.php"
        ],
    ];
} else {
    $EquipmentborrowRequestsCards = [
        [
            "title" => "Pending",
            "count" => $CONNECTION->CountRow("borrow_requests", ["request_status" => "pending"]),
            "icon" => "fas fa-clock mr-2",
            "link" => "borrow_requests.php"
        ],
        [
            "title" => "Accepted",
            "count" => $CONNECTION->CountRow("borrow_requests", ["request_status" => "accepted"]),
            "icon" => "fas fa-check-circle mr-2",
            "link" => "borrow_requests.php"
        ],
    ];

    $ToolsborrowRequestsCards = [
        [
            "title" => "Pending",
            "count" => $CONNECTION->CountRow("borrow_requests", ["request_status" => "pending"]),
            "icon" => "fas fa-clock mr-2",
            "link" => "borrow_requests.php"
        ],
        [
            "title" => "Accepted",
            "count" => $CONNECTION->CountRow("borrow_requests", ["request_status" => "accepted"]),
            "icon" => "fas fa-check-circle mr-2",
            "link" => "borrow_requests.php"
        ],
    ];
}

// Get the most borrowed items for equipment and tools categories
if ($isUser) {
    $mostBorrowedQuery = "SELECT ei.category, ed.id, ei.name, COUNT(*) as borrow_count 
                          FROM borrow_requests br
                          JOIN equipment_details ed ON br.item_id = ed.id
                          JOIN equipment_info ei ON ed.equipment_id = ei.id
                          WHERE ei.category IN ('equipment', 'tools')
                          AND br.user_id = '" . $_SESSION['user_id'] . "'
                          GROUP BY ei.category, ed.id, ei.name
                          ORDER BY borrow_count DESC
                          LIMIT 10";
} else {
    $mostBorrowedQuery = "SELECT ei.category, ed.id, ei.name, COUNT(*) as borrow_count 
                          FROM borrow_requests br
                          JOIN equipment_details ed ON br.item_id = ed.id
                          JOIN equipment_info ei ON ed.equipment_id = ei.id
                          WHERE ei.category IN ('equipment', 'tools')
                          GROUP BY ei.category, ed.id, ei.name
                          ORDER BY borrow_count DESC
                          LIMIT 10";
}

$mostBorrowedResult = $CONNECTION->Query($mostBorrowedQuery, true);

// Get the most requested materials
if ($isUser) {
    $mostRequestedMaterialsQuery = "SELECT ei.name, SUM(mgr.quantity) as total_quantity
                                    FROM material_get_requests mgr
                                    JOIN equipment_details ed ON mgr.item_id = ed.id
                                    JOIN equipment_info ei ON ed.equipment_id = ei.id
                                    WHERE ei.category = 'material'
                                    AND mgr.user_id = '" . $_SESSION['user_id'] . "'
                                    GROUP BY ei.name
                                    ORDER BY total_quantity DESC
                                    LIMIT 5";
} else {
    $mostRequestedMaterialsQuery = "SELECT ei.name, SUM(mgr.quantity) as total_quantity
                                    FROM material_get_requests mgr
                                    JOIN equipment_details ed ON mgr.item_id = ed.id
                                    JOIN equipment_info ei ON ed.equipment_id = ei.id
                                    WHERE ei.category = 'material'
                                    GROUP BY ei.name
                                    ORDER BY total_quantity DESC
                                    LIMIT 5";
}

$mostRequestedMaterialsResult = $CONNECTION->Query($mostRequestedMaterialsQuery, true);

// Separate results for equipment and tools
$equipmentResult = array_filter($mostBorrowedResult, function($item) {
    return $item['category'] === 'equipment';
});

$toolsResult = array_filter($mostBorrowedResult, function($item) {
    return $item['category'] === 'tools';
});

// Get total counts for equipment, tools, and materials
$totalEquipment = $CONNECTION->CountRow("equipment_info", ["category" => "equipment", "deleted" => "0"]);
$totalTools = $CONNECTION->CountRow("equipment_info", ["category" => "tools", "deleted" => "0"]);
$totalMaterials = $CONNECTION->CountRow("equipment_info", ["category" => "material", "deleted" => "0"]);

$totalCards = [
    [
        "title" => "Equipment",
        "count" => $totalEquipment,
        "icon" => "fas fa-cogs"
    ],
    [
        "title" => "Tools",
        "count" => $totalTools,
        "icon" => "fas fa-tools"
    ],
    [
        "title" => "Materials",
        "count" => $totalMaterials,
        "icon" => "fas fa-box"
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./style/styles.css">
    <link rel="stylesheet" href="./style/suchStyles.css">
</head>

<body>
<?php include "./includes/user_navigationbar.php"; ?>

<!-- Slide-in Menu -->
<?php include_once("./includes/menu.php") ?>

<div class="container-wrapper">
    <div class="main-content-container" id="container">
        <div class="container-fluid mt-4">
            <h1 class="mb-4 text-primary">Dashboard</h1>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <i class="fas fa-clipboard-list card-icon"></i>
                                <h3>Equipment Requests</h3>
                                <p><i class="fas fa-clock mr-2"></i>Pending: <?= $EquipmentborrowRequestsCards[0]['count'] ?></p>
                                <p><i class="fas fa-check-circle mr-2"></i>Accepted: <?= $EquipmentborrowRequestsCards[1]['count'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <i class="fas fa-clipboard-check card-icon"></i>
                                <h3>Tools Requests</h3>
                                <p><i class="fas fa-clock mr-2"></i>Pending: <?= $ToolsborrowRequestsCards[0]['count'] ?></p>
                                <p><i class="fas fa-check-circle mr-2"></i>Accepted: <?= $ToolsborrowRequestsCards[1]['count'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <i class="fas fa-cogs card-icon"></i>
                                <h3>Equipment Availability</h3>
                                <p><i class="fas fa-check-circle mr-2"></i>Available: <?= $equipmentAvailabilityCards[0]['count'] ?></p>
                                <p><i class="fas fa-times-circle mr-2"></i>Not Available: <?= $equipmentAvailabilityCards[1]['count'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <i class="fas fa-tools card-icon"></i>
                                <h3>Tools Availability</h3>
                                <p><i class="fas fa-check-circle mr-2"></i>Available: <?= $toolsAvailabilityCards[0]['count'] ?></p>
                                <p><i class="fas fa-times-circle mr-2"></i>Not Available: <?= $toolsAvailabilityCards[1]['count'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="dashboard-card">
                                <h3>Recently Borrowed Items</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Equipment</h4>
                                        <ul class="list-unstyled">
                                            <?php foreach ($recentlyBorrowed['equipment'] as $item): ?>
                                                <li class="recently-borrowed-item">
                                                    <i class="fas fa-cog"></i>
                                                    <div>
                                                        <span class="font-weight-bold"><?= $item['name'] ?></span><br>
                                                        <small class="text-muted">Serial: <?= $item['serials'] ?></small><br>
                                                        <small class="text-muted">Times borrowed: <?= $item['borrow_count'] ?></small>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Tools</h4>
                                        <ul class="list-unstyled">
                                            <?php foreach ($recentlyBorrowed['tools'] as $item): ?>
                                                <li class="recently-borrowed-item">
                                                    <i class="fas fa-wrench"></i>
                                                    <div>
                                                        <span class="font-weight-bold"><?= $item['name'] ?></span><br>
                                                        <small class="text-muted">Serial: <?= $item['serials'] ?></small><br>
                                                        <small class="text-muted">Times borrowed: <?= $item['borrow_count'] ?></small>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="row d-flex justify-content-between">
                        <?php foreach ($totalCards as $card): ?>
                            <div class="col-4 mb-3">
                                <div class="small-card text-center">
                                    <i class="<?= $card['icon'] ?> mb-2"></i>
                                    <h4><?= $card['title'] ?></h4>
                                    <p><?= $card['count'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!$isUser): ?>
                        <div class="dashboard-card" style="height: 360px; max-height: 360px; overflow-y: auto;">
                        <h3>Materials on Alert Level</h3>
                        <div class="alert-items">
                            <?php foreach ($alertItems as $item): ?>
                                <div class="alert-item">
                                    <p><i class="fas fa-exclamation-triangle mr-2"></i><?= $item['name'] ?></p>
                                    <p><strong>Serial:</strong> <?= $item['serials'] ?></p>
                                    <div class="alert-level-indicator" style="width: <?= max(0, min(100, ($item['quantity'] / $item['alert_level']) * 100)) ?>%;"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="dashboard-card mt-4" style="height: 325px; max-height: 325px; overflow: hidden;">
                        <h3>Late Returns</h3>
                        <div class="splide">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php
                                    if ($isUser) {
                                        $lateReturnsQuery = "
                                        SELECT ei.name AS item_name, ed.serials, br.date_created,
                                               DATEDIFF(CURRENT_DATE(), DATE_ADD(br.date_created, INTERVAL 1 DAY)) AS days_late
                                        FROM borrow_requests br
                                        JOIN equipment_details ed ON br.item_id = ed.id
                                        JOIN equipment_info ei ON ed.equipment_id = ei.id
                                        WHERE br.borrow_status = 'not_returned' AND br.request_status = 'accepted'
                                        AND DATE_ADD(br.date_created, INTERVAL 1 DAY) < CURRENT_DATE()
                                        AND br.user_id = '".$_SESSION['user_id']."'
                                        ORDER BY days_late DESC
                                        ";
                                        $lateReturns = $CONNECTION->Query($lateReturnsQuery, true);
                                    } else {
                                        $lateReturnsQuery = "
                                        SELECT u.id, u.first_name, u.last_name, u.profile_picture, 
                                               ei.name AS item_name, ed.serials, br.date_created,
                                               DATEDIFF(CURRENT_DATE(), DATE_ADD(br.date_created, INTERVAL 1 DAY)) AS days_late
                                        FROM borrow_requests br
                                        JOIN user u ON br.user_id = u.id
                                        JOIN equipment_details ed ON br.item_id = ed.id
                                        JOIN equipment_info ei ON ed.equipment_id = ei.id
                                        WHERE br.borrow_status = 'not_returned' AND br.request_status = 'accepted'
                                        AND DATE_ADD(br.date_created, INTERVAL 1 DAY) < CURRENT_DATE()
                                        ORDER BY days_late DESC
                                        ";
                                        $lateReturns = $CONNECTION->Query($lateReturnsQuery, true);
                                    }

                                    $lateReturnsCount = count($lateReturns);
                                    ?>
                                    <li class="splide__slide">
                                        <div class="late-return-summary">
                                            <h4>Total Late Returns: <?= $lateReturnsCount ?></h4>
                                        </div>
                                    </li>
                                    <?php
                                    if (!empty($lateReturns)):
                                        foreach ($lateReturns as $index => $late):
                                    ?>
                                        <li class="splide__slide">
                                            <div class="late-return-card">
                                                <div class="card-indicator"><?= $index + 1 ?> / <?= $lateReturnsCount ?></div>
                                                <?php if (!$isUser): ?>
                                                <div class="profile-image">
                                                    <img src="<?=  !empty($late['profile_picture']) ? './uploads/' . $late['profile_picture'] : GetPhotoURLByName($late['first_name']) ?>">
                                                </div>
                                                <?php endif; ?>
                                                <div class="user-info">
                                                    <?php if (!$isUser): ?>
                                                    <h4><?= $late['first_name'] . ' ' . $late['last_name'] ?></h4>
                                                    <?php endif; ?>
                                                    <p><strong>Item:</strong> <?= $late['item_name'] ?></p>
                                                    <p><strong>Serial:</strong> <?= $late['serials'] ?></p>
                                                    <p><strong>Borrowed:</strong> <?= date('M d, Y', strtotime($late['date_created'])) ?></p>
                                                    <p><strong>Days Late:</strong> <?= $late['days_late'] ?></p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                        <li class="splide__slide">
                                            <div class="empty-message">
                                                <p>No late returns at the moment.</p>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <style>
                        .late-return-card {
                            display: flex;
                            align-items: center;
                            background-color: #f8f9fa;
                            border-radius: 8px;
                            padding: 15px;
                            transition: all 0.3s ease;
                            position: relative;
                        }
                        .profile-image {
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            overflow: hidden;
                            margin-right: 15px;
                        }
                        .profile-image img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .user-info {
                            flex: 1;
                        }
                        .user-info h4 {
                            margin: 0 0 5px 0;
                            color: #2c3e50;
                        }
                        .user-info p {
                            margin: 0 0 3px 0;
                            font-size: 0.9rem;
                            color: #34495e;
                        }
                        .empty-message {
                            text-align: center;
                            padding: 20px;
                            color: #7f8c8d;
                            font-style: italic;
                        }
                        .late-return-summary {
                            text-align: center;
                            padding: 20px;
                            background-color: #f8f9fa;
                            color: #2c3e50;
                            border-radius: 8px;
                        }
                        .late-return-summary h4 {
                            margin: 0;
                            font-size: 1.2rem;
                        }
                        .card-indicator {
                            position: absolute;
                            top: 10px;
                            right: 10px;
                            background-color: #3498db;
                            color: #ffffff;
                            padding: 2px 8px;
                            border-radius: 10px;
                            font-size: 0.8rem;
                        }
                    </style>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
                    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Splide('.splide', {
                                type: 'loop',
                                perPage: 1,
                                arrows: false,
                                pagination: false,
                                autoplay: true,
                                interval: 5000,
                            }).mount();
                        });
                    </script>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="chart-container">
                        <canvas id="equipment-chart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container">
                        <canvas id="tools-chart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container">
                        <canvas id="materials-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("./includes/menu.php") ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($mostBorrowedResult) || !empty($mostRequestedMaterialsResult)): ?>
        function createChart(ctx, data, label, yAxisLabel) {
            const colors = [
                '#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6',
                '#1abc9c', '#f39c12', '#d35400', '#c0392b', '#8e44ad'
            ];
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        label: yAxisLabel,
                        data: data.map(item => item.borrow_count || item.total_quantity),
                        backgroundColor: data.map((_, index) => colors[index % colors.length])
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: yAxisLabel,
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 90
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: label,
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        }
                    }
                }
            });
        }

        var equipmentCtx = document.getElementById('equipment-chart');
        var toolsCtx = document.getElementById('tools-chart');
        var materialsCtx = document.getElementById('materials-chart');

        if (equipmentCtx) {
            createChart(equipmentCtx, <?= json_encode(array_values($equipmentResult)) ?>, 'Most Borrowed Equipment', 'Times Borrowed');
        }

        if (toolsCtx) {
            createChart(toolsCtx, <?= json_encode(array_values($toolsResult)) ?>, 'Most Borrowed Tools', 'Times Borrowed');
        }

        if (materialsCtx) {
            createChart(materialsCtx, <?= json_encode($mostRequestedMaterialsResult) ?>, 'Most Requested Materials', 'Total Quantity');
        }
        <?php endif; ?>
    });
</script>


<style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
        }
        .dashboard-card {
            padding: 25px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .dashboard-card h3 {
            font-size: 1.4rem;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .dashboard-card p {
            font-size: 1.1rem;
            color: #34495e;
            margin-bottom: 10px;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 25px;
            font-size: 1.8rem;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            display: inline-block;
            padding-bottom: 10px;
            font-weight: 600;
        }
        .chart-container {
            height: 350px;
            margin-bottom: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .alert-items {
            max-height: 450px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .alert-items::-webkit-scrollbar {
            width: 8px;
        }
        .alert-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .alert-items::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .alert-items::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .main-content-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .list-unstyled li {
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .list-unstyled li:last-child {
            border-bottom: none;
        }
        .dashboard-card .fas {
            margin-right: 10px;
            color: #3498db;
        }
        .alert-item {
            position: relative;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            min-height: 60px;
        }
        .alert-item:hover {
            background-color: #e9ecef;
        }
        .alert-level-indicator {
            height: 10px;
            background-color: #e74c3c;
            border-radius: 5px;
            margin-top: 5px;
        }
        .small-card {
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .small-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .small-card h4 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .small-card p {
            font-size: 0.9rem;
            color: #34495e;
            margin-bottom: 5px;
        }
        .small-card .fas {
            font-size: 1.2rem;
            margin-right: 5px;
        }
        .dashboard-card .card-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #3498db;
        }
        .recently-borrowed-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .recently-borrowed-item:hover {
            background-color: #e9ecef;
        }
        .recently-borrowed-item i {
            font-size: 1.2rem;
            margin-right: 10px;
        }
        .chart-legend {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .chart-legend-item {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        .chart-legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
        }
    </style>

</body>
</html>
