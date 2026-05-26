<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Payments | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* Pagination Styles */
    .pagination {
        margin: 0;
    }
    .pagination .page-link {
        background-color: #16213e;
        border-color: #0f3460;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
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
    .badge-completed { background-color: #28a745; }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-failed { background-color: #dc3545; }
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
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="text-white mb-0">
        <i class="bi bi-credit-card"></i> Payments (Transactions)
      </h3>
      <p class="text-white small mt-2">Manage payment transactions</p>
    </div>
    <div class="text-white">
      <i class="bi bi-cash-stack"></i> 
      <strong>Total: ₱<?= number_format($pager->totalAmount ?? 0, 2) ?></strong> | 
      <span id="total-payments"><?= $pager->total ?? 0 ?></span> Transactions
    </div>
  </div>

  <!-- SEARCH BAR -->
  <div class="row mb-4">
    <div class="col-md-5">
      <form method="get" action="<?= base_url('/admin/payments') ?>" class="d-flex gap-2">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" 
                 name="search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Search by property, buyer, or seller..." 
                 value="<?= esc($search ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
        <?php if (!empty($search)): ?>
          <a href="<?= base_url('/admin/payments') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </form>
      <?php if (!empty($search)): ?>
        <div class="mt-2 text-white small">
          <i class="bi bi-info-circle"></i> Showing results for: "<strong class="text-warning"><?= esc($search) ?></strong>"
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- END SEARCH BAR -->

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Property</th>
          <th>Buyer</th>
          <th>Seller</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($payments)): ?>
          <?php foreach ($payments as $p): ?>
           <tr>
            <td><?= esc($p['id']) ?></td>
            <td><i class="bi bi-house-door-fill text-info"></i> <?= esc($p['property_title'] ?? 'N/A') ?></td>
            <td><i class="bi bi-person-circle"></i> <?= esc($p['buyer_name'] ?? 'N/A') ?></td>
            <td><i class="bi bi-briefcase-fill"></i> <?= esc($p['seller_name'] ?? 'N/A') ?></td>
            <td class="fw-bold text-success">₱<?= number_format((float)$p['amount'], 2) ?></td>
            <td>
              <span class="badge <?= $p['status'] === 'completed' ? 'badge-completed' : ($p['status'] === 'pending' ? 'badge-pending' : 'badge-failed') ?>">
                <?= strtoupper(esc($p['status'] ?? 'pending')) ?>
              </span>
            </td>
            <td><small><?= esc($p['created_at'] ?? 'N/A') ?></small></td>
           </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-white">No payments found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <?php if (isset($pager) && $pager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-4">
      <nav aria-label="Page navigation">
          <ul class="pagination">
              <?php if ($pager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?page=<?= $pager->currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>" aria-label="Previous">
                          <span aria-hidden="true">&laquo; Prev</span>
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled">
                      <span class="page-link">&laquo; Prev</span>
                  </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $pager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $pager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                  </li>
              <?php endfor; ?>

              <?php if ($pager->currentPage < $pager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?page=<?= $pager->currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>" aria-label="Next">
                          <span aria-hidden="true">Next &raquo;</span>
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled">
                      <span class="page-link">Next &raquo;</span>
                  </li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>

  <!-- Showing results info -->
  <div class="text-center text-white-50 small mt-2">
      Showing <strong><?= $pager->firstItem ?></strong> to <strong><?= $pager->lastItem ?></strong> 
      of <strong><?= $pager->total ?></strong> transactions
  </div>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>