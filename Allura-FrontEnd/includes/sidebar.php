<link rel="stylesheet" href="../css/sidebar.css">

<button id="toggleSidebar">
    <i class="bi bi-list"></i>
</button>

<aside class="aside"> 
    <div class="sidebar-logo">
        <img src="../assets/images/Logo.png" alt="Allura">
    </div>

    <nav class="menu-sidebar">
        <a href="../pages/home.php"><i class="bi bi-house-door-fill"></i> Dashboard</a>
        <a href="../pages/compras.php"><i class="bi bi-bag-fill"></i> Compra</a>
        <a href="../pages/ventas.php"><i class="bi bi-cart-fill"></i> Venta</a>
        <a href="../pages/suplidores.php"><i class="bi bi-truck"></i> Suplidores</a>
        <a href="../pages/clientes.php"><i class="bi bi-people-fill"></i> Clientes</a>
        <a href="#" onclick="reportModalOpen()"><i class="bi bi-bar-chart-fill"></i> Reportes</a>
    </nav>

    <div class="sidebar-logout">
        <button onclick="confirmarCerrarSesion()">
            <i class="bi bi-box-arrow-left"></i>
            <p>Cerrar Sesión</p>
        </button>
    </div>
</aside>

<div class="fondo-sidebar"></div>

<!-- ========================== MODAL DE REPORTES ========================== -->
<div id="reportModalOverlay" class="report-modal-overlay">
  <div class="report-modal-content-wrapper">

    <button class="report-modal-close-button" onclick="reportModalClose()">&times;</button>

    <h2 class="report-modal-main-title">Generador de Reportes</h2>

    <div class="report-modal-info-section">
      <div class="report-modal-info-title">Rango de Fechas</div>
      <div class="report-modal-date-range">
        <div class="report-modal-date-input-wrapper">
          <label class="report-modal-date-label">Desde</label>
          <input type="datetime-local" id="reportModalDateFrom" class="report-modal-date-input">
        </div>
        <div class="report-modal-date-input-wrapper">
          <label class="report-modal-date-label">Hasta</label>
          <input type="datetime-local" id="reportModalDateTo" class="report-modal-date-input">
        </div>
      </div>
    </div>

    <div class="report-modal-section">
      <label class="report-modal-section-label">Tipo de Reporte</label>
      <select id="reportModalSelectDropdown" class="report-modal-select-dropdown">
        <option value="">Seleccione...</option>
        <option value="financiero">Financieros</option>
        <option value="compras">Compras y Proveedores</option>
        <option value="ventas">Ventas y Clientes</option>
        <option value="gestion">Gestión General</option>
      </select>
    </div>

    <div class="report-modal-action-buttons">
      <button class="report-modal-action-btn report-modal-pdf-btn" onclick="reportModalCreatePDF()">
        Generar PDF
      </button>
      <button class="report-modal-action-btn report-modal-close-btn" onclick="reportModalClose()">
        Cancelar
      </button>
    </div>
  </div>
</div>

<script>
  function checkToken() {
    const token = localStorage.getItem("token");
    if (!token) {
      Swal.fire("Sesión inválida", "Por favor inicia sesión", "warning")
        .then(() => window.location.href = "../index.php");
    } else {
      fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/validate_token.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token })
      })
      .then(res => res.json())
      .then(result => {
        if (!result.status) {
          Swal.fire("Sesión expirada", "Inicia sesión nuevamente", "warning")
            .then(() => window.location.href = "../index.php");
        }
      })
      .catch(() => {
        Swal.fire("Error", "No se pudo validar la sesión", "error");
      });
    }
  }

  checkToken();

</script>

<script>

  function reportModalOpen() {
    const modal = document.getElementById('reportModalOverlay');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function reportModalClose() {
    const modal = document.getElementById('reportModalOverlay');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
  }
  async function reportModalCreatePDF() {
    const tipo = document.getElementById('reportModalSelectDropdown').value;
    const desde = document.getElementById('reportModalDateFrom').value;
    const hasta = document.getElementById('reportModalDateTo').value;

    if (!tipo || !desde || !hasta) {
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Debe seleccionar tipo de reporte y rango de fechas.',
      });
      return;
    }

    Swal.fire({
      title: 'Generando reporte...',
      text: 'Por favor espere un momento.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/reports/pdf.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          report: tipo,
          filters: {
            from: desde,
            to: hasta
          }
        })
      });

      if (!res.ok) throw new Error('Error al generar el reporte.');

      const blob = await res.blob();
      const pdfUrl = URL.createObjectURL(blob);
      window.open(pdfUrl, "_blank"); // abre el PDF en nueva pestaña
      Swal.close();
    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo generar el PDF. Verifique el servidor o la conexión.'
      });
    }
  }
</script>

<script>
    function confirmarCerrarSesion() {
        Swal.fire({
            title: '¿Deseas cerrar sesión?',
            text: "Tu sesión actual se cerrará y deberás volver a iniciar sesión.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'red',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
            Swal.fire({
                title: 'Cerrando sesión...',
                text: 'Por favor espera un momento.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                localStorage.removeItem("userAllura");
                localStorage.removeItem("token");
                localStorage.removeItem("token_exp");
                window.location.href = "../index.php"
            });
            }
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('aside');
    const fondo = document.querySelector('.fondo-sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        fondo.classList.toggle('active');
        toggleBtn.classList.toggle('active');
        });

        fondo.addEventListener('click', () => {
        sidebar.classList.remove('active');
        fondo.classList.remove('active');
        toggleBtn.classList.remove('active');
        });
    }
    });
</script>
