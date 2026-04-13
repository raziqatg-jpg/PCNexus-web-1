/* profil.js — interaksi halaman profil PCNexus */

(function () {
  'use strict';

  /* ── Elemen ── */
  const overlay       = document.getElementById('editModalOverlay');
  const btnEdit       = document.getElementById('btnEditProfil');
  const btnEditAvatar = document.getElementById('btnEditAvatar');
  const btnClose      = document.getElementById('editModalClose');
  const btnCancel     = document.getElementById('editCancelBtn');
  const btnEditBanner = document.getElementById('btnEditBanner');
  const btnSave       = document.getElementById('editSaveBtn');

  const inputFoto     = document.getElementById('inputFoto');
  const inputBanner   = document.getElementById('inputBanner');

  const editAvatarPrev = document.getElementById('editAvatarPreview');
  const editBannerPrev = document.getElementById('editBannerPreview');
  const heroAvatar     = document.getElementById('avatarPreview');

  /* ── Buka & tutup modal ── */
  function openModal()  { overlay.classList.add('active');    document.body.style.overflow = 'hidden'; }
  function closeModal() { overlay.classList.remove('active'); document.body.style.overflow = ''; }

  if (btnEdit)       btnEdit.addEventListener('click', openModal);
  if (btnEditAvatar) btnEditAvatar.addEventListener('click', openModal);
  if (btnClose)      btnClose.addEventListener('click', closeModal);
  if (btnCancel)     btnCancel.addEventListener('click', closeModal);

  /* Klik di luar modal → tutup */
  if (overlay) {
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
  }

  /* Escape → tutup */
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  /* ── Trigger input file dari klik area ── */

  // Tombol "Ganti Banner" di hero page
  if (btnEditBanner) {
    btnEditBanner.addEventListener('click', () => inputBanner?.click());
  }

  // Klik area banner di dalam modal
  if (editBannerPrev) {
    editBannerPrev.addEventListener('click', () => inputBanner?.click());
  }

  // Klik avatar wrap di dalam modal
  const avatarWrap = document.getElementById('avatarWrap');
  if (avatarWrap) {
    avatarWrap.addEventListener('click', () => inputFoto?.click());
  }

  /* ── Preview foto profil ── */
  if (inputFoto) {
    inputFoto.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto maks 2MB ya!');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        if (editAvatarPrev) editAvatarPrev.src = e.target.result;
        if (heroAvatar)     heroAvatar.src     = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  /* ── Preview banner ── */
  if (inputBanner) {
    inputBanner.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.size > 4 * 1024 * 1024) {
        alert('Ukuran banner maks 4MB ya!');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        // Update preview di modal
        if (editBannerPrev) {
          editBannerPrev.innerHTML = `
            <img src="${e.target.result}" alt="Preview Banner" style="width:100%;height:100%;object-fit:cover;display:block;">
            <div class="banner-overlay">Ganti banner</div>
          `;
        }

        // Update banner di hero page
        const heroBannerWrap = document.getElementById('bannerWrap');
        if (heroBannerWrap) {
          let img = heroBannerWrap.querySelector('.banner-img');
          if (!img) {
            const placeholder = heroBannerWrap.querySelector('.banner-placeholder');
            if (placeholder) placeholder.remove();
            img = document.createElement('img');
            img.className = 'banner-img';
            img.alt = 'Banner';
            heroBannerWrap.insertBefore(img, heroBannerWrap.firstChild);
          }
          img.src = e.target.result;
        }
      };
      reader.readAsDataURL(file);
    });
  }

  /* ── Counter karakter bio ── */
  const bioField   = document.getElementById('inputBio');
  const bioCounter = document.getElementById('bioCounter');

  if (bioField && bioCounter) {
    function updateBioCount() {
      const len  = bioField.value.length;
      const left = 120 - len;
      bioCounter.textContent      = len;
      bioCounter.style.color      = left < 20 ? '#ef4444' : '';
    }
    bioField.addEventListener('input', updateBioCount);
    updateBioCount();
  }

  /* ── SIMPAN PERUBAHAN (UPDATE PROFIL + FOTO + BANNER) ── */
  if (btnSave) {
    btnSave.addEventListener('click', async function() {
      const nama = document.getElementById('inputNama')?.value.trim();
      const email = document.getElementById('inputEmail')?.value.trim();
      const bio = document.getElementById('inputBio')?.value.trim();
      
      if (!nama) {
        alert('⚠️ Nama tidak boleh kosong');
        return;
      }
      
      // Nonaktifkan tombol sementara
      const originalText = btnSave.innerHTML;
      btnSave.disabled = true;
      btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
      
      const formData = new FormData();
      formData.append('nama', nama);
      formData.append('email', email);
      formData.append('bio', bio);
      
      // Cek ada file foto baru?
      const fotoFile = document.getElementById('inputFoto')?.files[0];
      if (fotoFile) {
        formData.append('foto_profil', fotoFile);
      }
      
      // Cek ada file banner baru?
      const bannerFile = document.getElementById('inputBanner')?.files[0];
      if (bannerFile) {
        formData.append('banner', bannerFile);
      }
      
      try {
        const response = await fetch('../profil_page/update/update_profil.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          alert('✅ ' + result.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          alert('❌ ' + (result.message || 'Gagal menyimpan'));
          btnSave.disabled = false;
          btnSave.innerHTML = originalText;
        }
      } catch (err) {
        alert('❌ Gagal terhubung ke server: ' + err.message);
        btnSave.disabled = false;
        btnSave.innerHTML = originalText;
      }
    });
  }

})();