
   <!-- Modal Edit Profil -->
<div class="modal-overlay" id="editModalOverlay">
  <div class="modal-wrap">

    <div class="modal edit-modal">
      <div class="modal-header">
        <h2 class="modal-title">Edit profil</h2>
        <button class="btn-close" id="editModalClose">✕</button>
      </div>

      <form action="update_profil.php" method="POST" enctype="multipart/form-data" id="editProfilForm">
        <div class="modal-body">

          <!-- Banner -->
          <div class="field-group">
            <label class="field-label">Banner</label>
            <div class="banner-preview" id="editBannerPreview">
              <?php if ($user['banner']): ?>
                <img src="../uploads/banner/<?= htmlspecialchars($user['banner']) ?>" alt="Banner">
                <div class="banner-overlay">Ganti banner</div>
              <?php else: ?>
                <span class="placeholder-text">Belum ada banner — klik untuk unggah</span>
                <div class="banner-overlay">Unggah banner</div>
              <?php endif; ?>
            </div>
            <input type="file" id="inputBanner" name="banner" accept="image/*">
            <p class="field-hint">JPG / PNG · rasio 3:1 ideal · maks 4 MB</p>
          </div>

          <!-- Foto profil -->
          <div class="avatar-row">
            <div class="avatar-wrap" id="avatarWrap">
              <img src="../uploads/profile/<?= htmlspecialchars($user['foto_profil']) ?>"
                   class="avatar-img" id="editAvatarPreview" alt="Foto profil"
                   onerror="this.src='../uploads/profile/default.png'">
              <div class="avatar-overlay">Ganti</div>
            </div>
            <div class="avatar-info">
              <label class="btn-upload" for="inputFoto">Ganti foto profil</label>
              <p class="field-hint">JPG / PNG · maks 2 MB</p>
            </div>
            <input type="file" id="inputFoto" name="foto_profil" accept="image/*">
          </div>

          <!-- Nama -->
          <div class="field-group">
            <label class="field-label" for="inputNama">Nama</label>
            <input class="field-input" type="text" id="inputNama" name="nama"
                   value="<?= htmlspecialchars($user['nama']) ?>" maxlength="60" required
                   placeholder="Nama tampilan kamu">
          </div>

          <!-- Bio -->
          <div class="field-group">
            <label class="field-label" for="inputBio">Bio</label>
            <textarea class="field-input field-textarea" id="inputBio" name="bio"
                      maxlength="120" rows="3"
                      placeholder="Ceritakan sedikit tentang dirimu..."><?= htmlspecialchars($user['bio']) ?></textarea>
            <p class="field-hint text-right"><span id="bioCounter">0</span> / 120</p>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancel" id="editCancelBtn">Batal</button>
          <button type="submit" class="btn-save">Simpan perubahan</button>
        </div>
      </form>
    </div>

  </div>
</div>

<!-- SCRIPTS -->
<script src="../assets/script.js"></script>
<script src="profil.js"></script>
