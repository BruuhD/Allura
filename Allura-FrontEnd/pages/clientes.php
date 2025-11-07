<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes</title>
  <link rel="stylesheet" href="../css/clientes.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
          <div class="container">
            <div class="summary">
                <div class="summary-card">
                    <h3>Total de Facturas Por Pagar</h3>
                    <div class="amount text-danger fw-bold" id="totalPendiente">RD$ 0.00</div>
                </div>
                <div class="summary-card">
                    <h3>Total de Facturas Pagadas</h3>
                    <div class="amount text-success fw-bold" id="totalPagado">RD$ 0.00</div>
                </div>
            </div>

            <div class="clients-header d-flex justify-content-between align-items-center mb-3">
              <h3 class="fw-bold">Listado de Clientes</h3>

              <div class="d-flex align-items-center gap-2">
                <div class="search-input-wrap">
                  <i class="bi bi-search search-icon"></i>
                  <input type="text" id="searchClient" class="search-input"
                    placeholder="Buscar cliente...">
                </div>

                <button id="btnAddClient" class="btn btn-primary">
                  <i class="bi bi-person-plus"></i> Agregar Cliente
                </button>
              </div>
            </div>


            <div class="clients-grid" id="clientsGrid">
              <div class="text-center py-5 w-100">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-secondary">Cargando clientes...</p>
              </div>
            </div>
          </div>
        </div>

        <div class="copyrigth">
          <p>Copyrigth © 2025 • Desarrollado por Sethor</p>
        </div>
      </main>
    </div>
  </div>

  <!-- ================= MODAL NUEVO CLIENTE ================= -->
  <div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold" id="modalClienteLabel">Nuevo Cliente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <form id="formNuevoCliente" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Apellido <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lastname" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Cédula / RNC / Pasaporte <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="ruc" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Género <span class="text-danger">*</span></label>
              <select class="form-select" id="gender" required>
                <option value="">---Seleccione---</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Empresa">Empresa</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nombre Comercial</label>
              <input type="text" class="form-control" id="commercial_name">
            </div>

            <div class="col-md-6">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control" id="product" placeholder="Ej. ISO 9001:2015">
            </div>

            <div class="col-md-6">
              <label class="form-label">Número de cuenta de banco</label>
              <input type="text" class="form-control" id="account_number" placeholder="Número de cuenta">
            </div>

            <div class="col-md-6">
              <label class="form-label">Tipo de cuenta de Banco</label>
              <input type="text" class="form-control" id="bank_name" placeholder="Ej. Banco Popular">
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" placeholder="correo@ejemplo.com">
            </div>

            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="phone" placeholder="+1 809 555 5555">
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="btnGuardarCliente">Guardar Datos</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= MODAL DETALLES DE CLIENTE ================= -->
  <div class="modal fade" id="modalDetalleCliente" tabindex="-1" aria-labelledby="modalDetalleClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header text-white"  style="background:#8EB525">
          <h5 class="modal-title fw-bold" id="modalDetalleClienteLabel">Detalles del Cliente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-0">
          <ul class="nav nav-tabs" id="detalleTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-facturas" data-bs-toggle="tab" data-bs-target="#facturas" type="button" role="tab">
                <i class="bi bi-receipt"></i> Facturas
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-productos" data-bs-toggle="tab" data-bs-target="#productos" type="button" role="tab">
                <i class="bi bi-box-seam"></i> Productos
              </button>
            </li>
          </ul>

          <div class="tab-content p-3" id="detalleClienteTabsContent">
            <!-- 🧾 FACTURAS -->
            <div class="tab-pane fade show active" id="facturas" role="tabpanel">
              <div id="facturasBody">
                <div class="text-center text-muted py-5">
                  <div class="spinner-border text-primary" role="status"></div>
                  <p class="mt-3">Cargando facturas...</p>
                </div>
              </div>
            </div>

            <!-- 📦 PRODUCTOS -->
            <div class="tab-pane fade" id="productos" role="tabpanel">
              <div id="productosBody">
                <div class="text-center text-muted py-5">
                  <div class="spinner-border text-success" role="status"></div>
                  <p class="mt-3">Cargando productos...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        async function loadResumenVentas() {
        const totalPendiente = document.getElementById("totalPendiente");
        const totalPagado = document.getElementById("totalPagado");

        totalPendiente.innerHTML = `<div class="spinner-border spinner-border-sm text-danger" role="status"></div>`;
        totalPagado.innerHTML = `<div class="spinner-border spinner-border-sm text-success" role="status"></div>`;

        try {
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/clients/summary.php");
            const result = await res.json();

            if (result.status && result.data) {
            const p = result.data.pendiente;
            const g = result.data.pagado;

            totalPendiente.innerHTML = `
                RD$ ${Number(p.monto).toLocaleString("es-DO", { minimumFractionDigits: 1 })} 
                <br><small class="text-muted">${p.cantidad} factura(s)</small>
            `;

            totalPagado.innerHTML = `
                RD$ ${Number(g.monto).toLocaleString("es-DO", { minimumFractionDigits: 1 })} 
                <br><small class="text-muted">${g.cantidad} factura(s)</small>
            `;
            } else {
            totalPendiente.innerHTML = "RD$ 0.00";
            totalPagado.innerHTML = "RD$ 0.00";
            }
        } catch (err) {
            console.error("Error al cargar resumen:", err);
            totalPendiente.textContent = "Error";
            totalPagado.textContent = "Error";
        }
        }

        document.addEventListener("DOMContentLoaded", loadResumenVentas);

    </script>

  <!-- Script principal -->
  <script>
    const clientsGrid = document.getElementById("clientsGrid");
    const btnAddClient = document.getElementById("btnAddClient");

    // Abrir modal
    btnAddClient.addEventListener("click", () => {
      const modal = new bootstrap.Modal(document.getElementById("modalNuevoCliente"));
      modal.show();
    });

    // 🔹 Guardar nuevo cliente
    document.getElementById("btnGuardarCliente").addEventListener("click", async () => {
      const data = {
          name: `${document.getElementById("name").value.trim()} ${document.getElementById("lastname").value.trim()}`,
          ruc: document.getElementById("ruc").value.trim(),
          gender: document.getElementById("gender").value.trim(),
          commercial_name: document.getElementById("commercial_name").value.trim(),
          product: document.getElementById("product").value.trim(),
          bank_name: document.getElementById("bank_name").value.trim(),
          email: document.getElementById("email").value.trim(),
          phone: document.getElementById("phone").value.trim(),
      };

      const btn = document.getElementById("btnGuardarCliente");
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Guardando...`;

      if (!data.name || !data.ruc || !data.gender) {
        Swal.fire("Campos incompletos", "Por favor complete los campos obligatorios.", "warning");
        btn.disabled = false;
        btn.innerHTML = "Guardar Datos";
        return;
      }


      try {
        const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/clients/add.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.status) {
          Swal.fire("Éxito", result.msg, "success");
          document.getElementById("formNuevoCliente").reset();
          const modal = bootstrap.Modal.getInstance(document.getElementById("modalNuevoCliente"));
          modal.hide();
          loadClients();
        } else {
          Swal.fire("Error", result.msg, "error");
        }
      } catch (err) {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = "Guardar Datos";
        Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
      }
    });
  </script>

  <script>
    async function openClientDetail(cliente) {
      const modal = new bootstrap.Modal(document.getElementById("modalDetalleCliente"));
      const body = document.getElementById("detalleClienteBody");
      document.getElementById("modalDetalleClienteLabel").textContent = `Facturas de ${cliente.name}`;

      body.innerHTML = `
        <div class="text-center text-muted py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-3">Cargando facturas...</p>
        </div>
      `;
      modal.show();

      try {
        const res = await fetch("<?php echo URL_BACKEND; ?>" +`/allura/allura-backend/api/clients/orders_pending.php?ruc=${encodeURIComponent(cliente.ruc)}`);
        const result = await res.json();

        if (!result.status || !result.data || result.data.length === 0) {
          body.innerHTML = `
            <div class="text-center text-muted py-5">
              <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
              <p>No hay facturas pendientes para este cliente.</p>
            </div>
          `;
          return;
        }

        const rows = result.data.map(o => `
          <tr>
            <td>${o.order_number}</td>
            <td>${o.productos || "—"}</td>
            <td>RD$ ${Number(o.total).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
            <td>RD$ ${Number(o.abono).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
            <td class="text-danger fw-bold">RD$ ${Number(o.pendiente).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
            <td><span class="badge bg-warning text-dark">${o.estado}</span></td>
            <td>${o.fecha}</td>
          </tr>
        `).join("");

        body.innerHTML = `
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th># Orden</th>
                  <th>Productos</th>
                  <th>Total</th>
                  <th>Abono</th>
                  <th>Pendiente</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          </div>
        `;
      } catch (err) {
        console.error("Error al cargar órdenes:", err);
        body.innerHTML = `
          <div class="text-center text-danger py-5">
            <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
            Error al cargar facturas del cliente.
          </div>
        `;
      }
    }

    async function loadClients() {
      try {
        const response = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/clients/list.php");
        const data = await response.json();

        clientsGrid.innerHTML = "";

        if (!data.status || !data.data || data.data.length === 0) {
          clientsGrid.innerHTML = `
            <div class="text-center py-5 w-100 text-muted">
              <i class="bi bi-person-x fs-1 d-block mb-2"></i>
              No hay clientes registrados.
            </div>
          `;
          return;
        }

        data.data.forEach(cliente => {
          const card = document.createElement("div");
          card.className = "client-card shadow-sm";
          card.style.cursor = "pointer";
          card.innerHTML = `
            <div class="client-info text-center py-3">
              <h5 class="fw-bold mb-1">${cliente.name}</h5>
            </div>
          `;
          card.addEventListener("click", () => openClientDetail(cliente));
          clientsGrid.appendChild(card);
        });
      } catch (error) {
        console.error("Error al cargar clientes:", error);
        clientsGrid.innerHTML = `
          <div class="text-center py-5 w-100 text-danger">
            <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
            Error al conectar con el servidor.
          </div>
        `;
      }
    }

    // FILTRAR CLIENTES EN TIEMPO REAL
    const searchClient = document.getElementById("searchClient");

    searchClient.addEventListener("input", () => {
      const term = searchClient.value.toLowerCase();
      const cards = document.querySelectorAll(".client-card");

      cards.forEach(card => {
        const name = card.textContent.toLowerCase();
        card.style.display = name.includes(term) ? "block" : "none";
      });
    });

    async function openClientDetail(cliente) {
      const modal = new bootstrap.Modal(document.getElementById("modalDetalleCliente"));
      const facturasBody = document.getElementById("facturasBody");
      const productosBody = document.getElementById("productosBody");
      document.getElementById("modalDetalleClienteLabel").textContent = `Detalles de ${cliente.name}`;
      modal.show();

      // 🔹 Cargar facturas
      try {
        const res = await fetch("<?php echo URL_BACKEND; ?>" +`/allura/allura-backend/api/clients/orders_pending.php?ruc=${encodeURIComponent(cliente.ruc)}`);
        const result = await res.json();

        if (!result.status || !result.data || result.data.length === 0) {
          facturasBody.innerHTML = `<div class="text-center text-muted py-5"><i class="bi bi-journal-x fs-1 d-block mb-2"></i>No hay facturas pendientes.</div>`;
        } else {
          const rows = result.data.map(o => `
            <tr>
              <td>${o.order_number}</td>
              <td>${o.productos || "—"}</td>
              <td>RD$ ${Number(o.total).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
              <td>RD$ ${Number(o.abono).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
              <td class="text-danger fw-bold">RD$ ${Number(o.pendiente).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
              <td><span class="badge bg-warning text-dark">${o.estado}</span></td>
              <td>${o.fecha}</td>
            </tr>
          `).join("");

          facturasBody.innerHTML = `
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th># Orden</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Abono</th>
                    <th>Pendiente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>${rows}</tbody>
              </table>
            </div>
          `;
        }
      } catch (err) {
        facturasBody.innerHTML = `<div class="text-center text-danger py-5">Error al cargar facturas.</div>`;
      }

      // 🔹 Cargar productos
      try {
        const res = await fetch("<?php echo URL_BACKEND; ?>" +`/allura/allura-backend/api/clients/products.php?ruc=${encodeURIComponent(cliente.ruc)}`);
        const result = await res.json();

        if (!result.status || !result.data || result.data.length === 0) {
          productosBody.innerHTML = `<div class="text-center text-muted py-5">
            <i class="bi bi-box fs-1 d-block mb-2"></i>No hay productos registrados.
          </div>`;
        } else {
          const html = result.data.map(grupo => `
            <div class="mb-4">
              <h6 class="fw-bold text-primary mb-2">
                Orden #${grupo.order_number} <small class="text-muted">(${grupo.fecha || ''})</small>
              </h6>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Producto</th>
                      <th>Unidad</th>
                      <th>Cant.</th>
                      <th>Precio</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${grupo.productos.map(p => `
                      <tr>
                        <td>${p.producto}</td>
                        <td>${p.unidad}</td>
                        <td>${p.cantidad}</td>
                        <td>RD$ ${Number(p.precio).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
                        <td>RD$ ${Number(p.total).toLocaleString("es-DO", { minimumFractionDigits: 1 })}</td>
                      </tr>
                    `).join("")}
                  </tbody>
                </table>
              </div>
            </div>
          `).join("");

          productosBody.innerHTML = html;
        }

      } catch (err) {
        productosBody.innerHTML = `<div class="text-center text-danger py-5">Error al cargar productos.</div>`;
      }
    }

    document.addEventListener("DOMContentLoaded", loadClients);
  </script>


</body>
</html>
