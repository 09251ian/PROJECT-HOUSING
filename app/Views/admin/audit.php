<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Audit | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .badge-create { background-color: #28a745; }
    .badge-update { background-color: #ffc107; color: #000; }
    .badge-delete { background-color: #dc3545; }
    .badge-login { background-color: #17a2b8; }
    .badge-logout { background-color: #6c757d; }
    .badge-register { background-color: #20c997; }
    .badge-archive { background-color: #fd7e14; }
    .badge-unarchive { background-color: #20c997; }
    .old-value { color: #dc3545; text-decoration: line-through; }
    .new-value { color: #28a745; }
    .section-title { border-left: 4px solid #e94560; padding-left: 15px; margin: 25px 0 15px 0; }
    .pagination { margin: 0; }
    .pagination .page-link {
        background-color: #16213e;
        border-color: #0f3460;
        color: white;
        padding: 6px 12px;
        font-size: 13px;
    }
    .pagination .page-link:hover {
        background-color: #0f3460;
        color: white;
        border-color: #e94560;
    }
    .pagination .page-item.active .page-link {
        background-color: #e94560;
        border-color: #e94560;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        background-color: #16213e;
        color: #6c757d;
        border-color: #0f3460;
    }
    .search-bar {
        background: transparent;
        border-radius: 10px;
        padding: 10px 15px;
        margin-bottom: 15px;
    }
  </style>
</head>
<body class="app-dark">

<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/admin/dashboard') ?>">🏠 House Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/users') ?>">Users</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-outline-light btn-sm active" href="<?= base_url('/admin/audit') ?>">Audit</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="text-white mb-0">
      <i class="bi bi-journal-text"></i> Audit Report
    </h3>
  </div>

  <!-- ========== TABLE 1: LOGIN/LOGOUT HISTORY ========== -->
  <div class="section-title">
    <h4 class="text-white mb-0">
      <i class="bi bi-box-arrow-in-right"></i> Login / Logout History
    </h4>
    <p class="text-white small">Track user sessions and authentication activities</p>
  </div>

  <!-- Search Bar for Login/Logout Table -->
  <div class="search-bar">
    <form method="get" action="<?= base_url('/admin/audit') ?>" class="row g-2">
      <div class="col-md-4">
        <input type="hidden" name="property_page" value="<?= $propertyPager->currentPage ?? 1 ?>">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" 
                 name="login_search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Search by user ID, role, or activity..." 
                 value="<?= esc($loginSearch ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
      <div class="col-md-2">
        <?php if (!empty($loginSearch)): ?>
          <a href="<?= base_url('/admin/audit') ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </div>
    </form>
    <?php if (!empty($loginSearch)): ?>
      <div class="mt-2 text-white-50 small">
        <i class="bi bi-info-circle"></i> Showing results for: "<strong class="text-warning"><?= esc($loginSearch) ?></strong>"
      </div>
    <?php endif; ?>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Time</th>
          <th>User ID</th>
          <th>Role</th>
          <th>Activity</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($loginLogs)): ?>
          <tr><td colspan="5" class="text-center text-white">No login/logout records found</td></tr>
        <?php else: ?>
          <?php foreach ($loginLogs as $l): ?>
            <tr>
              <td><small><?= date('M d, Y h:i A', strtotime($l['created_at'] ?? 'now')) ?></small></td>
              <td><strong><?= esc($l['actor_user_id'] ?? '?') ?></strong></td>
              <td>
                <span class="badge bg-<?= $l['actor_role'] === 'admin' ? 'danger' : ($l['actor_role'] === 'seller' ? 'warning' : 'info') ?>">
                  <?= strtoupper(esc($l['actor_role'] ?? 'GUEST')) ?>
                </span>
              </td>
              <td>
                <span class="badge badge-<?= $l['activity_type'] ?? 'unknown' ?>">
                  <?= strtoupper(esc($l['activity_type'] ?? '')) ?>
                </span>
              </td>
              <td>
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                if ($l['activity_type'] === 'login' && isset($meta['email'])): ?>
                  <i class="bi bi-envelope"></i> <?= esc($meta['email']) ?>
                <?php elseif ($l['activity_type'] === 'register'): ?>
                  <i class="bi bi-person-plus"></i> New user: <strong><?= esc($meta['name'] ?? '') ?></strong><br>
                  <small>Email: <?= esc($meta['email'] ?? '') ?> | Role: <?= esc($meta['role'] ?? '') ?></small>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination for Login/Logout Table -->
  <?php if (isset($loginPager) && $loginPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3 mb-4">
      <nav aria-label="Login/Logout pagination">
          <ul class="pagination">
              <?php if ($loginPager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage - 1 ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          &laquo; Prev
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $loginPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $loginPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?login_page=<?= $i ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <?php if ($loginPager->currentPage < $loginPager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage + 1 ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          Next &raquo;
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-4">
      Showing <strong><?= $loginPager->firstItem ?></strong> to <strong><?= $loginPager->lastItem ?></strong> 
      of <strong><?= $loginPager->total ?></strong> records
  </div>
  <?php endif; ?>

  <!-- ========== TABLE 2: PROPERTY CRUD HISTORY ========== -->
  <div class="section-title">
    <h4 class="text-white mb-0">
      <i class="bi bi-building"></i> Property CRUD History
    </h4>
    <p class="text-white small">Track property creations, updates, deletions, and archive status</p>
  </div>

  <!-- Search Bar for Property CRUD Table -->
  <div class="search-bar">
    <form method="get" action="<?= base_url('/admin/audit') ?>" class="row g-2">
      <div class="col-md-4">
        <input type="hidden" name="login_page" value="<?= $loginPager->currentPage ?? 1 ?>">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" 
                 name="property_search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Search by property title, action, or user..." 
                 value="<?= esc($propertySearch ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
      <div class="col-md-2">
        <?php if (!empty($propertySearch)): ?>
          <a href="<?= base_url('/admin/audit') ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </div>
    </form>
    <?php if (!empty($propertySearch)): ?>
      <div class="mt-2 text-white-50 small">
        <i class="bi bi-info-circle"></i> Showing results for: "<strong class="text-warning"><?= esc($propertySearch) ?></strong>"
      </div>
    <?php endif; ?>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Time</th>
          <th>Property</th>
          <th>Action</th>
          <th>Changed By</th>
          <th>Changes / Details</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($propertyLogs)): ?>
          <tr><td colspan="5" class="text-center text-white">No property activity records found</td></tr>
        <?php else: ?>
          <?php foreach ($propertyLogs as $l): ?>
            <tr>
              <td><small><?= date('M d, Y h:i A', strtotime($l['created_at'] ?? 'now')) ?></small></td>
              <td>
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                $title = '';
                if ($l['activity_type'] === 'create' || $l['activity_type'] === 'delete' || $l['activity_type'] === 'archive' || $l['activity_type'] === 'unarchive') {
                  $title = $meta['title'] ?? 'N/A';
                } elseif ($l['activity_type'] === 'update') {
                  $title = $meta['new']['title'] ?? $meta['old']['title'] ?? 'N/A';
                }
                ?>
                <strong><?= esc($title) ?></strong><br>
                <small class="text-muted">ID: <?= esc($l['entity_id'] ?? '?') ?></small>
              </td>
              <td>
                <?php if ($l['activity_type'] === 'create'): ?>
                  <span class="badge badge-create"><i class="bi bi-plus-circle"></i> CREATE</span>
                <?php elseif ($l['activity_type'] === 'update'): ?>
                  <span class="badge badge-update"><i class="bi bi-pencil-square"></i> UPDATE</span>
                <?php elseif ($l['activity_type'] === 'delete'): ?>
                  <span class="badge badge-delete"><i class="bi bi-trash"></i> DELETE</span>
                <?php elseif ($l['activity_type'] === 'archive'): ?>
                  <span class="badge badge-archive"><i class="bi bi-archive"></i> ARCHIVE</span>
                <?php elseif ($l['activity_type'] === 'unarchive'): ?>
                  <span class="badge badge-unarchive"><i class="bi bi-arrow-repeat"></i> UNARCHIVE</span>
                <?php endif; ?>
              </td>
              <td>
                <strong><?= esc($l['actor_role'] ?? '?') ?></strong><br>
                <small class="text-muted">ID: <?= esc($l['actor_user_id'] ?? '?') ?></small>
                <?php if (isset($meta['source'])): ?>
                  <br><small class="text-muted">Source: <?= esc($meta['source']) ?></small>
                <?php endif; ?>
              </td>
              <td style="max-width:400px;">
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                
                if ($l['activity_type'] === 'create'):
                ?>
                  <div class="text-success">
                    <i class="bi bi-plus-circle"></i> Property created<br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'update'): ?>
                  <div class="text-warning">
                    <i class="bi bi-pencil-square"></i> Changes:<br>
                    <?php if (isset($meta['old']['title']) && $meta['old']['title'] != $meta['new']['title']): ?>
                      Title: <span class="old-value"><?= esc($meta['old']['title']) ?></span> → <span class="new-value"><?= esc($meta['new']['title']) ?></span><br>
                    <?php endif; ?>
                    <?php if (isset($meta['old']['price']) && $meta['old']['price'] != $meta['new']['price']): ?>
                      Price: <span class="old-value">₱<?= number_format($meta['old']['price'], 2) ?></span> → <span class="new-value">₱<?= number_format($meta['new']['price'], 2) ?></span><br>
                    <?php endif; ?>
                    <?php if (isset($meta['old']['location']) && $meta['old']['location'] != $meta['new']['location']): ?>
                      Location: <span class="old-value"><?= esc($meta['old']['location']) ?></span> → <span class="new-value"><?= esc($meta['new']['location']) ?></span>
                    <?php endif; ?>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'delete'): ?>
                  <div class="text-danger">
                    <i class="bi bi-trash"></i> <strong>PROPERTY DELETED!</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-danger">⚠️ This property has been permanently removed</span>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'archive'): ?>
                  <div class="text-warning">
                    <i class="bi bi-archive"></i> Property <strong>ARCHIVED</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-warning">📦 Property is now hidden from buyers</span>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'unarchive'): ?>
                  <div class="text-success">
                    <i class="bi bi-arrow-repeat"></i> Property <strong>UNARCHIVED / RESTORED</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-success">✅ Property is now visible to buyers again</span>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination for Property CRUD Table -->
  <?php if (isset($propertyPager) && $propertyPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3 mb-4">
      <nav aria-label="Property CRUD pagination">
          <ul class="pagination">
              <?php if ($propertyPager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $propertyPager->currentPage - 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          &laquo; Prev
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $propertyPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $propertyPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $i ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <?php if ($propertyPager->currentPage < $propertyPager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $propertyPager->currentPage + 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          Next &raquo;
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-4">
      Showing <strong><?= $propertyPager->firstItem ?></strong> to <strong><?= $propertyPager->lastItem ?></strong> 
      of <strong><?= $propertyPager->total ?></strong> records
  </div>
  <?php endif; ?>

</div>
</body>
</html>