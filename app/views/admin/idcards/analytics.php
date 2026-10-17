<div class="container-fluid">
    <h2>ID Card Verification Analytics</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Attempts (Last 24h)</div>
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($stats['total_24h']) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Full chart implementation (Chart.js) to be added here.
    </div>
</div>
