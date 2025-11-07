<link rel="stylesheet" href="../css/header.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<header>
    <div class="header-title">
        <i class="bi bi-house-door-fill"></i>
        <h3 id="header-title"></h3>
    </div>
    <div class="header-user">
        <div class="user-btn">
            <button>Subir NFC</button>
        </div>
        <div class="user-name">
            <i class="bi bi-person"></i>
            <p id="userNameDisplay"></p>
        </div>
    </div>
</header>


<!-- MODAL PASSWORD -->
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-pwd">
    <div class="modal-content content-pwd">
      <div class="modal-header header-pwd">
        <h5 class="modal-title title-pwd">
          <i class="bi bi-shield-lock-fill me-2"></i>Cambiar Contraseña
        </h5>
        <button type="button" class="btn-close-password" onclick="cerrarModalPassword()">
          X
        </button>
      </div>
      <div class="modal-body body-pwd">
        <form id="formCambiarPassword">
          <div class="mb-3">
            <label class="form-label">Contraseña Actual</label>
            <input type="password" class="form-control input-pwd" id="currentPassword" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control input-pwd" id="newPassword" required>
            <small class="text-muted">Mínimo 8 caracteres</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control input-pwd" id="confirmPassword" required>
          </div>
        </form>
      </div>
      <div class="modal-footer footer-pwd">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="formCambiarPassword" class="btn btn-pwd-primary" id="btnGuardarPassword">
          Guardar Cambios
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Subir NFC -->
<div class="modal fade" id="subirNfcModal"  aria-labelledby="subirNfcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subirNfcModalLabel">
          <i class="bi bi-cloud-upload-fill me-2"></i>Subir Códigos NFC
        </h5>
        <button type="button" class="btn-close-nfc" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body">
        <!-- Instrucciones -->
        <div class="alert alert-info" role="alert">
          <h6 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>Instrucciones</h6>
          <ul class="mb-0 small">
            <li>El archivo debe ser formato <strong>CSV</strong> o <strong>TXT</strong></li>
            <li>Cada línea o celda debe contener <strong>un solo código NFC</strong></li>
            <li>Los códigos pueden estar separados por:
              <ul>
                <li>Saltos de línea (uno por fila)</li>
                <li>Comas (<code>,</code>) o punto y coma (<code>;</code>)</li>
              </ul>
            </li>
            <li>Evita separar los códigos con espacios.</li>
            <li>Encabezados como <strong>"NFC"</strong> o <strong>"Código"</strong> serán ignorados automáticamente.</li>
          </ul>
        </div>


        <!-- Ejemplo visual -->
        <div class="ejemplo-formato mb-3">
          <p class="small text-muted mb-2"><strong>Ejemplo de formato:</strong></p>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead class="table-light">
                <tr>
                  <th>NFC</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>11732323232</td></tr>
                <tr><td>11732323233</td></tr>
                <tr><td>11732323234</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Zona de carga -->
        <div class="upload-zone" id="uploadZone">
          <input type="file" id="fileInput" accept=".csv, .txt" hidden>
          <div class="upload-content">
            <i class="bi bi-file-earmark-arrow-up upload-icon"></i>
            <p class="mb-2"><strong>Arrastra tu archivo aquí</strong></p>
            <p class="text-muted small mb-3">o</p>
            <button type="button" class="btn btn-primary" id="btnSeleccionar">
              <i class="bi bi-folder2-open me-2"></i>Seleccionar archivo
            </button>
          </div>
          <div class="upload-preview d-none" id="uploadPreview">
            <i class="bi bi-file-earmark-check-fill text-success" style="font-size: 3rem;"></i>
            <p class="mb-1"><strong id="fileName"></strong></p>
            <p class="text-muted small mb-2" id="fileSize"></p>
            <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemover">
              <i class="bi bi-trash me-1"></i>Remover
            </button>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="progress mt-3 d-none" id="uploadProgress">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnSubirArchivo" disabled>
          <i class="bi bi-upload me-2"></i>Subir Archivo
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
  // Evita el warning "Blocked aria-hidden" en todos los modales
  document.addEventListener("hide.bs.modal", () => {
    if (document.activeElement) {
      document.activeElement.blur();
    }
  });
</script>

<!-- SCRIPT PARA RESTAR PASSWORD -->
<script>
  function cerrarModalPassword() {
    document.activeElement.blur();
    const modalEl = document.getElementById('cambiarPasswordModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.hide();
  }

  document.addEventListener("DOMContentLoaded", () => {
    const API_URL = "<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/change_password.php";
    
    // Elementos
    const userNameEl = document.querySelector('.header-user .user-name');
    const modal = new bootstrap.Modal(document.getElementById('cambiarPasswordModal'));
    const form = document.getElementById('formCambiarPassword');
    const btnGuardar = document.getElementById('btnGuardarPassword');
    
    if (userNameEl) {
      userNameEl.style.cursor = 'pointer';
      userNameEl.addEventListener('click', () => modal.show());
    }
    
    // Submit formulario
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const currentPwd = document.getElementById('currentPassword').value;
      const newPwd = document.getElementById('newPassword').value;
      const confirmPwd = document.getElementById('confirmPassword').value;
      
      if (newPwd.length < 8) {
        Swal.fire({
          icon: 'warning',
          title: 'Contraseña muy corta',
          text: 'Debe tener al menos 8 caracteres',
          confirmButtonColor: '#8EB525'
        });
        return;
      }
      
      if (newPwd !== confirmPwd) {
        Swal.fire({
          icon: 'error',
          title: 'Las contraseñas no coinciden',
          confirmButtonColor: '#8EB525'
        });
        return;
      }
      
      // Loading
      btnGuardar.disabled = true;
      btnGuardar.textContent = 'Guardando...';
      
      try {
        const token = localStorage.getItem("token");

        const response = await fetch(API_URL, {
          method: 'POST',
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            token: token,
            current_password: currentPwd,
            new_password: newPwd
          })
        });

        const result = await response.json();

        if (result.status) {
          Swal.fire({
            icon: 'success',
            title: 'Contraseña actualizada',
            text: result.msg,
            confirmButtonColor: '#8EB525'
          }).then(() => modal.hide());
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.msg,
            confirmButtonColor: '#8EB525'
          });
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error de red',
          text: 'No se pudo conectar con el servidor.'
        });
      }

      btnGuardar.disabled = false;
      btnGuardar.textContent = 'Guardar Cambios';

    });
    
    document.getElementById('cambiarPasswordModal').addEventListener('hidden.bs.modal', () => {
      form.reset();
    });
  });
</script>


<script>
  document.addEventListener("DOMContentLoaded", () => {
    const nameContainer = document.getElementById("userNameDisplay");
    const userData = JSON.parse(localStorage.getItem("userAllura") || "{}");
    nameContainer.textContent = userData.name || userData.username || "Usuario";
  });
</script>


<script>
  document.addEventListener("DOMContentLoaded", () => {
    const titleElement = document.getElementById("header-title");

    const currentPage = window.location.pathname.split("/").pop().split(".")[0];

    const titles = {
      "compras": "Gestión de Compras",
      "suplidores": "Suplidores",
      "ventas": "Gestión de Ventas",
      "clientes": "Clientes",
      "home": "Dashboard"
    };

    titleElement.textContent = titles[currentPage] || "Allura Soluciones Integrales";
  });
</script>

<!-- Script para el modal NFC-->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const btnSubirNfc = document.querySelector('.user-btn button'); // Botón "Subir NFC"
    const modal = new bootstrap.Modal(document.getElementById('subirNfcModal'));
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const btnSeleccionar = document.getElementById('btnSeleccionar');
    const btnRemover = document.getElementById('btnRemover');
    const btnSubirArchivo = document.getElementById('btnSubirArchivo');
    const uploadContent = uploadZone.querySelector('.upload-content');
    const uploadPreview = document.getElementById('uploadPreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadProgress = document.getElementById('uploadProgress');

    let archivoSeleccionado = null;

    // Abrir modal al hacer clic en "Subir NFC"
    btnSubirNfc.addEventListener('click', () => {
      modal.show();
    });

    // Click en zona de carga
    uploadZone.addEventListener('click', (e) => {
      if (!e.target.closest('button')) {
        fileInput.click();
      }
    });

    // Click en botón seleccionar
    btnSeleccionar.addEventListener('click', () => {
      fileInput.click();
    });

    // Drag & Drop
    uploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadZone.classList.add('dragging');
    });

    uploadZone.addEventListener('dragleave', () => {
      uploadZone.classList.remove('dragging');
    });

    uploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadZone.classList.remove('dragging');
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        manejarArchivo(files[0]);
      }
    });

    // Selección de archivo
    fileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        manejarArchivo(e.target.files[0]);
      }
    });

    // Función para manejar archivo seleccionado
    function manejarArchivo(file) {
      const extensionesValidas = ['csv', 'txt'];
      const extension = file.name.split('.').pop().toLowerCase();

      if (!extensionesValidas.includes(extension)) {
        Swal.fire({
          icon: 'error',
          title: 'Formato no válido',
          text: 'Por favor selecciona un archivo CSV o TXT (.csv, .txt)'
        });
        return;
      }

      archivoSeleccionado = file;
      fileName.textContent = file.name;
      fileSize.textContent = `${(file.size / 1024).toFixed(1)} KB`;
      
      uploadContent.classList.add('d-none');
      uploadPreview.classList.remove('d-none');
      btnSubirArchivo.disabled = false;
    }

    // Remover archivo
    btnRemover.addEventListener('click', () => {
      archivoSeleccionado = null;
      fileInput.value = '';
      uploadContent.classList.remove('d-none');
      uploadPreview.classList.add('d-none');
      btnSubirArchivo.disabled = true;
    });

    // Subir archivo
    btnSubirArchivo.addEventListener('click', async () => {
      if (!archivoSeleccionado) return;

      const formData = new FormData();
      formData.append('file', archivoSeleccionado);

      // Mostrar progress bar
      uploadProgress.classList.remove('d-none');
      const progressBar = uploadProgress.querySelector('.progress-bar');
      progressBar.style.width = '30%';

      try {
        const response = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/upload_nfc.php", {
          method: 'POST',
          body: formData
        });

        progressBar.style.width = '70%';
        const result = await response.json();
        progressBar.style.width = '100%';

        setTimeout(() => {
          uploadProgress.classList.add('d-none');
          progressBar.style.width = '0%';

          if (result.status) {
            Swal.fire({
              icon: 'success',
              title: '¡Archivo subido correctamente!',
              text: `Se cargaron ${result.total || 0} códigos NFC`,
              timer: 2500,
              showConfirmButton: false
            });
            modal.hide();
            // Resetear formulario
            archivoSeleccionado = null;
            fileInput.value = '';
            uploadContent.classList.remove('d-none');
            uploadPreview.classList.add('d-none');
            btnSubirArchivo.disabled = true;
          } else {
            throw new Error(result.msg || 'Error al procesar el archivo');
          }
        }, 500);

      } catch (error) {
        uploadProgress.classList.add('d-none');
        Swal.fire({
          icon: 'error',
          title: 'Error al subir archivo',
          text: error.message || 'Verifica tu conexión e intenta nuevamente'
        });
      }
    });

    // Resetear modal al cerrar
    document.getElementById('subirNfcModal').addEventListener('hidden.bs.modal', () => {
      archivoSeleccionado = null;
      fileInput.value = '';
      uploadContent.classList.remove('d-none');
      uploadPreview.classList.add('d-none');
      btnSubirArchivo.disabled = true;
      uploadProgress.classList.add('d-none');
    });
  });
</script>