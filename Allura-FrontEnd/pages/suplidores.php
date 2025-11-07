<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suplidores</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../css/suplidor.css">
  <?php require_once('../config.php');?>
</head>

<body>
  <div class="layout">
    <div id="sidebar-container">
      <?php include("../includes/sidebar.php"); ?>
    </div>

    <div class="home-container">
      <?php include("../includes/header.php")?>

      <main>
        <div class="simbolo-fondo">
          <img src="../assets/images/simbolo-fondo.png">
        </div>

        <div class="container-custom">
          <div class="d-flex align-items-center gap-2 mb-3">
            <button class="header-btn" data-bs-toggle="modal" data-bs-target="#supplierModal">
              <i class="bi bi-plus-lg"></i> Crear Suplidor
            </button>

            <div class="search-input-wrap">
              <i class="bi bi-search search-icon"></i>
              <input type="text" id="searchSupplier" class="search-input" placeholder="Buscar suplidor...">
            </div>

          </div>

          <div class="table-responsive">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cédula/RNC</th>
                  <th>Nombre</th>
                  <th>Número de cuenta de banco</th>
                  <th>Contacto</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tableBody"></tbody>
            </table>
          </div>
        </div>

        <div class="copyrigth">
          <p>Copyrigth © 2025 • Desarrollado por Sethor</p>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Ver Productos -->
  <div class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="productsModalLabel">Productos del Suplidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="productsList" class="list-group"></ul>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Crear / Editar Suplidor (se reutiliza mismo formulario) -->
  <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="supplierModalLabel">Nuevo Suplidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="supplierForm">
            <input type="hidden" id="supplierId">

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" placeholder="Carlos" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="apellido" placeholder="Ortiz" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Cédula / RNC / Pasaporte <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ruc" placeholder="08099039" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Género <span class="text-danger">*</span></label>
                <select class="form-select" id="genero" required>
                  <option value="">---Seleccione---</option>
                  <option value="Hombre">Hombre</option>
                  <option value="Mujer">Mujer</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre comercial</span></label>
              <input type="text" class="form-control" id="comercial" placeholder="Sellner">
            </div>

            <div class="mb-3">
              <label class="form-label">Producto <span class="text-danger">*</span></label>
              <input class="form-control" id="producto" required></input>
            </div>

            <div class="mb-3">
              <label class="form-label">Número de cuenta de banco (opcional)</label>
              <input type="text" class="form-control" id="cuenta" placeholder="Número de cuenta">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de cuenta de Banco (opcional)</label>
                <input type="text" class="form-control" id="banco" placeholder="Ej. Banco Popular">
            </div>


            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Correo electrónico (opcional)</label>
                <input type="email" class="form-control" id="email" placeholder="correo@ejemplo.com">
              </div>
              <div class="col-md-6">
                <label class="form-label">Teléfono (opcional)</label>
                <input type="text" class="form-control" id="telefono" placeholder="+1 809 555 5555">
              </div>
            </div>

            <button type="submit" class="btn-save">Guardar Datos</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const API_BASE = "<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/suppliers";
    const tableBody = document.getElementById("tableBody");
    let deleteId = null;

    // ====================
    // 🔹 CARGAR SUPLIDORES
    // ====================
    async function loadSuppliers() {
      tableBody.innerHTML = `
        <tr><td colspan="7" class="text-center text-muted py-3">Cargando suplidores...</td></tr>`;

      try {
        const res = await fetch(`${API_BASE}/list.php`);
        const data = await res.json();
        if (!data.status) throw new Error(data.message);

        const suppliers = data.data || [];

        // 🔹 Guardar en almacenamiento local
        localStorage.setItem("suppliers", JSON.stringify(suppliers));

        renderSuppliersTable(suppliers);

      } catch (error) {
        console.warn("⚠️ Error al obtener suplidores desde la API:", error.message);

        // 🔹 Si hay un error, intentar usar datos locales
        const localData = JSON.parse(localStorage.getItem("suppliers") || "[]");

        if (localData.length > 0) {
          renderSuppliersTable(localData);
          Swal.fire("Modo sin conexión", "Mostrando suplidores guardados localmente.", "info");
        } else {
          tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3">No hay suplidores disponibles.</td></tr>`;
        }
      }
    }

    // ====================
    // 🔹 CREAR / ACTUALIZAR SUPLIDOR
    // ====================
    document.getElementById("supplierForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        const id = document.getElementById("supplierId").value.trim();
        const nombre = document.getElementById("nombre").value.trim();
        const apellido = document.getElementById("apellido").value.trim();
        const ruc = document.getElementById("ruc").value.trim();
        const genero = document.getElementById("genero").value.trim();
        const comercial = document.getElementById("comercial").value.trim();
        const producto = document.getElementById("producto").value.trim();
        const cuenta = document.getElementById("cuenta").value.trim();
        const email = document.getElementById("email").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const banco = document.getElementById("banco").value.trim();

        if (!nombre || !apellido || !ruc || !genero || !producto)
            return Swal.fire("Atención", "Complete los campos obligatorios.", "warning");

        const payload = {
            id: id || null,
            name: `${nombre} ${apellido}`,
            ruc,
            gender: genero,
            commercial_name: comercial || null,
            product: producto,
            address: cuenta || null,
            bank_name: banco || null,
            email: email || null,
            phone: telefono || null
        };

      const endpoint = id ? "update.php" : "add.php";
      const method = id ? "PUT" : "POST";

      try {
        const res = await fetch(`${API_BASE}/${endpoint}`, {
          method,
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.status) {
          const modalEl = document.getElementById("supplierModal");
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();

          // Esperar a que termine la animación de cierre antes de continuar
          modalEl.addEventListener('hidden.bs.modal', () => {
            // 🔹 Limpieza garantizada del backdrop
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // 🔹 Mostrar confirmación
            Swal.fire({
              icon: "success",
              title: "Éxito",
              text: data.message,
              confirmButtonColor: "#198754"
            });

            // 🔹 Recargar lista
            loadSuppliers();
          }, { once: true }); // <== evita duplicar eventos
        }
        else {
          Swal.fire("Error", data.message, "error");
        }
      } catch {
        Swal.fire("Error", "No se pudo conectar al servidor.", "error");
      }
    });

    function openEdit(s) {
        document.getElementById("supplierModalLabel").textContent = "Editar Suplidor";
        document.getElementById("supplierId").value = s.id;
        document.getElementById("nombre").value = s.name.split(" ")[0] || "";
        document.getElementById("apellido").value = s.name.split(" ").slice(1).join(" ");
        document.getElementById("ruc").value = s.ruc || "";
        document.getElementById("genero").value = s.gender || "";
        document.getElementById("comercial").value = s.commercial_name || "";
        document.getElementById("producto").value = s.product || "";
        document.getElementById("email").value = s.email || "";
        document.getElementById("telefono").value = s.phone || "";
        document.getElementById("cuenta").value = s.address || "";
        document.getElementById("banco").value = s.bank_name || "";

      new bootstrap.Modal(document.getElementById("supplierModal")).show();
    }

    // ====================
    // 🧹 LIMPIAR FORMULARIO AL CERRAR EL MODAL
    // ====================
    const supplierModal = document.getElementById("supplierModal");
    supplierModal.addEventListener("hidden.bs.modal", () => {
    // Reiniciar el formulario
    const form = document.getElementById("supplierForm");
    form.reset();

    // Limpiar ID oculto
    document.getElementById("supplierId").value = "";

    // Restablecer título
    document.getElementById("supplierModalLabel").textContent = "Nuevo Suplidor";

    // Asegurar que todos los selects vuelvan al valor inicial
    document.getElementById("genero").selectedIndex = 0;
    document.getElementById("producto").selectedIndex = 0;
    });


    function setDelete(id){ deleteId = id; }

    async function confirmDelete(){
      if(!deleteId) return;
      const res = await fetch(`${API_BASE}/delete.php`, {
        method:"DELETE",
        headers:{ "Content-Type": "application/json" },
        body:JSON.stringify({ id: deleteId })
      });
      const data = await res.json();
      if(data.status){
        Swal.fire("Eliminado", data.message, "success");
        bootstrap.Modal.getInstance(document.getElementById("deleteModal")).hide();
        
        const suppliers = JSON.parse(localStorage.getItem("suppliers") || "[]");
        const updated = suppliers.filter(s => (s.id ?? s.ID) !== deleteId);
        localStorage.setItem("suppliers", JSON.stringify(updated));
        loadSuppliers();
      } else Swal.fire("Error", data.message, "error");
      deleteId=null;
    }

    function showProductsModal(supplier) {
      const modal = new bootstrap.Modal(document.getElementById("productsModal"));
      const list = document.getElementById("productsList");
      const products = Array.isArray(supplier.products) ? supplier.products : [];

      list.innerHTML = products.map(p => `<li class="list-group-item">${p}</li>`).join("");
      document.getElementById("productsModalLabel").textContent = `Productos de ${supplier.name || 'Suplidor'}`;
      modal.show();
    }

    function renderSuppliersTable(suppliers) {
      if (suppliers.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No hay suplidores registrados</td></tr>`;
        return;
      }

      tableBody.innerHTML = suppliers.map((s, index) => {
        const productos = Array.isArray(s.products) ? s.products : [];
        const primeros = productos.slice(0, 3);
        const mostrarProductos = primeros.join(", ");
        const tieneMas = productos.length > 3;

        return `
          <tr>
            <td class="productos-cell" data-index="${index}" style="cursor:pointer;">
              ${mostrarProductos || "—"}${tieneMas ? " ..." : ""}
            </td>
            <td>${s.ruc || "—"}</td>
            <td>
              <div class="user-info">
                <div class="user-avatar">${s.name?.charAt(0).toUpperCase() || "?"}</div>
                <div class="user-details">
                  <span class="user-name">${s.name || "—"}</span>
                  <span class="user-location text-muted small">${s.commercial_name || ""}</span>
                  ${s.email ? `<span class="user-email text-muted small">${s.email}</span>` : ""}
                </div>
              </div>
            </td>
            <td>${s.address ? `${s.address}${s.bank_name ? ' (' + s.bank_name + ')' : ''}` : '—'}</td>
            <td class="text-center">
              <button class="action-btn" onclick="callPhone('${s.phone || ""}')"><i class="bi bi-telephone-fill"></i></button>
              <button class="action-btn" onclick="callWhats('${s.phone || ""}')"><i class="bi bi-whatsapp"></i></button>
            </td>
            <td class="text-center">
              <button class="action-btn edit" onclick='openEdit(${JSON.stringify(s)})'><i class="bi bi-pencil-fill"></i></button>
              <button class="action-btn delete" onclick="confirmDeleteSweet(${s.id || 0})"><i class="bi bi-trash-fill"></i></button>
            </td>
          </tr>
        `;
      }).join("");

      // 🔹 Evento clic productos
      document.querySelectorAll(".productos-cell").forEach(el => {
        el.addEventListener("click", e => {
          e.preventDefault();
          const index = parseInt(el.getAttribute("data-index"));
          showProductsModal(suppliers[index]);
        });
      });
    }
    const searchInput = document.getElementById("searchSupplier");

    searchInput.addEventListener("input", () => {
      let suppliers = JSON.parse(localStorage.getItem("suppliers") || "[]");
      const term = searchInput.value.toLowerCase();

      const filtered = suppliers.filter(s =>
        (s.name || "").toLowerCase().includes(term) ||
        (s.product || "").toLowerCase().includes(term) ||
        (s.commercial_name || "").toLowerCase().includes(term)
      );

      renderSuppliersTable(filtered);
    });

    function callPhone(num){ if(num) window.location.href=`tel:${num}`; }
    function callWhats(num){ if(num) window.open(`https://wa.me/${num}`,"_blank"); }

    loadSuppliers();
  </script>

  <!-- ELIMINAR  -->
  <script>
    async function confirmDeleteSweet(id) {
      const confirm = await Swal.fire({
        title: "¿Eliminar este suplidor?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6"
      });

      if (!confirm.isConfirmed) return;

      try {
        Swal.fire({ title: "Eliminando suplidor...", didOpen: () => Swal.showLoading() });

        const res = await fetch(`${API_BASE}/delete.php`, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id })
        });

        const data = await res.json();
        Swal.close();

        if (!data.status) throw new Error(data.message);

        Swal.fire("Eliminado", data.message, "success");

        // 🧹 Actualizar lista local y recargar
        const suppliers = JSON.parse(localStorage.getItem("suppliers") || "[]");
        const updated = suppliers.filter(s => (s.id ?? s.ID) !== id);
        localStorage.setItem("suppliers", JSON.stringify(updated));
        loadSuppliers();

      } catch (err) {
        Swal.fire("Error", err.message || "No se pudo eliminar el suplidor.", "error");
      }
    }

  </script>
</body>
</html>
