<?php function h($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');} ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Education Records</h1>
            <p class="text-muted mb-0">Beneficiary: <strong><?= h($beneficiary['full_name']) ?> (<?= h($beneficiary['gof_id']) ?>)</strong></p>
        </div>
        <a href="<?= url('/admin/beneficiaries/' . $beneficiary['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 text-primary"><i class="bi bi-journal-bookmark-fill me-2"></i>Records</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Institution</th>
                        <th>Type</th>
                        <th>Level/Course</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Period</th>
                        <th>Notes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($educationRecords)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No education records found.</td></tr>
                    <?php else: ?>
                        <?php foreach($educationRecords as $er): ?>
                        <tr>
                            <td class="fw-semibold"><?= h($er['institution_name']) ?></td>
                            <td><?= h(ucfirst($er['institution_type'])) ?></td>
                            <td><?= h($er['course_level']) ?></td>
                            <td><?= h($er['academic_session']) ?></td>
                            <td>
                                <?php 
                                $bg = 'secondary';
                                if($er['enrollment_status']==='enrolled') $bg='success';
                                elseif($er['enrollment_status']==='graduated') $bg='primary';
                                elseif($er['enrollment_status']==='dropped_out') $bg='danger';
                                elseif($er['enrollment_status']==='transferred') $bg='info text-dark';
                                elseif($er['enrollment_status']==='on_break') $bg='warning text-dark';
                                ?>
                                <span class="badge bg-<?= $bg ?>"><?= h(str_replace('_', ' ', $er['enrollment_status'])) ?></span>
                            </td>
                            <td class="small">
                                <?= h($er['start_date'] ?: '?') ?> to <br><?= h($er['end_date'] ?: 'Present') ?>
                            </td>
                            <td><?= h($er['remarks']) ?></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editModal<?= $er['id'] ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $er['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="<?= url('/admin/education/update') ?>">
                                        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= h($er['id']) ?>">
                                        <input type="hidden" name="beneficiary_id" value="<?= h($beneficiary['id']) ?>">
                                        
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Education Record</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Institution Name *</label>
                                                    <input type="text" name="institution" class="form-control" value="<?= h($er['institution_name']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Institution Type</label>
                                                    <select name="institution_type" class="form-select">
                                                        <?php $types = ['primary','secondary','tertiary','vocational','quranic','other']; ?>
                                                        <?php foreach($types as $t): ?>
                                                            <option value="<?= $t ?>" <?= $er['institution_type']===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Course/Level</label>
                                                    <input type="text" name="course_level" class="form-control" value="<?= h($er['course_level']) ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Academic Session</label>
                                                    <input type="text" name="academic_session" class="form-control" value="<?= h($er['academic_session']) ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Enrollment Status</label>
                                                    <select name="enrollment_status" class="form-select">
                                                        <?php $st = ['enrolled','graduated','dropped_out','transferred','on_break']; ?>
                                                        <?php foreach($st as $s): ?>
                                                            <option value="<?= $s ?>" <?= $er['enrollment_status']===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Performance/Remarks</label>
                                                    <textarea name="performance" class="form-control" rows="2"><?= h($er['performance']) ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Start Date</label>
                                                    <input type="date" name="start_date" class="form-control" value="<?= h($er['start_date']) ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">End Date</label>
                                                    <input type="date" name="end_date" class="form-control" value="<?= h($er['end_date']) ?>">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="2"><?= h($er['remarks']) ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 text-success"><i class="bi bi-plus-circle me-2"></i>Add Education Record</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/education/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="beneficiary_id" value="<?= h($beneficiary['id']) ?>">
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Institution Name *</label>
                        <input type="text" name="institution" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Institution Type *</label>
                        <select name="institution_type" class="form-select" required>
                            <option value="">Select...</option>
                            <option value="primary">Primary</option>
                            <option value="secondary">Secondary</option>
                            <option value="tertiary">Tertiary</option>
                            <option value="vocational">Vocational</option>
                            <option value="quranic">Quranic</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Course/Level *</label>
                        <input type="text" name="course_level" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Academic Session</label>
                        <input type="text" name="academic_session" class="form-control" placeholder="e.g. 2025/2026">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Enrollment Status *</label>
                        <select name="enrollment_status" class="form-select" required>
                            <option value="">Select...</option>
                            <option value="enrolled">Enrolled</option>
                            <option value="graduated">Graduated</option>
                            <option value="dropped_out">Dropped Out</option>
                            <option value="transferred">Transferred</option>
                            <option value="on_break">On Break</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Performance/Remarks</label>
                        <textarea name="performance" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Add Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
