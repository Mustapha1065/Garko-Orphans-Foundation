<div class="container-fluid">
    <h2>System Settings</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= url('/admin/settings/update') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="org-tab" data-bs-toggle="tab" data-bs-target="#org" type="button" role="tab">Organization</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sys-tab" data-bs-toggle="tab" data-bs-target="#sys" type="button" role="tab">System</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">Social Media</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="idcard-tab" data-bs-toggle="tab" data-bs-target="#idcard" type="button" role="tab">ID Card Settings</button>
                  </li>
                </ul>

                <div class="tab-content" id="settingsTabsContent">
                  <!-- Organization Tab -->
                  <div class="tab-pane fade show active" id="org" role="tabpanel">
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Organization Name</label>
                              <input type="text" name="org_name" class="form-control" value="<?= htmlspecialchars($settings['org_name'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>Email Address</label>
                              <input type="email" name="org_email" class="form-control" value="<?= htmlspecialchars($settings['org_email'] ?? '') ?>">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Phone Number</label>
                              <input type="text" name="org_phone" class="form-control" value="<?= htmlspecialchars($settings['org_phone'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>Website</label>
                              <input type="url" name="org_website" class="form-control" value="<?= htmlspecialchars($settings['org_website'] ?? '') ?>">
                          </div>
                      </div>
                      <div class="mb-3">
                          <label>Physical Address</label>
                          <textarea name="org_address" class="form-control"><?= htmlspecialchars($settings['org_address'] ?? '') ?></textarea>
                      </div>
                  </div>

                  <!-- System Tab -->
                  <div class="tab-pane fade" id="sys" role="tabpanel">
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Currency</label>
                              <input type="text" name="currency" class="form-control" value="<?= htmlspecialchars($settings['currency'] ?? 'NGN') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>System Version</label>
                              <input type="text" name="system_version" class="form-control" value="<?= htmlspecialchars($settings['system_version'] ?? '1.0.0') ?>" readonly>
                          </div>
                      </div>
                  </div>

                  <!-- Social Media Tab -->
                  <div class="tab-pane fade" id="social" role="tabpanel">
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Facebook URL</label>
                              <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>Twitter URL</label>
                              <input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Instagram URL</label>
                              <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>LinkedIn URL</label>
                              <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>">
                          </div>
                      </div>
                  </div>

                  <!-- ID Card Tab -->
                  <div class="tab-pane fade" id="idcard" role="tabpanel">
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label>Issuer Name</label>
                              <input type="text" name="id_card_issuer_name" class="form-control" value="<?= htmlspecialchars($settings['id_card_issuer_name'] ?? 'Garko Orphans Foundation') ?>">
                          </div>
                          <div class="col-md-6">
                              <label>Expiry Years (Default)</label>
                              <input type="number" name="id_card_expiry_years" class="form-control" value="<?= htmlspecialchars($settings['id_card_expiry_years'] ?? '5') ?>">
                          </div>
                      </div>
                      <div class="mb-3">
                          <label>Footer Text (Terms & Conditions)</label>
                          <textarea name="id_card_footer_text" class="form-control"><?= htmlspecialchars($settings['id_card_footer_text'] ?? 'This card is the property of GOF. If found, please return to the nearest GOF office.') ?></textarea>
                      </div>
                  </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
