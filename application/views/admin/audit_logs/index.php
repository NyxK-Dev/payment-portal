<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted small">Comprehensive logs monitoring internal database states and administrative activity actions.</div>
</div>

<div class="card card-outline card-danger">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="180">Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module Type</th>
                        <th>Record Target</th>
                        <th>IP Address</th>
                        <th width="100" class="text-center">Data Changes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="text-secondary small"><?= $log->created_at; ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($log->username ?? 'System / Guest'); ?></span>
                                    <?php if (!empty($log->email)): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($log->email); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $log->action === 'DELETE' ? 'danger' : ($log->action === 'UPDATE' ? 'warning text-dark' : 'success') ?>">
                                        <?= htmlspecialchars($log->action); ?>
                                    </span>
                                </td>
                                <td><code><?= htmlspecialchars($log->entity_type); ?></code></td>
                                <td class="fw-bold">#<?= htmlspecialchars($log->entity_id ?? 'N/A'); ?></td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($log->ip_address); ?>
                                    <br><small class="text-muted text-truncate d-inline-block" style="max-width: 150px;" title="<?= htmlspecialchars($log->user_agent); ?>"><?= htmlspecialchars($log->user_agent); ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($log->old_data) || !empty($log->new_data)): ?>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info view-payload-btn"
                                            data-old='<?= htmlspecialchars($log->old_data ?? '{}', ENT_QUOTES, 'UTF-8'); ?>'
                                            data-new='<?= htmlspecialchars($log->new_data ?? '{}', ENT_QUOTES, 'UTF-8'); ?>'
                                            data-action="<?= htmlspecialchars($log->action); ?>"
                                            data-target="#<?= htmlspecialchars($log->entity_type . ' ' . $log->entity_id); ?>">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No system audit activities captured.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dynamic Inspect JSON Modal Structure -->
<div class="modal fade" id="auditPayloadModal" tabindex="-1" aria-labelledby="auditPayloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="auditPayloadModalLabel">
                    <i class="fas fa-history me-2"></i> Audit Record Payload Delta Inspection
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white py-2 fw-bold small">
                                <i class="fas fa-arrow-circle-left me-1"></i> Pre-Existing State (Old Data Structure)
                            </div>
                            <div class="card-body p-0">
                                <pre class="m-0 p-3 bg-dark text-warning rounded-bottom" style="max-height: 450px; overflow-y: auto;"><code id="oldDataContainer"></code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white py-2 fw-bold small">
                                <i class="fas fa-arrow-circle-right me-1"></i> Post-Execution State (New Data Structure)
                            </div>
                            <div class="card-body p-0">
                                <pre class="m-0 p-3 bg-dark text-success rounded-bottom" style="max-height: 450px; overflow-y: auto;"><code id="newDataContainer"></code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Dismiss Panel</button>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Modal Controller Interceptor Logic -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payloadButtons = document.querySelectorAll('.view-payload-btn');
        const targetModal = new bootstrap.Modal(document.getElementById('auditPayloadModal'));

        const oldContainer = document.getElementById('oldDataContainer');
        const newContainer = document.getElementById('newDataContainer');
        const modalLabel = document.getElementById('auditPayloadModalLabel');

        payloadButtons.forEach(button => {
            button.addEventListener('click', function() {
                try {
                    // Parsing properties directly safe-passed from structural data tags
                    const rawOld = this.getAttribute('data-old');
                    const rawNew = this.getAttribute('data-new');
                    const action = this.getAttribute('data-action');
                    const targetText = this.getAttribute('data-target');

                    const oldObj = JSON.parse(rawOld);
                    const newObj = JSON.parse(rawNew);

                    // Pretty formatting JSON string layouts
                    oldContainer.textContent = Object.keys(oldObj).length > 0 ? JSON.stringify(oldObj, null, 4) : '// No prior historical snapshot recorded (INSERT statement)';
                    newContainer.textContent = Object.keys(newObj).length > 0 ? JSON.stringify(newObj, null, 4) : '// Record empty or completely dropped from system (DELETE statement)';

                    modalLabel.innerHTML = `<i class="fas fa-history me-2"></i> Audit Inspection: <span class="badge bg-info text-dark mx-1">${action}</span> Payload on ${targetText}`;

                    targetModal.show();
                } catch (err) {
                    console.error("Failed handling audit log payload formatting verification error:", err);
                    alert("An error occurred parsing this row's audit tracking payload delta schema snapshots.");
                }
            });
        });
    });
</script>